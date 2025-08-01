<?php

namespace App\Services;

use App\Models\FactusConfiguration;
use Illuminate\Support\Facades\Cache;

class FactusConfigurationService
{
    public static function apiConfiguration()
    {
        if (Cache::has('api_configuration')) {
            return Cache::get('api_configuration');
        }

        $api = FactusConfiguration::first()->api;
        Cache::forever('api_configuration', $api);

        return $api;
    }

    public static function isApiEnabled()
    {
        if (Cache::has('is_api_enabled')) {
            return (bool) Cache::get('is_api_enabled');
        }

        $apiEnabled = FactusConfiguration::first()->is_api_enabled;
        Cache::forever('is_api_enabled', $apiEnabled);

        return (bool) $apiEnabled;
    }
}
