<?php

namespace App\Services\Factus;

use App\Exceptions\CustomException;
use App\Models\AccessToken;
use App\Services\FactusConfigurationService;
use App\Traits\TokenTrait;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class HttpService
{
    use TokenTrait;

    private static PendingRequest $http;

    private static $accessToken;

    private static $apiConfiguration;

    private static function resolveAuthorization(): void
    {
        try {
            $accessToken = AccessToken::first();

            if (! $accessToken) {
                Log::info('🔑 HttpService - No existe token, obteniendo nuevo token...');
                $accessToken = self::getAccessToken();
            }

            if ($accessToken->expires_at <= now()) {
                Log::info('⏰ HttpService - Token expirado, refrescando...', [
                    'expires_at' => $accessToken->expires_at,
                    'now' => now()
                ]);

                $response = Http::acceptJson()->post(self::$apiConfiguration['url'].'oauth/token', [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $accessToken->refresh_token,
                    'client_id' => self::$apiConfiguration['client_id'],
                    'client_secret' => self::$apiConfiguration['client_secret'],
                ]);

                $access_token_data = $response->json();

                if ($response->status() === 401) {
                    Log::warning('⚠️ HttpService - Token inválido, obteniendo nuevo token...', [
                        'status' => $response->status(),
                        'response' => $access_token_data
                    ]);
                    
                    if (isset($access_token_data['message']) && $access_token_data['message'] === 'The refresh token is invalid.') {
                        $accessToken = self::getAccessToken();
                        self::$accessToken = $accessToken;
                        return;
                    }
                }

                // Verificar que la respuesta contiene los datos necesarios
                if (!isset($access_token_data['access_token']) || !isset($access_token_data['refresh_token']) || !isset($access_token_data['expires_in'])) {
                    Log::error('❌ HttpService - Respuesta inválida al refrescar token', [
                        'status' => $response->status(),
                        'response' => $access_token_data
                    ]);
                    throw new CustomException('Error al refrescar el token de acceso. Respuesta inválida de la API de Factus.');
                }

                $accessToken->fill([
                    'access_token' => $access_token_data['access_token'],
                    'refresh_token' => $access_token_data['refresh_token'],
                    'expires_at' => now()->addSecond($access_token_data['expires_in']),
                ]);

                $accessToken->save();
                Log::info('✅ HttpService - Token refrescado exitosamente');
            }

            self::$accessToken = $accessToken;
        } catch (\Exception $e) {
            Log::error('❌ HttpService::resolveAuthorization - Error inesperado', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function formatEnpoint(string $endpoint): string
    {
        return self::$apiConfiguration['url'].'v1/'.$endpoint;
    }

    public static function apiHttp(): self
    {
        self::$apiConfiguration = FactusConfigurationService::apiConfiguration();
        self::resolveAuthorization();

        self::$http = Http::timeout(50)->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.self::$accessToken->access_token,
        ])
            ->acceptJson();

        return new static;
    }

    public function get(string $endpoint, array $data = []): Response
    {
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
