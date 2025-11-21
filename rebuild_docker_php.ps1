#Requires -Version 5.1

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  Reconstruir Contenedor PHP con Extensiones" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

$projectPath = $PSScriptRoot
$projectPathWSL = $projectPath -replace 'C:', '/mnt/c' -replace '\\', '/'

Write-Host "1. Deteniendo contenedores..." -ForegroundColor Yellow
wsl bash -c "cd '$projectPathWSL' && docker compose -f docker-compose.nginx.yml down"
Write-Host "✓ Contenedores detenidos" -ForegroundColor Green
Write-Host ""

Write-Host "2. Construyendo nueva imagen PHP con extensiones..." -ForegroundColor Yellow
Write-Host "Esto puede tardar unos minutos..." -ForegroundColor Gray
wsl bash -c "cd '$projectPathWSL' && docker compose -f docker-compose.nginx.yml build php"

if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Imagen PHP construida" -ForegroundColor Green
} else {
    Write-Host "✗ Error construyendo imagen" -ForegroundColor Red
    exit 1
}
Write-Host ""

Write-Host "3. Iniciando contenedores..." -ForegroundColor Yellow
wsl bash -c "cd '$projectPathWSL' && docker compose -f docker-compose.nginx.yml up -d"

if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Contenedores iniciados" -ForegroundColor Green
} else {
    Write-Host "✗ Error iniciando contenedores" -ForegroundColor Red
    exit 1
}
Write-Host ""

Write-Host "4. Esperando que los servicios inicien..." -ForegroundColor Yellow
Start-Sleep -Seconds 5
Write-Host "✓ Servicios listos" -ForegroundColor Green
Write-Host ""

Write-Host "5. Verificando extensiones PHP..." -ForegroundColor Yellow
$extensions = wsl bash -c "docker exec laravel-php-fpm php -m" 2>&1

$requiredExtensions = @("PDO", "pdo_mysql", "mysqli", "mbstring", "zip", "gd")
$allInstalled = $true

foreach ($ext in $requiredExtensions) {
    if ($extensions -like "*$ext*") {
        Write-Host "  ✓ $ext instalada" -ForegroundColor Green
    } else {
        Write-Host "  ✗ $ext NO instalada" -ForegroundColor Red
        $allInstalled = $false
    }
}
Write-Host ""

if ($allInstalled) {
    Write-Host "============================================" -ForegroundColor Cyan
    Write-Host "  RECONSTRUCCION EXITOSA" -ForegroundColor Cyan
    Write-Host "============================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "PHP ahora tiene todas las extensiones necesarias" -ForegroundColor Green
    Write-Host ""
    Write-Host "Proximos pasos:" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "1. Crea el archivo .env si no existe:" -ForegroundColor White
    Write-Host "   Copia y renombra el archivo de configuracion apropiado" -ForegroundColor Gray
    Write-Host ""
    Write-Host "2. Genera la APP_KEY:" -ForegroundColor White
    Write-Host "   docker exec laravel-php-fpm php artisan key:generate" -ForegroundColor Gray
    Write-Host ""
    Write-Host "3. Ejecuta las migraciones:" -ForegroundColor White
    Write-Host "   docker exec laravel-php-fpm php artisan migrate" -ForegroundColor Gray
    Write-Host ""
} else {
    Write-Host "⚠ Algunas extensiones no se instalaron correctamente" -ForegroundColor Yellow
    Write-Host "Revisa los logs: docker compose -f docker-compose.nginx.yml logs php" -ForegroundColor Gray
}

Write-Host ""

