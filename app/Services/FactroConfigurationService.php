<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\FactroConfiguration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FactroConfigurationService
{
    public static function apiConfiguration()
    {
        if (Cache::has('factro_api_configuration')) {
            Log::info('FactroConfigurationService::apiConfiguration - fuente=cache', [
                'cache_key' => 'factro_api_configuration',
            ]);
            return Cache::get('factro_api_configuration');
        }

        $configuration = FactroConfiguration::first();
        Log::info('FactroConfigurationService::apiConfiguration - fuente=database', [
            'configuration_id' => optional($configuration)->id,
        ]);
        $api = $configuration ? $configuration->api : [];

        /*$api = [
            'url' => env('ARQFE_API_URL'),
            'api_key' => env('ARQFE_API_KEY'),
            'company_id' => env('ARQFE_COMPANY_ID'),
            'programa' => env('ARQFE_PROGRAMA', 'POS'),
        ];*/

        $configuration = FactroConfiguration::first();
        
        if (!$configuration) {
            throw new CustomException('No se ha configurado la facturación electrónica. Por favor, configure primero la conexión con Factus.');
        }

        $api = $configuration->api;
        
        if (!$api || !is_array($api)) {
            throw new CustomException('La configuración de la API de Factus es inválida.');
        }

        // Validar que existan las claves necesarias
        $requiredKeys = ['url', 'api_key_id', 'company_id', 'program'];
        foreach ($requiredKeys as $key) {
            if (!isset($api[$key]) || empty($api[$key])) {
                throw new CustomException("Falta la configuración: {$key}. Por favor, complete la configuración de Factus.");
            }
        }

        Cache::forever('factro_api_configuration', $api);
        Log::info('FactroConfigurationService::apiConfiguration - cache actualizado', [
            'cache_key' => 'factro_api_configuration',
        ]);

        return $api;
    }

    public static function isApiEnabled()
    {
        if (Cache::has('factro_is_api_enabled')) {
            $cachedValue = (bool) Cache::get('factro_is_api_enabled');
            Log::info('FactroConfigurationService::isApiEnabled - fuente=cache', [
                'cache_key' => 'factro_is_api_enabled',
                'enabled' => $cachedValue,
            ]);
            return $cachedValue;
        }

        $configuration = FactroConfiguration::first();
        Log::info('FactroConfigurationService::isApiEnabled - fuente=database', [
            'configuration_id' => optional($configuration)->id,
        ]);
        $apiEnabled = $configuration ? $configuration->is_api_enabled : false;

        Cache::forever('factro_is_api_enabled', $apiEnabled);
        Log::info('FactroConfigurationService::isApiEnabled - cache actualizado', [
            'cache_key' => 'factro_is_api_enabled',
            'enabled' => (bool) $apiEnabled,
        ]);

        return (bool) $apiEnabled;
    }
}
