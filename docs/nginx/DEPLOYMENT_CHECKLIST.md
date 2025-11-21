# ✅ Checklist de Deployment - Nginx Multi-Tenant

Lista completa de verificación para deployar la aplicación en producción.

## 📋 Pre-Deployment

### Preparación del Código

- [ ] Código en rama de producción (main/master)
- [ ] Todas las pruebas pasando
- [ ] Sin errores de linter
- [ ] Dependencias actualizadas
- [ ] Assets compilados (`npm run build`)
- [ ] Variables de entorno documentadas

### Preparación del Servidor

- [ ] VPS contratado y accesible vía SSH
- [ ] IP pública asignada
- [ ] Dominio registrado
- [ ] DNS configurado (puede tomar 24-48h)
- [ ] Certificados SSL (si se usarán)

### Configuración DNS

- [ ] Registro A para dominio principal
  ```
  Tipo: A
  Nombre: adminpos
  Valor: IP_DEL_VPS
  TTL: 3600
  ```

- [ ] Registro A para wildcard subdominios
  ```
  Tipo: A
  Nombre: *.adminpos
  Valor: IP_DEL_VPS
  TTL: 3600
  ```

- [ ] Registro CNAME para www (opcional)
  ```
  Tipo: CNAME
  Nombre: www.adminpos
  Valor: adminpos.dokploy.movete.cloud
  TTL: 3600
  ```

### Base de Datos

- [ ] Backup de base de datos local
- [ ] Plan de migración de datos
- [ ] Credenciales seguras generadas
- [ ] Backup automático configurado (opcional)

---

## 🚀 Deployment

### 1. Configuración Inicial del Servidor

```bash
# Conectar al servidor
ssh root@IP_DEL_VPS

# Ejecutar script de setup
bash scripts/setup-vps.sh
```

**Verificar:**
- [ ] Nginx instalado y corriendo
- [ ] PHP 8.2+ instalado
- [ ] MySQL instalado
- [ ] Composer instalado
- [ ] Git instalado
- [ ] Firewall configurado

### 2. Clonar Proyecto

```bash
cd /var/www
git clone [URL_DEL_REPO] html
cd html
```

**Verificar:**
- [ ] Proyecto clonado correctamente
- [ ] Rama correcta (main/master)
- [ ] Archivos .git presentes

### 3. Configurar Permisos

```bash
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 775 /var/www/html/storage
sudo chmod -R 775 /var/www/html/bootstrap/cache
```

**Verificar:**
- [ ] Propietario: www-data
- [ ] storage/ escribible
- [ ] bootstrap/cache/ escribible

### 4. Configurar .env

```bash
cd /var/www/html
cp .env.example .env
nano .env
```

**Configuraciones requeridas:**

```env
# Aplicación
APP_NAME="POS Multi-Tenant"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://adminpos.dokploy.movete.cloud

# Dominio
CENTRAL_DOMAIN=adminpos.dokploy.movete.cloud

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_central
DB_USERNAME=laravel
DB_PASSWORD=[PASSWORD_SEGURO]

# Cache (opcional pero recomendado)
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Redis (si se usa)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

**Verificar:**
- [ ] APP_ENV=production
- [ ] APP_DEBUG=false
- [ ] APP_URL correcto
- [ ] CENTRAL_DOMAIN correcto
- [ ] Credenciales DB correctas
- [ ] Sin credenciales de desarrollo

### 5. Instalar Dependencias

```bash
cd /var/www/html
composer install --no-dev --optimize-autoloader --no-interaction
php artisan key:generate --force
```

**Verificar:**
- [ ] Dependencias instaladas
- [ ] APP_KEY generado
- [ ] Sin errores de Composer

### 6. Configurar Base de Datos

```bash
# Crear base de datos
mysql -u root -p

CREATE DATABASE pos_central CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel'@'localhost' IDENTIFIED BY 'PASSWORD_SEGURO';
GRANT ALL PRIVILEGES ON *.* TO 'laravel'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EXIT;

# Ejecutar migraciones
cd /var/www/html
php artisan migrate --force
```

**Verificar:**
- [ ] Base de datos creada
- [ ] Usuario creado
- [ ] Permisos asignados
- [ ] Migraciones ejecutadas
- [ ] Tablas creadas

### 7. Configurar Nginx

```bash
# Copiar configuración
sudo cp /var/www/html/conf/nginx/nginx-multitenant-production.conf /etc/nginx/sites-available/laravel-multitenant

# Habilitar sitio
sudo ln -s /etc/nginx/sites-available/laravel-multitenant /etc/nginx/sites-enabled/

# Remover configuración default
sudo rm /etc/nginx/sites-enabled/default

# Probar configuración
sudo nginx -t

# Recargar Nginx
sudo systemctl reload nginx
```

**Verificar:**
- [ ] Configuración copiada
- [ ] Symlink creado
- [ ] nginx -t sin errores
- [ ] Nginx recargado

### 8. Configurar PHP-FPM

```bash
# Editar configuración de pool
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

**Ajustes recomendados:**
```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

```bash
# Reiniciar PHP-FPM
sudo systemctl restart php8.2-fpm
```

**Verificar:**
- [ ] Configuración ajustada
- [ ] PHP-FPM reiniciado
- [ ] Socket corriendo

### 9. Optimizar Aplicación

```bash
cd /var/www/html
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
```

**Verificar:**
- [ ] Cachés generados
- [ ] Sin errores

### 10. Crear Tenant de Prueba

```bash
php artisan tenant:create testempresa test@example.com
php artisan tenants:migrate --tenants=testempresa
```

**Verificar:**
- [ ] Tenant creado
- [ ] Base de datos de tenant creada
- [ ] Dominio registrado en tabla domains

---

## 🔒 Seguridad (Opcional pero Recomendado)

### 1. Configurar SSL con Let's Encrypt

```bash
sudo certbot --nginx -d adminpos.dokploy.movete.cloud -d *.adminpos.dokploy.movete.cloud
```

**Verificar:**
- [ ] Certificados instalados
- [ ] Redirección HTTPS funcionando
- [ ] Renovación automática configurada

### 2. Configurar Firewall

```bash
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw enable
sudo ufw status
```

**Verificar:**
- [ ] Puerto 80 abierto
- [ ] Puerto 443 abierto
- [ ] Puerto 22 abierto (SSH)
- [ ] Otros puertos cerrados

### 3. Hardening de MySQL

```bash
sudo mysql_secure_installation
```

**Completar:**
- [ ] Contraseña root configurada
- [ ] Usuarios anónimos removidos
- [ ] Login root remoto deshabilitado
- [ ] Base de datos test removida

### 4. Configurar Fail2Ban (Opcional)

```bash
sudo apt install fail2ban -y
sudo systemctl start fail2ban
sudo systemctl enable fail2ban
```

---

## ✅ Post-Deployment

### 1. Verificaciones Básicas

**Aplicación:**
- [ ] Dominio central accesible
- [ ] Tenant de prueba accesible
- [ ] Login funcionando
- [ ] Assets cargando correctamente
- [ ] Sin errores 500

**URLs a probar:**
- [ ] `http://adminpos.dokploy.movete.cloud`
- [ ] `http://testempresa.adminpos.dokploy.movete.cloud`
- [ ] `http://www.adminpos.dokploy.movete.cloud` (si aplica)

### 2. Verificaciones de Logs

```bash
# Nginx access
tail -f /var/log/nginx/central-access.log

# Nginx error
tail -f /var/log/nginx/central-error.log

# Laravel
tail -f /var/www/html/storage/logs/laravel.log

# PHP-FPM
tail -f /var/log/php8.2-fpm.log
```

**Verificar:**
- [ ] Sin errores críticos
- [ ] Requests siendo procesados
- [ ] Sin errores 502/503

### 3. Verificaciones de Rendimiento

```bash
# Verificar recursos
htop
df -h
free -m

# Verificar conexiones
netstat -an | grep :80 | wc -l
```

**Verificar:**
- [ ] CPU < 80%
- [ ] Memoria < 80%
- [ ] Disco > 20% libre

### 4. Pruebas Funcionales

- [ ] Crear un tenant nuevo
- [ ] Login en tenant
- [ ] Crear producto
- [ ] Realizar venta
- [ ] Generar reporte
- [ ] Subir imagen
- [ ] Exportar datos

### 5. Configurar Backups

```bash
# Backup de base de datos
crontab -e

# Agregar:
# Backup diario a las 2 AM
0 2 * * * /usr/bin/mysqldump -u laravel -p[PASSWORD] --all-databases > /backups/db-$(date +\%Y\%m\%d).sql
```

**Verificar:**
- [ ] Cronjob configurado
- [ ] Directorio de backups creado
- [ ] Backups funcionando

### 6. Configurar Monitoring (Opcional)

**Opciones:**
- New Relic
- Datadog
- Sentry (errores)
- UptimeRobot (disponibilidad)

**Verificar:**
- [ ] Monitoring instalado
- [ ] Alertas configuradas
- [ ] Dashboard funcional

### 7. Documentar

- [ ] Credenciales guardadas en gestor seguro
- [ ] IP del servidor documentada
- [ ] Accesos SSH documentados
- [ ] Contactos de soporte registrados

---

## 🔄 Actualizaciones Futuras

### Proceso de Actualización

```bash
# Conectar al servidor
ssh usuario@IP_DEL_VPS

# Ejecutar script de deployment
cd /var/www/html
bash scripts/deploy-vps.sh
```

**El script automáticamente:**
1. Actualiza código desde Git
2. Instala dependencias
3. Ejecuta migraciones
4. Optimiza aplicación
5. Reinicia servicios

---

## 🆘 Rollback Plan

### Si algo sale mal:

1. **Restaurar código anterior:**
   ```bash
   cd /var/www/html
   git reset --hard [COMMIT_ANTERIOR]
   ```

2. **Restaurar base de datos:**
   ```bash
   mysql -u laravel -p pos_central < /backups/db-YYYYMMDD.sql
   ```

3. **Limpiar cachés:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

4. **Reiniciar servicios:**
   ```bash
   sudo systemctl restart nginx
   sudo systemctl restart php8.2-fpm
   ```

---

## 📞 Contactos de Emergencia

- **Proveedor VPS:** [CONTACTO]
- **Proveedor DNS:** [CONTACTO]
- **Desarrollador:** [CONTACTO]
- **Soporte:** [EMAIL]

---

## 📊 Métricas de Éxito

**Post-deployment, verificar:**

- [ ] Uptime > 99%
- [ ] Tiempo de respuesta < 500ms
- [ ] Sin errores 500
- [ ] Sin quejas de usuarios
- [ ] Backups funcionando

---

## ✨ Checklist Completado

Si todas las casillas están marcadas, ¡felicidades! Tu aplicación está deployada correctamente.

**Fecha de deployment:** _____________

**Realizado por:** _____________

**Versión desplegada:** _____________

---

¿Problemas? Consulta [COMO_FUNCIONA_NGINX.md](COMO_FUNCIONA_NGINX.md) o [GUIA_RAPIDA_NGINX.md](GUIA_RAPIDA_NGINX.md).


