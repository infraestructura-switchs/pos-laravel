# Script de Setup Multi-Tenant para Laravel
# Ejecutar como Administrador

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "  SETUP MULTI-TENANT - LARAVEL + TENANCY" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

# Verificar si se ejecuta como administrador
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "[WARNING] ADVERTENCIA: Este script debe ejecutarse como Administrador" -ForegroundColor Yellow
    Write-Host "Por favor, cierra esta ventana y ejecuta PowerShell como Administrador" -ForegroundColor Yellow
    Write-Host ""
    Read-Host "Presiona Enter para salir"
    exit
}

Write-Host "[OK] Ejecutando como Administrador" -ForegroundColor Green
Write-Host ""

# Paso 1: Instalar Tenancy
Write-Host "Paso 1: Instalando Tenancy for Laravel..." -ForegroundColor Yellow
composer require stancl/tenancy

if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Error al instalar Tenancy" -ForegroundColor Red
    exit
}
Write-Host "[OK] Tenancy instalado correctamente" -ForegroundColor Green
Write-Host ""

# Paso 2: Publicar archivos de Tenancy
Write-Host "Paso 2: Publicando archivos de configuracion..." -ForegroundColor Yellow
php artisan tenancy:install

if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Error al publicar archivos de Tenancy" -ForegroundColor Red
    exit
}
Write-Host "[OK] Archivos publicados correctamente" -ForegroundColor Green
Write-Host ""

# Paso 3: Ejecutar migraciones
Write-Host "Paso 3: Ejecutando migraciones..." -ForegroundColor Yellow
php artisan migrate

if ($LASTEXITCODE -ne 0) {
    Write-Host "[WARNING] Advertencia: Revisa las migraciones manualmente" -ForegroundColor Yellow
} else {
    Write-Host "[OK] Migraciones ejecutadas correctamente" -ForegroundColor Green
}
Write-Host ""

# Paso 4: Verificar httpd.conf
Write-Host "Paso 4: Verificando configuracion de Apache..." -ForegroundColor Yellow

$httpdConfPath = "C:\xampp\apache\conf\httpd.conf"
if (Test-Path $httpdConfPath) {
    $httpdContent = Get-Content $httpdConfPath -Raw
    
    if ($httpdContent -match "^#.*Include conf/extra/httpd-vhosts.conf" -or $httpdContent -notmatch "Include conf/extra/httpd-vhosts.conf") {
        Write-Host "[WARNING] ACCION REQUERIDA: Debes descomentar la linea en httpd.conf:" -ForegroundColor Yellow
        Write-Host "   Include conf/extra/httpd-vhosts.conf" -ForegroundColor White
        Write-Host ""
    } else {
        Write-Host "[OK] httpd.conf configurado correctamente" -ForegroundColor Green
    }
} else {
    Write-Host "[ERROR] No se encontro httpd.conf en la ruta esperada" -ForegroundColor Red
}
Write-Host ""

# Paso 5: Crear archivo de Virtual Hosts
Write-Host "Paso 5: Configurando Virtual Hosts..." -ForegroundColor Yellow

$vhostsPath = "C:\xampp\apache\conf\extra\httpd-vhosts.conf"
$projectPath = (Get-Location).Path.Replace('\', '/')

$vhostsContent = @"
# Virtual Host para el dominio principal (aplicacion central)
<VirtualHost *:80>
    ServerName dokploy.movete.cloud
    ServerAlias www.dokploy.movete.cloud
    DocumentRoot "$projectPath/public"
    
    <Directory "$projectPath/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/dokploy.movete.cloud-error.log"
    CustomLog "logs/dokploy.movete.cloud-access.log" common
</VirtualHost>

# Virtual Host para TODOS los subdominios (wildcard)
<VirtualHost *:80>
    ServerName dokploy.movete.cloud
    ServerAlias *.dokploy.movete.cloud
    DocumentRoot "$projectPath/public"
    
    <Directory "$projectPath/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/dokploy-tenants-error.log"
    CustomLog "logs/dokploy-tenants-access.log" common
</VirtualHost>
"@

try {
    # Backup del archivo original
    if (Test-Path $vhostsPath) {
        Copy-Item $vhostsPath "$vhostsPath.backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
        Write-Host "   Backup creado: $vhostsPath.backup" -ForegroundColor Gray
    }
    
    Set-Content -Path $vhostsPath -Value $vhostsContent -Encoding UTF8
    Write-Host "[OK] Virtual Hosts configurado correctamente" -ForegroundColor Green
} catch {
    Write-Host "[ERROR] Error al configurar Virtual Hosts: $_" -ForegroundColor Red
}
Write-Host ""

# Paso 6: Configurar archivo hosts
Write-Host "Paso 6: Configurando archivo hosts de Windows..." -ForegroundColor Yellow

$hostsPath = "C:\Windows\System32\drivers\etc\hosts"
$hostsEntries = @"

# ============================================
# Multi-Tenant Laravel - dokploy.movete.cloud
# ============================================
127.0.0.1       dokploy.movete.cloud
127.0.0.1       www.dokploy.movete.cloud
127.0.0.1       empresa1.dokploy.movete.cloud
127.0.0.1       empresa2.dokploy.movete.cloud
127.0.0.1       empresa3.dokploy.movete.cloud
127.0.0.1       test.dokploy.movete.cloud
127.0.0.1       demo.dokploy.movete.cloud
# ============================================
"@

try {
    # Backup del archivo hosts
    Copy-Item $hostsPath "$hostsPath.backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
    Write-Host "   Backup creado: $hostsPath.backup" -ForegroundColor Gray
    
    # Verificar si las entradas ya existen
    $currentHosts = Get-Content $hostsPath -Raw
    
    if ($currentHosts -notmatch "dokploy.movete.cloud") {
        Add-Content -Path $hostsPath -Value $hostsEntries
        Write-Host "[OK] Archivo hosts actualizado correctamente" -ForegroundColor Green
    } else {
        Write-Host "[WARNING] Las entradas ya existen en el archivo hosts" -ForegroundColor Yellow
    }
} catch {
    Write-Host "[ERROR] Error al modificar archivo hosts: $_" -ForegroundColor Red
    Write-Host "   Debes agregar manualmente las entradas al archivo hosts" -ForegroundColor Yellow
}
Write-Host ""

# Paso 7: Limpiar cache DNS
Write-Host "Paso 7: Limpiando cache DNS..." -ForegroundColor Yellow
ipconfig /flushdns | Out-Null
Write-Host "[OK] Cache DNS limpiada" -ForegroundColor Green
Write-Host ""

# Paso 8: Verificar sintaxis de Apache
Write-Host "Paso 8: Verificando sintaxis de Apache..." -ForegroundColor Yellow
$apacheTest = & "C:\xampp\apache\bin\httpd.exe" -t 2>&1

if ($apacheTest -match "Syntax OK") {
    Write-Host "[OK] Sintaxis de Apache correcta" -ForegroundColor Green
} else {
    Write-Host "[ERROR] Error en la sintaxis de Apache:" -ForegroundColor Red
    Write-Host $apacheTest -ForegroundColor Red
}
Write-Host ""

# Paso 9: Preguntar si reiniciar Apache
Write-Host "Paso 9: Reiniciar Apache" -ForegroundColor Yellow
$restart = Read-Host "Deseas reiniciar Apache ahora? (S/N)"

if ($restart -eq "S" -or $restart -eq "s") {
    Write-Host "   Deteniendo Apache..." -ForegroundColor Gray
    net stop Apache2.4 2>&1 | Out-Null
    Start-Sleep -Seconds 2
    
    Write-Host "   Iniciando Apache..." -ForegroundColor Gray
    net start Apache2.4 2>&1 | Out-Null
    Start-Sleep -Seconds 2
    
    $apacheStatus = Get-Service -Name "Apache2.4" -ErrorAction SilentlyContinue
    if ($apacheStatus.Status -eq "Running") {
        Write-Host "[OK] Apache reiniciado correctamente" -ForegroundColor Green
    } else {
        Write-Host "[ERROR] Error al reiniciar Apache. Usa el Panel de XAMPP" -ForegroundColor Red
    }
}
Write-Host ""

# Resumen final
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "  [OK] SETUP COMPLETADO" -ForegroundColor Green
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "PROXIMOS PASOS:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. Verifica que Apache este corriendo en XAMPP" -ForegroundColor White
Write-Host "2. Abre tu navegador y visita:" -ForegroundColor White
Write-Host "   - http://dokploy.movete.cloud" -ForegroundColor Cyan
Write-Host "   - http://empresa1.dokploy.movete.cloud" -ForegroundColor Cyan
Write-Host ""
Write-Host "3. Consulta la documentacion en docs/guias/" -ForegroundColor White
Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

Read-Host "Presiona Enter para salir"
