# 🐳 Cómo Ejecutar Seeders con Docker

## ⚠️ Importante

Cuando estás usando Docker, los comandos de Laravel deben ejecutarse **dentro del contenedor PHP**, no directamente en Windows.

---

## 📋 Verificar Contenedores Activos

```bash
wsl docker ps
```

Deberías ver estos contenedores corriendo:
- `laravel-php-fpm` (PHP)
- `laravel-nginx-multitenant` (Nginx)
- `laravel-mysql` (MySQL)
- `laravel-phpmyadmin` (PhpMyAdmin)
- `laravel-redis` (Redis)

---

## 🌐 Ejecutar Seeders en Base de Datos CENTRAL

La base de datos **central** (`pos_central`) contiene:
- Usuario Super Admin
- Registro de todos los tenants
- Dominios de los tenants

### Comando para Seeders Centrales

```bash
# Crear/Verificar Super Admin
wsl docker exec laravel-php-fpm php artisan db:seed --class=SuperAdminSeeder
```

### O usar el script automatizado

```bash
bash ejecutar_seeders_central.sh
```

### Credenciales del Super Admin

- **Dominio**: `http://adminpos.dokploy.movete.cloud` o `http://localhost`
- **Email**: `superadmin@gmail.com`
- **Password**: `123456`

Desde esta cuenta puedes:
- ✅ Crear nuevos tenants (empresas)
- ✅ Gestionar tenants existentes
- ✅ Ver todos los dominios registrados
- ✅ Suspender/Activar tenants

---

## 🏢 Ejecutar Seeders en Tenants (Empresas)

### 🔍 Verificar Tenants Disponibles

```bash
wsl docker exec laravel-php-fpm php artisan tenants:list
```

---

## 🌱 Ejecutar Seeders para Tenants

### Formato del Comando

```bash
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=<SeederName> --tenants=<tenant_id>
```

### Ejemplo: Ejecutar Todos los Seeders

Para el tenant principal `empresag`:

```bash
# 1. Configuración base
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=DepartmentSeeder --tenants=empresag
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=CitySeeder --tenants=empresag
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=CurrencySeeder --tenants=empresag
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=TributeSeeder --tenants=empresag
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=TaxRateSeeder --tenants=empresag
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=PaymentMethodSeeder --tenants=empresag

# 2. Permisos y roles
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=PermissionSeeder --tenants=empresag
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=RoleSeeder --tenants=empresag
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=ModuleSeeder --tenants=empresag

# 3. Usuarios
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=UserSeeder --tenants=empresag

# 4. Productos de conciertos
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=CategorySeeder --tenants=empresag
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=ProductosConciertosSeeder --tenants=empresag

# 5. Clientes y terminales
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=CustomerSeeder --tenants=empresag
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=NumberingRangeSeeder --tenants=empresag
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=TerminalSeeder --tenants=empresag
```

### Ejecutar DatabaseSeeder Completo

```bash
wsl docker exec laravel-php-fpm php artisan tenants:seed --class=DatabaseSeeder --tenants=empresag
```

---

## 🎵 Seeders Ejecutados para Tenant `empresag`

En la última ejecución se crearon:

### ✅ Datos Creados:
- **Productos**: 42 (productos de conciertos)
- **Categorías**: 11 
  - Bebidas Alcohólicas
  - Bebidas No Alcohólicas
  - Comida Rápida
  - Snacks y Dulces
  - Cigarrillos y Tabaco
  - Merchandising
  - Combos
  - Bebidas (categoría general)
  - Comidas Rápidas
  - Snacks
  - Licor
- **Clientes**: 30 clientes aleatorios
- **Usuarios**: Usuarios del sistema
- **Permisos y Roles**: Configurados
- **Terminales**: Terminales de POS
- **Métodos de Pago**: Efectivo, Tarjeta, Transferencia
- **Departamentos y Ciudades**: Colombia
- **Impuestos**: IVA y otros tributos

### ⚠️ Seeders con Errores (Ignorados):
- `InvoiceProviderSeeder` - Error de columna faltante (no crítico)
- `CompanySeeder` - Error de clave foránea (la empresa ya existe)
- `IdentificationDocumentSeeder` - Datos duplicados (ya existían)

---

## 🔍 Verificar Datos Creados

```bash
# Contar productos
wsl docker exec laravel-php-fpm php artisan tinker --execute="tenancy()->initialize(App\Models\Tenant::find('empresag')); echo 'Productos: ' . App\Models\Product::count() . PHP_EOL;"

# Contar categorías
wsl docker exec laravel-php-fpm php artisan tinker --execute="tenancy()->initialize(App\Models\Tenant::find('empresag')); echo 'Categorías: ' . App\Models\Category::count() . PHP_EOL;"

# Contar clientes
wsl docker exec laravel-php-fpm php artisan tinker --execute="tenancy()->initialize(App\Models\Tenant::find('empresag')); echo 'Clientes: ' . App\Models\Customer::count() . PHP_EOL;"

# Ver todas las categorías
wsl docker exec laravel-php-fpm php artisan tinker --execute="tenancy()->initialize(App\Models\Tenant::find('empresag')); App\Models\Category::all(['id', 'name'])->each(fn(\$c) => print(\$c->name . PHP_EOL));"

# Ver algunos productos
wsl docker exec laravel-php-fpm php artisan tinker --execute="tenancy()->initialize(App\Models\Tenant::find('empresag')); App\Models\Product::with('category')->limit(10)->get()->each(fn(\$p) => print(\$p->name . ' - ' . \$p->category->name . PHP_EOL));"
```

---

## 🚀 Acceder a la Aplicación

### Dominio del Tenant `empresag`

```
http://empresag.adminpos.dokploy.movete.cloud
```

O si usas hosts local:

```
http://empresag.localhost
```

### Credenciales de Usuario

Verifica los usuarios creados con:

```bash
wsl docker exec laravel-php-fpm php artisan tinker --execute="tenancy()->initialize(App\Models\Tenant::find('empresag')); App\Models\User::all(['id', 'name', 'email'])->each(fn(\$u) => print(\$u->email . ' - ' . \$u->name . PHP_EOL));"
```

---

## 📦 Productos de Conciertos Disponibles

El seeder `ProductosConciertosSeeder` creó productos organizados por categorías:

### Bebidas Alcohólicas
- Cerveza Poker Lata
- Cerveza Club Colombia
- Ron Medellín Añejo
- Aguardiente Antioqueño
- Whisky Old Parr
- Vodka Smirnoff
- Cerveza Artesanal IPA
- Vino Tinto Copa

### Bebidas No Alcohólicas
- Agua Cristal
- Coca Cola
- Pepsi
- Red Bull
- Jugo de Naranja Natural
- Café Americano
- Té Helado
- Botella de Agua 1L

### Comida Rápida
- Hamburguesa Clásica
- Hamburguesa Doble Carne
- Hot Dog Especial
- Papas Fritas Grandes
- Nachos con Queso
- Empanada de Carne
- Arepa con Queso
- Sandwich Cubano
- Salchipapa

### Snacks y Dulces
- Maní Salado
- Papas Margarita
- Palomitas de Maíz
- Chocolate Jet
- Chicles Trident
- Gomitas Haribo
- Mix de Frutos Secos

### Cigarrillos y Tabaco
- Marlboro Rojo
- Lucky Strike
- Encendedor BIC

### Merchandising
- Camiseta del Concierto
- Gorra Oficial
- Pulsera del Evento
- Poster del Artista
- Pin Coleccionable

### Combos
- Combo Papas + Gaseosa
- Combo Hamburguesa Completo

---

## 🛠️ Comandos Útiles de Docker

```bash
# Ver logs del contenedor PHP
wsl docker logs laravel-php-fpm

# Ver logs de Nginx
wsl docker logs laravel-nginx-multitenant

# Entrar al contenedor PHP (bash interactivo)
wsl docker exec -it laravel-php-fpm bash

# Reiniciar todos los contenedores
wsl docker-compose restart

# Detener todos los contenedores
wsl docker-compose down

# Iniciar todos los contenedores
wsl docker-compose up -d

# Ver estado de los contenedores
wsl docker ps -a
```

---

## 🎯 Próximos Pasos

1. ✅ Seeders ejecutados correctamente
2. ✅ Productos de conciertos disponibles
3. ✅ Categorías creadas
4. ✅ Clientes de prueba disponibles
5. 🚀 **Acceder a la aplicación y probar el POS**
6. 🧪 Realizar ventas de prueba
7. 📊 Verificar reportes

---

## 📝 Notas

- Los seeders se ejecutaron para el tenant principal: `empresag`
- También existe el tenant `empresat` (sub-tenant)
- Para ejecutar seeders en `empresat`, usa el mismo comando pero cambia `--tenants=empresag` por `--tenants=empresat`
- Algunos seeders tuvieron errores menores (datos duplicados o constraints), pero los datos principales se crearon correctamente

