# Script para agregar rapidamente un nuevo subdominio al archivo hosts
# Ejecutar como Administrador

param(
    [Parameter(Mandatory=$true)]
    [string]$Subdomain
)

# Verificar si se ejecuta como administrador
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "[ERROR] Este script debe ejecutarse como Administrador" -ForegroundColor Red
    Write-Host ""
    Write-Host "Ejecuta en PowerShell como Admin:" -ForegroundColor Yellow
    Write-Host ".\add_tenant_subdomain.ps1 -Subdomain '$Subdomain'" -ForegroundColor Cyan
    exit 1
}

# Validar formato del subdominio
if ($Subdomain -notmatch "^[a-zA-Z0-9-]+$") {
    Write-Host "[ERROR] El subdominio solo puede contener letras, numeros y guiones" -ForegroundColor Red
    exit 1
}

$fullDomain = "$Subdomain.dokploy.movete.cloud"
$hostsPath = "C:\Windows\System32\drivers\etc\hosts"

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "  AGREGAR SUBDOMINIO AL ARCHIVO HOSTS" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Subdominio: $fullDomain" -ForegroundColor Yellow
Write-Host ""

try {
    # Leer contenido actual
    $currentHosts = Get-Content $hostsPath -Raw
    
    # Verificar si ya existe
    if ($currentHosts -match [regex]::Escape($fullDomain)) {
        Write-Host "[WARNING] El subdominio ya existe en el archivo hosts" -ForegroundColor Yellow
    } else {
        # Agregar nueva entrada
        $newEntry = "`n127.0.0.1       $fullDomain"
        Add-Content -Path $hostsPath -Value $newEntry -NoNewline
        
        Write-Host "[OK] Subdominio agregado correctamente" -ForegroundColor Green
        
        # Limpiar cache DNS
        Write-Host ""
        Write-Host "Limpiando cache DNS..." -ForegroundColor Gray
        ipconfig /flushdns | Out-Null
        Write-Host "[OK] Cache DNS limpiada" -ForegroundColor Green
    }
    
    Write-Host ""
    Write-Host "==================================================" -ForegroundColor Cyan
    Write-Host "Ahora puedes acceder a: http://$fullDomain" -ForegroundColor Cyan
    Write-Host "==================================================" -ForegroundColor Cyan
    
} catch {
    Write-Host "[ERROR] Error: $_" -ForegroundColor Red
    exit 1
}
