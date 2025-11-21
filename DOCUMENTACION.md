# 📘 Documentación Completa - Sistema POS Multi-Tenant

> **Última actualización:** Noviembre 2025  
> **Versión:** Laravel 9.x + Livewire 2.x + Nginx + Docker

---

## 📑 Índice

1. [Arquitectura Multi-Tenant](#arquitectura-multi-tenant)
2. [Instalación y Configuración](#instalación-y-configuración)
3. [Estructura de Dominios](#estructura-de-dominios)
4. [Optimizaciones de Rendimiento](#optimizaciones-de-rendimiento)
5. [Solución de Problemas Comunes](#solución-de-problemas-comunes)
6. [Comandos Útiles](#comandos-útiles)

---

## 🏗️ Arquitectura Multi-Tenant

### Descripción General

El sistema utiliza una arquitectura multi-tenant con:
- **Dominio Central:** Administración del sistema (`adminpos.dokploy.movete.cloud`)
- **Tenants Principales:** Empresas independientes (`empresa.dokploy.movete.cloud`)
- **Sub-Tenants:** Sucursales/franquicias (`sucursal.empresa.dokploy.movete.cloud`)

### Bases de Datos

#### Base de Datos Central: `pos_central`
**Contiene:**
- Tabla `tenants`: TODOS los tenants (principales y sub-tenants)
- Tabla `domains`: Dominios asignados
- Usuarios administradores del sistema

#### Base de Datos de Tenant: `tenant_xxxxx`
**Contiene:**
- Datos operacionales: usuarios, productos, ventas, clientes
- Configuraciones específicas de la empresa
- NO contiene información de otros tenants

---

## 🚀 Instalación y Configuración

> **📘 Guías disponibles:**
> - **Desarrollo Local (Windows):** Ver abajo
> - **Producción (VPS/Servidor):** Ver [docs/nginx/GUIA_DEPLOYMENT_VPS.md](docs/nginx/GUIA_DEPLOYMENT_VPS.md)

### Requisitos Previos (Desarrollo Local)

- Docker Desktop con WSL2 habilitado
- Windows 10/11
- Git

### Pasos de Instalación (Desarrollo Local)

#### 1. Clonar el Proyecto

```bash
git clone <repository-url>
cd app-pos-laravel
```

#### 2. Iniciar Docker con WSL2

```powershell
# Ejecutar desde PowerShell
.\iniciar_docker_wsl.ps1
```

O manualmente:

```bash
wsl bash -c "cd /mnt/c/Users/USUARIO/Documents/proyecto-pos/app-pos-laravel && docker compose -f docker-compose.nginx.yml up -d"
```

#### 3. Crear archivo .env

Ejecutar dentro del contenedor:

```bash
docker compose -f docker-compose.nginx.yml exec php bash
bash crear_env.sh
php artisan key:generate
```

#### 4. Ejecutar Migraciones

```bash
docker compose -f docker-compose.nginx.yml exec php php artisan migrate
docker compose -f docker-compose.nginx.yml exec php php artisan db:seed
```

#### 5. Publicar Assets de Livewire

```bash
docker compose -f docker-compose.nginx.yml exec php php artisan livewire:publish --assets
```

#### 6. Configurar archivo hosts (Windows)

**Ruta:** `C:\Windows\System32\drivers\etc\hosts`

```text
# Dominio Central
127.0.0.1       adminpos.dokploy.movete.cloud

# Tenants
127.0.0.1       testempresa.dokploy.movete.cloud
127.0.0.1       empresa1.testempresa.dokploy.movete.cloud
```

#### 7. Acceder al Sistema

- **Panel Central:** `http://adminpos.dokploy.movete.cloud/login`
- **Credenciales por defecto:**
  - Email: `superadmin@gmail.com`
  - Password: `12345678`

---

## 🌐 Estructura de Dominios

### Dominio Central
- **URL:** `adminpos.dokploy.movete.cloud`
- **Base de datos:** `pos_central`
- **Puede crear:** Tenants principales

### Tenant Principal
- **Ejemplo:** `testempresa.dokploy.movete.cloud`
- **Base de datos:** `tenant_testempresa`
- **Puede crear:** Sub-tenants

### Sub-Tenant
- **Ejemplo:** `empresa1.testempresa.dokploy.movete.cloud`
- **Base de datos:** `tenant_empresa1`

---

## ⚡ Optimizaciones de Rendimiento

### Configuraciones Aplicadas

#### 1. Sesiones en Redis
```env
SESSION_DRIVER=redis
CACHE_DRIVER=redis
```

**Mejora:** 5-10x más rápido que `file` driver

#### 2. Opcache Habilitado
```ini
opcache.enable=On
opcache.memory_consumption=256
opcache.max_accelerated_files=10000
```

#### 3. Precarga de Permisos
- Middleware: `LoadUserPermissions`
- Carga todos los permisos del usuario en 1 query
- Evita problema N+1 en el menú

#### 4. Cache de Configuración

```bash
php artisan config:cache
```

### Mantenimiento de Rendimiento

#### Limpiar Logs Grandes

```bash
echo '' > storage/logs/laravel.log
```

#### Limpiar Cachés

```bash
php artisan optimize:clear
```

---

## 🐛 Solución de Problemas Comunes

### 1. Error: `could not find driver`

**Causa:** Falta extensión MySQL PDO en PHP

**Solución:** Ya está incluida en `docker/php/Dockerfile`:

```dockerfile
RUN docker-php-ext-install pdo_mysql
```

Reconstruir imagen:

```bash
docker compose -f docker-compose.nginx.yml build php
docker compose -f docker-compose.nginx.yml up -d --force-recreate php
```

### 2. Error 404 en Livewire

**Causa:** Assets de Livewire no publicados

**Solución:**

```bash
php artisan livewire:publish --assets
```

Luego agregar ruta manual en `routes/web.php` y `routes/tenant.php`:

```php
Route::get('/livewire/livewire.js', function () {
    $path = public_path('vendor/livewire/livewire.js');
    if (!file_exists($path)) {
        abort(404, 'Livewire assets not published');
    }
    return response()->file($path, [
        'Content-Type' => 'application/javascript',
        'Cache-Control' => 'public, max-age=31536000',
    ]);
});
```

### 3. Error: `This cache store does not support tagging`

**Causa:** Driver de caché `file` no soporta tags

**Solución:** Cambiar a Redis en `.env`:

```env
CACHE_DRIVER=redis
```

Instalar extensión Redis en PHP (ya incluida en Dockerfile):

```dockerfile
RUN pecl install redis && docker-php-ext-enable redis
```

### 4. Lentitud General

**Causas comunes:**
- Sesiones en `file` (cambiar a `redis`)
- Logs muy grandes (limpiar `storage/logs/`)
- Problema N+1 en queries (usar eager loading)
- Apache corriendo en puerto 80 (detener con `detener_apache.ps1`)

**Solución rápida:**

```bash
# Cambiar sesiones a Redis
sed -i 's/SESSION_DRIVER=file/SESSION_DRIVER=redis/' .env

# Limpiar logs
echo '' > storage/logs/laravel.log

# Cachear configuración
php artisan config:cache

# Reiniciar servicios
docker compose -f docker-compose.nginx.yml restart php nginx
```

### 5. Error: `chmod(): Operation not permitted`

**Causa:** Windows monta directorios con permisos restrictivos

**Solución:** Usar volumen Docker para `bootstrap/cache`:

En `docker-compose.nginx.yml`:

```yaml
volumes:
  - laravel-bootstrap-cache:/var/www/html/bootstrap/cache

volumes:
  laravel-bootstrap-cache:
    driver: local
```

### 6. Conflicto de Puerto 80 (Apache/XAMPP)

**Síntoma:** Nginx no puede iniciar

**Solución:**

```powershell
.\detener_apache.ps1
```

O manualmente:

```powershell
Stop-Service -Name "Apache*"
Get-Process httpd | Stop-Process -Force
```

---

## 📋 Comandos Útiles

### Docker

```bash
# Iniciar contenedores
docker compose -f docker-compose.nginx.yml up -d

# Detener contenedores
docker compose -f docker-compose.nginx.yml down

# Ver logs
docker compose -f docker-compose.nginx.yml logs -f php
docker compose -f docker-compose.nginx.yml logs -f nginx

# Reiniciar servicios
docker compose -f docker-compose.nginx.yml restart php nginx

# Reconstruir imagen PHP
docker compose -f docker-compose.nginx.yml build php --no-cache
```

### Laravel

```bash
# Limpiar todas las cachés
docker compose -f docker-compose.nginx.yml exec php php artisan optimize:clear

# Cachear configuración
docker compose -f docker-compose.nginx.yml exec php php artisan config:cache

# Ver lista de tenants
docker compose -f docker-compose.nginx.yml exec php php artisan tenants:list

# Ejecutar migraciones
docker compose -f docker-compose.nginx.yml exec php php artisan migrate

# Ejecutar seeders
docker compose -f docker-compose.nginx.yml exec php php artisan db:seed
```

### Base de Datos

```bash
# Conectar a MySQL
docker compose -f docker-compose.nginx.yml exec mysql mysql -uroot -proot_password

# Ver tenants
docker compose -f docker-compose.nginx.yml exec mysql mysql -uroot -proot_password pos_central -e "SELECT t.id, t.name, d.domain FROM tenants t LEFT JOIN domains d ON t.id = d.tenant_id;"

# Backup de base de datos
docker compose -f docker-compose.nginx.yml exec mysql mysqldump -uroot -proot_password pos_central > backup.sql
```

---

## 📁 Estructura de Archivos Importantes

```
app-pos-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── TenantRegistrationController.php  # Registro de tenants
│   │   ├── Middleware/
│   │   │   └── LoadUserPermissions.php  # Optimización de permisos
│   │   └── Livewire/
│   │       └── Admin/  # Componentes Livewire
│   ├── Services/
│   │   └── ModuleService.php  # Gestión de módulos cacheados
│   └── Policies/
│       └── ModulePolicy.php  # Políticas de acceso
├── config/
│   ├── tenancy.php  # Configuración multi-tenant
│   └── livewire.php  # Configuración Livewire
├── docker/
│   ├── php/
│   │   └── Dockerfile  # Imagen PHP personalizada
│   └── nginx/
│       └── nginx-multitenant-docker.conf
├── routes/
│   ├── web.php  # Rutas dominio central
│   ├── tenant.php  # Rutas para tenants
│   └── admin.php  # Rutas de administración
├── docker-compose.nginx.yml  # Configuración Docker
└── .env  # Configuración de entorno
```

---

## 🔐 Variables de Entorno Importantes

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

# Cache y sesiones (OPTIMIZADO)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=sync

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cloudinary (opcional)
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

---

## 🎯 Mejores Prácticas

### 1. Desarrollo Local

- Usar `SESSION_DRIVER=redis` (nunca `file`)
- Limpiar logs periódicamente
- Mantener configuración cacheada en producción

### 2. Creación de Tenants

- Desde central: subdominios directos (`empresa.dokploy.movete.cloud`)
- Desde tenant: sub-subdominios (`sucursal.empresa.dokploy.movete.cloud`)
- Siempre agregar al archivo `hosts` en desarrollo

### 3. Mantenimiento

- Limpiar logs cada semana: `echo '' > storage/logs/laravel.log`
- Verificar tamaño de base de datos
- Monitorear rendimiento con Laravel Telescope (opcional)

### 4. Deployment

```bash
# 1. Cachear todo
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Optimizar autoload
composer dump-autoload -o

# 3. Reiniciar servicios
docker compose restart php nginx
```

---

## 📞 Soporte y Contacto

Para problemas o consultas, verificar:
1. ✅ Logs: `storage/logs/laravel.log`
2. ✅ Docker logs: `docker compose logs -f php`
3. ✅ Nginx logs en contenedor
4. ✅ Este documento de documentación

---

**Desarrollado con ❤️ usando Laravel, Livewire, Docker y Nginx**

