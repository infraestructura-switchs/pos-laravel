<p align="center">
    <a href="https://aimeos.org/">
        <img src="storage/app/public/images/system/logo-system.png" alt="Hallpos logo" title="Hallpos" height="180" />
    </a>
</p>

[Hallpos](https://hallpos.com.co): Agiliza tus ventas, controla tu inventario y obtén informes al instante donde te encuentres. Nuestro sistema POS es fácil de usar y conectar con servicios de facturación electrónica.

![dashboard](./public/dashboard.png)

Visit [demo](https://test.hallpos.com.co/) page:
- User: admin@gmail.com
- Password: 12345678

## Table Of Content

- [Installation](#installation)
  - [Requirements](#requirements)
  - [Steps](#steps)
- [Deploy](#deploy)
  - [Requirements](#requirements-1)
  - [Steps](#steps-1)

## Installation

### Requirements

| Technology   | Version   |
|--------------|-----------|
| <img src="https://www.php.net//images/logos/new-php-logo.svg" width="100" style="margin-top:10px"> <p align="center">php</p> | >= 8     |
| <img src="https://getcomposer.org/img/logo-composer-transparent3.png" width="80" style="margin-left:10px;margin-top:10px"> <p align="center">Composer</p> | >= 2 (optional)    |

### Steps

```bash
git clone git@github.com:Halltec/pos-laravel-v2.git
```

To install the composer dependencies, execute this command:

```bash
cd pos-laravel-v2

composer install
# or install from composer.phar file:
php composer.phar install
```

Create .env file, execute this command:

```bash
cp .env-example .env
```

Set database credentials to the following environment variables:

```env
DB_DATABASE=pos-laravel-v2
DB_USERNAME=example
DB_PASSWORD=example
```

After, execute this command:

```bash
php artisan key:generate
php artisan migrate
php artisan migrate:fresh --seed
```

## Deploy

### Requirements

| Technology   | Version   |
|--------------|-----------|
| <img src="https://www.php.net//images/logos/new-php-logo.svg" width="100" style="margin-top:10px"> <p align="center">php</p> | >= 8     |
| <img src="https://getcomposer.org/img/logo-composer-transparent3.png" width="80" style="margin-left:10px;margin-top:10px"> <p align="center">Composer</p> | >= 2 (optional)    |

### Steps

Create subdomain to new project:

```bash
example.hallpos.com.co
```

Create database, user and into folder created from subdomain, clone Hallpos repository:

```bash
cd <path_folder_subdomain>
rm default.php
git clone git@github.com:Halltec/pos-laravel-v2.git .
```

To install the composer dependencies, execute this command:

```bash
composer install
# or install from composer.phar file:
php composer.phar install
```

Create .env file, execute this command:

```bash
cp .env-example .env
```

Change the following environment variables:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=<subdomain>
```

Set database credentials to the following environment variables:

```env
DB_DATABASE=example
DB_USERNAME=example
DB_PASSWORD=example
```

After, execute this command:

```bash
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
cp htaccess .htaccess
chmod 775 -R storage/app/public
```

## 🌐 Configuración Multi-Tenant

Esta aplicación soporta **multi-tenancy** basado en subdominios. Cada empresa (tenant) tiene su propio subdominio y base de datos.

### Cambiar el Dominio Central

El sistema utiliza una **variable de entorno centralizada** para el dominio. Para cambiar de `dokploy.movete.cloud` a tu propio dominio:

1. **Edita tu archivo `.env`:**
   ```env
   CENTRAL_DOMAIN=tudominio.com
   APP_URL=http://tudominio.com
   CENTRAL_DOMAINS=tudominio.com,www.tudominio.com
   ```

2. **Limpia las cachés:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. **Si usas Vite, reinicia el servidor:**
   ```bash
   npm run dev
   ```

**📖 Documentación completa:** [docs/CAMBIAR_DOMINIO.md](docs/CAMBIAR_DOMINIO.md)

### Funciones Helper Disponibles

```php
// Obtener el dominio central
centralDomain(); // "tudominio.com"
centralDomain(withProtocol: true); // "http://tudominio.com"

// Verificar si es un tenant
isTenantDomain(); // true si estamos en empresa1.tudominio.com

// Extraer subdominio del tenant
tenantSubdomain(); // "empresa1" (si estamos en empresa1.tudominio.com)
```

---

## Documentación (Docs)

### 🌟 Documentación Multi-Tenant (Nuevas)

- **⚡ Resumen Rápido:** [`docs/RESUMEN_CAMBIO_DOMINIO.md`](docs/RESUMEN_CAMBIO_DOMINIO.md) - Cambio de dominio en 5 pasos
- **🌐 Cambiar Dominio (Completo):** [`docs/CAMBIAR_DOMINIO.md`](docs/CAMBIAR_DOMINIO.md) - Guía detallada con ejemplos y FAQs
- **🔧 Cómo Funciona Apache y Hosts:** [`docs/COMO_FUNCIONA_APACHE_HOSTS.md`](docs/COMO_FUNCIONA_APACHE_HOSTS.md) - Flujo completo desde navegador hasta BD
- **📊 Diagrama de Flujo:** [`docs/DIAGRAMA_FLUJO_MULTITENANT.md`](docs/DIAGRAMA_FLUJO_MULTITENANT.md) - Visualización del sistema multi-tenant

### 📚 Documentación General

- Guías: [`docs/guias/README.md`](docs/guias/README.md)
- Despliegue: [`docs/deploy/README.md`](docs/deploy/README.md)
- Soluciones Multi-tenant: [`docs/soluciones-multitenant/README.md`](docs/soluciones-multitenant/README.md)
- Scripts PowerShell: [`docs/scripts/README.md`](docs/scripts/README.md)
- API: [`docs/api/README.md`](docs/api/README.md)
- WhatsApp: [`docs/whatsapp/README.md`](docs/whatsapp/README.md)
- UI: [`docs/ui/README.md`](docs/ui/README.md)
- Resúmenes: [`docs/resumen/README.md`](docs/resumen/README.md)
