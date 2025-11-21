<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LoadUserPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Cachear permisos del usuario por 30 minutos para evitar consultas repetidas
            $cacheKey = 'user_permissions_' . $user->id . '_' . tenant('id');
            
            $user->cachedPermissions = Cache::remember($cacheKey, 1800, function() use ($user) {
                // Precargar todos los permisos del usuario de una sola vez
                // Esto evita el problema N+1 en las verificaciones @can
                $user->load(['permissions', 'roles.permissions']);
                
                return $user->getAllPermissions()->pluck('name')->toArray();
            });
        }

        return $next($request);
    }
}

