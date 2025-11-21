# 📘 Documentación Nginx Multi-Tenant

Documentación completa sobre la configuración y uso de Nginx para el sistema POS multi-tenant.

---

## 📚 Índice de Documentación

### 🚀 [Guía Rápida - Desarrollo Local](./GUIA_RAPIDA_NGINX.md)
**Inicio rápido en 7 pasos:**
- Configurar archivo hosts
- Iniciar Docker
- Configurar Laravel
- Ejecutar migraciones
- Crear tenants
- Verificar instalación
- Acceder al sistema

**Perfecta para:** Desarrollo local, primeros pasos.

---

### 🌐 [Guía de Deployment en VPS](./GUIA_DEPLOYMENT_VPS.md)
**Deployment completo en servidor VPS:**
- Requisitos del VPS
- Configuración DNS (wildcard)
- Instalación de Docker
- Deployment de la aplicación
- Configuración SSL/HTTPS
- Optimizaciones de producción
- Backups y monitoreo

**Perfecta para:** Producción, servidores VPS, deployment real.

---

### 🏗️ [Cómo Funciona Nginx](./COMO_FUNCIONA_NGINX.md)
**Arquitectura y funcionamiento:**
- Reverse Proxy
- Multi-tenancy por dominio
- PHP-FPM
- Servir archivos estáticos
- Manejo de errores

**Perfecta para:** Entender la arquitectura, debugging avanzado.

---

### ✅ [Checklist de Deployment](./DEPLOYMENT_CHECKLIST.md)
**Lista de verificación para producción:**
- Configuraciones de seguridad
- Optimizaciones
- Backups
- Monitoreo
- SSL/HTTPS

**Perfecta para:** Deployment a producción, VPS, servidores.

---

### 📊 [Diagrama de Arquitectura](./DIAGRAMA_NGINX_MULTITENANT.md)
**Diagramas visuales:**
- Flujo de peticiones
- Arquitectura de contenedores
- Multi-tenancy
- Base de datos

**Perfecta para:** Visualizar la arquitectura, presentaciones.

---

## 🎯 Inicio Rápido

### Para Desarrollo Local

```powershell
# 1. Iniciar Docker
.\iniciar_docker_wsl.ps1

# 2. Configurar Laravel
docker compose -f docker-compose.nginx.yml exec php bash
bash crear_env.sh
php artisan key:generate
php artisan migrate
php artisan db:seed
exit

# 3. Acceder
# http://adminpos.dokploy.movete.cloud/login
```

---

## 🔧 Configuración

### Archivo hosts (Windows)

**Ubicación:** `C:\Windows\System32\drivers\etc\hosts`

```text
127.0.0.1       adminpos.dokploy.movete.cloud
127.0.0.1       testempresa.dokploy.movete.cloud
```

### Archivo .env

```env
# Base de datos
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=pos_central
DB_USERNAME=root
DB_PASSWORD=root_password

# Dominio
CENTRAL_DOMAIN=dokploy.movete.cloud
APP_URL=http://adminpos.dokploy.movete.cloud

# Cache (IMPORTANTE)
CACHE_DRIVER=redis
SESSION_DRIVER=redis

# Redis
REDIS_HOST=redis
REDIS_PORT=6379
```

---

## 📁 Estructura de Archivos

### Configuración Docker

```
docker-compose.nginx.yml       # Orquestación de servicios
docker/
├── php/
│   ├── Dockerfile            # Imagen PHP personalizada
│   └── php.ini              # Configuración PHP
└── nginx/
    └── nginx-multitenant-docker.conf  # Config Nginx
```

### Scripts Disponibles

```
iniciar_docker_wsl.ps1        # Iniciar Docker desde PowerShell
detener_apache.ps1            # Detener Apache (conflicto puerto 80)
crear_env.sh                  # Crear archivo .env
rebuild_docker_php.ps1        # Reconstruir imagen PHP
```

---

## 🐳 Docker Compose

### Servicios

| Servicio | Puerto | Descripción |
|----------|--------|-------------|
| **nginx** | 80 | Reverse proxy |
| **php** | 9000 | PHP-FPM 8.2 |
| **mysql** | 3306 | MySQL 8.0 |
| **redis** | 6379 | Cache y sesiones |
| **phpmyadmin** | 8080 | Administración DB |

### Comandos Básicos

```bash
# Iniciar
docker compose -f docker-compose.nginx.yml up -d

# Detener
docker compose -f docker-compose.nginx.yml down

# Ver logs
docker compose -f docker-compose.nginx.yml logs -f

# Reiniciar
docker compose -f docker-compose.nginx.yml restart
```

---

## 🏢 Multi-Tenancy

### Tipos de Dominios

1. **Central:** `adminpos.dokploy.movete.cloud`
   - Administración global
   - Gestión de tenants

2. **Tenant Principal:** `empresa.dokploy.movete.cloud`
   - Empresa independiente
   - Base de datos: `tenant_empresa`

3. **Sub-Tenant:** `sucursal.empresa.dokploy.movete.cloud`
   - Sucursal/franquicia
   - Base de datos: `tenant_sucursal`

### Crear Tenant

**Desde interfaz web:**
```
http://adminpos.dokploy.movete.cloud/register-tenant
```

**Desde línea de comandos:**
```bash
docker compose -f docker-compose.nginx.yml exec php php artisan tenant:create nombre email@example.com
```

---

## 🐛 Troubleshooting

### Problemas Comunes

#### 1. Error 404 en todas las páginas
```bash
# Verificar configuración Nginx
docker compose -f docker-compose.nginx.yml exec nginx nginx -t

# Reiniciar Nginx
docker compose -f docker-compose.nginx.yml restart nginx
```

#### 2. Livewire no funciona
```bash
# Publicar assets
docker compose -f docker-compose.nginx.yml exec php php artisan livewire:publish --assets

# Limpiar cachés
docker compose -f docker-compose.nginx.yml exec php php artisan optimize:clear
```

#### 3. Puerto 80 ocupado
```powershell
# Detener Apache
.\detener_apache.ps1
```

#### 4. Lentitud
```bash
# Verificar driver de sesiones (debe ser redis)
docker compose -f docker-compose.nginx.yml exec php grep SESSION_DRIVER .env

# Limpiar logs
docker compose -f docker-compose.nginx.yml exec php bash -c "echo '' > storage/logs/laravel.log"

# Cachear configuración
docker compose -f docker-compose.nginx.yml exec php php artisan config:cache
```

---

## ⚡ Optimizaciones

### Para Desarrollo

```env
# .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=sync
APP_DEBUG=true
```

### Para Producción

```bash
# Cachear todo
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimizar autoload
composer dump-autoload -o
```

---

## 📊 Monitoreo

### Ver uso de recursos

```bash
# Docker stats
docker stats

# Ver logs en tiempo real
docker compose -f docker-compose.nginx.yml logs -f nginx
docker compose -f docker-compose.nginx.yml logs -f php
```

### Verificar salud de servicios

```bash
# Estado de contenedores
docker compose -f docker-compose.nginx.yml ps

# Verificar Nginx
docker compose -f docker-compose.nginx.yml exec nginx nginx -t

# Verificar MySQL
docker compose -f docker-compose.nginx.yml exec mysql mysqladmin -uroot -proot_password ping
```

---

## 🔐 Seguridad

### Checklist Básico

- [ ] Cambiar contraseñas por defecto
- [ ] Configurar firewall
- [ ] Usar HTTPS (SSL/TLS)
- [ ] Restringir acceso a PhpMyAdmin
- [ ] Hacer backups regulares
- [ ] Actualizar dependencias
- [ ] Revisar logs de errores

### Configurar SSL (Producción)

Ver [DEPLOYMENT_CHECKLIST.md](./DEPLOYMENT_CHECKLIST.md) para instrucciones completas.

---

## 📦 Backup y Restauración

### Backup Manual

```bash
# Base de datos
docker compose -f docker-compose.nginx.yml exec mysql mysqldump -uroot -proot_password pos_central > backup_$(date +%Y%m%d).sql

# Archivos
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/
```

### Restauración

```bash
# Base de datos
docker compose -f docker-compose.nginx.yml exec -T mysql mysql -uroot -proot_password pos_central < backup_20251109.sql

# Archivos
tar -xzf storage_backup_20251109.tar.gz
```

---

## 🎓 Recursos Adicionales

### Documentación Laravel
- [Multi-Tenancy Package](https://tenancyforlaravel.com/)
- [Livewire](https://laravel-livewire.com/)
- [Laravel Docs](https://laravel.com/docs)

### Documentación Docker
- [Docker Compose](https://docs.docker.com/compose/)
- [Docker Networks](https://docs.docker.com/network/)

### Documentación Nginx
- [Nginx Docs](https://nginx.org/en/docs/)
- [Nginx Reverse Proxy](https://docs.nginx.com/nginx/admin-guide/web-server/reverse-proxy/)

---

## 💬 Soporte

### Logs Importantes

```bash
# Laravel
docker compose -f docker-compose.nginx.yml exec php tail -f storage/logs/laravel.log

# Nginx
docker compose -f docker-compose.nginx.yml logs -f nginx

# PHP-FPM
docker compose -f docker-compose.nginx.yml logs -f php

# MySQL
docker compose -f docker-compose.nginx.yml logs -f mysql
```

### Verificar Configuración

```bash
# Ver archivo .env
docker compose -f docker-compose.nginx.yml exec php cat .env

# Ver configuración Nginx
docker compose -f docker-compose.nginx.yml exec nginx cat /etc/nginx/conf.d/default.conf

# Ver configuración PHP
docker compose -f docker-compose.nginx.yml exec php php -i
```

---

## 📝 Notas Importantes

### Desarrollo vs Producción

| Aspecto | Desarrollo | Producción |
|---------|-----------|------------|
| **Dominio** | Local (hosts) | DNS real |
| **SSL** | No | Sí (Let's Encrypt) |
| **Debug** | ON | OFF |
| **Cache** | Redis | Redis + Opcache |
| **Backups** | Manual | Automático |

### Credenciales por Defecto

**Cambiar en producción:**

- MySQL root: `root_password`
- Super Admin: `superadmin@gmail.com` / `12345678`
- PhpMyAdmin: puerto 8080 (deshabilitar en producción)

---

## 🚀 Próximos Pasos

1. ✅ Completar [Guía Rápida](./GUIA_RAPIDA_NGINX.md)
2. 📖 Leer [Cómo Funciona Nginx](./COMO_FUNCIONA_NGINX.md)
3. 🎯 Revisar [Deployment Checklist](./DEPLOYMENT_CHECKLIST.md)
4. 📊 Ver [Diagrama de Arquitectura](./DIAGRAMA_NGINX_MULTITENANT.md)
5. 🔧 Configurar tu empresa y productos
6. 🏢 Crear tus tenants
7. 🚀 Desplegar a producción

---

**¿Preguntas?** Revisa la [Documentación Principal](../../DOCUMENTACION.md)

---

**Desarrollado con ❤️ usando Laravel, Livewire, Docker y Nginx**
