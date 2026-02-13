# ====================================================================
# Script para dar acceso root a admin@gmail.com
# ====================================================================
# Uso: .\dar_acceso_root.ps1 nombre_base_datos
# Ejemplo: .\dar_acceso_root.ps1 tenant_empresax
# ====================================================================

param(
    [string]$DbName = ""
)

if ($DbName -eq "") {
    Write-Host ""
    Write-Host "Error: Debes especificar el nombre de la base de datos" -ForegroundColor Red
    Write-Host "Uso: .\dar_acceso_root.ps1 nombre_base_datos"
    Write-Host "Ejemplo: .\dar_acceso_root.ps1 tenant_empresax"
    Write-Host ""
    exit 1
}

Write-Host ""
Write-Host "====================================================================="
Write-Host "  DAR ACCESO ROOT A admin@gmail.com"
Write-Host "====================================================================="
Write-Host ""
Write-Host "Base de datos: $DbName"
Write-Host ""

# Ejecutar el update
docker compose -f docker-compose.nginx.yml exec mysql mysql -uroot -proot_password $DbName -e "UPDATE users SET is_root = 1 WHERE email = 'admin@gmail.com';"

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "[OK] Usuario actualizado exitosamente" -ForegroundColor Green
    Write-Host ""
    Write-Host "Verificando cambios:"
    docker compose -f docker-compose.nginx.yml exec mysql mysql -uroot -proot_password $DbName -e "SELECT id, name, email, is_root FROM users WHERE email = 'admin@gmail.com';"
    Write-Host ""
    Write-Host "====================================================================="
    Write-Host "  COMPLETADO"
    Write-Host "====================================================================="
    Write-Host ""
    Write-Host "Ahora admin@gmail.com tiene acceso a /administrador/modulos"
    Write-Host ""
} else {
    Write-Host ""
    Write-Host "[ERROR] No se pudo actualizar el usuario" -ForegroundColor Red
    Write-Host ""
}

