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

class AppServiceProvider extends ServiceProvider {

    public function register() {
        $this->app->bind(CloudinaryClientInterface::class, CloudinaryService::class);
        $this->app->bind(ImageServiceInterface::class, ImageService::class);
    }


    public function boot() {
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
        
        // Forzar que los assets se carguen desde el dominio central
        // Esto es crítico para multi-tenancy donde cada tenant tiene su propio subdominio
        // pero los assets CSS/JS compilados solo existen en el dominio central
        if (!$this->app->runningInConsole()) {
            $centralDomain = 'switchs.test'; // Dominio central sin www
            $currentHost = request()->getHost();
            
            // Si estamos en un subdominio de tenant, forzar assets desde el dominio central
            if ($currentHost !== $centralDomain && $currentHost !== 'www.switchs.test' && str_contains($currentHost, '.switchs.test')) {
                // Forzar el prefijo de assets al dominio central
                $assetUrl = 'http://' . $centralDomain;
                
                // Configurar APP_URL para que todos los assets usen el dominio central
                config(['app.url' => $assetUrl]);
                config(['app.asset_url' => $assetUrl]);
            }
        }
    }
}
 