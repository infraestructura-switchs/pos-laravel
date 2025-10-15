<?php

namespace App\Services\Factro;

use App\Exceptions\CustomException;
use App\Models\AccessToken;
use App\Services\FactroConfigurationService;
use App\Traits\FactroTokenTrait;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class HttpService
{
    use FactroTokenTrait;

    private static PendingRequest $http;

    private static $accessToken;

    private static $apiConfiguration;

    private static function resolveAuthorization(): void
    {
        $accessToken = AccessToken::first();

        if (! $accessToken) {
            $accessToken = self::getAccessToken();
        }

        if ($accessToken->expires_at <= now()) {

            $response = Http::acceptJson()->post(self::$apiConfiguration['url'].'oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $accessToken->refresh_token,
                'client_id' => self::$apiConfiguration['client_id'],
                'client_secret' => self::$apiConfiguration['client_secret'],
            ]);

            $access_token = $response->json();

            if ($response->status() === 401) {
                if ($access_token['message'] === 'The refresh token is invalid.') {
                    $access_token = self::getAccessToken();
                }
            }

            $accessToken->fill([
                'access_token' => $access_token['access_token'],
                'refresh_token' => $access_token['refresh_token'],
                'expires_at' => now()->addSeconds($access_token['expires_in']),
            ]);

            $accessToken->save();
        }

        self::$accessToken = $accessToken;
    }

    private function formatEnpoint(string $endpoint): string
    {
        return self::$apiConfiguration['url'].'v1/'.$endpoint;
    }

    public static function apiHttp(): self
    {
        self::$apiConfiguration = FactroConfigurationService::apiConfiguration();
        self::resolveAuthorization();

        $timeout = 120;
        self::$http = Http::timeout($timeout)->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.self::$accessToken->access_token,
        ])
            ->acceptJson();

        return new static;
    }

    public function get(string $endpoint, array $data = []): Response
    {
        Log::info('Realizando una solicitud GET al endpoint: '.$this->formatEnpoint($endpoint), ['data' => $data]);
        $response = self::$http->get($this->formatEnpoint($endpoint), $data);
        $this->checkResponseErrors($response);

        return $response;
    }

    public function post(string $endpoint, array $data = []): Response
    {
        $response = self::$http->post($this->formatEnpoint($endpoint), $data);
        $this->checkResponseErrors($response);

        return $response;
    }

    protected function checkResponseErrors(Response $response): void
    {
        $data = $response->json();
        $statusCode = $response->status();
        Log::info('Respuesta de la solicitud', ['status_code' => $statusCode, 'data' => $data]);

        if ($statusCode === 500 && array_key_exists('message', $data)) {
            throw new CustomException($data['message']);
        }

        if ($statusCode === 409 && (array_key_exists('status', $data) && $data['status'] === 'Conflict')) {
            $message = $data['errors'][0]['message'] ?? $data['message'] ?? 'Ha ocurrido un error inesperado, vuelve a intentarlo';
            throw new CustomException($message);
        }

        if ($statusCode === 422 && is_array($data)) {
            $errors = $data['data']['errors'] ?? $data['errors'] ?? null;

            if ($errors) {
                throw ValidationException::withMessages($errors);
            }
        }

        if ($statusCode === 202) {
            throw new CustomException($data['message']);
        }

        if ($statusCode !== 200 && $statusCode !== 201) {
            throw new CustomException('Ha ocurrido un error inesperado, vuelve a intentarlo');
        }
    }
}
