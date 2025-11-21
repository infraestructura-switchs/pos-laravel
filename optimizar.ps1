# ====================================================================
# Script de Optimizacion Rapida para Windows (PowerShell)
# ====================================================================
# Uso: .\optimizar.ps1 [opcion]
# Opciones:
#   - test: Probar configuracion actual
#   - optimize: Optimizar aplicacion
#   - clear: Limpiar caches
#   - redis: Configurar y activar Redis
# ====================================================================

param(
    [string]$Option = "menu"
)

function Write-ColorOutput($ForegroundColor) {
    $fc = $host.UI.RawUI.ForegroundColor
    $host.UI.RawUI.ForegroundColor = $ForegroundColor
    if ($args) {
        Write-Output $args
    }
    $host.UI.RawUI.ForegroundColor = $fc
}

Write-Host ""
Write-ColorOutput Cyan "====================================================================="
Write-ColorOutput Cyan "  OPTIMIZACION DE RENDIMIENTO - APP POS LARAVEL"
Write-ColorOutput Cyan "====================================================================="
Write-Host ""

switch ($Option) {
    "test" {
        Write-ColorOutput Yellow "Ejecutando pruebas de rendimiento..."
        Write-Host ""
        docker compose -f docker-compose.nginx.yml exec php php test_performance.php
    }
    
    "optimize" {
        Write-ColorOutput Green "Optimizando aplicacion..."
        Write-Host ""
        docker compose -f docker-compose.nginx.yml exec php php artisan app:optimize-performance optimize
    }
    
    "clear" {
        Write-ColorOutput Yellow "Limpiando caches..."
        Write-Host ""
        docker compose -f docker-compose.nginx.yml exec php php artisan app:optimize-performance clear
    }
    
    "redis" {
        Write-ColorOutput Green "Configurando Redis..."
        Write-Host ""
        Write-ColorOutput Yellow "IMPORTANTE: Debes editar el archivo .env manualmente"
        Write-Host ""
        Write-Host "Agrega estas lineas a tu .env:"
        Write-Host ""
        Write-ColorOutput Cyan "CACHE_DRIVER=redis"
        Write-ColorOutput Cyan "SESSION_DRIVER=redis"
        Write-ColorOutput Cyan "REDIS_HOST=redis"
        Write-ColorOutput Cyan "REDIS_PASSWORD=null"
        Write-ColorOutput Cyan "REDIS_PORT=6379"
        Write-Host ""
        Write-Host "Luego ejecuta:"
        Write-Host ""
        Write-ColorOutput Green "docker compose -f docker-compose.nginx.yml restart"
        Write-ColorOutput Green ".\optimizar.ps1 optimize"
        Write-Host ""
    }
    
    default {
        Write-Host "Selecciona una opcion:"
        Write-Host ""
        Write-ColorOutput Green "  1) Test - Probar configuracion actual"
        Write-ColorOutput Green "  2) Optimize - Optimizar aplicacion"
        Write-ColorOutput Green "  3) Clear - Limpiar caches"
        Write-ColorOutput Green "  4) Redis - Configurar Redis"
        Write-Host ""
        
        $choice = Read-Host "Opcion [1-4]"
        
        switch ($choice) {
            "1" { & $PSCommandPath -Option test }
            "2" { & $PSCommandPath -Option optimize }
            "3" { & $PSCommandPath -Option clear }
            "4" { & $PSCommandPath -Option redis }
            default { Write-ColorOutput Red "Opcion no valida" }
        }
    }
}

Write-Host ""
Write-ColorOutput Cyan "====================================================================="
Write-Host ""

