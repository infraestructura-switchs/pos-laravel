# 03. Arquitectura, Autenticación y Seguridad

## 1. Arquitectura general

- Backend: Laravel 9 (Eloquent, validación, transacciones, middleware, policies).
- Interfaz web: Livewire 2 + Tailwind/Alpine (componentes en `app/Http/Livewire`).
- API: rutas definidas en `routes/api.php`, protegidas con `auth:sanctum`.
- Autorización (web): RBAC con `spatie/laravel-permission` y middleware por “módulos”.
- Multi-tenant: `stancl/tenancy` por ruteo de dominio.

## 2. Multi-tenancy por dominio

En `routes/tenant.php`:

1. Se evita cargar rutas si el host pertenece a los dominios centrales (`config('tenancy.central_domains')`).
2. Se inicializa tenancy por dominio con `InitializeTenancyByDomain::class`.
3. Se impide acceso desde dominios centrales con `PreventAccessFromCentralDomains::class`.
4. Se incluye un middleware `tenant.status` para verificar que el tenant esté activo.

Datos de tenancy (tablas):

- Central: `pos_central` contiene `tenants` y `domains`.
- Tenant DB: `tenant_xxxxx` contiene los datos operacionales (productos, facturas, etc.).

Flujo de creación de tenant:

- Web central expone `GET/POST /register-tenant` vía `TenantRegistrationController`.
- La asignación de dominio se guarda en `domains` (tabla `domains` con `tenant_id` y `domain`).

## 3. Autenticación

### 3.1 Web (session)

Las rutas de autenticación web están en `routes/auth.php`:

- `GET /login` y `POST /login`
- `POST /logout`
- Verificación de email (según Breeze): `verify-email/*` y `email/verification-notification`

Los paneles requieren `auth` y, para la mayoría, también `verified`.

### 3.2 API (Sanctum tokens)

En `routes/api.php`:

- Pública:
  - `POST /auth/login`
- Protegida (`middleware('auth:sanctum')`):
  - `POST /auth/logout`
  - `GET /auth/me`
  - CRUD y operaciones por recursos (products, bills, customers, tax_rates, etc.)

En `AuthController@login`:

- Valida `email` (email) y `password` (string min:6).
- Crea token con `createToken('auth-token')->plainTextToken`.

## 4. Autorización por módulos y permisos

### 4.1 Middleware `HasModule`

Archivo: `app/Http/Middleware/HasModule.php`.

Reglas:

1. Si `isRoot()` o `$user->is_root == 1`, permite acceso a todo.
2. Si el módulo no existe en BD o el usuario no tiene permiso (`$user->hasPermissionTo($module)`), hace `abort(404)`.

Implicación para React:
- Un “404” puede significar “no autorizado”, no “recurso inexistente”.

### 4.2 Carga/caché de permisos

Archivo: `app/Http/Middleware/LoadUserPermissions.php`.

1. Si el usuario está autenticado, cachea permisos del usuario por 30 minutos (`Cache::remember` con TTL 1800).
2. Usa `tenant('id')` para separar por tenant.
3. Pre-carga `permissions` y `roles.permissions` para evitar N+1.

### 4.3 Habilitación de módulos

Archivo: `app/Services/ModuleService.php`.

- `ModuleService::getModules()` cachea `Module::all()` “forever” en `hallpos_modules`.
- `isEnabled($module)` busca por nombre case-insensitive y retorna `is_enabled`.
- `refreshCache()` borra el cache.

## 5. Integraciones externas (facturación electrónica y archivos)

### 5.1 Factus / Factro

Reglas generales:

- La creación y validación electrónica se coordina en `app/Services/BillService.php`.
- `BillService::validateElectronicBill()` llama a:
  - `validateElectronicBillFactus()` si `FactusConfigurationService::isApiEnabled()`
  - `validateElectronicBillFactro()` si `FactroConfigurationService::isApiEnabled()`

UI de configuración:

- `GET /factus/conexion`
- `GET /factro/conexion`

### 5.2 Cloudinary y PDFs

La API incluye un endpoint de upload de PDF a Cloudinary:

- `GET /pdf-upload/bill/{bill}` en `routes/api.php`.

También hay utilidades para imágenes de productos mediante base64 en:

- `POST /products/images/upload-base64` (en `routes/api.php`).

## 6. Concurrencia y consistencia

Flujo de creación de factura (Livewire):

- Usa `Cache::lock('createBill', 30)` para evitar crear facturas simultáneas desde el mismo flujo de UI.
- Persiste con `DB::beginTransaction()` y `commit/rollback`.

