# Guia de Despliegue Multi-Tenant en VPS/Hosting

Esta guia detallada explica como migrar el sistema multi-tenant de un entorno local (Windows/XAMPP) a un VPS o servidor de hosting.

## Tabla de Contenido

1. [Requisitos del Servidor](#requisitos-del-servidor)
2. [Diferencias Clave: Local vs Produccion](#diferencias-clave-local-vs-produccion)
3. [Preparacion del Servidor](#preparacion-del-servidor)
4. [Configuracion de DNS (Reemplazo del archivo hosts)](#configuracion-de-dns-reemplazo-del-archivo-hosts)
5. [Configuracion del Servidor Web](#configuracion-del-servidor-web)
6. [Configuracion de Base de Datos](#configuracion-de-base-de-datos)
7. [Despliegue del Codigo](#despliegue-del-codigo)
8. [Configuracion de Variables de Entorno](#configuracion-de-variables-de-entorno)
9. [Migracion de Datos y Tenants](#migracion-de-datos-y-tenants)
10. [Configuracion SSL/HTTPS](#configuracion-sslhttps)
11. [Creacion de Nuevos Tenants en Produccion](#creacion-de-nuevos-tenants-en-produccion)
12. [Troubleshooting](#troubleshooting)

---

## Requisitos del Servidor

### Requisitos Minimos

- **RAM:** 2GB minimo (4GB recomendado)
- **Disco:** 20GB SSD minimo
- **CPU:** 2 nucleos minimo
- **Sistema Operativo:** Ubuntu 20.04 LTS o superior (recomendado)
- **Conexion:** Ancho de banda suficiente para trafico esperado

### Software Requerido

- **PHP:** 8.0 o superior
- **Composer:** 2.x
- **Node.js:** 18.x o superior (para compilar assets)
- **NPM:** Incluido con Node.js
- **MySQL/MariaDB:** 8.0 o superior
- **Apache 2.4** o **Nginx** (recomendamos Apache para simplicidad)
- **Git**
- **Certbot** (para SSL gratuito con Let's Encrypt)

---

## Diferencias Clave: Local vs Produccion

### En Local (Windows/XAMPP)

| Aspecto | Configuracion Local |
|---------|-------------------|
| **DNS** | Archivo `hosts` de Windows (`C:\Windows\System32\drivers\etc\hosts`) |
| **Dominio** | `dokploy.movete.cloud` (dominio de prueba) |
| **Subdominios** | Manualmente agregados al archivo hosts |
| **SSL** | No requerido (HTTP) |
| **Base de Datos** | MySQL local en XAMPP |
| **IP** | 127.0.0.1 (localhost) |

### En Produccion (VPS/Hosting)

| Aspecto | Configuracion Produccion |
|---------|------------------------|
| **DNS** | Servidor DNS real (registros A y wildcard) |
| **Dominio** | Tu dominio real (ej: `tudominio.com`) |
| **Subdominios** | Configuracion wildcard DNS (*.tudominio.com) |
| **SSL** | Certificado SSL requerido (Let's Encrypt) |
| **Base de Datos** | MySQL remoto en servidor |
| **IP** | IP publica del servidor |

---

## Preparacion del Servidor

### 1. Actualizar Sistema

```bash
sudo apt update
sudo apt upgrade -y
```

### 2. Instalar PHP y Extensiones

```bash
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml \
    php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath \
    php8.2-intl php8.2-soap php8.2-redis
```

### 3. Instalar Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### 4. Instalar Node.js y NPM

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### 5. Instalar MySQL/MariaDB

```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

### 6. Instalar Apache

```bash
sudo apt install -y apache2
sudo a2enmod rewrite
sudo a2enmod ssl
sudo a2enmod headers
sudo systemctl restart apache2
```

### 7. Instalar Certbot (Para SSL)

```bash
sudo apt install -y certbot python3-certbot-apache
```

---

## Configuracion de DNS (Reemplazo del archivo hosts)

En local, editamos el archivo `hosts` de Windows. En produccion, debemos configurar DNS real.

### Opcion 1: Configuracion Wildcard (RECOMENDADO)

Esta es la forma mas eficiente: un solo registro DNS maneja todos los subdominios.

#### Paso 1: Registrar tu Dominio

Asegurate de tener acceso al panel de DNS de tu proveedor de dominio (GoDaddy, Namecheap, Cloudflare, etc.)

#### Paso 2: Configurar Registros DNS

En el panel de DNS de tu proveedor, agrega estos registros:

**Registro A para dominio principal:**
```
Tipo: A
Nombre: @ (o tu dominio sin www)
Valor: [IP_PUBLICA_DE_TU_SERVIDOR]
TTL: 3600 (o el minimo)
```

**Registro A para www:**
```
Tipo: A
Nombre: www
Valor: [IP_PUBLICA_DE_TU_SERVIDOR]
TTL: 3600
```

**Registro Wildcard (Muy importante):**
```
Tipo: A
Nombre: *
Valor: [IP_PUBLICA_DE_TU_SERVIDOR]
TTL: 3600
```

**Ejemplo visual:**
```
| Tipo | Nombre | Valor                | TTL  |
|------|--------|----------------------|------|
| A    | @      | 192.168.1.100        | 3600 |
| A    | www    | 192.168.1.100        | 3600 |
| A    | *      | 192.168.1.100        | 3600 |
```

#### Paso 3: Verificar Propagacion DNS

```bash
# Verificar dominio principal
dig tudominio.com

# Verificar wildcard
dig cualquiera.tudominio.com

# Verificar desde terminal
nslookup tudominio.com
nslookup test.tudominio.com
```

**Nota:** La propagacion DNS puede tardar entre 5 minutos y 48 horas. Normalmente es menos de 1 hora.

### Opcion 2: Subdominios Individuales (NO RECOMENDADO)

Si no puedes usar wildcard, deberas agregar cada subdominio manualmente:

```
Tipo: A
Nombre: empresa1
Valor: [IP_PUBLICA]

Tipo: A
Nombre: empresa2
Valor: [IP_PUBLICA]

Tipo: A
Nombre: empresa3
Valor: [IP_PUBLICA]
```

**Desventaja:** Debes agregar un registro DNS por cada nuevo tenant.

---

## Configuracion del Servidor Web

### Configuracion Apache para Multi-Tenant

#### 1. Crear Virtual Host Principal

```bash
sudo nano /etc/apache2/sites-available/tudominio.com.conf
```

**Contenido:**

```apache
<VirtualHost *:80>
    ServerName tudominio.com
    ServerAlias www.tudominio.com
    
    DocumentRoot /var/www/tudominio/public
    
    <Directory /var/www/tudominio/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Logs
    ErrorLog ${APACHE_LOG_DIR}/tudominio-error.log
    CustomLog ${APACHE_LOG_DIR}/tudominio-access.log combined
    
    # PHP Handler
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/var/run/php/php8.2-fpm.sock|fcgi://localhost"
    </FilesMatch>
</VirtualHost>

# Virtual Host Wildcard para Tenants
<VirtualHost *:80>
    ServerName tudominio.com
    ServerAlias *.tudominio.com
    
    DocumentRoot /var/www/tudominio/public
    
    <Directory /var/www/tudominio/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Logs
    ErrorLog ${APACHE_LOG_DIR}/tenants-error.log
    CustomLog ${APACHE_LOG_DIR}/tenants-access.log combined
    
    # PHP Handler
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/var/run/php/php8.2-fpm.sock|fcgi://localhost"
    </FilesMatch>
</VirtualHost>
```

#### 2. Habilitar el Sitio

```bash
sudo a2ensite tudominio.com.conf
sudo a2dissite 000-default.conf  # Deshabilitar sitio por defecto
sudo systemctl reload apache2
```

#### 3. Verificar Sintaxis

```bash
sudo apache2ctl configtest
```

Deberia mostrar: `Syntax OK`

#### 4. Configurar Permisos

```bash
# Propietario y permisos del directorio
sudo chown -R www-data:www-data /var/www/tudominio
sudo chmod -R 755 /var/www/tudominio
sudo chmod -R 775 /var/www/tudominio/storage
sudo chmod -R 775 /var/www/tudominio/bootstrap/cache
```

---

## Configuracion de Base de Datos

### 1. Crear Base de Datos Central

```bash
sudo mysql -u root -p
```

En MySQL:

```sql
-- Crear base de datos central
CREATE DATABASE switchs_central CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario
CREATE USER 'switchs_user'@'localhost' IDENTIFIED BY 'TU_PASSWORD_SEGURO';

-- Otorgar permisos
GRANT ALL PRIVILEGES ON switchs_central.* TO 'switchs_user'@'localhost';
GRANT ALL PRIVILEGES ON `tenant%`.* TO 'switchs_user'@'localhost';

-- Aplicar cambios
FLUSH PRIVILEGES;

-- Salir
EXIT;
```

**Nota:** El permiso `tenant%` permite que Laravel cree bases de datos automaticamente para cada tenant.

### 2. Verificar Conexion

```bash
mysql -u switchs_user -p switchs_central
```

Si puedes conectarte, esta todo bien.

---

## Despliegue del Codigo

### Opcion 1: Git Clone (Recomendado)

```bash
cd /var/www
sudo git clone https://github.com/tu-usuario/tu-repositorio.git tudominio
cd tudominio
```

### Opcion 2: Subir Archivos via SFTP

Usa FileZilla o similar para subir todos los archivos a `/var/www/tudominio`

### 3. Instalar Dependencias

```bash
cd /var/www/tudominio

# Dependencias PHP
composer install --optimize-autoloader --no-dev

# Dependencias Node.js
npm install

# Compilar assets
npm run build
```

### 4. Configurar Permisos

```bash
sudo chown -R www-data:www-data /var/www/tudominio
sudo chmod -R 755 /var/www/tudominio
sudo chmod -R 775 storage bootstrap/cache
```

---

## Configuracion de Variables de Entorno

### 1. Crear Archivo .env

```bash
cd /var/www/tudominio
cp .env.example .env
nano .env
```

### 2. Configuracion Basica

```env
APP_NAME="Switchs POS"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://tudominio.com

# Base de Datos Central
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=switchs_central
DB_USERNAME=switchs_user
DB_PASSWORD=TU_PASSWORD_SEGURO

# Tenancy
TENANCY_DATABASE_PREFIX=tenant
```

### 3. Generar Key de Aplicacion

```bash
php artisan key:generate
```

### 4. Optimizar para Produccion

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Migracion de Datos y Tenants

### 1. Ejecutar Migraciones Centrales

```bash
cd /var/www/tudominio
php artisan migrate --database=mysql --force
```

Esto crea las tablas:
- `tenants`
- `domains`
- Otras tablas centrales

### 2. Migrar Tenants desde Local

#### Opcion A: Exportar/Importar SQL

**En local (Windows):**

```bash
# Exportar base de datos central
mysqldump -u root -p switchs_central > central_backup.sql

# Exportar cada base de tenant
mysqldump -u root -p tenantempresa1 > tenant_empresa1.sql
mysqldump -u root -p tenantempresa2 > tenant_empresa2.sql
```

**En produccion (VPS):**

```bash
# Importar base central
mysql -u switchs_user -p switchs_central < central_backup.sql

# Para cada tenant:
# 1. Crear base de datos del tenant
mysql -u switchs_user -p -e "CREATE DATABASE tenantempresa1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Importar datos
mysql -u switchs_user -p tenantempresa1 < tenant_empresa1.sql
```

#### Opcion B: Usar Laravel Tinker (Recomendado para pocos tenants)

**En produccion:**

```bash
php artisan tinker
```

```php
// Crear tenant empresa1
$tenant = App\Models\Tenant::create([
    'id' => 'empresa1',
    'name' => 'Empresa 1',
    'email' => 'admin@empresa1.com',
    'status' => 'active'
]);

$tenant->domains()->create([
    'domain' => 'empresa1.tudominio.com'
]);

// Repetir para cada tenant
```

### 3. Ejecutar Migraciones de Tenants

```bash
# Esto ejecutara las migraciones en TODOS los tenants
php artisan tenants:migrate --force
```

### 4. Ejecutar Seeders (Opcional)

Si necesitas datos iniciales:

```bash
php artisan tenants:seed --class=DatabaseSeeder --tenants=all
```

---

## Configuracion SSL/HTTPS

### 1. Obtener Certificado SSL con Let's Encrypt

```bash
sudo certbot --apache -d tudominio.com -d www.tudominio.com -d *.tudominio.com
```

**Importante:** Certbot agregara automaticamente la configuracion SSL a tus Virtual Hosts.

### 2. Renovacion Automatica

Let's Encrypt expira cada 90 dias. Certbot crea un cron job automaticamente:

```bash
# Verificar cron job
sudo crontab -l | grep certbot
```

Deberia ejecutarse automaticamente.

### 3. Actualizar APP_URL en .env

```env
APP_URL=https://tudominio.com
```

### 4. Forzar HTTPS (Redireccionar HTTP a HTTPS)

Certbot ya deberia haberlo configurado, pero verifica en `/etc/apache2/sites-available/tudominio.com-le-ssl.conf`:

```apache
<VirtualHost *:443>
    # ... configuracion SSL ...
    
    # Redirigir HTTP a HTTPS
    RewriteEngine On
    RewriteCond %{HTTP_HOST} !^www\. [NC]
    RewriteRule ^(.*)$ https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>
```

---

## Creacion de Nuevos Tenants en Produccion

### Metodo 1: Via Interfaz Web (Si esta disponible)

Simplemente accede a `https://tudominio.com/register-tenant` y crea el tenant normalmente.

### Metodo 2: Via Artisan Command

```bash
php artisan tenants:create empresa-nueva "Empresa Nueva" admin@empresa-nueva.com
```

### Metodo 3: Via Tinker

```bash
php artisan tinker
```

```php
$tenant = App\Models\Tenant::create([
    'id' => 'nuevo-tenant',
    'name' => 'Nuevo Tenant',
    'email' => 'admin@nuevotenant.com',
    'status' => 'active'
]);

$tenant->domains()->create([
    'domain' => 'nuevo-tenant.tudominio.com'
]);
```

### Verificar Nuevo Tenant

1. Espera la propagacion DNS (si es nuevo dominio)
2. Accede a: `https://nuevo-tenant.tudominio.com`
3. Deberia cargar correctamente

---

## Checklist de Despliegue

Marca cada item conforme lo completes:

- [ ] Servidor preparado (PHP, MySQL, Apache)
- [ ] DNS configurado (dominio principal + wildcard)
- [ ] Virtual Hosts configurados en Apache
- [ ] Base de datos central creada
- [ ] Codigo desplegado en `/var/www/tudominio`
- [ ] Dependencias instaladas (Composer + NPM)
- [ ] Archivo `.env` configurado
- [ ] `APP_KEY` generado
- [ ] Migraciones centrales ejecutadas
- [ ] Tenants migrados desde local
- [ ] Migraciones de tenants ejecutadas
- [ ] SSL/HTTPS configurado (Let's Encrypt)
- [ ] Permisos de archivos correctos
- [ ] Optimizaciones de produccion aplicadas
- [ ] Tests de acceso funcionando:
  - [ ] Dominio principal: `https://tudominio.com`
  - [ ] Subdominio tenant: `https://empresa1.tudominio.com`
  - [ ] Login funcionando
  - [ ] Creacion de facturas funcionando

---

## Troubleshooting

### Problema: "Domain not found" o "No tenant identified"

**Causa:** DNS no configurado o no propagado

**Solucion:**
```bash
# Verificar DNS desde servidor
dig empresa1.tudominio.com

# Verificar desde terminal local
nslookup empresa1.tudominio.com
```

### Problema: "403 Forbidden" al acceder a tenant

**Causa:** Permisos incorrectos en archivos

**Solucion:**
```bash
sudo chown -R www-data:www-data /var/www/tudominio
sudo chmod -R 755 /var/www/tudominio
sudo chmod -R 775 storage bootstrap/cache
```

### Problema: "Database connection failed"

**Causa:** Credenciales incorrectas o usuario sin permisos

**Solucion:**
```bash
# Verificar usuario MySQL
mysql -u switchs_user -p

# Verificar permisos
SHOW GRANTS FOR 'switchs_user'@'localhost';
```

### Problema: Assets (CSS/JS) no cargan

**Causa:** Assets no compilados o rutas incorrectas

**Solucion:**
```bash
cd /var/www/tudominio
npm run build
php artisan config:clear
php artisan cache:clear
```

### Problema: SSL no funciona en subdominios

**Causa:** Certificado no incluye wildcard

**Solucion:**
```bash
# Obtener certificado con wildcard
sudo certbot --apache -d tudominio.com -d www.tudominio.com -d *.tudominio.com
```

**Nota:** Let's Encrypt requiere validacion DNS para wildcards. Sigue las instrucciones de Certbot.

### Problema: Tenants no crean base de datos

**Causa:** Usuario MySQL sin permisos para crear bases

**Solucion:**
```sql
GRANT CREATE ON *.* TO 'switchs_user'@'localhost';
FLUSH PRIVILEGES;
```

---

## Configuracion Adicional Recomendada

### 1. Firewall (UFW)

```bash
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable
```

### 2. Fail2Ban (Proteccion contra ataques)

```bash
sudo apt install fail2ban
sudo systemctl enable fail2ban
```

### 3. Cron Jobs para Laravel

```bash
sudo crontab -e -u www-data
```

Agregar:
```
* * * * * cd /var/www/tudominio && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Backup Automatico

Crea script de backup:

```bash
sudo nano /usr/local/bin/backup-switchs.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups"

# Backup base central
mysqldump -u switchs_user -pTU_PASSWORD switchs_central > $BACKUP_DIR/central_$DATE.sql

# Backup cada tenant
for db in $(mysql -u switchs_user -pTU_PASSWORD -e "SHOW DATABASES LIKE 'tenant%';" | grep tenant); do
    mysqldump -u switchs_user -pTU_PASSWORD $db > $BACKUP_DIR/${db}_$DATE.sql
done

# Comprimir
tar -czf $BACKUP_DIR/backup_$DATE.tar.gz $BACKUP_DIR/*_$DATE.sql
rm $BACKUP_DIR/*_$DATE.sql

# Mantener solo ultimos 7 dias
find $BACKUP_DIR -name "backup_*.tar.gz" -mtime +7 -delete
```

Hacer ejecutable:
```bash
sudo chmod +x /usr/local/bin/backup-switchs.sh
```

Agregar a crontab (diario a las 2 AM):
```bash
sudo crontab -e
```

```
0 2 * * * /usr/local/bin/backup-switchs.sh
```

---

## Monitoreo y Mantenimiento

### Ver Logs de Apache

```bash
sudo tail -f /var/log/apache2/tudominio-error.log
sudo tail -f /var/log/apache2/tenants-error.log
```

### Ver Logs de Laravel

```bash
tail -f /var/www/tudominio/storage/logs/laravel.log
```

### Verificar Estado de Servicios

```bash
sudo systemctl status apache2
sudo systemctl status mysql
sudo systemctl status php8.2-fpm
```

### Reiniciar Servicios

```bash
sudo systemctl restart apache2
sudo systemctl restart mysql
sudo systemctl restart php8.2-fpm
```

---

## Resumen: Diferencias Clave

| Accion | Local (Windows) | Produccion (VPS) |
|--------|----------------|------------------|
| **Configurar DNS** | Editar `C:\Windows\System32\drivers\etc\hosts` | Configurar registros DNS en proveedor |
| **Dominio** | `dokploy.movete.cloud` | `tudominio.com` |
| **Agregar Tenant** | Agregar a archivo hosts manualmente | Wildcard DNS maneja automaticamente |
| **Base de Datos** | MySQL en XAMPP (`localhost`) | MySQL remoto en servidor |
| **SSL** | No requerido | Let's Encrypt (certbot) |
| **URL** | `http://dokploy.movete.cloud` | `https://tudominio.com` |
| **Ruta del Proyecto** | `C:\Users\...\app-pos-laravel` | `/var/www/tudominio` |

---

## Soporte

Si tienes problemas durante el despliegue:

1. Revisa los logs: `/var/log/apache2/` y `storage/logs/`
2. Verifica permisos de archivos
3. Confirma que DNS esta propagado
4. Asegurate de que firewall permite trafico HTTP/HTTPS
5. Consulta la documentacion oficial de Laravel Tenancy

---

**Ultima actualizacion:** 2025-01-29

