#Requires -Version 5.1

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  Iniciar Docker desde WSL2" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

$projectPath = $PSScriptRoot
$projectPathWSL = $projectPath -replace 'C:', '/mnt/c' -replace '\\', '/'

# Verificar que WSL existe
$wslExists = Get-Command wsl -ErrorAction SilentlyContinue
if (-not $wslExists) {
    Write-Host "✗ WSL no encontrado" -ForegroundColor Red
    Write-Host "Por favor instala WSL2 primero" -ForegroundColor Yellow
    exit 1
}

# Verificar Docker en WSL2
Write-Host "Verificando Docker en WSL2..." -ForegroundColor Yellow
$dockerCheck = wsl docker --version 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "✗ Docker no funciona en WSL2" -ForegroundColor Red
    Write-Host "Mensaje: $dockerCheck" -ForegroundColor Gray
    Write-Host ""
    Write-Host "Por favor:" -ForegroundColor Yellow
    Write-Host "  1. Instala Docker Desktop para Windows" -ForegroundColor White
    Write-Host "  2. Activa la integracion WSL en Docker Desktop" -ForegroundColor White
    exit 1
}

Write-Host "✓ Docker encontrado en WSL2" -ForegroundColor Green
Write-Host ""

# Verificar que docker compose existe
$composeCheck = wsl docker compose version 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "✗ docker compose no encontrado" -ForegroundColor Red
    exit 1
}

Write-Host "✓ docker compose encontrado" -ForegroundColor Green
Write-Host ""

# Verificar que el archivo docker-compose existe
$dockerComposeFile = Join-Path $projectPath "docker-compose.nginx.yml"
if (-not (Test-Path $dockerComposeFile)) {
    Write-Host "✗ docker-compose.nginx.yml no encontrado" -ForegroundColor Red
    exit 1
}

Write-Host "Iniciando contenedores..." -ForegroundColor Yellow
Write-Host "Ruta del proyecto en WSL2: $projectPathWSL" -ForegroundColor Gray
Write-Host ""

# Construir comando (usar docker compose sin guion)
$command = "cd '$projectPathWSL' && docker compose -f docker-compose.nginx.yml up -d"

# Ejecutar en WSL2
wsl bash -c $command

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "✓ Contenedores iniciados correctamente" -ForegroundColor Green
    Write-Host ""
    Write-Host "Verificando estado..." -ForegroundColor Yellow
    Write-Host ""
    
    wsl bash -c "cd '$projectPathWSL' && docker compose -f docker-compose.nginx.yml ps"
    
    Write-Host ""
    Write-Host "Para ver los logs:" -ForegroundColor Yellow
    Write-Host "  wsl bash -c `"cd '$projectPathWSL' && docker compose -f docker-compose.nginx.yml logs -f`"" -ForegroundColor Gray
} else {
    Write-Host ""
    Write-Host "✗ Error al iniciar contenedores" -ForegroundColor Red
    exit 1
}

Write-Host ""

