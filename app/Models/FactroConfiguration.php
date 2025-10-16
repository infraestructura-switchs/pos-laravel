<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactroConfiguration extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected function api(): Attribute
    {
        return new Attribute(
            get: fn($value) => json_decode($value, true),
            set: fn($value) => json_encode($value)
        );
    }

    /**
     * Get API configuration from cache or database
     */
    public static function apiConfiguration(): array
    {
        return cache()->remember('factro_api_configuration', 3600, function () {
            $configuration = static::first();
            return $configuration ? $configuration->api : [];
        });
    }
}
