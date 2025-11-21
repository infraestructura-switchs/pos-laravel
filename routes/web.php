<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\Admin\TenantController;

// ========================================
// RUTAS DEL DOMINIO CENTRAL
// ========================================

// Ruta manual para servir assets de Livewire (DEBE IR PRIMERO, SIN MIDDLEWARE)
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

// Redirigir a login si no está autenticado, o al dashboard si ya lo está
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Registro de Tenants (público)
Route::get('/register-tenant', [TenantRegistrationController::class, 'showRegistrationForm'])->name('tenant.register.form');
Route::post('/register-tenant', [TenantRegistrationController::class, 'register'])->name('tenant.register');

// Dashboard Principal (para usuarios autenticados del dominio central)
Route::get('/dashboard', function () {
    return view('welcome');
})->middleware('HasLoggenIn')->name('dashboard');

// Panel de Administración de Tenants (solo para SuperAdmin)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('tenants', TenantController::class);
    Route::post('tenants/{id}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
    Route::post('tenants/{id}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
});

// Ruta de prueba temporal para debug
Route::get('/debug-mesas', function () {
    $allOrders = Order::all();
    $activeOrders = Order::where('is_active', 1)->get();
    
    return response()->json([
        'total_mesas' => $allOrders->count(),
        'mesas_activas' => $activeOrders->count(),
        'todas_las_mesas' => $allOrders->toArray(),
        'mesas_activas_data' => $activeOrders->toArray()
    ]);
});

// Ruta de debug que usa el método del componente
Route::get('/debug-mesas-componente', function () {
    $ordersComponent = new \App\Http\Livewire\Admin\QuickSale\Orders();
    $processedOrders = $ordersComponent->getOrders();
    
    return response()->json([
        'mesas_procesadas' => $processedOrders->toArray(),
        'total_procesadas' => $processedOrders->count()
    ]);
});

// Ruta de debug para limpiar toda la cache de la aplicación
Route::get('/debug-clear-all', function () {
    $results = [];
    
    try {
        // Estado antes de limpiar
        $cachePath = storage_path('framework/cache');
        $viewsPath = storage_path('framework/views');
        $configPath = base_path('bootstrap/cache/config.php');
        $routesPath = base_path('bootstrap/cache/routes-v7.php');
        
        $before = [
            'cache_exists' => is_dir($cachePath) && count(glob($cachePath . '/*')) > 0,
            'views_exist' => is_dir($viewsPath) && count(glob($viewsPath . '/*')) > 0,
            'config_cached' => file_exists($configPath),
            'routes_cached' => file_exists($routesPath),
        ];
        
        // Limpiar cache
        \Artisan::call('cache:clear');
        $results['cache_clear'] = [
            'output' => \Artisan::output(),
            'status' => 'ejecutado'
        ];
        
        // Limpiar config
        \Artisan::call('config:clear');
        $results['config_clear'] = [
            'output' => \Artisan::output(),
            'status' => 'ejecutado'
        ];
        
        // Limpiar rutas
        \Artisan::call('route:clear');
        $results['route_clear'] = [
            'output' => \Artisan::output(),
            'status' => 'ejecutado'
        ];
        
        // Limpiar vistas
        \Artisan::call('view:clear');
        $results['view_clear'] = [
            'output' => \Artisan::output(),
            'status' => 'ejecutado'
        ];
        
        // Opcional: Volver a cachear config para mejor rendimiento
        \Artisan::call('config:cache');
        $results['config_cache'] = [
            'output' => \Artisan::output(),
            'status' => 'ejecutado'
        ];
        
        // Estado después de limpiar
        $after = [
            'config_cached' => file_exists($configPath),
            'routes_cached' => file_exists($routesPath),
        ];
        
        return response()->json([
            'status' => 'success',
            'message' => 'Todos los comandos de limpieza ejecutados correctamente',
            'estado_antes' => $before,
            'comandos_ejecutados' => $results,
            'estado_despues' => $after,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error al ejecutar comandos de limpieza',
            'error' => $e->getMessage(),
            'comandos_ejecutados' => $results
        ], 500);
    }
});

require __DIR__.'/auth.php';



// Maintenance artisan routes (public under /login/*)
Route::get('config/cache-clear', function () {
    \Artisan::call('cache:clear');
    return redirect()->back()->with('success', 'Cache limpiada correctamente.');
})->withoutMiddleware(\App\Http\Middleware\Authenticate::class)->name('cache.clear');

Route::get('config/config-clear', function () {
    \Artisan::call('config:clear');
    return redirect()->back()->with('success', 'Config limpiada correctamente.');
})->withoutMiddleware(\App\Http\Middleware\Authenticate::class)->name('config.clear');

Route::get('config/route-clear', function () {
    \Artisan::call('route:clear');
    return redirect()->back()->with('success', 'Rutas limpiadas correctamente.');
})->withoutMiddleware(\App\Http\Middleware\Authenticate::class)->name('route.clear');

Route::get('config/view-clear', function () {
    \Artisan::call('view:clear');
    return redirect()->back()->with('success', 'Vistas limpiadas correctamente.');
})->withoutMiddleware(\App\Http\Middleware\Authenticate::class)->name('view.clear');

Route::get('config/config-cache', function () {
    \Artisan::call('config:cache');
    return redirect()->back()->with('success', 'Config cacheada correctamente.');
})->withoutMiddleware(\App\Http\Middleware\Authenticate::class)->name('config.cache');
