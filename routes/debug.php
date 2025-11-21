<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Debug Routes
|--------------------------------------------------------------------------
| Rutas de depuración protegidas con middleware de autenticación
*/

Route::middleware(['auth', 'web'])->group(function () {
    
    // Verificar permisos del usuario autenticado
    Route::get('/verificar-permisos-auth', function () {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'error' => 'No hay usuario autenticado'
            ], 401);
        }
        
        $roles = $user->roles;
        $permissions = $user->getAllPermissions();
        
        $testPermissions = [
            'dashboard',
            'usuarios',
            'clientes',
            'productos',
            'facturas',
            'cierre de caja'
        ];
        
        $permissionTests = [];
        foreach ($testPermissions as $perm) {
            $permissionTests[$perm] = $user->can($perm);
        }
        
        return response()->json([
            'usuario' => [
                'id' => $user->id,
                'nombre' => $user->name,
                'email' => $user->email
            ],
            'roles' => $roles->pluck('name'),
            'total_permisos' => $permissions->count(),
            'permisos' => $permissions->pluck('name'),
            'pruebas_permisos' => $permissionTests,
            'session_id' => session()->getId()
        ], 200, [], JSON_PRETTY_PRINT);
    });
    
});

