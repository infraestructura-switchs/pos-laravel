<?php

namespace App\Http\Livewire\Admin;

use App\Exceptions\CustomException;
use App\Services\Factus\AppService;
use App\Models\FactroConfiguration;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Menu extends Component
{
    protected $listeners = ['render' => 'refresh'];

    public function render()
    {
        return view('livewire.admin.menu');
    }

    public function refresh() {}

    public function openCashRegister()
    {
        // Abrir el modal de apertura de caja
        $this->emitTo('admin.cash-opening.create', 'openCreate');
    }

    public function getTokenFactus(): array
    {
        try {
            $token = AppService::getTokenFactus('/administrador/dashboard');
        } catch (\Throwable $th) {
            if ($th instanceof CustomException) {
                $this->emit('error', $th->getMessage());
            } else {
                $this->emit('error', 'Ha ocurrido un error inesperado al abrir Factus');
                return [];
            }
        }

        return $token;
    }

    public function getTokenFactro(): array
    {
        try {
            $domain = config('app.app_factro_url') ?? (App::isLocal() ? 'http://factro.test' : 'https://factro.com.co');
            
            $factroConfig = FactroConfiguration::first();
            if (!$factroConfig || !$factroConfig->api) {
                throw new CustomException('Configuración de Factro no encontrada');
            }

            $config = $factroConfig->api;

            $data = [
                'email' => $config['email'] ?? '',
                'password' => $config['password'] ?? '',
            ];

            $response = Http::acceptJson()->post("{$domain}/api/external-authentication", $data);

            if ($response->status() !== 200) {
                throw new CustomException("Error al autenticar con Factro");
            }

            $token = $response->json()['token'] ?? null;
            
            if (!$token) {
                throw new CustomException("No se recibió token de Factro");
            }

            return [
                'token' => $token,
                'domain' => $domain,
                'redirect_url' => '/administrador/dashboard'
            ];
        } catch (\Throwable $th) {
            if ($th instanceof CustomException) {
                $this->emit('error', $th->getMessage());
            } else {
                $this->emit('error', 'Ha ocurrido un error inesperado al abrir Factro');
            }
            return [];
        }
    }

    protected function deleteRegisters()
    {
        $logFilePath = storage_path('logs/cashRegister.log');

        if (! file_exists($logFilePath)) {
            return;
        }

        $logContent = file_get_contents($logFilePath);

        $fechaActualMenos15Dias = date('Y-m-d H:i:s', strtotime('-15 days'));

        $logLines = explode(PHP_EOL, $logContent);

        $newLogLines = array_filter($logLines, function ($line) use ($fechaActualMenos15Dias) {
            if (strpos($line, 'local.INFO:') !== false) {
                preg_match('/\[(.*?)\]/', $line, $matches);
                $fechaLinea = $matches[1];

                return strtotime($fechaLinea) > strtotime($fechaActualMenos15Dias);
            }

            return true;
        });

        $newLogContent = implode(PHP_EOL, $newLogLines);

        file_put_contents($logFilePath, $newLogContent);
    }
}
