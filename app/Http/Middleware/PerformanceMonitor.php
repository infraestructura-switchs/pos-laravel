<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Iniciar medición
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Contador de queries
        DB::enableQueryLog();
        
        $response = $next($request);
        
        // Calcular métricas
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        $executionTime = round(($endTime - $startTime) * 1000, 2); // ms
        $memoryUsed = round(($endMemory - $startMemory) / 1024 / 1024, 2); // MB
        $queryCount = count(DB::getQueryLog());
        
        // Log solo si es request web (no AJAX ni assets)
        if (!$request->ajax() && !$request->wantsJson() && !str_contains($request->path(), 'livewire')) {
            Log::channel('daily')->info('⚡ Performance Monitor', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time_ms' => $executionTime,
                'memory_mb' => $memoryUsed,
                'query_count' => $queryCount,
                'user_id' => auth()->id(),
            ]);
            
            // Alertar si es muy lento
            if ($executionTime > 1000) {
                Log::channel('daily')->warning('🐌 SLOW REQUEST DETECTED', [
                    'url' => $request->fullUrl(),
                    'execution_time_ms' => $executionTime,
                    'query_count' => $queryCount,
                ]);
            }
        }
        
        return $response;
    }
}

