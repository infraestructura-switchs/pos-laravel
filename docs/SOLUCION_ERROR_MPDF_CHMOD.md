# Solución: Error chmod() mPDF en Docker/WSL

## Fecha de Resolución
19 de noviembre de 2025

## Problema

Al intentar generar PDFs de facturas en la vista **"Vender"**, se presentaba el siguiente error:

```
chmod(): Operation not permitted
Error en: vendor/mpdf/mpdf/src/Cache.php:79
```

### Síntomas
- ❌ La descarga de facturas desde "Vender" fallaba con error 500
- ✅ La descarga desde "Crear Factura" funcionaba correctamente
- ✅ La descarga desde "Ventas Rápidas" funcionaba correctamente

## Causa Raíz

1. El directorio `vendor` está configurado como un **volumen de Docker** para mejorar el rendimiento:
   ```yaml
   volumes:
     - laravel-vendor:/var/www/html/vendor
   ```

2. mPDF intenta crear archivos de caché de fuentes y cambiar sus permisos con `chmod()`

3. En sistemas de archivos montados desde WSL/Windows, la operación `chmod()` falla con "Operation not permitted"

4. Laravel captura este error y lo convierte en una excepción fatal, deteniendo la generación del PDF

## Solución Aplicada

### 1. Modificación de mPDF

**Archivo modificado:** `vendor/mpdf/mpdf/src/Cache.php`

```php
public function write($filename, $data)
{
    $tempFile = tempnam($this->basePath, 'cache_tmp_');
    file_put_contents($tempFile, $data);
    
    // Modificado para Docker: no fallar si chmod no funciona (WSL/Windows mounted filesystem)
    try {
        @chmod($tempFile, 0664);
    } catch (\Throwable $e) {
        // Ignorar errores de chmod en sistemas de archivos montados
    }

    $path = $this->getFilePath($filename);
    rename($tempFile, $path);

    return $path;
}
```

### 2. Scripts de Parche Automático

Se crearon scripts para aplicar el parche automáticamente después de `composer install/update`:

- **Bash (Linux/WSL):** `scripts/patch-mpdf-chmod.sh`
- **PowerShell (Windows):** `scripts/patch-mpdf-chmod.ps1`

### 3. Configuración de Composer

Se agregaron hooks en `composer.json` para ejecutar el parche automáticamente:

```json
"scripts": {
    "post-install-cmd": [
        "bash scripts/patch-mpdf-chmod.sh || true"
    ],
    "post-update-cmd": [
        "bash scripts/patch-mpdf-chmod.sh || true"
    ]
}
```

## Cómo Aplicar el Parche Manualmente

### En WSL/Linux:

```bash
# Hacer ejecutable el script
chmod +x scripts/patch-mpdf-chmod.sh

# Ejecutar el script
bash scripts/patch-mpdf-chmod.sh

# Copiar al contenedor Docker (si es necesario)
docker cp vendor/mpdf/mpdf/src/Cache.php $(docker compose -f docker-compose.nginx.yml ps -q php):/var/www/html/vendor/mpdf/mpdf/src/Cache.php

# Reiniciar PHP-FPM
docker compose -f docker-compose.nginx.yml restart php
```

### En Windows (PowerShell):

```powershell
# Ejecutar el script
.\scripts\patch-mpdf-chmod.ps1
```

## Importante: Mantenimiento

⚠️ **CRÍTICO:** Este parche modifica archivos en `vendor/` que NO están versionados en Git.

### Cuándo Re-aplicar el Parche

El parche se **perderá** y debe **re-aplicarse** en estos casos:

1. Después de ejecutar `composer install` en un nuevo entorno
2. Después de ejecutar `composer update`
3. Después de actualizar mPDF específicamente: `composer update mpdf/mpdf`
4. Al clonar el repositorio en un nuevo servidor

### Verificación

Para verificar si el parche está aplicado:

```bash
grep -n "Ignorar errores de chmod" vendor/mpdf/mpdf/src/Cache.php
```

Si no muestra resultados, el parche NO está aplicado.

## Archivos Modificados

### Archivos del Proyecto (versionados en Git)
- `composer.json` - Hooks post-install/post-update
- `scripts/patch-mpdf-chmod.sh` - Script de parche para Bash
- `scripts/patch-mpdf-chmod.ps1` - Script de parche para PowerShell
- `app/Http/Controllers/Admin/BillController.php` - Error handlers mejorados
- `app/Traits/UtilityTrait.php` - Error handlers mejorados
- `.gitignore` - Agregado `/storage/app/mpdf`

### Archivos en vendor/ (NO versionados, requieren parche)
- `vendor/mpdf/mpdf/src/Cache.php` - Parche para ignorar errores de chmod

## Alternativas Consideradas

1. ❌ **Suprimir errores con `@` operator** - No funciona porque Laravel captura los errores antes
2. ❌ **Usar `set_error_handler()`** - Laravel tiene su propio handler que se ejecuta primero
3. ❌ **Usar `error_reporting(0)`** - No previene que Laravel capture el error
4. ✅ **Modificar mPDF directamente** - Solución efectiva que funciona

## Impacto

- ✅ **Positivo:** Los PDFs se generan correctamente sin errores
- ✅ **Seguridad:** El `chmod()` era solo para permisos de lectura, no es crítico
- ⚠️ **Mantenimiento:** Requiere re-aplicar el parche después de updates de composer

## Testing

Para probar que la solución funciona:

1. Ir a la vista **"Vender"**
2. Agregar productos al carrito
3. Crear una factura
4. Intentar descargar el PDF
5. Verificar que se descarga sin errores

## Soporte

Si el error vuelve a aparecer:

1. Verificar que el parche esté aplicado (ver "Verificación")
2. Ejecutar manualmente el script de parche
3. Reiniciar los contenedores Docker
4. Verificar los logs: `tail -f storage/logs/laravel.log`

## Referencias

- Issue relacionado: Error chmod en sistemas de archivos montados desde WSL
- mPDF Documentation: https://mpdf.github.io/
- Docker Volumes: https://docs.docker.com/storage/volumes/

