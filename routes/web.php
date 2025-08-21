<?php

use Illuminate\Support\Facades\Route;
use App\Models\Order;

Route::get('/', function () {
    return view('welcome');
})->middleware('HasLoggenIn')->name('dashboard');

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

require __DIR__.'/auth.php';



