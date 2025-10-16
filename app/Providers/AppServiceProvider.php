<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\CloudinaryClientInterface;
use App\Services\Contracts\ImageServiceInterface;
use App\Services\CloudinaryService;
use App\Services\ImageService;
use Illuminate\Support\Facades\URL;

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
        
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
 