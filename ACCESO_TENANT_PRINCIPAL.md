# 🎉 Tenant Principal Creado Exitosamente


## ✅ Datos del Tenant

- **ID**: `principal`
- **Nombre**: `Empresa Principal`
- **Estado**: Activo
- **Base de Datos**: `tenantprincipal`

---

## 🌐 Acceso a la Aplicación

### URL del Tenant

```
http://principal.adminpos.dokploy.movete.cloud
```

O si usas localhost:

```
http://principal.localhost
```

### Credenciales de Acceso

**Usuario Administrador:**
- **Email**: `admin@gmail.com`
- **Password**: `12345678`

**Usuario Cajero (opcional):**
- **Email**: `cajero@gmail.com`
- **Password**: `12345678`

---

## 📦 Datos Creados

El tenant viene con los siguientes datos de prueba:

### Productos: 42
Organizados en categorías para conciertos:
- Bebidas Alcohólicas (8 productos)
- Bebidas No Alcohólicas (8 productos)
- Comida Rápida (9 productos)
- Snacks y Dulces (7 productos)
- Cigarrillos y Tabaco (3 productos)
- Merchandising (5 productos)
- Combos (2 productos)

### Categorías: 11
- Bebidas
- Comidas Rápidas
- Snacks
- Licor
- Bebidas Alcohólicas
- Bebidas No Alcohólicas
- Comida Rápida
- Snacks y Dulces
- Cigarrillos y Tabaco
- Merchandising
- Combos

### Clientes: 30
Clientes de prueba generados automáticamente

### Otros Datos:
- ✅ Permisos y Roles configurados
- ✅ Módulos del sistema activados
- ✅ Terminales de POS creados
- ✅ Métodos de pago (Efectivo, Tarjeta, Transferencia)
- ✅ Departamentos y Ciudades de Colombia
- ✅ Tipos de documentos de identificación
- ✅ Rangos de numeración para facturas

---

## 🚀 Pasos para Probar el POS

### 1. Agregar al Archivo Hosts (Solo si usas localhost)

**Windows**: `C:\Windows\System32\drivers\etc\hosts`

Agrega esta línea:
```
127.0.0.1 principal.adminpos.dokploy.movete.cloud
```

O si prefieres un nombre más corto:
```
127.0.0.1 principal.localhost
```

### 2. Acceder a la Aplicación

Abre tu navegador y ve a:
```
http://principal.adminpos.dokploy.movete.cloud
```

### 3. Iniciar Sesión

- Email: `admin@gmail.com`
- Password: `12345678`

### 4. Abrir Caja

1. Ve al menú **"Apertura de Caja"** o **"Caja"**
2. Selecciona el terminal (debería haber uno llamado "Principal")
3. Ingresa el monto inicial (ejemplo: 50000)
4. Click en **"Abrir Caja"**

### 5. Realizar una Venta

1. Ve al menú **"Vender"** o **"POS"**
2. Selecciona productos del catálogo
3. Agrega al carrito
4. Selecciona cliente (puedes usar uno de los 30 clientes de prueba)
5. Selecciona método de pago
6. Completa la venta

---

## 🔍 Verificar Datos

Si quieres ver los datos desde la terminal:

### Ver productos
```bash
wsl docker exec laravel-php-fpm php artisan tinker --execute="tenancy()->initialize(App\Models\Tenant::find('principal')); App\Models\Product::with('category')->limit(10)->get(['name', 'price', 'category_id'])->each(function(\$p) { echo \$p->name . ' - $' . \$p->price . ' - ' . \$p->category->name . PHP_EOL; });"
```

### Ver categorías
```bash
wsl docker exec laravel-php-fpm php artisan tinker --execute="tenancy()->initialize(App\Models\Tenant::find('principal')); App\Models\Category::all(['name'])->each(function(\$c) { echo \$c->name . PHP_EOL; });"
```

### Ver clientes
```bash
wsl docker exec laravel-php-fpm php artisan tinker --execute="tenancy()->initialize(App\Models\Tenant::find('principal')); echo 'Total clientes: ' . App\Models\Customer::count();"
```

---

## 🆚 Diferencia entre Central y Tenant

### Base de Datos Central
- **URL**: `http://adminpos.dokploy.movete.cloud` o `http://localhost`
- **Usuario**: `superadmin@gmail.com` / `123456`
- **Función**: Administrar tenants (crear, editar, eliminar empresas)
- **No tiene**: Productos, ventas, clientes

### Tenant "principal"
- **URL**: `http://principal.adminpos.dokploy.movete.cloud`
- **Usuario**: `admin@gmail.com` / `12345678`
- **Función**: Operación del negocio (ventas, productos, clientes)
- **Tiene**: 42 productos, 30 clientes, terminales, etc.

---

## ⚠️ Solución de Problemas

### Error: "No se puede conectar"

Verifica que Docker esté corriendo:
```bash
wsl docker ps
```

### Error: "No se encuentra el dominio"

Agrega al archivo hosts:
```
127.0.0.1 principal.adminpos.dokploy.movete.cloud
```

### Error: "Credenciales inválidas"

Usa estas credenciales:
- Email: `admin@gmail.com`
- Password: `12345678`

### Error: "No hay productos"

Los productos ya fueron creados (42 productos). Si no aparecen:
```bash
wsl docker exec laravel-php-fpm php artisan tinker --execute="tenancy()->initialize(App\Models\Tenant::find('principal')); echo App\Models\Product::count();"
```

---

## 📊 Comandos Útiles

### Listar todos los tenants
```bash
wsl docker exec laravel-php-fpm php artisan tenants:list
```

### Ver logs del tenant
```bash
wsl docker exec laravel-php-fpm tail -f storage/tenantprincipal/logs/laravel.log
```

### Agregar más productos
```bash
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=ProductSeeder --tenants=principal
```

---

## 🎯 ¡Listo para Probar!

Ya tienes todo configurado:
- ✅ Base de datos central con SuperAdmin
- ✅ Tenant "principal" con todos los datos
- ✅ 42 productos de conciertos
- ✅ 30 clientes de prueba
- ✅ Permisos y roles configurados
- ✅ Terminales listos para usar

**Accede ahora a**: `http://principal.adminpos.dokploy.movete.cloud`

¡Disfruta probando el POS! 🚀

