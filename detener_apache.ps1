#Requires -RunAsAdministrator

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  Detener Apache para usar Docker Nginx" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Buscar proceso httpd (Apache)
$apacheProcess = Get-Process -Name httpd -ErrorAction SilentlyContinue

if ($apacheProcess) {
    Write-Host "Apache encontrado (PID: $($apacheProcess[0].Id))" -ForegroundColor Yellow
    Write-Host "Deteniendo Apache..." -ForegroundColor Yellow
    
    # Detener todos los procesos httpd
    Stop-Process -Name httpd -Force -ErrorAction SilentlyContinue
    
    Start-Sleep -Seconds 2
    
    # Verificar
    $stillRunning = Get-Process -Name httpd -ErrorAction SilentlyContinue
    if (-not $stillRunning) {
        Write-Host "✓ Apache detenido correctamente" -ForegroundColor Green
    } else {
        Write-Host "✗ Apache sigue corriendo, intentando con servicio..." -ForegroundColor Yellow
    }
} else {
    Write-Host "Apache no esta corriendo como proceso" -ForegroundColor Gray
}

# Intentar detener servicio
Write-Host ""
Write-Host "Intentando detener servicio de Apache..." -ForegroundColor Yellow

$apacheService = Get-Service -Name Apache2.4 -ErrorAction SilentlyContinue

if ($apacheService) {
    if ($apacheService.Status -eq "Running") {
        try {
            Stop-Service -Name Apache2.4 -Force -ErrorAction Stop
            Write-Host "✓ Servicio Apache2.4 detenido" -ForegroundColor Green
        } catch {
            Write-Host "✗ Error deteniendo servicio: $($_.Exception.Message)" -ForegroundColor Red
        }
    } else {
        Write-Host "Servicio Apache2.4 ya esta detenido" -ForegroundColor Gray
    }
    
    # Deshabilitar inicio automático (opcional)
    Write-Host ""
    $disable = Read-Host "Deseas deshabilitar el inicio automatico de Apache? (s/n)"
    if ($disable -eq "s") {
        Set-Service -Name Apache2.4 -StartupType Disabled
        Write-Host "✓ Inicio automatico deshabilitado" -ForegroundColor Green
    }
} else {
    Write-Host "Servicio Apache2.4 no encontrado" -ForegroundColor Gray
}

# Verificar puerto 80
Write-Host ""
Write-Host "Verificando puerto 80..." -ForegroundColor Yellow
$port80 = netstat -ano | findstr ":80 " | findstr "LISTENING"

if ($port80) {
    Write-Host "Puerto 80 aun ocupado:" -ForegroundColor Red
    Write-Host $port80 -ForegroundColor Gray
    
    # Extraer PID
    $pid = ($port80 -split '\s+')[-1]
    $process = Get-Process -Id $pid -ErrorAction SilentlyContinue
    
    if ($process) {
        Write-Host "Proceso: $($process.ProcessName) (PID: $pid)" -ForegroundColor Yellow
        Write-Host "Ruta: $($process.Path)" -ForegroundColor Gray
    }
} else {
    Write-Host "✓ Puerto 80 libre" -ForegroundColor Green
}

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  COMPLETADO" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Ahora recarga tu navegador:" -ForegroundColor Yellow
Write-Host "  http://adminpos.dokploy.movete.cloud" -ForegroundColor White
Write-Host ""

