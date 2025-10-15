# Verificar archivos
Write-Host "=== Archivos CSS ===" -ForegroundColor Green
Get-ChildItem public\build\assets\app-*.css | Select-Object Name, Length, LastWriteTime

Write-Host "`n=== Archivos JS ===" -ForegroundColor Green  
Get-ChildItem public\build\assets\app-*.js | Select-Object Name, Length, LastWriteTime

Write-Host "`n=== Contenido manifest.json ===" -ForegroundColor Green
Get-Content public\build\manifest.json

Write-Host "`n=== Verificando APP_ENV ===" -ForegroundColor Green
php artisan env

