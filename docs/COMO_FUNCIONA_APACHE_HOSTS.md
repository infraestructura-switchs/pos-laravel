# 🔧 Cómo Funciona Apache con el Archivo Hosts y Multi-Tenancy

Esta guía explica el flujo completo de cómo funciona la aplicación desde que escribes una URL en el navegador hasta que Apache sirve la aplicación.

## 📋 Tabla de Contenidos

- [Resumen del Flujo](#resumen-del-flujo)
- [1. El Archivo Hosts de Windows](#1-el-archivo-hosts-de-windows)
- [2. Apache y Virtual Hosts](#2-apache-y-virtual-hosts)
- [3. Laravel y el Paquete Tenancy](#3-laravel-y-el-paquete-tenancy)
- [4. Flujo Completo Paso a Paso](#4-flujo-completo-paso-a-paso)
- [5. Configuración Actual del Proyecto](#5-configuración-actual-del-proyecto)
- [6. Cómo Adaptar a Tu Nuevo Dominio](#6-cómo-adaptar-a-tu-nuevo-dominio)
- [FAQ](#faq)

---

## 🎯 Resumen del Flujo

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  Navegador   │───▶│ Archivo HOSTS│───▶│    Apache    │───▶│   Laravel    │
│              │    │              │    │ VirtualHosts │    │   Tenancy    │
└──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘
      │                    │                    │                    │
      │  empresa1.         │   127.0.0.1       │   DocumentRoot    │   Detecta
      │  dominio.com       │   (localhost)     │   /public         │   Tenant
      └────────────────────┴───────────────────┴───────────────────┴──────────
```

---

## 1. El Archivo Hosts de Windows

### 📍 Ubicación

```
C:\Windows\System32\drivers\etc\hosts
```

### ¿Qué es y para qué sirve?

El archivo `hosts` es un archivo de configuración del sistema operativo que **mapea nombres de dominio a direcciones IP** antes de consultar un servidor DNS.

**Es como una libreta de contactos local para tu computadora.**

### Contenido Típico para Multi-Tenancy

```
127.0.0.1       localhost

# ============================================
# Multi-Tenant Laravel - dokploy.movete.cloud
# ============================================
127.0.0.1       dokploy.movete.cloud
127.0.0.1       www.dokploy.movete.cloud
127.0.0.1       empresa1.dokploy.movete.cloud
127.0.0.1       empresa2.dokploy.movete.cloud
127.0.0.1       empresa3.dokploy.movete.cloud
# ============================================
```

### ¿Cómo Funciona?

1. **Escribes en el navegador**: `http://empresa1.dokploy.movete.cloud`
2. **Windows busca en el archivo hosts** primero (antes de ir a internet)
3. **Encuentra la entrada**: `127.0.0.1    empresa1.dokploy.movete.cloud`
4. **Traduce**: "empresa1.dokploy.movete.cloud = 127.0.0.1 (mi computadora)"
5. **El navegador envía la petición a localhost** (tu computadora)

> ⚠️ **IMPORTANTE**: El archivo hosts solo funciona en **desarrollo local**. En producción (servidor real), usarías DNS real.

### Por Qué es Necesario

Sin el archivo hosts, cuando escribes `empresa1.dokploy.movete.cloud`:
- Windows intentaría buscar ese dominio en internet
- No lo encontraría (no existe en DNS público)
- Obtendrías un error "No se puede acceder al sitio"

**Con el archivo hosts:**
- Windows sabe que ese dominio está en tu computadora
- Redirige la petición a `127.0.0.1` (localhost)
- Apache recibe la petición y la procesa

---

## 2. Apache y Virtual Hosts

### 📂 Archivos de Configuración

#### A. Archivo Principal: `httpd.conf`

**Ubicación XAMPP:**
```
C:\xampp\apache\conf\httpd.conf
```

**Línea Importante que Debe Estar Descomentada:**
```apache
Include conf/extra/httpd-vhosts.conf
```

Esta línea le dice a Apache que cargue la configuración de Virtual Hosts.

#### B. Archivo de Virtual Hosts: `httpd-vhosts.conf`

**Ubicación XAMPP:**
```
C:\xampp\apache\conf\extra\httpd-vhosts.conf
```

### ¿Qué son los Virtual Hosts?

Los **Virtual Hosts** permiten que Apache maneje **múltiples sitios web** en un solo servidor usando el **mismo puerto (80)**.

Apache decide qué sitio servir basándose en el **nombre del dominio** (ServerName/ServerAlias) que aparece en la petición HTTP.

### Configuración para Multi-Tenancy

Tu proyecto usa DOS Virtual Hosts:

#### Virtual Host 1: Dominio Central (sin subdominios)

```apache
<VirtualHost *:80>
    ServerName dokploy.movete.cloud
    ServerAlias www.dokploy.movete.cloud
    DocumentRoot "C:/ruta/proyecto/public"
    
    <Directory "C:/ruta/proyecto/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/dokploy.movete.cloud-error.log"
    CustomLog "logs/dokploy.movete.cloud-access.log" common
</VirtualHost>
```

**Qué hace:**
- **ServerName**: Responde a `dokploy.movete.cloud`
- **ServerAlias**: También responde a `www.dokploy.movete.cloud`
- **DocumentRoot**: Apunta a la carpeta `public` de tu proyecto Laravel
- **AllowOverride All**: Permite usar el `.htaccess` de Laravel (importante para las rutas bonitas)

#### Virtual Host 2: Wildcard para TODOS los Subdominios (Tenants)

```apache
<VirtualHost *:80>
    ServerName dokploy.movete.cloud
    ServerAlias *.dokploy.movete.cloud
    DocumentRoot "C:/ruta/proyecto/public"
    
    <Directory "C:/ruta/proyecto/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/dokploy-tenants-error.log"
    CustomLog "logs/dokploy-tenants-access.log" common
</VirtualHost>
```

**Qué hace:**
- **ServerAlias con wildcard**: `*.dokploy.movete.cloud` captura TODOS los subdominios
  - `empresa1.dokploy.movete.cloud` ✅
  - `empresa2.dokploy.movete.cloud` ✅
  - `cualquiernombre.dokploy.movete.cloud` ✅
- **DocumentRoot**: Todos apuntan a la **misma carpeta** `public`
- Apache NO diferencia entre tenants, simplemente sirve la misma aplicación

### ¿Cómo Apache Decide Qué Virtual Host Usar?

Cuando Apache recibe una petición HTTP:

1. **Lee el header `Host`** de la petición HTTP
   ```
   GET / HTTP/1.1
   Host: empresa1.dokploy.movete.cloud
   ```

2. **Compara con los Virtual Hosts** en orden:
   - Primero busca coincidencia exacta en `ServerName`
   - Si no encuentra, busca en `ServerAlias`
   - El `*` en `*.dokploy.movete.cloud` funciona como comodín

3. **Selecciona el Virtual Host** que coincida

4. **Sirve los archivos** desde el `DocumentRoot` especificado

### Ejemplo Práctico

**Petición 1:** `http://dokploy.movete.cloud`
- Coincide con Virtual Host 1 (ServerName exacto)
- Logs van a: `dokploy.movete.cloud-error.log`

**Petición 2:** `http://empresa1.dokploy.movete.cloud`
- Coincide con Virtual Host 2 (wildcard `*`)
- Logs van a: `dokploy-tenants-error.log`

**Petición 3:** `http://www.dokploy.movete.cloud`
- Coincide con Virtual Host 1 (ServerAlias)
- Logs van a: `dokploy.movete.cloud-error.log`

---

## 3. Laravel y el Paquete Tenancy

### Una Vez que Apache Sirve Laravel

Todos los subdominios apuntan al **mismo código** (mismo `DocumentRoot`). Entonces, ¿cómo Laravel sabe qué tenant es?

### El Paquete Stancl/Tenancy

Laravel usa el paquete **stancl/tenancy** que hace lo siguiente:

#### Paso 1: Middleware de Identificación

```php
// routes/tenant.php (rutas de tenants)
// Estas rutas tienen middleware que identifica el tenant
```

El middleware lee el **dominio** de la petición HTTP:
```php
$domain = request()->getHost(); // empresa1.dokploy.movete.cloud
```

#### Paso 2: Buscar el Tenant en la Base de Datos

```php
// Tabla: domains
// +----+----------------------------------+-----------+
// | id | domain                           | tenant_id |
// +----+----------------------------------+-----------+
// | 1  | empresa1.dokploy.movete.cloud    | empresa1  |
// | 2  | empresa2.dokploy.movete.cloud    | empresa2  |
// +----+----------------------------------+-----------+
```

Laravel busca en la tabla `domains`:
```php
$domain = Domain::where('domain', 'empresa1.dokploy.movete.cloud')->first();
$tenant = $domain->tenant; // Obtiene el tenant asociado
```

#### Paso 3: Cambiar la Conexión de Base de Datos

Una vez identificado el tenant, Laravel **cambia dinámicamente** la conexión de base de datos:

```php
// Base de datos central: pos_central
// Bases de datos de tenants:
//   - tenantempresa1
//   - tenantempresa2
//   - tenantempresa3

// Laravel hace automáticamente:
config(['database.connections.tenant.database' => 'tenantempresa1']);
DB::setDefaultConnection('tenant');
```

#### Paso 4: Servir la Aplicación con los Datos del Tenant

Ahora todas las consultas usan la base de datos del tenant:

```php
// Esta consulta va a la base de datos: tenantempresa1
$products = Product::all();
```

### Configuración de Dominios Centrales

En `config/tenancy.php`:

```php
'central_domains' => [
    '127.0.0.1',
    'localhost',
    env('CENTRAL_DOMAIN', 'dokploy.movete.cloud'),
    'www.' . env('CENTRAL_DOMAIN', 'dokploy.movete.cloud'),
],
```

**¿Para qué sirve?**

Laravel necesita saber qué dominios **NO son tenants** (son la aplicación central). Si visitas:
- `dokploy.movete.cloud` → Aplicación central (admin, registro de tenants)
- `empresa1.dokploy.movete.cloud` → Aplicación del tenant

---

## 4. Flujo Completo Paso a Paso

Vamos a ver todo el flujo cuando un usuario visita `http://empresa1.dokploy.movete.cloud/productos`

### 1️⃣ Usuario escribe en el navegador
```
http://empresa1.dokploy.movete.cloud/productos
```

### 2️⃣ Sistema Operativo consulta el archivo hosts

```
Windows busca en: C:\Windows\System32\drivers\etc\hosts

Encuentra:
127.0.0.1    empresa1.dokploy.movete.cloud

Traduce: empresa1.dokploy.movete.cloud = 127.0.0.1
```

### 3️⃣ Navegador envía petición HTTP a localhost

```http
GET /productos HTTP/1.1
Host: empresa1.dokploy.movete.cloud
User-Agent: Mozilla/5.0 ...
```

### 4️⃣ Apache recibe la petición en puerto 80

```
Apache lee el header: Host: empresa1.dokploy.movete.cloud
```

### 5️⃣ Apache busca Virtual Host que coincida

```apache
# Compara con Virtual Hosts:
# 1. dokploy.movete.cloud → NO coincide
# 2. *.dokploy.movete.cloud → SÍ COINCIDE ✅

# Usa DocumentRoot: C:/proyecto/public
```

### 6️⃣ Apache ejecuta el index.php de Laravel

```
C:/proyecto/public/index.php
```

### 7️⃣ Laravel inicia y carga middleware

```php
// Laravel lee el dominio de la petición
$host = 'empresa1.dokploy.movete.cloud';
```

### 8️⃣ Middleware de Tenancy identifica el tenant

```php
// Busca en la tabla domains
$domain = Domain::where('domain', 'empresa1.dokploy.movete.cloud')->first();
$tenant = $domain->tenant; // Tenant ID: empresa1
```

### 9️⃣ Laravel cambia a la base de datos del tenant

```php
// Cambia de: pos_central
// A: tenantempresa1
config(['database.connections.tenant.database' => 'tenantempresa1']);
```

### 🔟 Laravel procesa la ruta y ejecuta el controlador

```php
// routes/tenant.php
Route::get('/productos', [ProductController::class, 'index']);

// Consulta productos de la base de datos: tenantempresa1
$productos = Product::all();
```

### 1️⃣1️⃣ Laravel genera la respuesta HTML

```html
<!DOCTYPE html>
<html>
<head>
    <title>Productos - Empresa 1</title>
    <link href="http://dokploy.movete.cloud/build/assets/app.css" rel="stylesheet">
</head>
<body>
    <!-- Lista de productos del tenant empresa1 -->
</body>
</html>
```

### 1️⃣2️⃣ Apache envía la respuesta al navegador

```http
HTTP/1.1 200 OK
Content-Type: text/html
...

<!DOCTYPE html>...
```

### 1️⃣3️⃣ Navegador renderiza la página

El navegador muestra la página de productos de **Empresa 1**.

### 1️⃣4️⃣ Navegador carga assets (CSS/JS)

```html
<link href="http://dokploy.movete.cloud/build/assets/app.css" rel="stylesheet">
```

**Nota importante**: Los assets se cargan desde el **dominio central** gracias a los middlewares:
- `FixViteAssetsForTenants`
- `AppServiceProvider` (configuración de asset_url)

---

## 5. Configuración Actual del Proyecto

### Script de Configuración Automática

Tu proyecto incluye `setup_multitenant.ps1` que hace TODO esto automáticamente:

```powershell
# Ejecutar como Administrador
.\setup_multitenant.ps1
```

**Qué hace el script:**

1. ✅ Instala el paquete Tenancy
2. ✅ Publica archivos de configuración
3. ✅ Ejecuta migraciones
4. ✅ Verifica que `httpd.conf` tenga `Include conf/extra/httpd-vhosts.conf`
5. ✅ Crea/actualiza `httpd-vhosts.conf` con los dos Virtual Hosts
6. ✅ Agrega entradas al archivo `hosts`
7. ✅ Limpia caché DNS (`ipconfig /flushdns`)
8. ✅ Verifica sintaxis de Apache
9. ✅ Reinicia Apache (opcional)

### Estructura de Rutas en Laravel

```php
// routes/web.php (dominio central)
Route::get('/login', ...);
Route::get('/register-tenant', ...);
Route::get('/admin', ...);

// routes/tenant.php (subdominios de tenants)
Route::get('/dashboard', ...);
Route::get('/productos', ...);
Route::get('/ventas', ...);
```

Laravel automáticamente **separa** qué rutas son para el dominio central y cuáles para tenants.

---

## 6. Cómo Adaptar a Tu Nuevo Dominio

Ahora con el sistema de **variable de entorno centralizada**, es muy fácil:

### Paso 1: Editar `.env`

```env
CENTRAL_DOMAIN=tudominio.com
APP_URL=http://tudominio.com
CENTRAL_DOMAINS=tudominio.com,www.tudominio.com
```

### Paso 2: Editar archivo hosts

```
C:\Windows\System32\drivers\etc\hosts
```

```
127.0.0.1       tudominio.com
127.0.0.1       www.tudominio.com
127.0.0.1       empresa1.tudominio.com
127.0.0.1       empresa2.tudominio.com
```

### Paso 3: Editar `httpd-vhosts.conf`

```apache
<VirtualHost *:80>
    ServerName tudominio.com
    ServerAlias www.tudominio.com
    DocumentRoot "C:/ruta/proyecto/public"
    
    <Directory "C:/ruta/proyecto/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName tudominio.com
    ServerAlias *.tudominio.com
    DocumentRoot "C:/ruta/proyecto/public"
    
    <Directory "C:/ruta/proyecto/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Paso 4: Reiniciar Apache

```powershell
# Desde panel XAMPP o:
net stop Apache2.4
net start Apache2.4
```

### Paso 5: Limpiar cachés de Laravel

```bash
php artisan config:clear
php artisan cache:clear
ipconfig /flushdns
```

### Paso 6: Actualizar dominios de tenants existentes

```bash
php fix_tenants_domains.php
```

Este script actualizará todos los dominios en la base de datos.

---

## FAQ

### ¿Por qué no puedo usar solo `localhost`?

`localhost` no soporta subdominios. No puedes tener:
- `empresa1.localhost` (no funciona en la mayoría de navegadores)

Por eso necesitas un dominio real (o simulado con el archivo hosts).

### ¿Qué pasa si no configuro el archivo hosts?

Sin el archivo hosts en desarrollo local, el navegador intentaría resolver `dokploy.movete.cloud` en internet (DNS público) y fallaría.

### ¿Por qué todos los tenants usan el mismo DocumentRoot?

Porque Laravel maneja la separación de datos a nivel de **base de datos**, no de código. El código es el mismo, pero cada tenant tiene su propia BD.

### ¿Puedo usar Nginx en lugar de Apache?

Sí, tu proyecto incluye configuración para Nginx en `conf/nginx/nginx-site.conf`. El concepto es el mismo, solo cambia la sintaxis.

### ¿Cómo funciona en producción sin archivo hosts?

En producción:
1. Compras un dominio real (ej: `miapp.com`)
2. Configuras DNS wildcard en tu proveedor: `*.miapp.com → IP del servidor`
3. Configuras Virtual Hosts en el servidor con el dominio real
4. El DNS de internet resuelve automáticamente todos los subdominios

### ¿El wildcard `*.dominio.com` tiene límites?

No, puedes tener infinitos subdominios:
- `empresa1.dominio.com`
- `empresa2.dominio.com`
- `empresa999.dominio.com`

Todos son capturados por el Virtual Host con wildcard.

### ¿Por qué los logs están separados?

```
dokploy.movete.cloud-error.log     → Dominio central
dokploy-tenants-error.log          → Todos los tenants
```

Esto facilita el debugging. Si un tenant tiene problemas, revisas `dokploy-tenants-error.log`.

### ¿Qué hace `AllowOverride All`?

Permite que Laravel use su archivo `.htaccess` para:
- Reescribir URLs (rutas bonitas: `/productos` en lugar de `/index.php?route=productos`)
- Configuraciones de seguridad
- Headers personalizados

---

## 📊 Diagrama Visual Completo

```
┌─────────────────────────────────────────────────────────────────────┐
│                         USUARIO FINAL                                │
│          Escribe: http://empresa1.dokploy.movete.cloud              │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    ARCHIVO HOSTS (Windows)                           │
│              C:\Windows\System32\drivers\etc\hosts                   │
│                                                                      │
│   127.0.0.1    empresa1.dokploy.movete.cloud                        │
│                                                                      │
│   Traduce el dominio a: 127.0.0.1 (localhost)                       │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      APACHE (Puerto 80)                              │
│         C:\xampp\apache\conf\extra\httpd-vhosts.conf                │
│                                                                      │
│   <VirtualHost *:80>                                                │
│       ServerAlias *.dokploy.movete.cloud                            │
│       DocumentRoot "C:/proyecto/public"                             │
│   </VirtualHost>                                                    │
│                                                                      │
│   Lee el header HTTP: Host: empresa1.dokploy.movete.cloud           │
│   Coincide con el wildcard ✅                                        │
│   Sirve archivos desde: C:/proyecto/public                          │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         LARAVEL                                      │
│                    public/index.php                                  │
│                                                                      │
│   1. Lee dominio: empresa1.dokploy.movete.cloud                     │
│   2. Busca en tabla domains                                         │
│   3. Encuentra tenant: empresa1                                     │
│   4. Cambia BD a: tenantempresa1                                    │
│   5. Procesa la petición con datos del tenant                       │
│   6. Genera respuesta HTML                                          │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    RESPUESTA AL NAVEGADOR                            │
│                                                                      │
│   HTML con datos específicos del tenant empresa1                    │
│   Assets cargados desde: http://dokploy.movete.cloud/build/         │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🎓 Resumen Ejecutivo

1. **Archivo hosts**: Traduce dominios ficticios a `127.0.0.1` (solo en desarrollo)
2. **Apache Virtual Hosts**: Recibe peticiones y sirve el código de Laravel desde `public/`
3. **Laravel Tenancy**: Lee el dominio, identifica el tenant, y cambia la BD
4. **Mismo código, diferentes datos**: Todos los tenants usan el mismo código PHP, pero cada uno tiene su BD

**Ventaja clave**: No necesitas crear un proyecto separado por cada tenant. Todo está centralizado.

---

## 🆘 Problemas Comunes

### Error: "No se puede acceder al sitio"

**Causa**: El dominio no está en el archivo hosts.  
**Solución**: Agrega `127.0.0.1    tudominio.com` al archivo hosts.

### Apache no inicia

**Causa**: Error de sintaxis en `httpd-vhosts.conf`.  
**Solución**: Ejecuta `httpd.exe -t` para verificar sintaxis.

### Página en blanco

**Causa**: Error en Laravel (revisar logs).  
**Solución**: Revisa `storage/logs/laravel.log`.

### Assets (CSS/JS) no cargan

**Causa**: Los assets no se están sirviendo desde el dominio central.  
**Solución**: Verifica que `FixViteAssetsForTenants` middleware esté activo.

---

¿Necesitas más información sobre alguna parte específica? Consulta la [documentación completa](README.md) del proyecto.

