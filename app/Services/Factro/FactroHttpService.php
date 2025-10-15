<?php
namespace App\Services\Factro;

use App\Exceptions\CustomException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Services\FactroConfigurationService;

class FactroHttpService
{
    private static PendingRequest $http;

    private static function resolveConfiguration(): void
    {
        $config = FactroConfigurationService::apiConfiguration();
        if (empty($config['url']) || empty($config['api_key'])) {
            throw new CustomException('Configuración ARQFE incompleta en env');
        }
    }

    private function formatEndpoint(string $endpoint): string
    {
        $config = FactroConfigurationService::apiConfiguration();
        $baseUrl = rtrim($config['url'], '/');
        Log::info('Formateando endpoint FACTRO', ['baseUrl' => $baseUrl, 'endpoint' => $endpoint]);
        return $baseUrl . '/' . ltrim($endpoint, '/');
    }

    public static function apiHttp(): self
    {
        $instance = new static;
        $config = FactroConfigurationService::apiConfiguration();
        $instance::$http = Http::timeout(180) 
            ->retry(3, 10000, function ($attempt) { 
                return $attempt * 2;
            })
            ->withHeaders([
                'Accept' => 'application/json',
                'api-key' => $config['api_key'],
            ])
            ->asForm();

        Log::info('HttpService ARQFE inicializado', ['url' => $config['url']]);
        return $instance;
    }

    public function post(string $endpoint, array $data = []): Response
    {
        $config = FactroConfigurationService::apiConfiguration();
        $formData = [
            'prefijo' => $data['documento']['prefijo'],
            'numero' => $data['documento']['numeroDocumento'] ?? '1',
            'jsonOriginal' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'tipoData' => 'json',
            'programa' => $config['programa'],
            'companyId' => $config['company_id'],
            'procesar' => 'true',
            'tipoFactura' => 'factura',
        ];

        Log::info('Enviando a FACTRO', ['endpoint' => $endpoint, 'formData_keys' => array_keys($formData)]);

        ini_set('max_execution_time', 300); 
        Log::info('Max execution time extendido a 300s para FACTRO');

        $startTime = microtime(true);

        try {
            $response = self::$http->post($this->formatEndpoint($endpoint), $formData);
            Log::info('Respuesta raw de FACTRO', [
                'status' => $response->status(),
                'body_preview' => substr($response->body(), 0, 500)
            ]);
        } catch (RequestException $e) {
            $endTime = microtime(true);
            Log::error('Excepción en request FACTRO', [
                'duration_s' => round($endTime - $startTime, 2),
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            if ($e->getCode() === 0 || strpos($e->getMessage(), 'timeout') !== false) {
                throw new CustomException('Timeout en validación FACTRO. Intenta más tarde o verifica conexión.');
            }
            throw $e;
        }

        $endTime = microtime(true);
        Log::info('Respuesta raw de FACTRO', [
            'status' => $response->status(),
            'duration_s' => round($endTime - $startTime, 2),
            'body_preview' => substr($response->body(), 0, 500)
        ]);

        $this->checkResponseErrors($response);
        return $response;
    }

    protected function checkResponseErrors(Response $response): void
    {
        $data = $response->json();
        $statusCode = $response->status();

        Log::info('Check errors FACTRO', ['status' => $statusCode, 'has_data' => !empty($data)]);

        if ($statusCode === 500 && isset($data['message'])) {
            throw new CustomException($data['message']);
        }
        if ($statusCode === 409 && isset($data['status']) && $data['status'] === 'Conflict') {
            $message = $data['errors'][0]['message'] ?? $data['message'] ?? 'Conflicto en Factro';
            throw new CustomException($message);
        }
        if ($statusCode === 422 && is_array($data)) {
            $errors = $data['data']['errors'] ?? $data['errors'] ?? null;
            if ($errors) {
                Log::error('Errores validación Factro', ['errors' => $errors]);
                throw ValidationException::withMessages($errors);
            }
        }
        if ($statusCode === 202) {
            throw new CustomException($data['message'] ?? 'Proceso pendiente en Factro');
        }
        if ($statusCode !== 200 && $statusCode !== 201) {
            $errorMessage = $data['message'] ?? 'Error inesperado en Factro (status: ' . $statusCode . ')';
            Log::error('Error Factro no manejado', ['body' => $response->body()]);
            throw new CustomException($errorMessage);
        }
    }
}