# 🔧 Cómo Funciona Nginx con Multi-Tenancy

Esta guía explica el flujo completo de cómo funciona la aplicación con Nginx desde que escribes una URL en el navegador hasta que Nginx sirve la aplicación.

## 📋 Tabla de Contenidos

- [Resumen del Flujo](#resumen-del-flujo)
- [1. Diferencias entre Apache y Nginx](#1-diferencias-entre-apache-y-nginx)
- [2. El Archivo Hosts de Windows](#2-el-archivo-hosts-de-windows)
- [3. Nginx y Server Blocks](#3-nginx-y-server-blocks)
- [4. Laravel y el Paquete Tenancy](#4-laravel-y-el-paquete-tenancy)
- [5. Flujo Completo Paso a Paso](#5-flujo-completo-paso-a-paso)
- [6. Configuración para Desarrollo Local](#6-configuración-para-desarrollo-local)
- [7. Configuración para Producción](#7-configuración-para-producción)
- [FAQ](#faq)

---

## 🎯 Resumen del Flujo

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  Navegador   │───▶│ Archivo HOSTS│───▶│    Nginx     │───▶│   Laravel    │
│              │    │              │    │ Server Block │    │   Tenancy    │
└──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘
      │                    │                    │                    │
      │  testempresa.      │   127.0.0.1       │   PHP-FPM         │   Detecta
      │  adminpos....      │   (localhost)     │   /public         │   Tenant
      └────────────────────┴───────────────────┴───────────────────┴──────────
```

---

## 1. Diferencias entre Apache y Nginx

### Apache (Anterior)

```apache
# Virtual Hosts en Apache
<VirtualHost *:80>
    ServerName adminpos.dokploy.movete.cloud
    ServerAlias *.adminpos.dokploy.movete.cloud
    DocumentRoot "C:/proyecto/public"
    
    <Directory "C:/proyecto/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Características:**
- Usa archivo `.htaccess` para reescritura de URLs
- `AllowOverride All` permite configuración por directorio
- Más uso de memoria (proceso por conexión)
- Configuración distribuida (`.htaccess`)

### Nginx (Nuevo)

```nginx
# Server Block en Nginx
server {
    listen 80;
    server_name ~^(?<tenant>.+)\.adminpos\.dokploy\.movete\.cloud$;
    
    root /var/www/html/public;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass php:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

**Características:**
- NO usa `.htaccess` (todo en configuración central)
- Expresiones regulares poderosas para server_name
- Menos uso de memoria (event-driven)
- Configuración centralizada
- Mejor rendimiento para archivos estáticos

### Ventajas de Nginx para Multi-Tenant

1. **Mejor rendimiento**: Event-driven architecture
2. **Menos memoria**: No crea proceso por conexión
3. **Wildcard avanzado**: Regex potente para subdominios
4. **Cache integrado**: Mejor manejo de assets estáticos
5. **Producción ready**: Usado por grandes empresas

---

## 2. El Archivo Hosts de Windows

### 📍 Ubicación

```
C:\Windows\System32\drivers\etc\hosts
```

### Configuración para Multi-Tenant con Nginx

```
127.0.0.1       localhost

# ============================================
# Multi-Tenant Laravel - Nginx - adminpos.dokploy.movete.cloud
# ============================================
127.0.0.1       adminpos.dokploy.movete.cloud
127.0.0.1       www.adminpos.dokploy.movete.cloud
127.0.0.1       testempresa.adminpos.dokploy.movete.cloud
127.0.0.1       empresa1.adminpos.dokploy.movete.cloud
127.0.0.1       empresa2.adminpos.dokploy.movete.cloud
# ============================================
```

### ¿Cómo Funciona?

1. **Usuario escribe**: `http://testempresa.adminpos.dokploy.movete.cloud`
2. **Windows busca** en el archivo hosts primero
3. **Encuentra**: `127.0.0.1    testempresa.adminpos.dokploy.movete.cloud`
4. **Traduce**: "testempresa.adminpos.dokploy.movete.cloud = 127.0.0.1"
5. **Navegador envía** petición a localhost (127.0.0.1)

> ⚠️ **IMPORTANTE**: El archivo hosts solo funciona en **desarrollo local**. En producción usas DNS real.

---

## 3. Nginx y Server Blocks

### 📂 Archivos de Configuración

En tu proyecto tienes 3 archivos de configuración Nginx:

1. **nginx-multitenant-local.conf** - Para desarrollo en Windows
2. **nginx-multitenant-docker.conf** - Para Docker local
3. **nginx-multitenant-production.conf** - Para servidor VPS

### ¿Qué son los Server Blocks?

Los **Server Blocks** en Nginx son equivalentes a los **Virtual Hosts** de Apache. Permiten manejar **múltiples sitios** en un solo servidor.

### Configuración para Multi-Tenancy

Tu proyecto usa **DOS Server Blocks**:

#### Server Block 1: Dominio Central (sin subdominios)

```nginx
server {
    listen 80;
    server_name adminpos.dokploy.movete.cloud;
    
    root /var/www/html/public;
    index index.php index.html;
    
    # ... configuración de Laravel
}
```

**Qué hace:**
- **server_name**: Responde exactamente a `adminpos.dokploy.movete.cloud`
- **root**: Apunta a la carpeta `public` de Laravel
- **Sin wildcard**: Solo dominio principal

#### Server Block 2: Wildcard para TODOS los Subdominios (Tenants)

```nginx
server {
    listen 80;
    # Wildcard con regex: captura cualquier subdominio
    server_name ~^(?<tenant>.+)\.adminpos\.dokploy\.movete\.cloud$;
    
    root /var/www/html/public;
    index index.php index.html;
    
    # Pasar el subdominio a PHP
    fastcgi_param TENANT_SUBDOMAIN $tenant;
    
    # ... configuración de Laravel
}
```

**Qué hace:**
- **server_name con regex**: `~^(?<tenant>.+)\.` captura CUALQUIER subdominio
  - `testempresa.adminpos.dokploy.movete.cloud` ✅
  - `empresa1.adminpos.dokploy.movete.cloud` ✅
  - `cualquiernombre.adminpos.dokploy.movete.cloud` ✅
- **Captura variable**: `(?<tenant>.+)` guarda el subdominio en la variable `$tenant`
- **Mismo root**: Todos apuntan a la misma carpeta `public`
- Nginx NO diferencia tenants, Laravel lo hace

### Sintaxis de Server Name con Regex

```nginx
# Sintaxis básica
server_name ~^(?<nombre_variable>.+)\.dominio\.com$;

# Desglose:
# ~^           - Inicio de regex (^ = empieza con)
# (?<tenant>   - Captura grupo nombrado "tenant"
# .+           - Uno o más caracteres (el subdominio)
# )            - Fin del grupo de captura
# \.           - Punto literal (escapado)
# adminpos\.   - Parte fija del dominio
# dokploy\.    - Parte fija del dominio
# movete\.     - Parte fija del dominio
# cloud        - TLD
# $            - Fin de la cadena
```

### Ejemplo: Excluir el Dominio Central

Si quieres que el wildcard NO capture el dominio central:

```nginx
server {
    listen 80;
    # Negative lookahead: excluye "adminpos" como subdominio
    server_name ~^(?!adminpos$)(?<tenant>.+)\.dokploy\.movete\.cloud$;
    
    # ... resto de configuración
}
```

### ¿Cómo Nginx Decide Qué Server Block Usar?

Cuando Nginx recibe una petición HTTP:

1. **Lee el header `Host`** de la petición:
   ```
   GET / HTTP/1.1
   Host: testempresa.adminpos.dokploy.movete.cloud
   ```

2. **Compara con los Server Blocks** en orden:
   - Primero busca coincidencia exacta en `server_name`
   - Si no encuentra, busca en wildcards/regex
   - El regex `~^(?<tenant>.+)\.` funciona como comodín

3. **Selecciona el Server Block** que coincida

4. **Sirve los archivos** desde el `root` especificado

5. **Pasa a PHP-FPM** si es un archivo `.php`

### Logs Separados

```nginx
# Dominio central
server {
    access_log /var/log/nginx/central-access.log;
    error_log /var/log/nginx/central-error.log;
}

# Tenants
server {
    access_log /var/log/nginx/tenants-access.log;
    error_log /var/log/nginx/tenants-error.log;
}
```

Esto facilita el debugging. Si un tenant tiene problemas, revisas `tenants-error.log`.

---

## 4. Laravel y el Paquete Tenancy

### Una Vez que Nginx Sirve Laravel

Todos los subdominios apuntan al **mismo código** (mismo `root`). ¿Cómo Laravel sabe qué tenant es?

### El Paquete Stancl/Tenancy

#### Paso 1: Middleware de Identificación

```php
// routes/tenant.php
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    // Rutas de tenants
});
```

El middleware lee el **dominio** de la petición:
```php
$domain = request()->getHost(); // testempresa.adminpos.dokploy.movete.cloud
```

#### Paso 2: Buscar el Tenant en la Base de Datos

```php
// Tabla: domains
// +----+-------------------------------------------+-----------+
// | id | domain                                    | tenant_id |
// +----+-------------------------------------------+-----------+
// | 1  | testempresa.adminpos.dokploy.movete.cloud | test001   |
// | 2  | empresa1.adminpos.dokploy.movete.cloud    | emp001    |
// +----+-------------------------------------------+-----------+
```

Laravel busca en la tabla `domains`:
```php
$domain = Domain::where('domain', 'testempresa.adminpos.dokploy.movete.cloud')->first();
$tenant = $domain->tenant;
```

#### Paso 3: Cambiar la Conexión de Base de Datos

```php
// BD central: pos_central
// BDs de tenants:
//   - tenanttest001
//   - tenantemp001

// Laravel cambia automáticamente:
config(['database.connections.tenant.database' => 'tenanttest001']);
DB::setDefaultConnection('tenant');
```

#### Paso 4: Servir la Aplicación

Ahora todas las consultas usan la BD del tenant:

```php
$products = Product::all(); // Consulta desde tenanttest001
```

---

## 5. Flujo Completo Paso a Paso

### Escenario: Usuario visita `http://testempresa.adminpos.dokploy.movete.cloud/productos`

#### 1️⃣ Usuario escribe en el navegador
```
http://testempresa.adminpos.dokploy.movete.cloud/productos
```

#### 2️⃣ Sistema Operativo consulta archivo hosts

```
Windows busca en: C:\Windows\System32\drivers\etc\hosts

Encuentra:
127.0.0.1    testempresa.adminpos.dokploy.movete.cloud

Traduce: testempresa.adminpos.dokploy.movete.cloud = 127.0.0.1
```

#### 3️⃣ Navegador envía petición HTTP a localhost

```http
GET /productos HTTP/1.1
Host: testempresa.adminpos.dokploy.movete.cloud
User-Agent: Mozilla/5.0 ...
```

#### 4️⃣ Nginx recibe la petición en puerto 80

```
Nginx lee el header: Host: testempresa.adminpos.dokploy.movete.cloud
```

#### 5️⃣ Nginx busca Server Block que coincida

```nginx
# Compara con Server Blocks:
# 1. adminpos.dokploy.movete.cloud → NO coincide
# 2. ~^(?<tenant>.+)\.adminpos\.dokploy\.movete\.cloud$ → SÍ COINCIDE ✅

# Captura variable: $tenant = "testempresa"
# Usa root: /var/www/html/public
```

#### 6️⃣ Nginx procesa la petición

```nginx
location / {
    # Intenta servir archivo estático primero
    try_files $uri $uri/ /index.php?$query_string;
    # /productos no existe como archivo → pasa a index.php
}
```

#### 7️⃣ Nginx pasa a PHP-FPM

```nginx
location ~ \.php$ {
    fastcgi_pass php:9000;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    # Ejecuta: /var/www/html/public/index.php
}
```

#### 8️⃣ Laravel inicia y carga middleware

```php
$host = request()->getHost(); // testempresa.adminpos.dokploy.movete.cloud
```

#### 9️⃣ Middleware de Tenancy identifica el tenant

```php
$domain = Domain::where('domain', 'testempresa.adminpos.dokploy.movete.cloud')->first();
$tenant = $domain->tenant; // Tenant ID: test001
```

#### 🔟 Laravel cambia a la BD del tenant

```php
// Cambia de: pos_central
// A: tenanttest001
config(['database.connections.tenant.database' => 'tenanttest001']);
```

#### 1️⃣1️⃣ Laravel procesa la ruta

```php
// routes/tenant.php
Route::get('/productos', [ProductController::class, 'index']);

// Consulta desde tenanttest001
$productos = Product::all();
```

#### 1️⃣2️⃣ Laravel genera HTML

```html
<!DOCTYPE html>
<html>
<head>
    <link href="http://adminpos.dokploy.movete.cloud/build/app.css">
</head>
<body>
    <h1>Productos</h1>
    <!-- Lista de productos del tenant -->
</body>
</html>
```

#### 1️⃣3️⃣ Nginx envía respuesta al navegador

```http
HTTP/1.1 200 OK
Content-Type: text/html

<!DOCTYPE html>...
```

#### 1️⃣4️⃣ Navegador renderiza y carga assets

Assets se cargan desde el **dominio central** gracias a la configuración.

---

## 6. Configuración para Desarrollo Local

### Opción 1: Docker (Recomendado)

#### Paso 1: Iniciar contenedores

```powershell
docker-compose -f docker-compose.nginx.yml up -d
```

Esto inicia:
- **nginx**: Servidor web (puerto 80)
- **php**: PHP-FPM 8.2
- **mysql**: Base de datos
- **phpmyadmin**: Gestión de BD (puerto 8080)
- **redis**: Cache (opcional)

#### Paso 2: Verificar contenedores

```powershell
docker-compose -f docker-compose.nginx.yml ps
```

#### Paso 3: Ejecutar migraciones

```powershell
docker-compose -f docker-compose.nginx.yml exec php php artisan migrate
```

#### Paso 4: Crear un tenant

```powershell
docker-compose -f docker-compose.nginx.yml exec php php artisan tenant:create testempresa test@example.com
```

#### Paso 5: Acceder

- Dominio central: `http://adminpos.dokploy.movete.cloud`
- Tenant: `http://testempresa.adminpos.dokploy.movete.cloud`
- PhpMyAdmin: `http://localhost:8080`

### Opción 2: Nginx Nativo en Windows

#### Paso 1: Descargar Nginx

```
https://nginx.org/en/download.html
```

Descarga la versión Windows (nginx/Windows-X.X.X)

#### Paso 2: Extraer Nginx

```
C:\nginx\
```

#### Paso 3: Copiar configuración

Copia `conf/nginx/nginx-multitenant-local.conf` y ajusta:

1. Reemplaza rutas de proyecto
2. Ajusta `fastcgi_pass` a tu instalación PHP-FPM

#### Paso 4: Iniciar PHP-FPM

```powershell
# Si usas XAMPP
C:\xampp\php\php-cgi.exe -b 127.0.0.1:9000
```

#### Paso 5: Iniciar Nginx

```powershell
cd C:\nginx
nginx.exe
```

#### Paso 6: Verificar

```powershell
# Ver procesos
tasklist | findstr nginx

# Probar configuración
nginx -t

# Recargar configuración
nginx -s reload
```

---

## 7. Configuración para Producción

### Arquitectura en VPS

```
Internet → DNS Wildcard → VPS → Nginx → PHP-FPM → MySQL
                                    ↓
                                 Laravel
                                    ↓
                            Base de Datos Tenants
```

### Paso 1: Configurar DNS Wildcard

En tu proveedor de dominio (ej: Namecheap, GoDaddy):

```
Tipo    Nombre              Valor
A       @                   IP_DEL_VPS
A       adminpos            IP_DEL_VPS
A       *.adminpos          IP_DEL_VPS (wildcard)
CNAME   www.adminpos        adminpos.dokploy.movete.cloud
```

Esto hace que TODOS los subdominios apunten al VPS:
- `adminpos.dokploy.movete.cloud` → VPS
- `testempresa.adminpos.dokploy.movete.cloud` → VPS
- `cualquiernombre.adminpos.dokploy.movete.cloud` → VPS

### Paso 2: Instalar stack en VPS

```bash
# Conectar al VPS
ssh usuario@IP_DEL_VPS

# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Nginx
sudo apt install nginx -y

# Instalar PHP 8.2
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd -y

# Instalar MySQL
sudo apt install mysql-server -y

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Paso 3: Clonar proyecto

```bash
cd /var/www
sudo git clone https://github.com/tu-usuario/tu-repo.git html
cd html
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 775 storage bootstrap/cache
```

### Paso 4: Configurar Nginx

```bash
# Copiar configuración
sudo cp conf/nginx/nginx-multitenant-production.conf /etc/nginx/sites-available/laravel-multitenant

# Crear enlace simbólico
sudo ln -s /etc/nginx/sites-available/laravel-multitenant /etc/nginx/sites-enabled/

# Remover configuración por defecto
sudo rm /etc/nginx/sites-enabled/default

# Probar configuración
sudo nginx -t

# Reiniciar Nginx
sudo systemctl restart nginx
```

### Paso 5: Configurar .env

```bash
cd /var/www/html
cp .env.example .env
nano .env
```

Configurar:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://adminpos.dokploy.movete.cloud

CENTRAL_DOMAIN=adminpos.dokploy.movete.cloud

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_central
DB_USERNAME=root
DB_PASSWORD=TU_PASSWORD_SEGURO
```

### Paso 6: Instalar dependencias

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Paso 7: Configurar base de datos

```bash
# Conectar a MySQL
sudo mysql

# Crear base de datos
CREATE DATABASE pos_central;
CREATE USER 'laravel'@'localhost' IDENTIFIED BY 'PASSWORD_SEGURO';
GRANT ALL PRIVILEGES ON *.* TO 'laravel'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Ejecutar migraciones
php artisan migrate --force
```

### Paso 8: Configurar SSL (Opcional pero recomendado)

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtener certificado wildcard
sudo certbot --nginx -d adminpos.dokploy.movete.cloud -d *.adminpos.dokploy.movete.cloud

# Auto-renovación
sudo systemctl enable certbot.timer
```

### Paso 9: Configurar firewall

```bash
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw enable
```

### Paso 10: Optimizaciones

```bash
# Configurar PHP-FPM
sudo nano /etc/php/8.2/fpm/pool.d/www.conf

# Ajustar:
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20

# Reiniciar PHP-FPM
sudo systemctl restart php8.2-fpm
```

---

## FAQ

### ¿Por qué usar Nginx en lugar de Apache?

**Ventajas:**
- Mejor rendimiento (hasta 4x más rápido para contenido estático)
- Menos uso de memoria
- Event-driven (no bloqueante)
- Mejor para alta concurrencia
- Más popular en producción moderna

**Desventajas:**
- No usa `.htaccess` (todo en config central)
- Curva de aprendizaje inicial
- Menos módulos que Apache

### ¿Puedo usar ambos Apache y Nginx?

Sí, es posible:
- Nginx como reverse proxy (puerto 80)
- Apache como backend (puerto 8080)

Pero es más complejo y no recomendado para este proyecto.

### ¿Cómo funciona el wildcard en producción sin archivo hosts?

En producción:
1. Configuras **DNS wildcard** en tu proveedor
2. El DNS de internet resuelve automáticamente todos los subdominios
3. No necesitas archivo hosts

### ¿El wildcard tiene límites?

No, puedes tener infinitos subdominios:
- `empresa1.adminpos.dokploy.movete.cloud`
- `empresa2.adminpos.dokploy.movete.cloud`
- `empresa9999.adminpos.dokploy.movete.cloud`

Todos son capturados por el regex.

### ¿Qué es PHP-FPM?

**PHP-FPM** (FastCGI Process Manager):
- Implementación alternativa de PHP FastCGI
- Mejor rendimiento que mod_php
- Usado por Nginx (Nginx no puede ejecutar PHP directamente)
- Pool de procesos para manejar múltiples peticiones

### ¿Cómo debugging logs de Nginx?

```bash
# Logs de acceso
tail -f /var/log/nginx/tenants-access.log

# Logs de errores
tail -f /var/log/nginx/tenants-error.log

# Logs de Laravel
tail -f storage/logs/laravel.log

# Logs de PHP-FPM
tail -f /var/log/php8.2-fpm.log
```

### ¿Cómo hacer reload de Nginx sin downtime?

```bash
# Probar configuración
sudo nginx -t

# Reload suave (sin interrumpir conexiones activas)
sudo nginx -s reload

# O con systemctl
sudo systemctl reload nginx
```

### ¿Cómo optimizar Nginx para producción?

```nginx
# nginx.conf
worker_processes auto;
worker_connections 1024;

http {
    # Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript;
    
    # Cache
    open_file_cache max=1000 inactive=20s;
    open_file_cache_valid 30s;
    
    # Buffers
    client_body_buffer_size 128k;
    client_max_body_size 50m;
    
    # Timeouts
    keepalive_timeout 65;
    send_timeout 300;
}
```

### ¿Cómo migrar de Apache a Nginx?

1. Instala Nginx en paralelo (puerto diferente)
2. Prueba la configuración
3. Actualiza DNS para apuntar al puerto de Nginx
4. Detén Apache una vez que todo funcione
5. Mueve Nginx al puerto 80

---

## 🆘 Problemas Comunes

### Error: "502 Bad Gateway"

**Causa**: PHP-FPM no está corriendo o Nginx no puede conectar.

**Solución**:
```bash
# Verificar PHP-FPM
sudo systemctl status php8.2-fpm

# Reiniciar PHP-FPM
sudo systemctl restart php8.2-fpm

# Verificar socket
ls -la /var/run/php/php8.2-fpm.sock
```

### Error: "403 Forbidden"

**Causa**: Permisos incorrectos.

**Solución**:
```bash
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 775 storage bootstrap/cache
```

### Error: "No se puede acceder al sitio"

**Causa**: Dominio no está en archivo hosts (local) o DNS no configurado (producción).

**Solución Local**:
```
1. Edita C:\Windows\System32\drivers\etc\hosts como administrador
2. Agrega: 127.0.0.1  tu-dominio.dokploy.movete.cloud
3. Guarda el archivo
4. Limpia DNS: ipconfig /flushdns
```

**Solución Producción**:
```
Verifica DNS wildcard en tu proveedor
```

### Assets (CSS/JS) no cargan

**Causa**: CORS o assets no están en la ruta correcta.

**Solución**:
```bash
# Verificar que existan
ls -la public/build/

# Compilar assets
npm run build

# Verificar permisos
sudo chmod -R 755 public/build
```

---

## 📚 Recursos Adicionales

- [Documentación Oficial Nginx](https://nginx.org/en/docs/)
- [Nginx Regex Tester](https://nginx.viraptor.info/)
- [Laravel Tenancy](https://tenancyforlaravel.com/)
- [PHP-FPM Configuration](https://www.php.net/manual/en/install.fpm.php)

---

¿Necesitas más información? Consulta la [documentación completa](../README.md) del proyecto.


