# 🚀 Guía de Deployment en VPS

> Guía completa para desplegar el sistema POS Multi-Tenant en un servidor VPS con Docker y Nginx.

---

## 📋 Tabla de Contenidos

1. [Requisitos del VPS](#requisitos-del-vps)
2. [Configuración DNS](#configuración-dns)
3. [Preparación del Servidor](#preparación-del-servidor)
4. [Instalación de Docker](#instalación-de-docker)
5. [Deployment de la Aplicación](#deployment-de-la-aplicación)
6. [Configuración SSL (HTTPS)](#configuración-ssl-https)
7. [Optimizaciones de Producción](#optimizaciones-de-producción)
8. [Mantenimiento y Monitoreo](#mantenimiento-y-monitoreo)
9. [Troubleshooting](#troubleshooting)

---

## 🖥️ Requisitos del VPS

### Especificaciones Mínimas

| Recurso | Mínimo | Recomendado |
|---------|--------|-------------|
| **CPU** | 2 cores | 4 cores |
| **RAM** | 2 GB | 4-8 GB |
| **Disco** | 20 GB SSD | 40-80 GB SSD |
| **Ancho de banda** | 1 TB/mes | 2 TB/mes |

### Sistema Operativo

- ✅ **Ubuntu 20.04 LTS** (Recomendado)
- ✅ Ubuntu 22.04 LTS
- ✅ Debian 11/12
- ✅ CentOS 8 Stream

### Proveedores VPS Recomendados

- **DigitalOcean** - $12-24/mes
- **Linode** - $12-24/mes
- **Vultr** - $12-24/mes
- **AWS Lightsail** - $10-20/mes
- **Contabo** - €6-12/mes

---

## 🌐 Configuración DNS

### ⚠️ IMPORTANTE: DNS Wildcard

Para multi-tenancy, necesitas configurar un **DNS wildcard** que apunte todos los subdominios a tu VPS.

### Paso 1: Obtener IP del VPS

```bash
# En tu VPS, obtén la IP pública
curl ifconfig.me
# Ejemplo: 203.0.113.45
```

### Paso 2: Configurar DNS en tu Proveedor

#### Opción A: Cloudflare (Recomendado)

1. Ve a tu dominio en Cloudflare
2. Agrega estos registros DNS:

| Tipo | Nombre | Contenido | Proxy | TTL |
|------|--------|-----------|-------|-----|
| **A** | `@` | `203.0.113.45` | ✅ Proxied | Auto |
| **A** | `*` | `203.0.113.45` | ✅ Proxied | Auto |
| **A** | `adminpos` | `203.0.113.45` | ✅ Proxied | Auto |

**Explicación:**
- `@` → `dokploy.movete.cloud` apunta a tu VPS
- `*` → Cualquier subdominio (`empresa.dokploy.movete.cloud`) apunta a tu VPS
- `adminpos` → Panel central apunta a tu VPS

#### Opción B: Namecheap

1. Ve a "Advanced DNS"
2. Agrega estos registros:

| Tipo | Host | Valor | TTL |
|------|------|-------|-----|
| **A Record** | `@` | `203.0.113.45` | 1 min |
| **A Record** | `*` | `203.0.113.45` | 1 min |
| **A Record** | `adminpos` | `203.0.113.45` | 1 min |

#### Opción C: GoDaddy

1. Ve a "DNS Management"
2. Agrega:
   - A Record: `@` → `203.0.113.45`
   - A Record: `*` → `203.0.113.45`
   - A Record: `adminpos` → `203.0.113.45`

### Paso 3: Verificar Propagación DNS

```bash
# Verificar desde tu computadora local (esperar 5-30 minutos)
nslookup adminpos.dokploy.movete.cloud
nslookup testempresa.dokploy.movete.cloud

# Debería mostrar tu IP: 203.0.113.45
```

**🕐 Tiempo de propagación:** 5 minutos a 48 horas (usualmente 5-30 minutos)

---

## 🔧 Preparación del Servidor

### Paso 1: Conectar al VPS

```bash
# Desde tu computadora local
ssh root@203.0.113.45

# O si tienes usuario no-root
ssh usuario@203.0.113.45
```

### Paso 2: Actualizar Sistema

```bash
# Ubuntu/Debian
sudo apt update && sudo apt upgrade -y

# CentOS
sudo yum update -y
```

### Paso 3: Instalar Dependencias Básicas

```bash
sudo apt install -y \
    git \
    curl \
    wget \
    unzip \
    software-properties-common \
    apt-transport-https \
    ca-certificates \
    gnupg \
    lsb-release
```

### Paso 4: Configurar Firewall

```bash
# Instalar UFW
sudo apt install -y ufw

# Permitir SSH (IMPORTANTE: hazlo ANTES de habilitar el firewall)
sudo ufw allow 22/tcp

# Permitir HTTP y HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Habilitar firewall
sudo ufw enable

# Verificar estado
sudo ufw status
```

### Paso 5: Crear Usuario para la Aplicación (Opcional pero recomendado)

```bash
# Crear usuario
sudo adduser appuser

# Agregar a grupo sudo
sudo usermod -aG sudo appuser

# Cambiar a ese usuario
su - appuser
```

---

## 🐳 Instalación de Docker

### Método 1: Script Automático (Recomendado)

```bash
# Descargar e instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Agregar usuario actual al grupo docker
sudo usermod -aG docker $USER

# Aplicar cambios de grupo (o cerrar sesión y volver a entrar)
newgrp docker

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Verificar instalación
docker --version
docker compose version
```

### Método 2: Manual (Ubuntu)

```bash
# Agregar repositorio oficial de Docker
sudo mkdir -p /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg

echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Instalar Docker Engine
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Agregar usuario a grupo docker
sudo usermod -aG docker $USER
newgrp docker

# Verificar
docker --version
docker compose version
```

### Configurar Docker para Producción

```bash
# Crear archivo de configuración daemon
sudo mkdir -p /etc/docker
sudo tee /etc/docker/daemon.json > /dev/null <<EOF
{
  "log-driver": "json-file",
  "log-opts": {
    "max-size": "10m",
    "max-file": "3"
  },
  "storage-driver": "overlay2"
}
EOF

# Reiniciar Docker
sudo systemctl restart docker
sudo systemctl enable docker
```

---

## 📦 Deployment de la Aplicación

### Paso 1: Clonar Repositorio

```bash
# Ir al directorio home
cd ~

# Clonar repositorio
git clone https://github.com/tu-usuario/app-pos-laravel.git
cd app-pos-laravel

# O si es repositorio privado
git clone https://tu-usuario:tu-token@github.com/tu-usuario/app-pos-laravel.git
cd app-pos-laravel
```

### Paso 2: Configurar Variables de Entorno

```bash
# Copiar ejemplo de .env
cp .env.example .env

# Editar .env
nano .env
```

**Configuración para Producción:**

```env
# ==========================================
# APLICACIÓN
# ==========================================
APP_NAME="POS Multi-Tenant"
APP_ENV=production
APP_KEY=  # Se generará después
APP_DEBUG=false
APP_URL=https://adminpos.dokploy.movete.cloud

# ==========================================
# BASE DE DATOS
# ==========================================
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=pos_central
DB_USERNAME=root
DB_PASSWORD=TU_PASSWORD_SEGURO_AQUI_12345  # ⚠️ CAMBIAR

# ==========================================
# DOMINIO CENTRAL (SIN http:// o https://)
# ==========================================
CENTRAL_DOMAIN=dokploy.movete.cloud

# ==========================================
# CACHE Y SESIONES (Redis para rendimiento)
# ==========================================
CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120
QUEUE_CONNECTION=redis

# ==========================================
# REDIS
# ==========================================
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# ==========================================
# CLOUDINARY (opcional)
# ==========================================
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
CLOUDINARY_URL=cloudinary://your_api_key:your_api_secret@your_cloud_name

# ==========================================
# LOGS
# ==========================================
LOG_CHANNEL=stack
LOG_LEVEL=error  # En producción: error o critical

# ==========================================
# MAIL (Configurar según tu proveedor)
# ==========================================
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@dokploy.movete.cloud
MAIL_FROM_NAME="${APP_NAME}"
```

**Guardar:** `Ctrl + O`, `Enter`, `Ctrl + X`

### Paso 3: Actualizar docker-compose para Producción

```bash
# Editar docker-compose
nano docker-compose.nginx.yml
```

**Cambiar la configuración de MySQL:**

```yaml
mysql:
  image: mysql:8.0
  container_name: mysql
  restart: always
  environment:
    MYSQL_ROOT_PASSWORD: TU_PASSWORD_SEGURO_AQUI_12345  # ⚠️ MISMO del .env
    MYSQL_DATABASE: pos_central
  volumes:
    - mysql-data:/var/lib/mysql
  networks:
    - laravel-multitenant
  command: --default-authentication-plugin=mysql_native_password
```

### Paso 4: Iniciar Contenedores

```bash
# Iniciar en modo detached
docker compose -f docker-compose.nginx.yml up -d

# Ver logs en tiempo real
docker compose -f docker-compose.nginx.yml logs -f

# Verificar que todos los contenedores estén corriendo
docker compose -f docker-compose.nginx.yml ps
```

### Paso 5: Configurar Laravel

```bash
# Generar APP_KEY
docker compose -f docker-compose.nginx.yml exec php php artisan key:generate

# Publicar assets de Livewire
docker compose -f docker-compose.nginx.yml exec php php artisan livewire:publish --assets

# Ejecutar migraciones
docker compose -f docker-compose.nginx.yml exec php php artisan migrate --force

# Ejecutar seeders
docker compose -f docker-compose.nginx.yml exec php php artisan db:seed --force

# Cachear configuración (IMPORTANTE en producción)
docker compose -f docker-compose.nginx.yml exec php php artisan config:cache
docker compose -f docker-compose.nginx.yml exec php php artisan route:cache
docker compose -f docker-compose.nginx.yml exec php php artisan view:cache

# Optimizar autoload
docker compose -f docker-compose.nginx.yml exec php composer dump-autoload -o
```

### Paso 6: Configurar Permisos

```bash
# Dar permisos a storage y bootstrap/cache
docker compose -f docker-compose.nginx.yml exec php chown -R www-data:www-data storage bootstrap/cache
docker compose -f docker-compose.nginx.yml exec php chmod -R 775 storage bootstrap/cache
```

---

## 🔐 Configuración SSL (HTTPS)

### Opción 1: Cloudflare (Más Fácil)

Si usas Cloudflare con proxy habilitado:

1. **En Cloudflare:**
   - Ve a SSL/TLS → Overview
   - Modo: **Full** (no Full Strict por ahora)
   - Cloudflare manejará SSL automáticamente ✅

2. **En tu VPS:**
   - No necesitas certificado en Nginx
   - Cloudflare se encarga de HTTPS

### Opción 2: Let's Encrypt con Certbot (Recomendado sin Cloudflare)

```bash
# Instalar Certbot
sudo apt install -y certbot

# Detener Nginx temporalmente
docker compose -f docker-compose.nginx.yml stop nginx

# Obtener certificados (reemplaza con tus dominios)
sudo certbot certonly --standalone \
  -d dokploy.movete.cloud \
  -d adminpos.dokploy.movete.cloud \
  -d *.dokploy.movete.cloud \
  --email tu-email@example.com \
  --agree-tos \
  --non-interactive

# Los certificados se guardan en:
# /etc/letsencrypt/live/dokploy.movete.cloud/fullchain.pem
# /etc/letsencrypt/live/dokploy.movete.cloud/privkey.pem
```

**Actualizar Nginx para HTTPS:**

```bash
# Editar configuración de Nginx
nano conf/nginx/nginx-multitenant-docker.conf
```

Agregar configuración SSL:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name adminpos.dokploy.movete.cloud *.adminpos.dokploy.movete.cloud *.dokploy.movete.cloud;
    
    # Redirigir a HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name adminpos.dokploy.movete.cloud *.adminpos.dokploy.movete.cloud *.dokploy.movete.cloud;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/dokploy.movete.cloud/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/dokploy.movete.cloud/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    root /var/www/html/public;
    index index.php index.html;

    # ... resto de la configuración
}
```

**Montar certificados en Docker:**

Editar `docker-compose.nginx.yml`:

```yaml
nginx:
  image: nginx:1.29.3-alpine
  container_name: laravel-nginx-multitenant
  restart: unless-stopped
  ports:
    - "80:80"
    - "443:443"
  volumes:
    - .:/var/www/html
    - ./conf/nginx/nginx-multitenant-docker.conf:/etc/nginx/conf.d/default.conf
    - /etc/letsencrypt:/etc/letsencrypt:ro  # Montar certificados SSL
  networks:
    - laravel-multitenant
```

**Reiniciar Nginx:**

```bash
docker compose -f docker-compose.nginx.yml restart nginx
```

**Renovación Automática:**

```bash
# Configurar cron para renovar certificados
sudo crontab -e

# Agregar esta línea (renueva cada día a las 2 AM)
0 2 * * * certbot renew --quiet && docker compose -f /home/appuser/app-pos-laravel/docker-compose.nginx.yml restart nginx
```

---

## ⚡ Optimizaciones de Producción

### 1. Optimizar PHP (php.ini)

```bash
nano docker/php/php.ini
```

```ini
[PHP]
memory_limit = 256M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 60
max_input_time = 60

[opcache]
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.validate_timestamps=0  # En producción, deshabilitar
opcache.save_comments=1
opcache.fast_shutdown=1
```

### 2. Optimizar MySQL

```bash
# Crear archivo de configuración MySQL
mkdir -p docker/mysql
nano docker/mysql/my.cnf
```

```ini
[mysqld]
max_connections = 200
innodb_buffer_pool_size = 512M
innodb_log_file_size = 128M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
query_cache_type = 1
query_cache_size = 32M
```

**Montar en docker-compose:**

```yaml
mysql:
  volumes:
    - mysql-data:/var/lib/mysql
    - ./docker/mysql/my.cnf:/etc/mysql/conf.d/custom.cnf
```

### 3. Configurar Nginx para Alto Rendimiento

```nginx
# Al inicio de nginx-multitenant-docker.conf
worker_processes auto;
worker_rlimit_nofile 65535;

events {
    worker_connections 4096;
    use epoll;
    multi_accept on;
}

http {
    # Habilitar compresión
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json;
    
    # Cache de archivos estáticos
    open_file_cache max=1000 inactive=20s;
    open_file_cache_valid 30s;
    open_file_cache_min_uses 2;
    open_file_cache_errors on;
    
    # Buffers
    client_body_buffer_size 128k;
    client_max_body_size 64m;
    client_header_buffer_size 1k;
    large_client_header_buffers 4 16k;
    
    # Timeouts
    client_body_timeout 12;
    client_header_timeout 12;
    keepalive_timeout 15;
    send_timeout 10;
    
    # ... resto de configuración
}
```

### 4. Cron Jobs para Mantenimiento

```bash
# Editar crontab
crontab -e

# Agregar:
# Limpiador de logs cada semana
0 2 * * 0 cd /home/appuser/app-pos-laravel && docker compose -f docker-compose.nginx.yml exec php bash -c "echo '' > storage/logs/laravel.log"

# Laravel Scheduler (si usas colas o tareas programadas)
* * * * * cd /home/appuser/app-pos-laravel && docker compose -f docker-compose.nginx.yml exec php php artisan schedule:run >> /dev/null 2>&1

# Backup diario de base de datos (3 AM)
0 3 * * * cd /home/appuser/app-pos-laravel && docker compose -f docker-compose.nginx.yml exec mysql mysqldump -uroot -pTU_PASSWORD pos_central > ~/backups/db_$(date +\%Y\%m\%d).sql
```

---

## 📊 Mantenimiento y Monitoreo

### Monitoreo Básico

```bash
# Ver uso de recursos
docker stats

# Ver logs en tiempo real
docker compose -f docker-compose.nginx.yml logs -f --tail=100

# Ver logs de un servicio específico
docker compose -f docker-compose.nginx.yml logs -f nginx
docker compose -f docker-compose.nginx.yml logs -f php

# Ver estado de contenedores
docker compose -f docker-compose.nginx.yml ps
```

### Backups Automáticos

```bash
# Crear directorio de backups
mkdir -p ~/backups

# Script de backup completo
nano ~/backup.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR=~/backups
APP_DIR=~/app-pos-laravel

# Backup de base de datos
docker compose -f $APP_DIR/docker-compose.nginx.yml exec -T mysql \
  mysqldump -uroot -pTU_PASSWORD pos_central > $BACKUP_DIR/db_$DATE.sql

# Backup de storage
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz -C $APP_DIR storage/

# Eliminar backups antiguos (más de 7 días)
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completado: $DATE"
```

```bash
chmod +x ~/backup.sh

# Programar en crontab (diario a las 3 AM)
crontab -e
# Agregar:
0 3 * * * ~/backup.sh >> ~/backup.log 2>&1
```

### Actualizar Aplicación

```bash
cd ~/app-pos-laravel

# Pull últimos cambios
git pull origin main

# Reconstruir contenedores si hay cambios en Docker
docker compose -f docker-compose.nginx.yml build --no-cache

# Reiniciar contenedores
docker compose -f docker-compose.nginx.yml down
docker compose -f docker-compose.nginx.yml up -d

# Ejecutar migraciones
docker compose -f docker-compose.nginx.yml exec php php artisan migrate --force

# Limpiar y cachear
docker compose -f docker-compose.nginx.yml exec php php artisan optimize:clear
docker compose -f docker-compose.nginx.yml exec php php artisan config:cache
docker compose -f docker-compose.nginx.yml exec php php artisan route:cache
docker compose -f docker-compose.nginx.yml exec php php artisan view:cache
```

---

## 🐛 Troubleshooting VPS

### Problema: No se puede acceder al sitio

```bash
# 1. Verificar DNS
nslookup adminpos.dokploy.movete.cloud

# 2. Verificar que Nginx esté corriendo
docker compose -f docker-compose.nginx.yml ps nginx

# 3. Verificar logs de Nginx
docker compose -f docker-compose.nginx.yml logs nginx

# 4. Verificar firewall
sudo ufw status

# 5. Test de conectividad
curl -I http://adminpos.dokploy.movete.cloud
```

### Problema: Error 502 Bad Gateway

```bash
# Verificar que PHP-FPM esté corriendo
docker compose -f docker-compose.nginx.yml ps php

# Ver logs de PHP
docker compose -f docker-compose.nginx.yml logs php

# Reiniciar PHP-FPM
docker compose -f docker-compose.nginx.yml restart php
```

### Problema: Base de datos no conecta

```bash
# Verificar MySQL
docker compose -f docker-compose.nginx.yml ps mysql

# Logs de MySQL
docker compose -f docker-compose.nginx.yml logs mysql

# Test de conexión
docker compose -f docker-compose.nginx.yml exec php php artisan tinker
# Luego: DB::connection()->getPdo();
```

### Problema: Lentitud

```bash
# Ver uso de recursos
docker stats

# Verificar disco
df -h

# Verificar RAM
free -h

# Limpiar logs
docker compose -f docker-compose.nginx.yml exec php bash -c "echo '' > storage/logs/laravel.log"

# Limpiar Docker
docker system prune -a
```

---

## ✅ Checklist Post-Deployment

- [ ] DNS configurado (wildcard A record)
- [ ] Docker y Docker Compose instalados
- [ ] Firewall configurado (puertos 80, 443, 22)
- [ ] Aplicación clonada y configurada
- [ ] .env configurado para producción
- [ ] Contenedores Docker corriendo
- [ ] Migraciones ejecutadas
- [ ] SSL/HTTPS configurado
- [ ] Backups automáticos configurados
- [ ] Cron jobs configurados
- [ ] Logs rotando correctamente
- [ ] Monitoreo básico funcionando
- [ ] Acceso al panel central funciona
- [ ] Puede crear tenants
- [ ] Tenants son accesibles

---

## 📚 Recursos Adicionales

- **Monitoreo Avanzado:** Considerar Uptime Kuma, Netdata
- **Logs Centralizados:** Loki, Graylog
- **Performance:** New Relic, Datadog (pagos)
- **Backups:** Rsync a otro servidor, S3

---

**¿Problemas?** Revisa los logs:

```bash
# Laravel
docker compose -f docker-compose.nginx.yml exec php tail -f storage/logs/laravel.log

# Nginx
docker compose -f docker-compose.nginx.yml logs -f nginx

# Sistema
sudo journalctl -u docker.service
```

---

**Desarrollado con ❤️ para producción en VPS**

