<?php

namespace App\Http\Livewire\Admin\Factus;

use App\Models\AccessToken;
use App\Models\FactusConfiguration;
use App\Services\Factus\HttpService;
use App\Services\FactusConfigurationService;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Connection extends Component
{
    public $api;

    public $isApiEnabled;

    protected function rules()
    {
        return [
            'api.url' => 'required|string|url|max:255',
            'api.client_id' => 'required|string|max:255',
            'api.client_secret' => 'required|string|max:255',
            'api.email' => 'required|email|string|max:255',
            'api.password' => 'required|string|max:255',
        ];
    }

    public function mount()
    {
        // Permitir acceso al SuperAdmin o a usuarios con rol Administrador del tenant
        if (!isRoot()) {
            if (!auth()->check() || !auth()->user()->hasRole('Administrador')) {
                abort(403, 'No tienes permisos para acceder a esta sección');
            }
        }
    }

    public function render()
    {
        $this->api = FactusConfigurationService::apiConfiguration();
        $this->isApiEnabled = FactusConfigurationService::isApiEnabled();

        return view('livewire.admin.factus.connection')
            ->layout('layouts.app')
            ->layoutData(['title' => 'Conexión Factus']);
    }

    public function testConnection()
    {
        $url = 'test-connection';

        try {
            AccessToken::truncate();

            $request = HttpService::apiHttp()->get($url);

            if ($request->status() === 200 && $request->json()['message'] === 'success') {
                $this->emit('success', 'Conexión exitosa');

                return true;
            }
        } catch (\Throwable $th) {
            $this->emit('error', 'Ha ocurrido un error al intentar conectarse a la API, verifica las credenciales');
        }

        return false;
    }

    public function update()
    {
        $data = $this->validate();

        try {
            $factusConfiguration = FactusConfiguration::first();
            $data['api']['url'] = $this->formatUrl($data['api']['url']);
            $factusConfiguration->api = $data['api'];
            $factusConfiguration->save();

            Cache::forget('api_configuration');
        } catch (\Throwable $th) {
            $this->emit('error', 'Ha ocurrido un error al actualizar la configuración de la API');
        }

        $this->emit('success', 'Configuración actualizada con éxito');
    }

    public function updateApiStatus()
    {
        if (!FactusConfigurationService::isApiEnabled() && ! $this->testConnection()) {
            return;
        }

        try {
            $factusConfiguration = FactusConfiguration::first();
            $factusConfiguration->is_api_enabled = ! $factusConfiguration->is_api_enabled;
            $factusConfiguration->save();
            Cache::forget('is_api_enabled');
        } catch (\Throwable $th) {
            $this->emit('error', 'Ha ocurrido un error al actualizar la configuración de la API');
        }

        $this->emitTo('admin.menu', 'render');
        $this->emit('success', 'Configuración actualizada con éxito');
    }

    protected function formatUrl(string $url): string
    {
        return substr($url, -1) === '/' ? $url : $url.'/';
    }
}
