# 🗄️ Seeders: Base de Datos Central vs Tenants

## 📊 Arquitectura de Bases de Datos

```
┌─────────────────────────────────────────┐
│       BASE DE DATOS CENTRAL             │
│         (pos_central)                   │
├─────────────────────────────────────────┤
│ - Tabla: tenants                        │
│ - Tabla: domains                        │
│ - Tabla: users (Super Admin)            │
└─────────────────────────────────────────┘
              │
              ├─────────────────┬─────────────────┐
              ▼                 ▼                 ▼
┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐
│ TENANT: empresag │  │ TENANT: empresat │  │ TENANT: empresa1 │
├──────────────────┤  ├──────────────────┤  ├──────────────────┤
│ - users          │  │ - users          │  │ - users          │
│ - products       │  │ - products       │  │ - products       │
│ - customers      │  │ - customers      │  │ - customers      │
│ - sales          │  │ - sales          │  │ - sales          │
│ - categories     │  │ - categories     │  │ - categories     │
│ - companies      │  │ - companies      │  │ - companies      │
│ - etc...         │  │ - etc...         │  │ - etc...         │
└──────────────────┘  └──────────────────┘  └──────────────────┘
```

---

## 🌐 Seeders para Base de Datos CENTRAL

### ¿Qué contiene?

La base de datos central **NO** contiene datos operacionales de las empresas. Solo contiene:
- Usuario Super Administrador
- Registro de tenants (empresas)
- Dominios asignados a cada tenant

### Seeders Disponibles

#### SuperAdminSeeder
Crea el usuario administrador del sistema central.

**Datos creados:**
- Email: `superadmin@gmail.com`
- Password: `123456`
- Nombre: `Super Admin`

### Cómo Ejecutar

```bash
# Desde Docker
wsl docker exec laravel-php-fpm php artisan db:seed --class=SuperAdminSeeder

# Localmente (sin Docker)
php artisan db:seed --class=SuperAdminSeeder
```

### Acceso

- **URL**: `http://adminpos.dokploy.movete.cloud` o `http://localhost`
- **Email**: `superadmin@gmail.com`
- **Password**: `123456`

### Funciones del Super Admin

Desde esta cuenta puedes:
- ✅ Ver lista de todos los tenants
- ✅ Crear nuevos tenants (empresas)
- ✅ Editar información de tenants
- ✅ Suspender/Activar tenants
- ✅ Eliminar tenants
- ✅ Ver dominios registrados

---

## 🏢 Seeders para TENANTS (Empresas)

### ¿Qué contienen?

Cada tenant tiene su **propia base de datos** con todos los datos operacionales:
- Usuarios de la empresa
- Productos
- Clientes
- Ventas
- Facturas
- Inventario
- Configuraciones
- Y todo lo relacionado con el negocio

### Seeders Disponibles (33 seeders)

#### 1. Configuración Base (8 seeders)
- `DepartmentSeeder` - Departamentos
- `CitySeeder` - Ciudades
- `CurrencySeeder` - Monedas
- `InvoiceProviderSeeder` - Proveedores de facturación
- `TributeSeeder` - Tributos
- `TaxRateSeeder` - Tasas de impuestos
- `PaymentMethodSeeder` - Métodos de pago
- `IdentificationDocumentSeeder` - Tipos de documentos

#### 2. Usuarios y Permisos (4 seeders)
- `PermissionSeeder` - Permisos
- `RoleSeeder` - Roles
- `UserSeeder` - Usuarios
- `ModuleSeeder` - Módulos

#### 3. Empresa (3 seeders)
- `CompanySeeder` - Datos de la empresa
- `StaffSeeder` - Personal
- `PayrollSeeder` - Nómina

#### 4. Productos (3 seeders)
- `CategorySeeder` - Categorías
- `ProductSeeder` - Productos generales
- `ProductosConciertosSeeder` - Productos para conciertos

#### 5. Clientes y Proveedores (2 seeders)
- `CustomerSeeder` - Clientes
- `ProviderSeeder` - Proveedores

#### 6. Operaciones (7 seeders)
- `OrderSeeder` - Órdenes
- `PurchaseSeeder` - Compras
- `PurchaseDetailSeeder` - Detalles de compras
- `BillSeeder` - Facturas
- `InventoryRemissionSeeder` - Remisiones
- `OutputSeeder` - Salidas
- `NumberingRangeSeeder` - Rangos de numeración

#### 7. Configuración Avanzada (4 seeders)
- `TerminalSeeder` - Terminales
- `FactusConfigurationSeeder` - Configuración Factus
- `FactroConfigurationSeeder` - Configuración Factro
- `InitialSetupSeeder` - Configuración inicial

#### 8. Seeder Principal
- `DatabaseSeeder` - Ejecuta todos los demás

### Cómo Ejecutar

```bash
# Un seeder específico
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=ProductSeeder --tenants=empresag

# Todos los seeders (DatabaseSeeder)
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=DatabaseSeeder --tenants=empresag

# Para múltiples tenants
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=ProductSeeder --tenants=empresag,empresat

# Para todos los tenants
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=ProductSeeder --tenants=all
```

### Acceso a Tenants

Cada tenant tiene su propio subdominio:

- **empresag**: `http://empresag.adminpos.dokploy.movete.cloud`
- **empresat**: `http://empresat.empresag.adminpos.dokploy.movete.cloud`

Las credenciales de usuario dependen del `UserSeeder` ejecutado para ese tenant.

---

## 🔑 Diferencias Clave

| Aspecto | Base de Datos Central | Base de Datos Tenant |
|---------|----------------------|---------------------|
| **Nombre BD** | `pos_central` | `tenantempresag`, `tenantempresax`, etc. |
| **Propósito** | Administración del sistema | Operación del negocio |
| **Usuarios** | Super Admin únicamente | Usuarios de la empresa |
| **Comando Seeder** | `php artisan db:seed --class=Seeder` | `php artisan tenants:seed --class=Seeder --tenants=id` |
| **Datos** | Tenants, Dominios | Productos, Ventas, Clientes, etc. |
| **Acceso** | `adminpos.dokploy.movete.cloud` | `empresag.adminpos.dokploy.movete.cloud` |

---

## 📝 Comandos Rápidos

### Base de Datos Central

```bash
# Ejecutar seeder central
wsl docker exec laravel-php-fpm php artisan db:seed --class=SuperAdminSeeder

# Ver usuarios centrales
wsl docker exec laravel-php-fpm php artisan tinker --execute="User::all();"
```

### Base de Datos Tenant

```bash
# Listar tenants
wsl docker exec laravel-php-fpm php artisan tenants:list

# Ejecutar seeder en tenant específico
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=ProductSeeder --tenants=empresag

# Ver productos de un tenant (con tinker)
wsl docker exec laravel-php-fpm php artisan tinker --execute="tenancy()->initialize(App\Models\Tenant::find('empresag')); Product::count();"
```

---

## 🎯 Flujo de Trabajo Recomendado

### 1. Primera Vez (Setup Inicial)

```bash
# Paso 1: Crear Super Admin en BD Central
wsl docker exec laravel-php-fpm php artisan db:seed --class=SuperAdminSeeder

# Paso 2: Acceder a adminpos.dokploy.movete.cloud y crear un tenant desde la interfaz

# Paso 3: Ejecutar seeders en el tenant creado
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=DatabaseSeeder --tenants=<nuevo_tenant_id>
```

### 2. Agregar Datos a Tenant Existente

```bash
# Ejecutar seeders específicos
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=ProductosConciertosSeeder --tenants=empresag
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=CustomerSeeder --tenants=empresag
```

### 3. Resetear Datos de Tenant (Cuidado!)

```bash
# Eliminar y recrear tenant
wsl docker exec laravel-php-fpm php artisan tinker --execute="Tenant::find('empresag')->delete();"

# O usar el panel de administración (Recomendado)
# http://adminpos.dokploy.movete.cloud/admin/tenants
```

---

## ⚠️ Advertencias Importantes

1. **No ejecutar seeders de tenant en BD central**: Los seeders de productos, clientes, etc. están diseñados para tenants, no para la BD central.

2. **No usar `tenants:seed` para seeders centrales**: El comando `tenants:seed` es solo para tenants. Para la BD central usa `db:seed`.

3. **Verificar conexión**: Asegúrate de estar conectado a la base de datos correcta antes de ejecutar seeders.

4. **Duplicación de datos**: Algunos seeders pueden duplicar datos si se ejecutan múltiples veces. Usa `updateOrCreate` en seeders o limpia la BD antes.

---

## 📚 Referencias

- `database/seeders/SuperAdminSeeder.php` - Seeder central
- `database/seeders/DatabaseSeeder.php` - Seeder principal de tenants
- `config/tenancy.php` - Configuración de tenancy
- `EJECUTAR_SEEDERS_DOCKER.md` - Guía de ejecución con Docker

