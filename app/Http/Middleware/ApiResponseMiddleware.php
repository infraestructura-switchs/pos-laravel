<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Si la respuesta ya es JSON, no la modificamos
        if ($response->headers->get('Content-Type') === 'application/json') {
            return $response;
        }

        // Para respuestas de error, las convertimos a JSON
        if ($response->getStatusCode() >= 400) {
            $content = $response->getContent();
            
            // Si es una vista de error, la convertimos a JSON
            if (str_contains($content, '<html>')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error en la solicitud',
                    'error' => $response->getStatusCode() === 404 ? 'Recurso no encontrado' : 'Error interno del servidor'
                ], $response->getStatusCode());
            }
        }

        return $response;
    }
} 