<?php

namespace App\Services;

use App\Models\FactroConfiguration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FactroConfigurationService
{
    public static function apiConfiguration()
    {
        if (Cache::has('factro_api_configuration')) {
            return Cache::get('factro_api_configuration');
        }

        //$configuration = FactroConfiguration::first();
        //$api = $configuration ? $configuration->api : [];

        $api = [
            'url' => env('ARQFE_API_URL'),
            'api_key' => env('ARQFE_API_KEY'),
            'company_id' => env('ARQFE_COMPANY_ID'),
            'programa' => env('ARQFE_PROGRAMA', 'POS'),
        ];

        Cache::forever('factro_api_configuration', $api);
        Log::info('Config Factro cargada desde base de datos', ['api' => $api]);

        return $api;
    }

    public static function isApiEnabled()
    {
        if (Cache::has('factro_is_api_enabled')) {
            return (bool) Cache::get('factro_is_api_enabled');
        }

        $configuration = FactroConfiguration::first();
        $apiEnabled = $configuration ? $configuration->is_api_enabled : false;

        Cache::forever('factro_is_api_enabled', $apiEnabled);
        Log::info('Factro API Enabled?', ['enabled' => $apiEnabled]);

        return (bool) $apiEnabled;
    }
}
