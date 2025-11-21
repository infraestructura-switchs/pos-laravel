# 🚀 Pasos para Iniciar y Probar la Aplicación POS

## ✅ Estado Actual del Sistema

### Base de Datos Central (`pos_central`)
- ✅ Migraciones ejecutadas
- ✅ SuperAdmin creado
- ✅ Sistema listo para gestionar tenants

**Credenciales SuperAdmin:**
- **Email**: `superadmin@gmail.com`
- **Password**: `123456`
- **URL**: `http://localhost` o `http://adminpos.dokploy.movete.cloud`

---

## 📋 Paso a Paso para Empezar

### Paso 1: Acceder al Panel Central ✅

```bash
# Asegúrate de que Docker esté corriendo
wsl docker ps
```

**Accede a:**
- `http://localhost` 
- O `http://adminpos.dokploy.movete.cloud`

**Inicia sesión con:**
- Email: `superadmin@gmail.com`
- Password: `123456`

---

### Paso 2: Crear un Tenant (Empresa)

Desde el panel de administración:

1. Ve a **"Tenants"** o **"Empresas"**
2. Click en **"Crear Nuevo Tenant"**
3. Completa el formulario:
   - **ID del Tenant**: `miempresa` (sin espacios, minúsculas)
   - **Nombre**: `Mi Empresa de Prueba`
   - **Email**: `admin@miempresa.com`
   - **Teléfono**: `3001234567`
   - **Contraseña**: `12345678`

4. Click en **"Guardar"**

El sistema automáticamente:
- ✅ Crea la base de datos del tenant
- ✅ Ejecuta las migraciones
- ✅ Ejecuta seeders básicos (permisos, roles, módulos)
- ✅ Crea el dominio: `miempresa.adminpos.dokploy.movete.cloud`
- ✅ Crea el usuario administrador del tenant

---

### Paso 3: Ejecutar Seeders de Productos (Opcional pero Recomendado)

Ahora que tienes un tenant, necesitas productos, clientes, etc.

#### Opción A: Productos de Conciertos

```bash
# Ejecutar seeders de productos de conciertos
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=ProductosConciertosSeeder --tenants=miempresa
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=CustomerSeeder --tenants=miempresa
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=NumberingRangeSeeder --tenants=miempresa
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=TerminalSeeder --tenants=miempresa
```

Esto creará:
- 42 productos para conciertos
- 30 clientes de prueba
- Rangos de numeración
- Terminales de POS

#### Opción B: Productos Generales

```bash
# Ejecutar seeders de productos generales
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=CategorySeeder --tenants=miempresa
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=ProductSeeder --tenants=miempresa
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=CustomerSeeder --tenants=miempresa
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=NumberingRangeSeeder --tenants=miempresa
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=TerminalSeeder --tenants=miempresa
```

Esto creará:
- 50+ productos generales (incluyendo Ibuprofeno, Aspirina)
- Categorías (Bebidas, Comidas Rápidas, Snacks, Licor)
- 30 clientes de prueba
- Rangos de numeración
- Terminales de POS

#### Opción C: Todos los Datos (Completo)

```bash
# Ejecutar todos los seeders disponibles
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=DatabaseSeeder --tenants=miempresa
```

⚠️ **Nota**: Esto creará MUCHOS datos de prueba, incluyendo ventas, compras, personal, etc.

---

### Paso 4: Acceder al Tenant

**URL del Tenant:**
- `http://miempresa.localhost`
- O `http://miempresa.adminpos.dokploy.movete.cloud`

**Credenciales:**
- Email: `admin@miempresa.com` (el que configuraste al crear el tenant)
- Password: `12345678` (la que configuraste)

O si ejecutaste el `DatabaseSeeder` completo, también puedes usar:
- Email: `admin@gmail.com`
- Password: `12345678`

---

### Paso 5: Probar el POS

1. **Inicia sesión** en el tenant
2. Ve a **"Apertura de Caja"** y abre una caja
3. Ve a **"Vender"** o **"POS"**
4. Agrega productos al carrito
5. Completa la venta

---

## 🔍 Verificar Datos Creados

### Ver Productos del Tenant

```bash
wsl docker exec laravel-php-fpm php artisan tinker --execute="tenancy()->initialize(App\Models\Tenant::find('miempresa')); echo 'Productos: ' . App\Models\Product::count() . PHP_EOL;"
```

### Ver Categorías del Tenant

```bash
wsl docker exec laravel-php-fpm php artisan tinker --execute="tenancy()->initialize(App\Models\Tenant::find('miempresa')); App\Models\Category::all(['name'])->each(fn(\$c) => print(\$c->name . PHP_EOL));"
```

### Ver Clientes del Tenant

```bash
wsl docker exec laravel-php-fpm php artisan tinker --execute="tenancy()->initialize(App\Models\Tenant::find('miempresa')); echo 'Clientes: ' . App\Models\Customer::count() . PHP_EOL;"
```

---

## 🎯 Tenants Existentes (Si ya creaste alguno)

### Listar Tenants

```bash
wsl docker exec laravel-php-fpm php artisan tenants:list
```

### Si ya existe `empresag`

Puedes usar el tenant que ya creaste:

**URL:** `http://empresag.adminpos.dokploy.movete.cloud`

**Ya tiene:** ✅
- 42 productos de conciertos
- 11 categorías
- 30 clientes
- Permisos y roles
- Terminales

**Credenciales:** Depende de cómo lo creaste, pero probablemente:
- Email: `admin@gmail.com`
- Password: `12345678`

---

## 🐛 Solución de Problemas

### Error: "No se puede acceder al tenant"

1. Verifica que el tenant existe:
```bash
wsl docker exec laravel-php-fpm php artisan tenants:list
```

2. Verifica el archivo hosts (Windows):
   - Ruta: `C:\Windows\System32\drivers\etc\hosts`
   - Debe tener: `127.0.0.1 miempresa.localhost`

### Error: "No hay productos"

Ejecuta los seeders de productos:
```bash
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=ProductosConciertosSeeder --tenants=miempresa
```

### Error: "No puedo abrir caja"

Ejecuta el seeder de terminales:
```bash
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=TerminalSeeder --tenants=miempresa
```

### Error: "Duplicate entry"

Si ya ejecutaste seeders y te da error de duplicados, es normal. Los datos ya existen.

---

## 📊 Resumen Rápido

```bash
# 1. Ver tenants existentes
wsl docker exec laravel-php-fpm php artisan tenants:list

# 2. Crear productos para un tenant
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=ProductosConciertosSeeder --tenants=<tenant_id>

# 3. Crear clientes para un tenant
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=CustomerSeeder --tenants=<tenant_id>

# 4. Crear terminales para un tenant
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=TerminalSeeder --tenants=<tenant_id>

# 5. Verificar datos
wsl docker exec laravel-php-fpm php artisan tinker --execute="tenancy()->initialize(App\Models\Tenant::find('<tenant_id>')); echo Product::count();"
```

---

## 🎉 ¡Listo para Probar!

Ahora puedes:
1. ✅ Acceder al panel central como SuperAdmin
2. ✅ Crear tenants (empresas)
3. ✅ Ejecutar seeders para cada tenant
4. ✅ Acceder a cada tenant con su URL específica
5. ✅ Probar el POS con productos de conciertos

---

## 📝 Próximos Pasos

- **Personalizar productos**: Agregar tus propios productos desde la interfaz
- **Configurar facturación**: Configurar Factus o Factro para facturación electrónica
- **Usuarios adicionales**: Crear cajeros, vendedores, etc.
- **Reportes**: Ver reportes de ventas, inventario, etc.

---

## 🔗 Enlaces Rápidos

- **Panel Central**: `http://localhost` o `http://adminpos.dokploy.movete.cloud`
- **Documentación Seeders**: `docs/SEEDERS_GUIA.md`
- **Seeders Docker**: `EJECUTAR_SEEDERS_DOCKER.md`
- **Central vs Tenant**: `docs/SEEDERS_CENTRAL_VS_TENANT.md`

