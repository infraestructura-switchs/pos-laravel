Write-Host "=== DIAGNOSTICO DE ESTILOS ===" -ForegroundColor Cyan

Write-Host "`n1. Verificando archivos compilados:" -ForegroundColor Yellow
if (Test-Path "public\build\manifest.json") {
    Write-Host "[OK] manifest.json existe" -ForegroundColor Green
    $manifest = Get-Content "public\build\manifest.json" | ConvertFrom-Json
    Write-Host "   CSS apunta a: $($manifest.'resources/css/app.css'.file)" -ForegroundColor White
    Write-Host "   JS apunta a: $($manifest.'resources/js/app.js'.file)" -ForegroundColor White
    
    $cssFile = "public\build\$($manifest.'resources/css/app.css'.file)"
    $jsFile = "public\build\$($manifest.'resources/js/app.js'.file)"
    
    if (Test-Path $cssFile) {
        $size = (Get-Item $cssFile).Length
        Write-Host "   [OK] CSS existe ($size bytes)" -ForegroundColor Green
    } else {
        Write-Host "   [ERROR] CSS NO EXISTE: $cssFile" -ForegroundColor Red
    }
    
    if (Test-Path $jsFile) {
        $size = (Get-Item $jsFile).Length
        Write-Host "   [OK] JS existe ($size bytes)" -ForegroundColor Green
    } else {
        Write-Host "   [ERROR] JS NO EXISTE: $jsFile" -ForegroundColor Red
    }
} else {
    Write-Host "[ERROR] manifest.json NO existe" -ForegroundColor Red
}

Write-Host "`n2. Verificando APP_ENV:" -ForegroundColor Yellow
$env = Get-Content .env | Select-String "APP_ENV="
Write-Host "   $env" -ForegroundColor White

Write-Host "`n3. Verificando package.json scripts:" -ForegroundColor Yellow
$package = Get-Content package.json | ConvertFrom-Json
Write-Host "   build: $($package.scripts.build)" -ForegroundColor White
Write-Host "   dev: $($package.scripts.dev)" -ForegroundColor White

Write-Host "`n4. Vite dev server corriendo?" -ForegroundColor Yellow
$viteRunning = Get-Process -Name node -ErrorAction SilentlyContinue | Where-Object {$_.CommandLine -like "*vite*"}
if ($viteRunning) {
    Write-Host "   [OK] Vite dev server esta corriendo" -ForegroundColor Green
} else {
    Write-Host "   [ERROR] Vite dev server NO esta corriendo" -ForegroundColor Red
}

Write-Host "`n=== SOLUCION ===" -ForegroundColor Cyan
Write-Host "Ejecuta: npm run build" -ForegroundColor Yellow
Write-Host "Luego: php artisan config:clear" -ForegroundColor Yellow
Write-Host "Y recarga con Ctrl+F5" -ForegroundColor Yellow
