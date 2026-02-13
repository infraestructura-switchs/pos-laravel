# Script de verificacion de configuracion Multi-Tenant
# No requiere permisos de administrador

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "  VERIFICACION MULTI-TENANT" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

$allGood = $true

# 1. Verificar PHP
Write-Host "1. Verificando PHP..." -ForegroundColor Yellow
try {
    $phpVersion = php -v 2>&1 | Select-String -Pattern "PHP (\d+\.\d+\.\d+)" | ForEach-Object { $_.Matches.Groups[1].Value }
    if ($phpVersion) {
        Write-Host "   [OK] PHP $phpVersion detectado" -ForegroundColor Green
    } else {
        Write-Host "   [ERROR] PHP no encontrado" -ForegroundColor Red
        $allGood = $false
    }
} catch {
    Write-Host "   [ERROR] PHP no encontrado en PATH" -ForegroundColor Red
    $allGood = $false
}
Write-Host ""

# 2. Verificar Apache
Write-Host "2. Verificando Apache..." -ForegroundColor Yellow
$apacheService = Get-Service -Name "Apache2.4" -ErrorAction SilentlyContinue
if ($apacheService) {
    if ($apacheService.Status -eq "Running") {
        Write-Host "   [OK] Apache esta corriendo" -ForegroundColor Green
    } else {
        Write-Host "   [WARNING] Apache esta detenido" -ForegroundColor Yellow
        $allGood = $false
    }
} else {
    Write-Host "   [ERROR] Servicio Apache2.4 no encontrado" -ForegroundColor Red
    $allGood = $false
}
Write-Host ""

# 3. Verificar archivo hosts
Write-Host "3. Verificando archivo hosts..." -ForegroundColor Yellow
$hostsPath = "C:\Windows\System32\drivers\etc\hosts"
if (Test-Path $hostsPath) {
    $hostsContent = Get-Content $hostsPath -Raw
    
    $domains = @("dokploy.movete.cloud", "empresa1.dokploy.movete.cloud", "empresa2.dokploy.movete.cloud")
    $foundDomains = 0
    
    foreach ($domain in $domains) {
        if ($hostsContent -match [regex]::Escape($domain)) {
            Write-Host "   [OK] $domain encontrado" -ForegroundColor Green
            $foundDomains++
        } else {
            Write-Host "   [ERROR] $domain NO encontrado" -ForegroundColor Red
        }
    }
    
    if ($foundDomains -eq 0) {
        $allGood = $false
    }
} else {
    Write-Host "   [ERROR] Archivo hosts no encontrado" -ForegroundColor Red
    $allGood = $false
}
Write-Host ""

# 4. Verificar httpd-vhosts.conf
Write-Host "4. Verificando Virtual Hosts..." -ForegroundColor Yellow
$vhostsPath = "C:\xampp\apache\conf\extra\httpd-vhosts.conf"
if (Test-Path $vhostsPath) {
    $vhostsContent = Get-Content $vhostsPath -Raw
    
    if ($vhostsContent -match "dokploy.movete.cloud") {
        Write-Host "   [OK] Configuracion de dokploy.movete.cloud encontrada" -ForegroundColor Green
        
        if ($vhostsContent -match "\*\.dokploy\.movete\.cloud") {
            Write-Host "   [OK] Wildcard (*.dokploy.movete.cloud) configurado" -ForegroundColor Green
        } else {
            Write-Host "   [WARNING] Wildcard no encontrado" -ForegroundColor Yellow
        }
    } else {
        Write-Host "   [ERROR] Configuracion NO encontrada" -ForegroundColor Red
        $allGood = $false
    }
} else {
    Write-Host "   [ERROR] Archivo httpd-vhosts.conf no encontrado" -ForegroundColor Red
    $allGood = $false
}
Write-Host ""

# 5. Verificar sintaxis de Apache
Write-Host "5. Verificando sintaxis de Apache..." -ForegroundColor Yellow
if (Test-Path "C:\xampp\apache\bin\httpd.exe") {
    $apacheTest = & "C:\xampp\apache\bin\httpd.exe" -t 2>&1
    
    if ($apacheTest -match "Syntax OK") {
        Write-Host "   [OK] Sintaxis correcta" -ForegroundColor Green
    } else {
        Write-Host "   [ERROR] Error en la sintaxis:" -ForegroundColor Red
        Write-Host "   $apacheTest" -ForegroundColor Red
        $allGood = $false
    }
} else {
    Write-Host "   [WARNING] httpd.exe no encontrado en la ruta esperada" -ForegroundColor Yellow
}
Write-Host ""

# 6. Verificar paquete Tenancy
Write-Host "6. Verificando Tenancy for Laravel..." -ForegroundColor Yellow
if (Test-Path "vendor/stancl/tenancy") {
    Write-Host "   [OK] Paquete Tenancy instalado" -ForegroundColor Green
} else {
    Write-Host "   [ERROR] Paquete Tenancy NO instalado" -ForegroundColor Red
    Write-Host "   Ejecuta: composer require stancl/tenancy" -ForegroundColor Yellow
    $allGood = $false
}
Write-Host ""

# 7. Verificar archivos de configuracion de Tenancy
Write-Host "7. Verificando archivos de Tenancy..." -ForegroundColor Yellow
$tenancyFiles = @(
    "config/tenancy.php",
    "app/Providers/TenancyServiceProvider.php"
)

foreach ($file in $tenancyFiles) {
    if (Test-Path $file) {
        Write-Host "   [OK] $file existe" -ForegroundColor Green
    } else {
        Write-Host "   [ERROR] $file NO existe" -ForegroundColor Red
        Write-Host "   Ejecuta: php artisan tenancy:install" -ForegroundColor Yellow
        $allGood = $false
    }
}
Write-Host ""

# 8. Test de conectividad (Ping)
Write-Host "8. Probando conectividad de dominios..." -ForegroundColor Yellow
$testDomains = @("dokploy.movete.cloud", "empresa1.dokploy.movete.cloud")

foreach ($domain in $testDomains) {
    $pingResult = Test-Connection -ComputerName $domain -Count 1 -Quiet -ErrorAction SilentlyContinue
    
    if ($pingResult) {
        Write-Host "   [OK] $domain responde" -ForegroundColor Green
    } else {
        Write-Host "   [ERROR] $domain NO responde" -ForegroundColor Red
        $allGood = $false
    }
}
Write-Host ""

# 9. Verificar .env
Write-Host "9. Verificando archivo .env..." -ForegroundColor Yellow
if (Test-Path ".env") {
    $envContent = Get-Content ".env" -Raw
    
    if ($envContent -match "APP_URL") {
        Write-Host "   [OK] .env existe y contiene APP_URL" -ForegroundColor Green
    } else {
        Write-Host "   [WARNING] .env existe pero falta APP_URL" -ForegroundColor Yellow
    }
} else {
    Write-Host "   [ERROR] .env no encontrado" -ForegroundColor Red
    Write-Host "   Copia .env.example a .env" -ForegroundColor Yellow
    $allGood = $false
}
Write-Host ""

# Resumen final
Write-Host "==================================================" -ForegroundColor Cyan
if ($allGood) {
    Write-Host "  [OK] TODAS LAS VERIFICACIONES PASARON" -ForegroundColor Green
    Write-Host "==================================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Tu entorno multi-tenant esta configurado correctamente" -ForegroundColor Green
    Write-Host ""
    Write-Host "Prueba en tu navegador:" -ForegroundColor White
    Write-Host "  - http://dokploy.movete.cloud" -ForegroundColor Cyan
    Write-Host "  - http://empresa1.dokploy.movete.cloud" -ForegroundColor Cyan
} else {
    Write-Host "  [WARNING] ALGUNAS VERIFICACIONES FALLARON" -ForegroundColor Yellow
    Write-Host "==================================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Revisa los errores marcados con [ERROR] arriba" -ForegroundColor Yellow
    Write-Host "Consulta la guia en docs/guias/" -ForegroundColor White
}
Write-Host ""
