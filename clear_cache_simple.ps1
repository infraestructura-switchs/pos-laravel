#!/usr/bin/env pwsh
# Script simple para limpiar cachés de Laravel (solo limpieza, sin recrear)

Write-Host " Limpiando caches de Laravel..." -ForegroundColor Cyan

# Ejecutar comandos de limpieza en secuencia
php artisan config:clear
php artisan route:clear  
php artisan view:clear
php artisan cache:clear
php artisan event:clear

Write-Host "Caches limpiados" -ForegroundColor Green
