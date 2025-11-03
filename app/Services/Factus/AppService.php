<?php

namespace App\Services\Factus;

use App\Exceptions\CustomException;
use App\Models\FactusConfiguration;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class AppService
{
    public static function getTokenFactus(string $redirectUrl): array
    {
        $domain = (config('app.app_factus_url') ?? (App::isLocal() ? 'http://app.test' : 'https://app.factus.com.co'));

        $model = FactusConfiguration::first();
        if (!$model) {
            throw new CustomException('Configuración de Factus no encontrada');
        }

        $config = $model->api ?? [];
        $email = $config['email'] ?? null;
        $password = $config['password'] ?? null;
        if (!$email || !$password) {
            throw new CustomException('Faltan credenciales de Factus (email/password). Configúralas en Conexión Factus.');
        }

        $data = [
            'email' => $email,
            'password' => $password,
        ];

        $response = Http::acceptJson()->post("{$domain}/api/external-authentication", $data);

        if($response->status() !== 200) {
            throw new CustomException("Ha ocurrido un error inesperado al abrir Factus", 1);
        }

        $token = $response->json()['token'] ?? null;
        if (!$token) {
            throw new CustomException('Respuesta inválida de Factus: token no recibido');
        }

        return [
            'token' => $token,
            'domain' => $domain,
            'redirect_url' => $redirectUrl
        ];
    }
}
