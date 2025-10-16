Write-Host "=== 1. Instalando dependencias de Node ===" -ForegroundColor Green
npm install

Write-Host "`n=== 2. Limpiando cache de Laravel ===" -ForegroundColor Yellow
php artisan cache:clear
php artisan config:clear
php artisan view:clear

Write-Host "`n=== 3. Compilando assets con Vite ===" -ForegroundColor Cyan
npm run build

Write-Host "`n=== 4. Verificando archivos generados ===" -ForegroundColor Magenta
Get-ChildItem public\build\assets\app-*.css | Select-Object Name
Get-ChildItem public\build\assets\app-*.js | Select-Object Name

Write-Host "`n✅ Proceso completado!" -ForegroundColor Green
Write-Host "Recarga la página con Ctrl+F5 para limpiar el cache del navegador" -ForegroundColor Yellow

