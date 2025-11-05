# Script para verificar que dominios de tenants estan configurados en el archivo hosts
# NO requiere permisos de administrador

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "  VERIFICADOR DE HOSTS - MULTI-TENANT" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

$hostsPath = "C:\Windows\System32\drivers\etc\hosts"

# Verificar si existe el archivo
if (-not (Test-Path $hostsPath)) {
    Write-Host "[ERROR] Archivo hosts no encontrado" -ForegroundColor Red
    exit 1
}

# Leer contenido
try {
    $hostsContent = Get-Content $hostsPath
} catch {
    Write-Host "[ERROR] Error al leer el archivo hosts" -ForegroundColor Red
    Write-Host "   Asegurate de tener permisos para leer el archivo" -ForegroundColor Yellow
    exit 1
}

# Buscar dominios relacionados con dokploy.movete.cloud
Write-Host "Buscando dominios dokploy.movete.cloud en el archivo hosts..." -ForegroundColor Yellow
Write-Host ""

$foundDomains = @()
$lineNumber = 0

foreach ($line in $hostsContent) {
    $lineNumber++
    
    # Ignorar lineas comentadas y vacias
    if ($line -match "^\s*#" -or $line -match "^\s*$") {
        continue
    }
    
    # Buscar lineas que contengan dokploy.movete.cloud
    if ($line -match "dokploy\.movete\.cloud") {
        # Extraer IP y dominio
        if ($line -match "^\s*(\d+\.\d+\.\d+\.\d+)\s+(.+)") {
            $ip = $matches[1]
            $domain = $matches[2].Trim()
            
            $foundDomains += [PSCustomObject]@{
                LineNumber = $lineNumber
                IP = $ip
                Domain = $domain
            }
        }
    }
}

# Mostrar resultados
if ($foundDomains.Count -eq 0) {
    Write-Host "[WARNING] No se encontraron dominios dokploy.movete.cloud en el archivo hosts" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Para agregar el dominio principal, ejecuta como Administrador:" -ForegroundColor White
    Write-Host '  Add-Content -Path "C:\Windows\System32\drivers\etc\hosts" -Value "`n127.0.0.1       dokploy.movete.cloud"' -ForegroundColor Cyan
} else {
    Write-Host "[OK] Se encontraron $($foundDomains.Count) dominio(s):" -ForegroundColor Green
    Write-Host ""
    
    # Mostrar tabla
    $foundDomains | Format-Table -AutoSize @{
        Label = "Linea"
        Expression = { $_.LineNumber }
    }, @{
        Label = "IP"
        Expression = { $_.IP }
    }, @{
        Label = "Dominio"
        Expression = { $_.Domain }
    }
    
    # Categorizar dominios
    $mainDomain = $foundDomains | Where-Object { $_.Domain -eq "dokploy.movete.cloud" -or $_.Domain -eq "www.dokploy.movete.cloud" }
    $subdomains = $foundDomains | Where-Object { $_.Domain -match "^[^.]+\.dokploy\.movete\.cloud$" -and $_.Domain -ne "www.dokploy.movete.cloud" }
    
    Write-Host "RESUMEN:" -ForegroundColor Cyan
    Write-Host ""
    
    if ($mainDomain) {
        Write-Host "  [OK] Dominio principal configurado" -ForegroundColor Green
    } else {
        Write-Host "  [WARNING] Dominio principal NO configurado" -ForegroundColor Yellow
    }
    
    if ($subdomains) {
        Write-Host "  [OK] $($subdomains.Count) subdominio(s) de tenant configurado(s)" -ForegroundColor Green
        Write-Host ""
        Write-Host "  Subdominios:" -ForegroundColor White
        foreach ($subdomain in $subdomains) {
            $tenantName = $subdomain.Domain -replace "\.dokploy\.movete\.cloud$", ""
            Write-Host "    - $tenantName ($($subdomain.Domain))" -ForegroundColor Cyan
        }
    } else {
        Write-Host "  [WARNING] No hay subdominios de tenant configurados" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan

# Verificar tenants en la base de datos
Write-Host ""
Write-Host "Verificando tenants en la base de datos..." -ForegroundColor Yellow
Write-Host ""

$tenantsList = php artisan tenants:list 2>&1

if ($LASTEXITCODE -eq 0 -and $tenantsList) {
    Write-Host $tenantsList
    
    # Intentar extraer dominios de tenants
    Write-Host ""
    Write-Host "RECOMENDACION:" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Si creaste nuevos tenants y no funcionan:" -ForegroundColor White
    Write-Host "1. Agrega sus dominios al archivo hosts" -ForegroundColor White
    Write-Host "2. Ejecuta como Administrador:" -ForegroundColor White
    Write-Host '   .\add_tenant_subdomain.ps1 -Subdomain "nombre_tenant"' -ForegroundColor Cyan
    Write-Host ""
} else {
    Write-Host "[WARNING] No se pudieron listar los tenants" -ForegroundColor Yellow
    Write-Host "   Asegurate de haber ejecutado las migraciones" -ForegroundColor Gray
}

Write-Host ""
