<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\TenantRegistrationController;

// NO cargar rutas de tenant si estamos en un dominio central
if (in_array(request()->getHost(), config('tenancy.central_domains'))) {
    return;
}

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// Rutas para servir assets estáticos (sin inicializar tenancy para mejor rendimiento)
Route::middleware(['web'])->group(function () {
    // Proxy para assets de build
    Route::get('build/assets/{file}', function ($file) {
        $path = public_path('build/assets/' . $file);
        
        if (!file_exists($path)) {
            abort(404);
        }
        
        $mimeType = mime_content_type($path);
        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    })->where('file', '.*');
    
    // Proxy para vendor assets
    Route::get('vendor/{path}', function ($path) {
        $fullPath = public_path('vendor/' . $path);
        
        if (!file_exists($fullPath)) {
            abort(404);
        }
        
        $mimeType = mime_content_type($fullPath);
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    })->where('path', '.*');
    
    // Proxy para ts assets
    Route::get('ts/{file}', function ($file) {
        $path = public_path('ts/' . $file);
        
        if (!file_exists($path)) {
            abort(404);
        }
        
        $mimeType = mime_content_type($path);
        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    })->where('file', '.*');
    
    // Ruta manual para servir assets de Livewire
    Route::get('/livewire/livewire.js', function () {
        $path = public_path('vendor/livewire/livewire.js');
        
        if (!file_exists($path)) {
            abort(404, 'Livewire assets not published');
        }
        
        return response()->file($path, [
            'Content-Type' => 'application/javascript',
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    });
});

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    'tenant.status', // Verificar que el tenant esté activo
])->group(function () {
    // Redirigir a login si no está autenticado, o al dashboard si ya lo está
    Route::get('/', function () {
        if (Auth::check()) {
            return redirect()->route('admin.home'); // Redirigir al dashboard de admin
        }
        return redirect()->route('login');
    })->name('home');
    
    // Incluir rutas de autenticación (login, register, password reset, etc.)
    require __DIR__.'/auth.php';
    
    // Registro de sub-tenants (sucursales/franquicias)
    Route::get('/register-tenant', [TenantRegistrationController::class, 'showRegistrationForm'])->name('tenant.register.form');
    Route::post('/register-tenant', [TenantRegistrationController::class, 'register'])->name('tenant.register');
    
    // Rutas protegidas que requieren autenticación
    Route::middleware(['auth', 'verified'])->group(function () {
        // Rutas de administración del tenant (con prefijo /administrador)
        Route::prefix('administrador')
            ->name('admin.')
            ->group(base_path('routes/admin.php'));
    });
    
    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'Tenant funcionando correctamente',
            'tenant_id' => tenant('id'),
            'tenant_name' => tenant('name'),
            'tenant_email' => tenant('email'),
            'database' => tenant()->database()->getName(),
            'domain' => request()->getHost(),
        ]);
    });
});
