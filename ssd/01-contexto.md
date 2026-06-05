# 01. Contexto Inicial del Proyecto

## 1. Resumen del producto

El proyecto es un **Sistema POS multi-tenant** desarrollado en **Laravel 9** con **Livewire 2** para la interfaz web y **API REST** protegida con **Laravel Sanctum**. El sistema gestiona:

- Catálogos (productos, categorías, presentaciones, impuestos/tributos).
- Clientes/proveedores/empleados.
- Ventas (facturación “directa” y por facturas), con opción de **facturación electrónica**.
- Compras y egresos.
- Inventario (inventario con/ sin unidades, remisiones, movimientos, transferencias entre bodegas).
- Caja (apertura/cierre) y consolidación por día.
- Nómina (pagos/nomina) e integración con Factus (cuando aplica).
- Autorización por **roles/permisos** y **módulos habilitados** por empresa.

## 2. Multi-tenant por dominio

El sistema usa `stancl/tenancy` con ruteo por host:

- Dominio central: administración global del sistema (gestión de tenants).
- Tenants principales: base de datos aislada por empresa.
- Sub-tenants (sucursales/franquicias): base de datos adicional aislada.

En `routes/tenant.php` se cargan rutas solo cuando el host **no** corresponde a los dominios centrales, y se inicializa tenancy con `InitializeTenancyByDomain`.

Además existe una protección para impedir acceso desde dominios centrales: `PreventAccessFromCentralDomains`.

## 3. Tech stack relevante

Backend:

- Laravel 9 (Eloquent, validaciones, transacciones, policies/middleware).
- Livewire 2 (pantallas CRUD y flujos interactivos).
- Sanctum (API con tokens Bearer).
- spatie/laravel-permission (RBAC: roles + permisos).
- Redis (documentado/esperado en la documentación para sesiones y caché).
- Cache (ModuleService y LoadUserPermissions).

Integraciones externas:

- **Facturación electrónica**: servicios “Factus” y “Factro” (validación y creación de notas crédito electrónicas).
- **Cloudinary**: subida de PDFs/adjuntos (y manejo de imágenes de productos).
- Generación de PDF y QR/CUFE (integración a través de servicios y librerías del proyecto).

## 4. Experiencia de usuario y pantallas

La navegación web está organizada por rutas del tenant:

- Menú/control por módulos vía middleware `module:*`.
- Pantallas administradas por Livewire (ej. `app/Http/Livewire/Admin/*`).
- Flujos de negocio principales: creación de facturas, validación electrónica, inventario y caja.

## 5. Fuentes para la SSD

Este documento toma como base:

- Rutas: `routes/web.php`, `routes/tenant.php`, `routes/admin.php`, `routes/api.php`
- Middleware: `app/Http/Middleware/*`
- Servicios y validación: `app/Services/*` y componentes Livewire de negocio
- Esquema: migraciones en `database/migrations/*`

## 6. Estado Actual de la Migración (Fase 1)

El sistema se está migrando progresivamente. En esta etapa inicial:
- Se ha creado el proyecto base en React (`POS-React`).
- Se utiliza **MSW (Mock Service Worker)** para simular las respuestas de la API, permitiendo desarrollar la UI sin depender del backend Laravel aún.
- Las acciones de menú y muchas funcionalidades operativas (Inventario, Caja, Nómina) **aún no se han migrado** y requieren ser especificadas para su implementación en React.
