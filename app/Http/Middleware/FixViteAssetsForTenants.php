<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FixViteAssetsForTenants
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
        $response = $next($request);

        // Dominio central (sin www)
        $centralDomain = 'dokploy.movete.cloud';
        $currentHost = $request->getHost();

        // Si no estamos en un tenant, no hacer nada
        if ($currentHost === $centralDomain || $currentHost === 'www.dokploy.movete.cloud' || !str_contains($currentHost, '.dokploy.movete.cloud')) {
            return $response;
        }

        // Solo procesar respuestas HTML
        $contentType = $response->headers->get('Content-Type', '');
        if (!str_contains($contentType, 'text/html') && !empty($contentType)) {
            return $response;
        }

        // Obtener el contenido de la respuesta
        $content = $response->getContent();

        // Si no hay contenido o está vacío, no hacer nada
        if (empty($content)) {
            return $response;
        }

        // Reemplazar las URLs de assets relativos con URLs absolutas al dominio central
        $assetUrl = 'https://' . $centralDomain;

        // Patrón 1: href="/build/assets/..." -> href="http://switchs.test/build/assets/..."
        $content = preg_replace(
            '/(href=["\'])(\/)?(build\/assets\/[^"\']+)/i',
            '$1' . $assetUrl . '/$3',
            $content
        );

        // Patrón 2: src="/build/assets/..." -> src="http://switchs.test/build/assets/..."
        $content = preg_replace(
            '/(src=["\'])(\/)?(build\/assets\/[^"\']+)/i',
            '$1' . $assetUrl . '/$3',
            $content
        );

        // Patrón 3: url(/build/assets/...) -> url(http://switchs.test/build/assets/...)
        $content = preg_replace(
            '/(url\(["\']?)(\/)?(build\/assets\/[^)"\']+)/i',
            '$1' . $assetUrl . '/$3',
            $content
        );

        // Patrón 4: href="/vendor/..." -> href="http://switchs.test/vendor/..."
        $content = preg_replace(
            '/(href=["\'])(\/)?(vendor\/[^"\']+)/i',
            '$1' . $assetUrl . '/$3',
            $content
        );

        // Patrón 5: src="/ts/..." -> src="http://switchs.test/ts/..."
        $content = preg_replace(
            '/(src=["\'])(\/)?(ts\/[^"\']+)/i',
            '$1' . $assetUrl . '/$3',
            $content
        );

        // Patrón 6: Cualquier otro asset local (storage, images, etc.)
        $content = preg_replace(
            '/((?:href|src)=["\'])(\/(?:storage|images|fonts|js|css)\/[^"\']+)/i',
            '$1' . $assetUrl . '$2',
            $content
        );

        // Actualizar el contenido de la respuesta
        $response->setContent($content);

        return $response;
    }
}

