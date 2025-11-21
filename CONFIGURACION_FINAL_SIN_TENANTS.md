# ✅ Sistema Configurado SIN Multi-Tenancy

## 🎉 ¡Sistema Listo!

El sistema ha sido configurado para usar **directamente la base de datos central** (`pos_central`) **SIN tenants**.

---

## ✅ Lo que se hizo

1. ✅ **Eliminados TODOS los tenants** (principal, empresag, etc.)
2. ✅ **Limpiada la base de datos central** completamente
3. ✅ **Ejecutadas las migraciones** (117 tablas)
4. ✅ **Ejecutados TODOS los seeders** incluyendo productos de conciertos
5. ✅ **Creados usuarios** para acceso directo
6. ✅ **Configurada empresa principal**

---

## 🌐 Acceso a la Aplicación

### URL Principal

```
http://adminpos.dokploy.movete.cloud
```

O si usas localhost:

```
http://localhost
```

### 🔑 Credenciales de Acceso

**Usuario Principal:**
- **Email**: `admin@gmail.com`
- **Password**: `12345678`

**Super Admin (opcional):**
- **Email**: `superadmin@gmail.com`
- **Password**: `123456`

---

## 📦 Datos Disponibles en `pos_central`

✅ **42 Productos** de conciertos organizados en:
- Bebidas Alcohólicas (Cerveza, Ron, Whisky, Vodka, Vino)
- Bebidas No Alcohólicas (Agua, Gaseosas, Jugos, Café)
- Comida Rápida (Hamburguesas, Hot Dogs, Papas, Nachos)
- Snacks y Dulces (Maní, Palomitas, Chocolates, Chicles)
- Cigarrillos y Tabaco (Marlboro, Lucky Strike, Encendedores)
- Merchandising (Camisetas, Gorras, Pulseras, Posters)
- Combos (Papas + Gaseosa, Hamburguesa Completa)

✅ **11 Categorías**

✅ **31 Clientes** (30 de prueba + Consumidor Final)

✅ **2 Usuarios** (admin y superadmin)

✅ **1 Empresa** configurada (Empresa Principal)

✅ **1 Terminal** (Terminal Principal)

✅ **Permisos y Roles** completos

✅ **Métodos de Pago** (Efectivo, Tarjeta Crédito, Tarjeta Débito, Transferencia)

✅ **Departamentos y Ciudades** de Colombia

✅ **Tipos de Documentos** de identificación

✅ **Rangos de Numeración** para facturas

---

## 🚀 Cómo Probar el POS

### 1. Acceder al Sistema

Abre tu navegador y ve a:
```
http://adminpos.dokploy.movete.cloud
```

### 2. Iniciar Sesión

- Email: `admin@gmail.com`
- Password: `12345678`

### 3. Abrir Caja

1. Ve al menú **"Apertura de Caja"** o **"Caja"**
2. Selecciona el terminal: **"Terminal Principal"**
3. Ingresa un monto inicial (ejemplo: 100000)
4. Click en **"Abrir Caja"**

### 4. Realizar una Venta

1. Ve al menú **"Vender"** o **"POS"**
2. Busca productos (Cerveza, Hamburguesa, etc.)
3. Agrega productos al carrito
4. Selecciona un cliente de la lista
5. Elige método de pago
6. Completa la venta

---

## 📊 Diferencia con la Configuración Anterior

| Aspecto | Antes (Multi-Tenant) | Ahora (Sin Tenants) |
|---------|---------------------|---------------------|
| **Base de Datos** | pos_central + tenantprincipal, etc. | Solo pos_central |
| **URL** | principal.adminpos.dokploy.movete.cloud | adminpos.dokploy.movete.cloud |
| **Tenants** | Múltiples empresas | No hay tenants |
| **Productos** | En BD de cada tenant | Directamente en pos_central |
| **Acceso** | Un dominio por empresa | Un solo dominio |

---

## 🎯 Ventajas de esta Configuración

✅ **Más Simple**: No necesitas gestionar múltiples tenants
✅ **Acceso Directo**: Todo en una sola base de datos
✅ **Más Rápido**: No hay overhead de tenancy
✅ **Ideal para Pruebas**: Perfecto para testing y desarrollo
✅ **Una Sola URL**: `adminpos.dokploy.movete.cloud`

---

## 🔍 Verificar Datos desde Terminal

### Ver productos
```bash
wsl docker exec laravel-php-fpm php artisan tinker --execute="echo 'Productos: ' . App\Models\Product::count();"
```

### Ver productos por categoría
```bash
wsl docker exec laravel-php-fpm php artisan tinker --execute="App\Models\Product::with('category')->limit(10)->get(['name', 'price'])->each(function(\$p) { echo \$p->name . ' - $' . number_format(\$p->price) . ' - ' . \$p->category->name . PHP_EOL; });"
```

### Ver categorías
```bash
wsl docker exec laravel-php-fpm php artisan tinker --execute="App\Models\Category::all(['name'])->each(function(\$c) { echo \$c->name . PHP_EOL; });"
```

### Ver clientes
```bash
wsl docker exec laravel-php-fpm php artisan tinker --execute="echo 'Total clientes: ' . App\Models\Customer::count();"
```

---

## ⚠️ Notas Importantes

### ✅ Sistema Completamente Funcional

- Ya NO hay multi-tenancy activa
- TODOS los datos están en `pos_central`
- Acceso directo a `adminpos.dokploy.movete.cloud`
- 42 productos de conciertos listos para usar

### 🔄 Si Quieres Volver a Multi-Tenancy

Si en el futuro quieres volver al sistema multi-tenant:

1. Limpia la BD central (elimina productos, clientes, etc.)
2. Deja solo el SuperAdmin
3. Crea tenants desde la interfaz
4. Ejecuta seeders para cada tenant

---

## 📝 Estructura de Base de Datos Actual

```
pos_central (BD Única)
├── users (admin@gmail.com, superadmin@gmail.com)
├── products (42 productos de conciertos)
├── categories (11 categorías)
├── customers (31 clientes)
├── company (Empresa Principal)
├── terminals (Terminal Principal)
├── permissions y roles
├── payment_methods
├── departments y cities
└── ... (todas las tablas del negocio)
```

**NO hay**:
- ❌ Tabla `tenants` (vacía)
- ❌ Bases de datos `tenantprincipal`, `tenantempresag`, etc.

---

## 🎉 ¡Listo para Usar!

Accede ahora a:

### 🌐 http://adminpos.dokploy.movete.cloud

**Credenciales:**
- Email: `admin@gmail.com`
- Password: `12345678`

¡Disfruta probando el POS con productos de conciertos! 🎵🍻🍔

---

## 📚 Comandos Útiles

### Agregar más productos
```bash
wsl docker exec laravel-php-fpm php artisan db:seed --class=ProductSeeder
```

### Agregar más clientes
```bash
wsl docker exec laravel-php-fpm php artisan db:seed --class=CustomerSeeder
```

### Ver logs
```bash
wsl docker exec laravel-php-fpm tail -f storage/logs/laravel.log
```

### Reiniciar caché
```bash
wsl docker exec laravel-php-fpm php artisan cache:clear
wsl docker exec laravel-php-fpm php artisan config:clear
wsl docker exec laravel-php-fpm php artisan view:clear
```

