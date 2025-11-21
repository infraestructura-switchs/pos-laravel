<?php

namespace App\Http\Middleware;

use App\Models\Module;
use App\Services\ModuleService;
use Closure;
use Illuminate\Http\Request;

class HasModule
{
    public function handle(Request $request, Closure $next, $module)
    {
        $user = auth()->user();

        // Si es root (SuperAdmin), tiene acceso a TODO
        if (isRoot() || ($user && $user->is_root == 1)) {
            return $next($request);
        }

        $result = ModuleService::exists($module);

        if (!$result || !$user->hasPermissionTo($module)) {
            abort(404);
        }

        return $next($request);
    }
}
