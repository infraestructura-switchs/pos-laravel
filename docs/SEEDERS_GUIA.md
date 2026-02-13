# 📦 Guía de Seeders - Sistema POS Multitenant

## 📋 Lista de Seeders Disponibles

### 🔧 Seeders de Configuración Base
1. **DepartmentSeeder** - Departamentos de Colombia
2. **CitySeeder** - Ciudades de Colombia
3. **CurrencySeeder** - Monedas (COP, USD, etc.)
4. **InvoiceProviderSeeder** - Proveedores de facturación electrónica
5. **TributeSeeder** - Tributos/impuestos
6. **TaxRateSeeder** - Tasas de impuestos
7. **PaymentMethodSeeder** - Métodos de pago (Efectivo, Tarjeta, Transferencia)
8. **IdentificationDocumentSeeder** - Tipos de documentos de identificación
9. **TerminalSeeder** - Terminales de punto de venta
10. **NumberingRangeSeeder** - Rangos de numeración para facturas

### 👥 Seeders de Usuarios y Permisos
11. **PermissionSeeder** - Permisos del sistema
12. **RoleSeeder** - Roles (Administrador, Vendedor, etc.)
13. **UserSeeder** - Usuarios del sistema
14. **ModuleSeeder** - Módulos del sistema

### 🏢 Seeders de Empresa
15. **CompanySeeder** - Datos de la empresa/tenant
16. **StaffSeeder** - Personal/empleados
17. **PayrollSeeder** - Nómina

### 🛒 Seeders de Productos y Ventas
18. **CategorySeeder** - Categorías de productos (Bebidas, Comidas Rápidas, Snacks, Licor)
19. **ProductSeeder** - Productos (50 productos aleatorios + Ibuprofeno y Aspirina con presentaciones)
20. **ProductosConciertosSeeder** - Productos especiales para conciertos

### 👤 Seeders de Clientes y Proveedores
21. **CustomerSeeder** - Clientes (30 clientes aleatorios)
22. **ProviderSeeder** - Proveedores

### 📊 Seeders de Operaciones
23. **OrderSeeder** - Órdenes de venta
24. **PurchaseSeeder** - Compras
25. **PurchaseDetailSeeder** - Detalles de compras
26. **BillSeeder** - Facturas
27. **InventoryRemissionSeeder** - Remisiones de inventario
28. **OutputSeeder** - Salidas de inventario (comentado en DatabaseSeeder)

### ⚙️ Seeders de Configuración Avanzada
29. **FactusConfigurationSeeder** - Configuración Factus
30. **FactroConfigurationSeeder** - Configuración Factro
31. **InitialSetupSeeder** - Configuración inicial
32. **SuperAdminSeeder** - Usuario super administrador

### 🎯 Seeder Principal
33. **DatabaseSeeder** - Seeder principal que ejecuta todos los demás en orden

---

## 🚀 Cómo Ejecutar Seeders por Tenant

### Opción 1: Ejecutar DatabaseSeeder Completo (Recomendado)

Ejecuta todos los seeders en el orden correcto para un tenant específico:

```bash
# Para un tenant específico
php artisan tenants:seed --class=DatabaseSeeder --tenants=empresa1

# Para múltiples tenants
php artisan tenants:seed --class=DatabaseSeeder --tenants=empresa1,empresa2

# Para todos los tenants
php artisan tenants:seed --class=DatabaseSeeder --tenants=all
```

### Opción 2: Ejecutar un Seeder Específico

```bash
# Ejecutar solo ProductSeeder para un tenant
php artisan tenants:seed --class=ProductSeeder --tenants=empresa1

# Ejecutar CategorySeeder y ProductSeeder
php artisan tenants:seed --class=CategorySeeder --tenants=empresa1
php artisan tenants:seed --class=ProductSeeder --tenants=empresa1
```

### Opción 3: Usar Tinker (Para Desarrollo)

```bash
php artisan tinker
```

```php
// Ejecutar seeders dentro del contexto de un tenant
$tenant = App\Models\Tenant::find('empresa1');
tenancy()->initialize($tenant);

// Ejecutar seeder
Artisan::call('db:seed', ['--class' => 'ProductSeeder']);

// O directamente
$seeder = new \Database\Seeders\ProductSeeder();
$seeder->run();
```

---

## 🎯 Seeders Necesarios para Probar el POS

Para tener un sistema POS completamente funcional, necesitas ejecutar estos seeders en orden:

### Orden de Ejecución Recomendado:

1. **Configuración Base** (obligatorio):
   ```bash
   php artisan tenants:seed --class=DepartmentSeeder --tenants=empresa1
   php artisan tenants:seed --class=CitySeeder --tenants=empresa1
   php artisan tenants:seed --class=CurrencySeeder --tenants=empresa1
   php artisan tenants:seed --class=InvoiceProviderSeeder --tenants=empresa1
   php artisan tenants:seed --class=TributeSeeder --tenants=empresa1
   php artisan tenants:seed --class=TaxRateSeeder --tenants=empresa1
   php artisan tenants:seed --class=PaymentMethodSeeder --tenants=empresa1
   php artisan tenants:seed --class=IdentificationDocumentSeeder --tenants=empresa1
   ```

2. **Permisos y Roles** (obligatorio):
   ```bash
   php artisan tenants:seed --class=PermissionSeeder --tenants=empresa1
   php artisan tenants:seed --class=RoleSeeder --tenants=empresa1
   php artisan tenants:seed --class=ModuleSeeder --tenants=empresa1
   ```

3. **Empresa y Usuarios** (obligatorio):
   ```bash
   php artisan tenants:seed --class=CompanySeeder --tenants=empresa1
   php artisan tenants:seed --class=UserSeeder --tenants=empresa1
   ```

4. **Productos y Categorías** (para probar ventas):
   ```bash
   php artisan tenants:seed --class=CategorySeeder --tenants=empresa1
   php artisan tenants:seed --class=ProductSeeder --tenants=empresa1
   ```

5. **Clientes** (para probar ventas):
   ```bash
   php artisan tenants:seed --class=CustomerSeeder --tenants=empresa1
   ```

6. **Terminales y Configuración** (para POS):
   ```bash
   php artisan tenants:seed --class=NumberingRangeSeeder --tenants=empresa1
   php artisan tenants:seed --class=TerminalSeeder --tenants=empresa1
   ```

### ⚡ Comando Rápido (Todo en Uno)

La forma más fácil es ejecutar el `DatabaseSeeder` completo:

```bash
php artisan tenants:seed --class=DatabaseSeeder --tenants=empresa1
```

Esto ejecutará todos los seeders en el orden correcto.

---

## 🎵 Seeders para Productos de Conciertos

Si necesitas productos específicos para eventos y conciertos, usa el seeder especializado:

### Opción 1: Script Automático (Recomendado)

```bash
php ejecutar_seeders_conciertos.php empresap
```

Este script ejecuta todos los seeders necesarios en orden, incluyendo `ProductosConciertosSeeder`.

### Opción 2: Ejecutar Manualmente

Primero los seeders base, luego el de conciertos:

```bash
# Seeders base (obligatorios)
php artisan tenants:seed --class=DepartmentSeeder --tenants=empresap
php artisan tenants:seed --class=CitySeeder --tenants=empresap
php artisan tenants:seed --class=CurrencySeeder --tenants=empresap
php artisan tenants:seed --class=InvoiceProviderSeeder --tenants=empresap
php artisan tenants:seed --class=TributeSeeder --tenants=empresap
php artisan tenants:seed --class=TaxRateSeeder --tenants=empresap
php artisan tenants:seed --class=PaymentMethodSeeder --tenants=empresap
php artisan tenants:seed --class=IdentificationDocumentSeeder --tenants=empresap
php artisan tenants:seed --class=PermissionSeeder --tenants=empresap
php artisan tenants:seed --class=RoleSeeder --tenants=empresap
php artisan tenants:seed --class=ModuleSeeder --tenants=empresap
php artisan tenants:seed --class=CompanySeeder --tenants=empresap
php artisan tenants:seed --class=UserSeeder --tenants=empresap

# Productos de conciertos
php artisan tenants:seed --class=ProductosConciertosSeeder --tenants=empresap

# Opcionales pero recomendados
php artisan tenants:seed --class=CustomerSeeder --tenants=empresap
php artisan tenants:seed --class=NumberingRangeSeeder --tenants=empresap
php artisan tenants:seed --class=TerminalSeeder --tenants=empresap
```

### 📦 Productos Creados por ProductosConciertosSeeder

El seeder `ProductosConciertosSeeder` crea:

- **Bebidas Alcohólicas**: Cerveza Poker, Club Colombia, Ron Medellín, Aguardiente, Whisky, Vodka, Cerveza Artesanal, Vino
- **Bebidas No Alcohólicas**: Agua, Coca Cola, Pepsi, Red Bull, Jugos, Café, Té, Botellas de agua
- **Comida Rápida**: Hamburguesas, Hot Dogs, Papas Fritas, Nachos, Empanadas, Arepas, Sandwiches, Salchipapas
- **Snacks y Dulces**: Maní, Papas Margarita, Palomitas, Chocolate, Chicles, Gomitas, Frutos Secos
- **Cigarrillos y Tabaco**: Marlboro, Lucky Strike, Encendedores
- **Merchandising**: Camisetas, Gorras, Pulseras, Posters, Pines
- **Combos**: Combo Papas + Gaseosa, Combo Hamburguesa Completo

Todos los productos incluyen:
- ✅ Categorías específicas para conciertos
- ✅ Códigos de barras
- ✅ Precios de costo y venta
- ✅ Stock inicial
- ✅ Presentaciones por defecto
- ✅ Tasas de impuestos asignadas

---

## 📝 Verificar Tenants Disponibles

Antes de ejecutar seeders, verifica qué tenants tienes:

```bash
php artisan tenants:list
```

O usando Tinker:

```bash
php artisan tinker
```

```php
App\Models\Tenant::all(['id', 'name', 'email', 'status'])->toArray();
```

---

## ⚠️ Notas Importantes

1. **Orden de Ejecución**: Algunos seeders dependen de otros. Por ejemplo:
   - `ProductSeeder` necesita que `CategorySeeder` y `TaxRateSeeder` ya se hayan ejecutado
   - `TerminalSeeder` necesita que `NumberingRangeSeeder` y `UserSeeder` ya se hayan ejecutado

2. **Datos de Prueba**: 
   - `ProductSeeder` crea 50 productos aleatorios + 2 productos específicos (Ibuprofeno y Aspirina) con presentaciones
   - `CustomerSeeder` crea 30 clientes aleatorios
   - El `DatabaseSeeder` también crea un cliente "Consumidor Final" automáticamente

3. **Re-ejecutar Seeders**: 
   - Algunos seeders usan `updateOrCreate`, por lo que se pueden ejecutar múltiples veces sin duplicar datos
   - Otros seeders pueden crear datos duplicados si se ejecutan varias veces

4. **Ambiente de Producción**: 
   - En producción, usa `--force` si es necesario:
   ```bash
   php artisan tenants:seed --class=DatabaseSeeder --tenants=empresa1 --force
   ```

---

## 🔍 Verificar Datos Creados

Después de ejecutar los seeders, puedes verificar los datos:

```bash
php artisan tinker
```

```php
// Inicializar tenant
$tenant = App\Models\Tenant::find('empresa1');
tenancy()->initialize($tenant);

// Verificar productos
App\Models\Product::count();
App\Models\Product::with('category')->get();

// Verificar categorías
App\Models\Category::all();

// Verificar clientes
App\Models\Customer::count();

// Verificar métodos de pago
App\Models\PaymentMethod::all();
```

---

## 📚 Referencias

- `database/seeders/DatabaseSeeder.php` - Seeder principal
- `app/Http/Controllers/TenantRegistrationController.php` - Seeders ejecutados al crear un tenant
- `config/tenancy.php` - Configuración de parámetros para seeders

