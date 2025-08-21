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
        date_default_timezone_set('America/Bogota');
        Blade::directive('formatToCop', function ($value) {
            return "<?php echo '$ ' . number_format($value, 0, '.', ','); ?>";
        });
        
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
 