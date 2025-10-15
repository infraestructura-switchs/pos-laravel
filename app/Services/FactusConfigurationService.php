<?php
namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\FactusConfiguration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FactusConfigurationService
{
    public static function apiConfiguration()
    {
        if (Cache::has('api_configuration')) {
            return Cache::get('api_configuration');
        }

        $configuration = FactusConfiguration::first();
        
        if (!$configuration) {
            throw new CustomException('No se ha configurado la facturación electrónica. Por favor, configure primero la conexión con Factus.');
        }

        $api = $configuration->api;
        
        if (!$api || !is_array($api)) {
            throw new CustomException('La configuración de la API de Factus es inválida.');
        }

        // Validar que existan las claves necesarias
        $requiredKeys = ['url', 'client_id', 'client_secret', 'email', 'password'];
        foreach ($requiredKeys as $key) {
            if (!isset($api[$key]) || empty($api[$key])) {
                throw new CustomException("Falta la configuración: {$key}. Por favor, complete la configuración de Factus.");
            }
        }

        Cache::forever('api_configuration', $api);

        return $api;
    }

    public static function isApiEnabled()
    {
        if (Cache::has('is_api_enabled')) {
            return (bool) Cache::get('is_api_enabled');
        }

        $configuration = FactusConfiguration::first();
        
        if (!$configuration) {
            return false;
        }

        $apiEnabled = $configuration->is_api_enabled;
        Cache::forever('is_api_enabled', $apiEnabled);

        return (bool) $apiEnabled;
    }
}