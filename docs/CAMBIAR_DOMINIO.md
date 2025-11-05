# 🌐 Guía para Cambiar el Dominio Central

Esta guía te explica cómo cambiar el dominio central de tu aplicación multi-tenant de forma sencilla y centralizada.

## 📋 Tabla de Contenidos

- [Introducción](#introducción)
- [Pasos para Cambiar el Dominio](#pasos-para-cambiar-el-dominio)
- [Archivos que se Actualizan Automáticamente](#archivos-que-se-actualizan-automáticamente)
- [Funciones Helper Disponibles](#funciones-helper-disponibles)
- [Ejemplos de Uso](#ejemplos-de-uso)
- [Preguntas Frecuentes](#preguntas-frecuentes)

---

## 🎯 Introducción

Anteriormente, el dominio `dokploy.movete.cloud` estaba hardcodeado en múltiples archivos del proyecto, lo que hacía difícil cambiarlo. Ahora, todo el sistema utiliza una **variable de entorno centralizada** llamada `CENTRAL_DOMAIN`.

### Ventajas de este sistema:

✅ **Cambio en un solo lugar**: Modifica solo la variable `CENTRAL_DOMAIN` en tu archivo `.env`  
✅ **Sin código hardcoded**: No hay dominios escritos directamente en el código  
✅ **Funciones helper**: Uso sencillo con funciones como `centralDomain()`  
✅ **Multi-tenant automático**: Los subdominios de tenants se generan automáticamente  

---

## 🔧 Pasos para Cambiar el Dominio

### 1. Edita tu archivo `.env`

Abre tu archivo `.env` y busca (o agrega) la siguiente variable:

```env
# Dominio central de la aplicación (sin protocolo, sin www)
CENTRAL_DOMAIN=tudominio.com
```

**Ejemplos válidos:**
- `CENTRAL_DOMAIN=miempresa.com`
- `CENTRAL_DOMAIN=app.miempresa.com`
- `CENTRAL_DOMAIN=pos.local`
- `CENTRAL_DOMAIN=dokploy.movete.cloud` (valor por defecto)

⚠️ **IMPORTANTE**: 
- NO incluyas `http://` o `https://`
- NO incluyas `www.` (a menos que sea parte del dominio base)
- Solo el dominio base, sin barras ni rutas

### 2. Actualiza `APP_URL` (opcional pero recomendado)

También deberías actualizar la variable `APP_URL` para que coincida:

```env
APP_URL=http://tudominio.com
```

En producción, usa `https`:

```env
APP_URL=https://tudominio.com
```

### 3. Actualiza `CENTRAL_DOMAINS` en tu archivo `.env`

Esta variable es una lista separada por comas de los dominios centrales (para el paquete de tenancy):

```env
CENTRAL_DOMAINS=tudominio.com,www.tudominio.com
```

### 4. Limpia las cachés

Después de cambiar las variables de entorno, limpia las cachés de Laravel:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 5. Actualiza tus dominios en el archivo hosts (desarrollo local)

Si estás en desarrollo local, actualiza tu archivo hosts:

**Windows:** `C:\Windows\System32\drivers\etc\hosts`  
**Linux/Mac:** `/etc/hosts`

```
127.0.0.1       tudominio.com
127.0.0.1       www.tudominio.com
127.0.0.1       empresa1.tudominio.com
127.0.0.1       empresa2.tudominio.com
```

### 6. Reconstruye los assets (si usas Vite)

Si estás usando Vite para el desarrollo:

```bash
npm run dev
```

O para producción:

```bash
npm run build
```

---

## 📂 Archivos que se Actualizan Automáticamente

Cuando cambias la variable `CENTRAL_DOMAIN`, los siguientes archivos la utilizan automáticamente:

### Archivos de Configuración

| Archivo | Descripción |
|---------|-------------|
| `config/app.php` | Lee `CENTRAL_DOMAIN` desde `.env` |
| `config/tenancy.php` | Usa `CENTRAL_DOMAIN` para identificar dominios centrales |
| `vite.config.js` | Usa `CENTRAL_DOMAIN` para servir assets |

### Código PHP

| Archivo | Uso |
|---------|-----|
| `app/helpers.php` | Funciones `centralDomain()`, `isTenantDomain()`, `tenantSubdomain()` |
| `app/Providers/AppServiceProvider.php` | Configuración de assets para multi-tenant |
| `app/Http/Middleware/FixViteAssetsForTenants.php` | Redirección de assets al dominio central |
| `app/Http/Controllers/TenantRegistrationController.php` | Creación de dominios de tenants |
| `app/Console/Commands/CreateTenantCommand.php` | Comando Artisan para crear tenants |
| `app/Http/Livewire/Dashboard.php` | Verificación de dominios habilitados |
| `fix_tenants_domains.php` | Script para agregar dominios a tenants existentes |

---

## 🛠️ Funciones Helper Disponibles

El sistema incluye funciones helper que facilitan el trabajo con el dominio central:

### 1. `centralDomain()`

Obtiene el dominio central de la aplicación.

```php
// Dominio simple
centralDomain(); 
// Resultado: "tudominio.com"

// Con www
centralDomain(withWww: true); 
// Resultado: "www.tudominio.com"

// Con protocolo
centralDomain(withProtocol: true); 
// Resultado: "http://tudominio.com" (desarrollo)
// Resultado: "https://tudominio.com" (producción)

// Ambos
centralDomain(withWww: true, withProtocol: true); 
// Resultado: "http://www.tudominio.com"
```

### 2. `isTenantDomain()`

Verifica si el dominio actual (o un dominio específico) es un subdominio de tenant.

```php
// Verificar el dominio actual
if (isTenantDomain()) {
    // Estamos en un tenant (ejemplo: empresa1.tudominio.com)
} else {
    // Estamos en el dominio central (tudominio.com)
}

// Verificar un dominio específico
isTenantDomain('empresa1.tudominio.com'); // true
isTenantDomain('tudominio.com'); // false
isTenantDomain('www.tudominio.com'); // false
```

### 3. `tenantSubdomain()`

Extrae el subdominio del tenant desde el host actual.

```php
// Si estamos en: empresa1.tudominio.com
$subdomain = tenantSubdomain(); 
// Resultado: "empresa1"

// Si estamos en el dominio central
$subdomain = tenantSubdomain(); 
// Resultado: null

// Desde un dominio específico
tenantSubdomain('miempresa.tudominio.com'); 
// Resultado: "miempresa"
```

---

## 💡 Ejemplos de Uso

### Ejemplo 1: Crear un enlace al dominio central

```php
$loginUrl = centralDomain(withProtocol: true) . '/login';
// Resultado: "http://tudominio.com/login"

return redirect()->away($loginUrl);
```

### Ejemplo 2: Verificar si estamos en un tenant

```php
public function boot()
{
    if (isTenantDomain()) {
        // Configuración específica para tenants
        config(['app.name' => 'Tenant App']);
    } else {
        // Configuración para el dominio central
        config(['app.name' => 'Central App']);
    }
}
```

### Ejemplo 3: Crear dominio para nuevo tenant

```php
$tenant = Tenant::create([
    'id' => 'nuevaempresa',
    'name' => 'Nueva Empresa',
]);

$tenant->domains()->create([
    'domain' => 'nuevaempresa.' . centralDomain(),
]);
// Crea: nuevaempresa.tudominio.com
```

### Ejemplo 4: Obtener información del tenant actual

```php
if (isTenantDomain()) {
    $subdomain = tenantSubdomain();
    $centralDomain = centralDomain();
    
    echo "Tenant: {$subdomain}";
    echo "Dominio completo: {$subdomain}.{$centralDomain}";
}
```

---

## ❓ Preguntas Frecuentes

### ¿Puedo usar subdominios adicionales?

Sí. Por ejemplo, si tu dominio central es `app.miempresa.com`, los tenants serán:
- `tenant1.app.miempresa.com`
- `tenant2.app.miempresa.com`

### ¿Qué pasa con mis tenants existentes?

Los tenants existentes seguirán funcionando, pero sus dominios registrados en la base de datos deben actualizarse. Usa el script:

```bash
php fix_tenants_domains.php
```

Este script actualizará automáticamente todos los dominios de tenants para usar el nuevo dominio central.

### ¿Necesito cambiar algo en mi servidor web?

Sí, debes configurar tu servidor web (Apache/Nginx) para aceptar el nuevo dominio:

**Nginx:**
```nginx
server {
    server_name tudominio.com *.tudominio.com;
    # ... resto de la configuración
}
```

**Apache:**
```apache
<VirtualHost *:80>
    ServerName tudominio.com
    ServerAlias *.tudominio.com
    # ... resto de la configuración
</VirtualHost>
```

### ¿Funciona en producción con HTTPS?

Sí. La función `centralDomain(withProtocol: true)` detecta automáticamente si estás en producción y usa `https://` en lugar de `http://`.

Asegúrate de tener un certificado SSL wildcard que cubra `*.tudominio.com`.

### ¿Puedo tener múltiples dominios centrales?

La aplicación está diseñada para un dominio central principal. Sin embargo, puedes agregar alias en `config/tenancy.php`:

```php
'central_domains' => [
    '127.0.0.1',
    'localhost',
    env('CENTRAL_DOMAIN', 'dokploy.movete.cloud'),
    'www.' . env('CENTRAL_DOMAIN', 'dokploy.movete.cloud'),
    'otro-dominio.com', // Dominio adicional
],
```

### ¿Qué pasa si no configuro `CENTRAL_DOMAIN`?

El sistema usa el valor por defecto `dokploy.movete.cloud`, por lo que seguirá funcionando sin cambios.

### ¿Necesito reiniciar algo después del cambio?

En desarrollo con Vite:
```bash
# Detén el servidor de Vite (Ctrl+C) y vuelve a iniciarlo
npm run dev
```

En producción:
```bash
php artisan config:clear
php artisan cache:clear
# Reinicia tu servidor web si es necesario
```

---

## 📝 Resumen Rápido

**Para cambiar tu dominio:**

1. Edita `.env` y cambia `CENTRAL_DOMAIN=tudominio.com`
2. Actualiza `APP_URL=http://tudominio.com`
3. Actualiza `CENTRAL_DOMAINS=tudominio.com,www.tudominio.com`
4. Ejecuta `php artisan config:clear`
5. Si usas Vite: reinicia con `npm run dev`
6. Actualiza dominios de tenants: `php fix_tenants_domains.php`
7. Actualiza tu archivo hosts (desarrollo local)
8. Actualiza configuración de servidor web

**¡Y listo!** 🎉 Todo el sistema usará automáticamente tu nuevo dominio.

---

## 🆘 Soporte

Si encuentras algún problema o el dominio no se actualiza correctamente en algún archivo, revisa:

1. Que hayas limpiado todas las cachés
2. Que el archivo `.env` tenga la variable correcta
3. Que hayas reiniciado los servicios necesarios (Vite, servidor web, etc.)

Para más ayuda, consulta la documentación del proyecto o contacta al equipo de desarrollo.

