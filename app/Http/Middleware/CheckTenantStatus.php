<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTenantStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Solo verificar si estamos en un contexto de tenant
        if (tenancy()->initialized) {
            $tenant = tenant();

            // Si el tenant está suspendido, mostrar página de error
            if ($tenant && $tenant->isSuspended()) {
                abort(403, 'Esta empresa ha sido suspendida temporalmente. Por favor, contacte al administrador.');
            }

            // Si el tenant está inactivo, mostrar página de error
            if ($tenant && $tenant->status === 'inactive') {
                abort(403, 'Esta empresa está inactiva. Por favor, contacte al administrador.');
            }
        }

        return $next($request);
    }
}
