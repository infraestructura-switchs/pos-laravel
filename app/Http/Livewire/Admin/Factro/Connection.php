<?php

namespace App\Http\Livewire\Admin\Factro;

use App\Models\AccessToken;
use App\Models\FactroConfiguration;
use App\Services\Factro\HttpService;
use App\Services\FactroConfigurationService;
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
        if (!isRoot()) abort(404);
    }

    public function render()
    {
        $this->api = FactroConfiguration::apiConfiguration();
        $this->isApiEnabled = FactroConfigurationService::isApiEnabled();

        return view('livewire.admin.factro.connection');
    }

    public function testConnection()
    {
        $url = 'test-connection';

        try {
            //AccessToken::truncate();

            //$request = HttpService::apiHttp()->get($url);
            return true;
            /*if ($request->status() === 200 && $request->json()['message'] === 'success') {
                $this->emit('success', 'Conexión exitosa');

                return true;
            }*/
        } catch (\Throwable $th) {
            $this->emit('error', 'Ha ocurrido un error al intentar conectarse a la API, verifica las credenciales');
        }

        return false;
    }

    public function update()
    {
        $data = $this->validate();

        try {
            $factroConfiguration = FactroConfiguration::first();
            $data['api']['url'] = $this->formatUrl($data['api']['url']);
            $factroConfiguration->api = $data['api'];
            $factroConfiguration->save();

            Cache::forget('factro_api_configuration');
        } catch (\Throwable $th) {
            $this->emit('error', 'Ha ocurrido un error al actualizar la configuración de la API');
        }

        $this->emit('success', 'Configuración actualizada con éxito');
    }

    public function updateApiStatus()
    {
        if (!FactroConfigurationService::isApiEnabled() && ! $this->testConnection()) {
            return;
        }

        try {
            $factroConfiguration = FactroConfiguration::first();
            $factroConfiguration->is_api_enabled = ! $factroConfiguration->is_api_enabled;
            $factroConfiguration->save();
            Cache::forget('factro_is_api_enabled');
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
