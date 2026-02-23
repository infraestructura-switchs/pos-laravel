<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\CloudinaryClientInterface;
use App\Services\Contracts\ImageServiceInterface;
use App\Services\CloudinaryService;
use App\Services\ImageService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite as ViteFacade;

class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(CloudinaryClientInterface::class, CloudinaryService::class);
        $this->app->bind(ImageServiceInterface::class, ImageService::class);
    }


    public function boot()
    {
        // Configurar zona horaria
        date_default_timezone_set('America/Bogota');

        // Configurar locale para asegurar consistencia en el formateo de números
        // LC_NUMERIC = 'C' asegura que PHP use punto como separador decimal en operaciones internas
        setlocale(LC_NUMERIC, 'C');
        // Configurar locale para fechas y texto en español
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'es_CO.UTF-8', 'es_CO', 'es');
        setlocale(LC_MONETARY, 'es_CO.UTF-8', 'es_CO', 'es_ES.UTF-8', 'es_ES', 'es');

        Blade::directive('formatToCop', function ($value) {
            return "<?php echo '$ ' . number_format((float)$value, 0, '.', ','); ?>";
        });
        // Directiva personalizada para vite con soporte multi-tenant
        Blade::directive('tenantVite', function ($expression) {
            return "<?php echo app(\\Illuminate\\Foundation\\Vite::class)($expression); ?>";
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Configurar assets para que se carguen desde el dominio central en entornos multi-tenant
        if (!$this->app->runningInConsole()) {
            $currentHost = request()->getHost();

            // Si estamos en un subdominio de tenant, asegurar que los assets apunten al central
            if (isTenantDomain($currentHost)) {
                // Forzar el prefijo de assets al dominio central para que CSS/JS carguen bien
                $assetUrl = centralDomain(withProtocol: true);
                
                // IMPORTANTE: NO sobreescribir config(['app.url' => $assetUrl]) ya que esto rompe 
                // la validación de firmas de Livewire (genera 401 en uploads)
                config(['app.asset_url' => $assetUrl]);
            }
        }
    }
}
