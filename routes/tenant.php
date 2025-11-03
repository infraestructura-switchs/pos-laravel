<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

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
});

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    'tenant.status', // Verificar que el tenant esté activo
])->group(function () {
    Route::get('/', function () {
        $tenantData = [
            'message' => '¡Bienvenido a tu aplicación Multi-Tenant!',
            'tenant_id' => tenant('id'),
            'tenant_name' => tenant('name'),
            'tenant_email' => tenant('email'),
            'database' => tenant()->database()->getName(),
            'domain' => request()->getHost(),
        ];
        
        return view('welcome', ['tenantData' => $tenantData]);
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
