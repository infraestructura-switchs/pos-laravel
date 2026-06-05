# 05. Reglas de Negocio (y validaciones críticas)

## 1. Autorización por módulos (web)

- Middleware `HasModule`:
  - Si `isRoot()` o `$user->is_root == 1`, el usuario puede acceder a todo.
  - En caso contrario, si el módulo no existe o el usuario no tiene permiso `hasPermissionTo($module)`, responde `abort(404)`.
- `LoadUserPermissions`:
  - Cachea permisos del usuario por tenant durante 30 minutos.

## 2. Concurrencia

- Creación de factura:
  - Livewire (`Admin\Bills\Create`) usa `Cache::lock('createBill', 30)` para evitar doble creación concurrente en la UI.
  - Persistencia en transacción (`DB::beginTransaction/commit/rollback`).

## 3. Reglas de inventario (BillService)

El sistema diferencia si el producto maneja inventario con `products.has_inventory`:

- `has_inventory == 0`: el sistema valida stock/unidades y decrementa.
- `has_inventory == 1`: el sistema NO valida/decrementa inventario.

Además, `products.has_presentations` controla la unidad de decremento/validación:

- `has_presentations == '0'`: el producto tiene presentaciones. Se valida/decrementa por presentaciones (usando `presentation.quantity`).
- `has_presentations == '1'`: el producto NO tiene presentaciones. Se valida/decrementa por `stock`/`units` directos.

### 3.1 `validateInventory(products, productsDB)`

1. Para cada producto en DB:
   - Si `!has_inventory`:
     - Si `has_presentations === '0'`:
       - Convierte cantidades a “unidades” sumando:
         - si la presentación del input coincide, `quantity += presentation.quantity * item.amount`
       - Valida `quantity <= productDB.units` (si excede, error).
     - Si `has_presentations !== '0'`:
       - Suma `amount` desde el input y valida `amount <= productDB.stock`.
2. Verifica descuento por producto:
   - Si `discount > (price * amount)`, lanza error.

### 3.2 `addCostToItems(products, productsDB)`

- Para cada ítem:
  - Si el producto no maneja presentaciones (`!has_presentations`):
    - Calcula costo por unidad con:
      - `costForUnit = product.cost / product.quantity`
      - `units = presentation.quantity * item.amount`
      - `item.cost = costForUnit * units`
  - Si el producto tiene costo directo:
    - `item.cost = product.cost * item.amount`

### 3.3 `calcTotales(products, productsDB)`

- Por ítem:
  - Si tiene presentaciones:
    - `price = presentation.price`
    - `total = (presentation.price * amount) - discount`
  - Si no tiene:
    - `price = product.price`
    - `total = (product.price * amount) - discount`

### 3.4 Decremento de stock/unidades

- `updateUnitsOrStock(product, productsDB)` decrementa:
  - Si `has_inventory == 1`: retorna sin cambios.
  - Si no tiene inventario por presentaciones:
    - Decrementa `Product.units` por `presentation.quantity * amount`
  - Si no tiene presentaciones:
    - Decrementa `Product.stock` por `amount`

- `updateStock(productsDB)` ajusta `stock` cuando el producto usa presentaciones:
  - si `!has_presentations`, recalcula `stock = (units / quantity)`

## 4. Facturación (Bills y electrónicos)

### 4.1 `BillService::store(...)`

Campos y cálculos principales:

- `reference_code`: UUID.
- `terminal_id`: `getTerminal()->id`.
- Totales:
  - `taxRate`: suma de `tax_amount` calculados en `tax_rates` de cada ítem.
  - `subtotal = (products.sum(total) - taxRate) + products.sum(discount)`
  - `discount = products.sum(discount)`
  - `total = products.sum(total)`
  - `tip` y `cash` se guardan tal cual.
- Estado:
  - Se crea con `status => 1` en `BillService::store()`.

Nota importante para implementación:

- Migración y modelo indican `status`:
  - `0` = activa
  - `1` = anulada
- Sin embargo, el flujo web (`cancelBill`) asume que `status === '1'` significa “ya anulada”.
- Esto sugiere que hay una inconsistencia a revisar/corregir en el código para replicar el comportamiento esperado.

### 4.2 Validación facturación electrónica

`BillService::validateElectronicBill(bill)`:

- Si Factus está habilitado: llama `ElectronicBillService::validate($bill)` y guarda `ElectronicBill`.
- Si Factro está habilitado: llama `FactroElectronicBillService::validate($bill)` y guarda `ElectronicBill`.

### 4.3 Nota crédito electrónica (anulación)

`BillService::storeElectronicCreditNote(bill)`:

- Si existe `electronicBill` y no existe `electronicCreditNote`:
  - crea nota crédito con servicios Factus o Factro según habilitación.

`BillService::validateElectronicCreditNote(bill)`:

- Refresca `bill` y valida la nota crédito con los servicios correspondientes.

## 5. Validaciones de servicios (API)

- `ProductService`:
  - `cost < price` (si `cost >= price` lanza error).
  - `presentations` solo aplica cuando `has_presentations == 0`.
  - Valida impuestos (`tax_rates.*.id` existe, `tax_rates.*.value` numérico).
  - Import de productos: solo si `Bill::count() === 0` y `Purchase::count() === 0`.
- `CustomerService`:
  - Obligatoriedad de `identification_document_id`, `legal_organization`, `tribute`, `no_identification`.
  - Valida `dv` requerido si `identification_document_id == 6`.
  - `names` min 5.
- `TerminalService`:
  - `verifyTerminal()`: falla si el usuario no tiene terminales activas.
- `RoleService`:
  - No permite editar el rol “Administrador”.

