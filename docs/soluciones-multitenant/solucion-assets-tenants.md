# Solución consolidada: Carga de assets/CSS en tenants

## Contexto
En entornos multi-tenant con subdominios (`empresa1.dokploy.movete.cloud`) los assets compilados (CSS/JS) deben servirse de `public/`. Si las vistas intentan resolverlos desde el dominio del tenant, aparecen 404 o páginas sin estilos.

## Enfoque final aplicado (vigente)
1. Reemplazar `@vite` por enlaces directos a los artefactos compilados.
2. Usar `url()` en vez de `asset()` para evitar resoluciones al dominio incorrecto.
3. Exponer rutas proxy en `routes/tenant.php` para servir assets desde `public/` bajo el dominio del tenant.

### Cambios en vistas
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/guest.blade.php`

Ejemplo:
```blade
<link rel="stylesheet" href="{{ url('build/assets/app-fd737ff0.css') }}">
<script src="{{ url('build/assets/app-f737933f.js') }}" defer></script>
```

### Rutas proxy en `routes/tenant.php`
```php
Route::middleware(['web'])->group(function () {
    Route::get('build/assets/{file}', fn ($file) => response()->file(public_path('build/assets/'.$file)))->where('file', '.*');
    Route::get('vendor/{path}', fn ($path) => response()->file(public_path('vendor/'.$path)))->where('path', '.*');
    Route::get('ts/{file}', fn ($file) => response()->file(public_path('ts/'.$file)))->where('file', '.*');
});
```

## Alternativas descartadas (históricas)
- ASSET_URL en .env: útil para casos simples, pero no consistente con `@vite` y multi-tenant.
- Middleware que reescribe HTML: funcional pero complejo y costoso; se retiró.

## Checklist de verificación
- `public/build/manifest.json` existe y lista los archivos.
- Los requests a:
  - `/build/assets/app-*.css` → 200
  - `/build/assets/app-*.js` → 200
  - `/vendor/icomoon-v1.0/style.css` → 200
  - `/ts/app.js` → 200
- La vista de login y el panel del tenant cargan con estilos.

## Troubleshooting
- Si hay 404, confirmar nombres de archivos reales en `public/build/assets` y limpiar vistas/cache:
```bash
php artisan view:clear && php artisan cache:clear
```
