# Script PowerShell para parchear mPDF y solucionar el error de chmod en Docker/WSL
# Este script debe ejecutarse después de composer install/update

Write-Host "🔧 Aplicando parche a mPDF para solucionar error de chmod..." -ForegroundColor Cyan

$CacheFile = "vendor/mpdf/mpdf/src/Cache.php"

if (-not (Test-Path $CacheFile)) {
    Write-Host "❌ Error: No se encontró $CacheFile" -ForegroundColor Red
    exit 1
}

# Leer el contenido del archivo
$content = Get-Content $CacheFile -Raw

# Verificar si el parche ya está aplicado
if ($content -match "Ignorar errores de chmod en sistemas de archivos montados") {
    Write-Host "✅ El parche ya está aplicado" -ForegroundColor Green
    exit 0
}

# Aplicar el parche
$oldCode = "@chmod(`$tempFile, 0664);"
$newCode = @"
try {
			@chmod(`$tempFile, 0664);
		} catch (\Throwable `$e) {
			// Ignorar errores de chmod en sistemas de archivos montados
		}
"@

$content = $content -replace [regex]::Escape($oldCode), $newCode

# Guardar el archivo modificado
Set-Content -Path $CacheFile -Value $content -NoNewline

Write-Host "✅ Parche aplicado exitosamente" -ForegroundColor Green

