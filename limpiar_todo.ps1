# ====================================================================
# Script para Limpiar TODO - Windows PowerShell
# ====================================================================
# Uso: .\limpiar_todo.ps1
# ====================================================================

Write-Host ""
Write-Host "====================================================================="
Write-Host "  LIMPIEZA COMPLETA - APP POS LARAVEL"
Write-Host "====================================================================="
Write-Host ""

Write-Host "Limpiando caches, configuracion, rutas y vistas..."
Write-Host ""

# Limpiar cache de aplicacion
Write-Host "1. Cache de aplicacion..."
docker compose -f docker-compose.nginx.yml exec php php artisan cache:clear
Write-Host "   [OK] Cache limpiado"
Write-Host ""

# Limpiar cache de configuracion
Write-Host "2. Cache de configuracion..."
docker compose -f docker-compose.nginx.yml exec php php artisan config:clear
Write-Host "   [OK] Config limpiado"
Write-Host ""

# Limpiar cache de rutas
Write-Host "3. Cache de rutas..."
docker compose -f docker-compose.nginx.yml exec php php artisan route:clear
Write-Host "   [OK] Rutas limpiadas"
Write-Host ""

# Limpiar cache de vistas
Write-Host "4. Cache de vistas..."
docker compose -f docker-compose.nginx.yml exec php php artisan view:clear
Write-Host "   [OK] Vistas limpiadas"
Write-Host ""

# Limpiar OPcache
Write-Host "5. OPcache..."
docker compose -f docker-compose.nginx.yml exec php php artisan optimize:clear
Write-Host "   [OK] OPcache limpiado"
Write-Host ""

Write-Host "====================================================================="
Write-Host "  LIMPIEZA COMPLETADA"
Write-Host "====================================================================="
Write-Host ""
Write-Host "Si necesitas optimizar de nuevo ejecuta: .\optimizar.ps1 optimize"
Write-Host ""

