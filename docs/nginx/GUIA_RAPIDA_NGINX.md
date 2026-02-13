# 🚀 Guía Rápida: Nginx Multi-Tenant

> Guía actualizada para configurar Nginx con tu aplicación multi-tenant usando Docker.

---

## 📦 Instalación Rápida - Desarrollo Local con Docker

### Requisitos Previos

- ✅ Docker Desktop con WSL2 habilitado
- ✅ Windows 10/11
- ✅ Git
- ✅ Permisos de administrador (para editar archivo hosts)

---

## 🎯 Paso 1: Configurar Archivo Hosts

**Ubicación:** `C:\Windows\System32\drivers\etc\hosts`

**Abrir como administrador** y agregar:

```text
# ========================================
# POS Multi-Tenant - Desarrollo Local
# ========================================

# Dominio Central (Administración)
127.0.0.1       adminpos.dokploy.movete.cloud
127.0.0.1       www.adminpos.dokploy.movete.cloud

# Tenants de Prueba
127.0.0.1       testempresa.dokploy.movete.cloud
127.0.0.1       empresa1.testempresa.dokploy.movete.cloud
```

**💡 Tip:** Puedes agregar más tenants según necesites.

---

## 🐳 Paso 2: Iniciar Contenedores Docker

### Opción A: Usar el script PowerShell (Recomendado)

```powershell
.\iniciar_docker_wsl.ps1
```

### Opción B: Comando manual

```powershell
# Desde PowerShell en la raíz del proyecto
docker compose -f docker-compose.nginx.yml up -d
```

**Servicios que se iniciarán:**
- 🌐 Nginx (puerto 80)
- 🐘 PHP-FPM 8.2
- 🗄️ MySQL 8.0
- 💾 Redis
- 📊 PhpMyAdmin (puerto 8080)

---

## 🔧 Paso 3: Configurar Laravel

### 3.1 Crear archivo .env

```bash
# Entrar al contenedor PHP
docker compose -f docker-compose.nginx.yml exec php bash

# Ejecutar script de configuración
bash crear_env.sh

# Generar APP_KEY
php artisan key:generate

# Salir del contenedor
exit
```

### 3.2 Publicar Assets de Livewire

```bash
docker compose -f docker-compose.nginx.yml exec php php artisan livewire:publish --assets
```

### 3.3 Verificar configuración .env

El archivo `.env` debe contener:

```env
# Base de datos
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=pos_central
DB_USERNAME=root
DB_PASSWORD=root_password

# Dominio central
CENTRAL_DOMAIN=dokploy.movete.cloud
APP_URL=http://adminpos.dokploy.movete.cloud

# Cache y sesiones (IMPORTANTE para rendimiento)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=sync

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

## 🗄️ Paso 4: Ejecutar Migraciones y Seeders

```bash
# Ejecutar migraciones
docker compose -f docker-compose.nginx.yml exec php php artisan migrate

# Ejecutar seeders (crea usuarios y datos iniciales)
docker compose -f docker-compose.nginx.yml exec php php artisan db:seed

# Opcional: Seeder específico de módulos
docker compose -f docker-compose.nginx.yml exec php php artisan db:seed --class=ModuleSeeder
```

---

## 🏢 Paso 5: Crear Tenant de Prueba

### Método 1: Desde la Interfaz Web

1. Accede a: `http://adminpos.dokploy.movete.cloud/register-tenant`
2. Completa el formulario
3. Accede al tenant creado

### Método 2: Usando Artisan (si existe el comando)

```bash
docker compose -f docker-compose.nginx.yml exec php php artisan tenant:create testempresa test@example.com
```

**Nota:** El dominio del tenant será: `testempresa.dokploy.movete.cloud`

---

## ✅ Paso 6: Verificar Instalación

### 6.1 Verificar servicios Docker

```powershell
docker compose -f docker-compose.nginx.yml ps
```

**Deberías ver:**
```
NAME                        STATUS
laravel-nginx-multitenant   Up
laravel-php-fpm             Up
mysql                       Up
redis                       Up
phpmyadmin                  Up
```

### 6.2 Verificar conectividad

```powershell
# Ping al dominio central
ping adminpos.dokploy.movete.cloud

# Debería resolver a 127.0.0.1
```

### 6.3 Verificar en navegador

- **Panel Central:** http://adminpos.dokploy.movete.cloud/login
- **PhpMyAdmin:** http://localhost:8080
  - Usuario: `root`
  - Password: `root_password`

---

## 🔐 Paso 7: Acceder al Sistema

### Dominio Central
- **URL:** http://adminpos.dokploy.movete.cloud/login
- **Usuario:** `superadmin@gmail.com`
- **Contraseña:** `12345678`

### Tenant de Prueba
- **URL:** http://testempresa.dokploy.movete.cloud/login
- **Usuario:** Email configurado al crear el tenant
- **Contraseña:** Contraseña configurada al crear el tenant

---

## 🛠️ Comandos Útiles Docker

### Gestión de Contenedores

```bash
# Ver estado de contenedores
docker compose -f docker-compose.nginx.yml ps

# Ver logs en tiempo real
docker compose -f docker-compose.nginx.yml logs -f

# Ver logs de un servicio específico
docker compose -f docker-compose.nginx.yml logs -f nginx
docker compose -f docker-compose.nginx.yml logs -f php

# Reiniciar todos los servicios
docker compose -f docker-compose.nginx.yml restart

# Reiniciar un servicio específico
docker compose -f docker-compose.nginx.yml restart php
docker compose -f docker-compose.nginx.yml restart nginx

# Detener servicios
docker compose -f docker-compose.nginx.yml stop

# Iniciar servicios
docker compose -f docker-compose.nginx.yml start

# Detener y eliminar contenedores
docker compose -f docker-compose.nginx.yml down

# Detener, eliminar y limpiar volúmenes
docker compose -f docker-compose.nginx.yml down -v
```

### Ejecutar Comandos Laravel

```bash
# Comandos Artisan
docker compose -f docker-compose.nginx.yml exec php php artisan [comando]

# Ejemplos:
docker compose -f docker-compose.nginx.yml exec php php artisan migrate
docker compose -f docker-compose.nginx.yml exec php php artisan db:seed
docker compose -f docker-compose.nginx.yml exec php php artisan tenants:list
docker compose -f docker-compose.nginx.yml exec php php artisan optimize:clear

# Composer
docker compose -f docker-compose.nginx.yml exec php composer install
docker compose -f docker-compose.nginx.yml exec php composer update

# Bash (acceso al contenedor)
docker compose -f docker-compose.nginx.yml exec php bash
```

### Base de Datos

```bash
# Conectar a MySQL
docker compose -f docker-compose.nginx.yml exec mysql mysql -uroot -proot_password

# Ver tenants
docker compose -f docker-compose.nginx.yml exec mysql mysql -uroot -proot_password pos_central -e "SELECT t.id, t.name, d.domain FROM tenants t LEFT JOIN domains d ON t.id = d.tenant_id;"

# Backup de base de datos
docker compose -f docker-compose.nginx.yml exec mysql mysqldump -uroot -proot_password pos_central > backup.sql

# Restaurar backup
docker compose -f docker-compose.nginx.yml exec -T mysql mysql -uroot -proot_password pos_central < backup.sql
```

---

## 🐛 Solución de Problemas Comunes

### Problema 1: Error 404 en todas las páginas

**Causa:** Nginx no está configurado correctamente o no se reinició.

**Solución:**

```bash
# Verificar configuración de Nginx
docker compose -f docker-compose.nginx.yml exec nginx nginx -t

# Reiniciar Nginx
docker compose -f docker-compose.nginx.yml restart nginx
```

### Problema 2: Error de conexión a base de datos

**Causa:** MySQL no está corriendo o credenciales incorrectas.

**Solución:**

```bash
# Verificar que MySQL esté corriendo
docker compose -f docker-compose.nginx.yml ps mysql

# Ver logs de MySQL
docker compose -f docker-compose.nginx.yml logs mysql

# Verificar .env
docker compose -f docker-compose.nginx.yml exec php cat .env | grep DB_
```

### Problema 3: Livewire no funciona (error 404)

**Causa:** Assets de Livewire no publicados.

**Solución:**

```bash
# Publicar assets
docker compose -f docker-compose.nginx.yml exec php php artisan livewire:publish --assets

# Limpiar cachés
docker compose -f docker-compose.nginx.yml exec php php artisan optimize:clear

# Reiniciar PHP-FPM
docker compose -f docker-compose.nginx.yml restart php
```

### Problema 4: Puerto 80 ocupado (Apache/XAMPP)

**Causa:** Apache corriendo en Windows.

**Solución:**

```powershell
# Usar el script
.\detener_apache.ps1

# O manual:
Stop-Service -Name "Apache*"
Get-Process httpd | Stop-Process -Force
```

### Problema 5: Lentitud general

**Causas comunes:**
- Sesiones en `file` (debe ser `redis`)
- Logs muy grandes
- Cache no optimizada

**Solución:**

```bash
# 1. Verificar driver de sesiones
docker compose -f docker-compose.nginx.yml exec php grep SESSION_DRIVER .env
# Debe ser: SESSION_DRIVER=redis

# 2. Limpiar logs
docker compose -f docker-compose.nginx.yml exec php bash -c "echo '' > storage/logs/laravel.log"

# 3. Cachear configuración
docker compose -f docker-compose.nginx.yml exec php php artisan config:cache

# 4. Reiniciar servicios
docker compose -f docker-compose.nginx.yml restart php nginx
```

### Problema 6: Cambios en .env no se reflejan

**Causa:** Configuración cacheada.

**Solución:**

```bash
# Limpiar cache de configuración
docker compose -f docker-compose.nginx.yml exec php php artisan config:clear

# O limpiar todas las cachés
docker compose -f docker-compose.nginx.yml exec php php artisan optimize:clear
```

---

## 🔄 Mantenimiento Regular

### Limpiar Logs

```bash
# Limpiar log principal
docker compose -f docker-compose.nginx.yml exec php bash -c "echo '' > storage/logs/laravel.log"

# Ver tamaño de logs
docker compose -f docker-compose.nginx.yml exec php du -sh storage/logs/
```

### Optimizar Base de Datos

```bash
# Optimizar tablas
docker compose -f docker-compose.nginx.yml exec mysql mysqlcheck -uroot -proot_password --optimize --all-databases
```

### Actualizar Dependencias

```bash
# Composer
docker compose -f docker-compose.nginx.yml exec php composer update

# NPM (si usas assets compilados)
docker compose -f docker-compose.nginx.yml exec php npm update
```

---

## 📋 Checklist Post-Instalación

- [ ] Servicios Docker corriendo
- [ ] Archivo hosts configurado
- [ ] `.env` configurado correctamente
- [ ] Migraciones ejecutadas
- [ ] Seeders ejecutados
- [ ] Assets de Livewire publicados
- [ ] Dominio central accesible
- [ ] Login funciona
- [ ] Puedo crear tenants
- [ ] Tenants son accesibles
- [ ] PhpMyAdmin funciona

---

## 🎓 Siguientes Pasos

1. **Configurar tu empresa:**
   - Accede a Configuración
   - Completa datos de la empresa
   - Configura impuestos y métodos de pago

2. **Crear usuarios:**
   - Ve a Usuarios
   - Asigna roles y permisos

3. **Agregar productos:**
   - Ve a Productos
   - Carga tu catálogo

4. **Crear tenant de prueba:**
   - Prueba la funcionalidad multi-tenant
   - Verifica aislamiento de datos

---

## 📚 Documentación Adicional

- **Arquitectura Multi-Tenant:** `ARQUITECTURA_MULTITENANT.md`
- **Documentación Completa:** `DOCUMENTACION.md`
- **Cómo Funciona Nginx:** `docs/nginx/COMO_FUNCIONA_NGINX.md`
- **Deployment Checklist:** `docs/nginx/DEPLOYMENT_CHECKLIST.md`

---

## 💡 Tips de Rendimiento

1. **Siempre usa Redis:**
   ```env
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   ```

2. **Cachea en producción:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Limpia logs regularmente:**
   ```bash
   echo '' > storage/logs/laravel.log
   ```

4. **Monitorea el uso de recursos:**
   ```bash
   docker stats
   ```

---

**¿Problemas?** Revisa la [Documentación Completa](../../DOCUMENTACION.md) o los logs:

```bash
# Ver logs de Laravel
docker compose -f docker-compose.nginx.yml exec php tail -f storage/logs/laravel.log

# Ver logs de Nginx
docker compose -f docker-compose.nginx.yml logs -f nginx

# Ver logs de PHP-FPM
docker compose -f docker-compose.nginx.yml logs -f php
```

---

**Desarrollado con ❤️ usando Laravel, Livewire, Docker y Nginx**
