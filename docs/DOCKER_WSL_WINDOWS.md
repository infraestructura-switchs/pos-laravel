# Guía: Docker con WSL2 en Windows

## 🔍 Entendiendo el Problema

Cuando tienes Docker corriendo en WSL2, hay una confusión común:
- **WSL2** es un sistema Linux virtualizado dentro de Windows
- **Docker Desktop** puede correr en WSL2, pero puede acceder a tus archivos de Windows
- Los archivos de Windows están montados automáticamente en WSL2

## 📁 Ubicación de Archivos en WSL2

Tus archivos de Windows están disponibles en WSL2 en esta ruta:
```
/mnt/c/Users/USUARIO/Documents/proyecto-pos/app-pos-laravel
```

**Ejemplo:**
- Windows: `C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel`
- WSL2: `/mnt/c/Users/USUARIO/Documents/proyecto-pos/app-pos-laravel`

## 🚀 Solución: Dos Formas de Trabajar

### Opción 1: Ejecutar Docker desde WSL2 (Recomendado)

1. **Abrir WSL2:**
   ```bash
   # Desde PowerShell o CMD, ejecuta:
   wsl
   ```

2. **Navegar a tu proyecto:**
   ```bash
   cd /mnt/c/Users/USUARIO/Documents/proyecto-pos/app-pos-laravel
   ```

3. **Verificar Docker:**
   ```bash
   docker --version
   docker-compose --version
   ```

4. **Iniciar contenedores:**
   ```bash
   docker-compose -f docker-compose.nginx.yml up -d
   ```

### Opción 2: Ejecutar Docker desde PowerShell (Si Docker Desktop está configurado)

Si tienes **Docker Desktop para Windows** instalado, puedes ejecutar desde PowerShell:

```powershell
# Navegar a tu proyecto
cd C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel

# Iniciar contenedores
docker-compose -f docker-compose.nginx.yml up -d
```

## ⚠️ Problema Común: Rutas en docker-compose.yml

El archivo `docker-compose.nginx.yml` usa rutas relativas (`.`), que funcionan tanto en Windows como en WSL2:

```yaml
volumes:
  - .:/var/www/html  # Esto funciona en ambos sistemas
```

**PERO** hay una diferencia importante:
- En **Windows**: Las rutas usan `\` y pueden tener problemas de permisos
- En **WSL2**: Las rutas usan `/` y funcionan mejor con Docker

## 🔧 Configuración Recomendada

### Paso 1: Verificar Docker Desktop

1. Abre **Docker Desktop**
2. Ve a **Settings** → **General**
3. Asegúrate que **"Use the WSL 2 based engine"** esté marcado
4. Ve a **Settings** → **Resources** → **WSL Integration**
5. Activa la integración con tu distribución WSL2

### Paso 2: Probar desde WSL2

```bash
# Abrir WSL2
wsl

# Ir a tu proyecto
cd /mnt/c/Users/USUARIO/Documents/proyecto-pos/app-pos-laravel

# Verificar que Docker funciona
docker ps

# Si funciona, iniciar tu proyecto
docker-compose -f docker-compose.nginx.yml up -d
```

### Paso 3: Si Docker no funciona desde PowerShell

Si `docker` no funciona desde PowerShell, significa que Docker Desktop no está configurado para Windows. En ese caso:

1. **Solo usa WSL2** para ejecutar Docker
2. O instala **Docker Desktop para Windows** desde: https://www.docker.com/products/docker-desktop

## 📝 Script de Verificación

Crea este script para verificar tu configuración:

```powershell
# verificar_docker.ps1
Write-Host "Verificando Docker..." -ForegroundColor Cyan

# Verificar Docker en PowerShell
$dockerWin = Get-Command docker -ErrorAction SilentlyContinue
if ($dockerWin) {
    Write-Host "✓ Docker encontrado en PowerShell" -ForegroundColor Green
    docker --version
} else {
    Write-Host "✗ Docker NO encontrado en PowerShell" -ForegroundColor Red
}

# Verificar Docker en WSL2
Write-Host "`nVerificando Docker en WSL2..." -ForegroundColor Cyan
$wslDocker = wsl docker --version 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Docker funciona en WSL2" -ForegroundColor Green
    Write-Host $wslDocker
} else {
    Write-Host "✗ Docker NO funciona en WSL2" -ForegroundColor Red
}

# Verificar ruta del proyecto
Write-Host "`nRuta del proyecto:" -ForegroundColor Cyan
Write-Host "Windows: $PSScriptRoot" -ForegroundColor Yellow
Write-Host "WSL2: /mnt/c/Users/USUARIO/Documents/proyecto-pos/app-pos-laravel" -ForegroundColor Yellow
```

## 🎯 Resumen: Qué Hacer

1. **Abre WSL2** (desde PowerShell: `wsl`)
2. **Ve a tu proyecto:** `cd /mnt/c/Users/USUARIO/Documents/proyecto-pos/app-pos-laravel`
3. **Ejecuta Docker:** `docker-compose -f docker-compose.nginx.yml up -d`

**¡Eso es todo!** Docker en WSL2 puede acceder a tus archivos de Windows sin problemas.

## 🔍 Solución de Problemas

### Error: "Cannot connect to the Docker daemon"

**Solución:** Asegúrate que Docker Desktop esté corriendo.

### Error: "Permission denied"

**Solución:** En WSL2, los archivos de Windows pueden tener problemas de permisos. Ejecuta:
```bash
sudo chmod -R 775 /mnt/c/Users/USUARIO/Documents/proyecto-pos/app-pos-laravel/storage
```

### Error: "No such file or directory"

**Solución:** Verifica que estés en la ruta correcta:
```bash
pwd  # Debe mostrar: /mnt/c/Users/USUARIO/Documents/proyecto-pos/app-pos-laravel
ls   # Debe mostrar tus archivos
```

## 💡 Tip Pro

Puedes crear un alias en WSL2 para ir rápido a tu proyecto:

```bash
# Agregar al ~/.bashrc o ~/.zshrc
alias proj='cd /mnt/c/Users/USUARIO/Documents/proyecto-pos/app-pos-laravel'
```

Luego solo ejecuta: `proj`

