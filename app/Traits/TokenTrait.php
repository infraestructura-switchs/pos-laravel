<?php

namespace App\Traits;

use App\Exceptions\CustomException;
use App\Models\AccessToken;
use App\Services\FactusConfigurationService;
use Illuminate\Support\Facades\Http;

trait TokenTrait
{
    public static function getAccessToken()
    {
        try {
            \Log::info('🔐 TokenTrait - Obteniendo nuevo token de acceso...');
            
            $apiConfiguration = FactusConfigurationService::apiConfiguration();

            $data = [
                'grant_type' => 'password',
                'client_id' => $apiConfiguration['client_id'],
                'client_secret' => $apiConfiguration['client_secret'],
                'username' => $apiConfiguration['email'],
                'password' => $apiConfiguration['password'],
            ];

            \Log::info('📡 TokenTrait - Enviando solicitud de autenticación a Factus', [
                'url' => $apiConfiguration['url'].'oauth/token',
                'grant_type' => 'password',
                'client_id' => substr($apiConfiguration['client_id'], 0, 10).'...',
                'email' => $apiConfiguration['email']
            ]);

            $response = Http::acceptJson()->post($apiConfiguration['url'].'oauth/token', $data);

            $access_token_data = $response->json();

            \Log::info('📥 TokenTrait - Respuesta recibida de Factus', [
                'status' => $response->status(),
                'has_access_token' => isset($access_token_data['access_token']),
                'has_refresh_token' => isset($access_token_data['refresh_token']),
                'has_expires_in' => isset($access_token_data['expires_in'])
            ]);

            if ($response->status() !== 200) {
                if (is_array($access_token_data) && array_key_exists('error', $access_token_data)) {
                    \Log::error('❌ TokenTrait - Error de autenticación', [
                        'error' => $access_token_data['error'],
                        'error_description' => $access_token_data['error_description'] ?? 'N/A'
                    ]);
                    
                    if ($access_token_data['error'] === 'invalid_client') {
                        throw new CustomException('Error al autenticarse con la API de Factus. Verifique las credenciales (client_id y client_secret).');
                    } elseif ($access_token_data['error'] === 'invalid_grant') {
                        throw new CustomException('Error al autenticarse con la API de Factus. Verifique el email y contraseña.');
                    }
                }
                
                \Log::error('❌ TokenTrait - Error al obtener token', [
                    'status' => $response->status(),
                    'response' => $access_token_data
                ]);
                throw new CustomException('Error al obtener el token de acceso de Factus. Código de respuesta: ' . $response->status());
            }

            // Validar que la respuesta contiene los datos necesarios
            if (!isset($access_token_data['access_token']) || !isset($access_token_data['refresh_token']) || !isset($access_token_data['expires_in'])) {
                \Log::error('❌ TokenTrait - Respuesta incompleta', [
                    'response' => $access_token_data
                ]);
                throw new CustomException('La respuesta de la API de Factus no contiene los datos esperados del token.');
            }

            AccessToken::whereNotNull('id')->delete();

            $access_token = AccessToken::create([
                'access_token' => $access_token_data['access_token'],
                'refresh_token' => $access_token_data['refresh_token'],
                'expires_at' => now()->addSecond($access_token_data['expires_in']),
            ]);

            \Log::info('✅ TokenTrait - Token de acceso creado exitosamente', [
                'expires_at' => $access_token->expires_at
            ]);

            return $access_token;
        } catch (\Exception $e) {
            \Log::error('❌ TokenTrait::getAccessToken - Error inesperado', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            throw $e;
        }
    }
}
