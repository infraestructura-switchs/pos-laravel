# 04. Modelo de Datos (Entidades, Campos y Relaciones)

Esta SSD documenta el modelo de datos “operacional” que React debe consumir/duplicar. Los campos listados provienen de:

- migraciones en `database/migrations/*`
- modelos Eloquent en `app/Models/*`
- validaciones/uso en `app/Services/*` y componentes Livewire de negocio

## 1. Tenancy (Central y Tenant)

### `tenants` (central)

- `id` (string, PK)
- `name` (string)
- `email` (string, unique)
- `data` (json, nullable)
- `status` (enum: `active|suspended|inactive`, default `active`)
- `suspended_at` (timestamp, nullable)

### `domains` (central)

- `id` (int, PK)
- `domain` (string, unique)
- `tenant_id` (string, FK -> `tenants.id`)

## 2. Autorización y módulos

### `modules`

- `id`
- `name` (string)
- `is_enabled` (boolean)
- `timestamps`

Uso:
- Middleware web `module:<name>` valida que el módulo exista/esté habilitado y que el usuario tenga permiso para ese módulo.
- `ModuleService` cachea el listado de módulos.

### Roles/Permisos (Spatie)

- Se usa `spatie/laravel-permission` (no se listan tablas porque la SSD se centra en contratos del producto).
- En API se gestionan roles desde `RoleController` (ver `05-reglas-negocio.md` y `06-api-rest.md`).

## 3. Usuarios y personal

### `staff`

- `id`
- `no_identification` (unique)
- `names`
- `direction` (nullable)
- `phone` (nullable)
- `email` (nullable, unique)
- `description` (nullable)
- `status` (enum 0/1)
- `timestamps`

### `providers`

- `id`
- `no_identification` (unique)
- `name`
- `phone` (nullable)
- `direction` (nullable)
- `type` (enum nullable; ver `App\\Enums\\TypesProviders`)
- `description` (nullable)
- `status` (enum 0/1)
- `timestamps`

## 4. Empresa, identificación y catálogos legales

### `companies`

- `id`
- `nit`
- `name`
- `direction` (nullable)
- `phone` (nullable)
- `email` (nullable)
- `type_bill` (enum `0|1`: 0=factura, 1=ticket)
- `barcode` (enum `0|1`: 0=pistola, 1=manual)
- `timestamps`

### `identification_documents`

- `id`
- `code` (unique)
- `name` (unique)
- `is_enabled` (boolean)
- `timestamps`

## 5. Terminales y numeración

### `numbering_ranges`

- `id`
- `prefix` (string)
- `from` (bigint)
- `to` (bigint)
- `current` (bigint)
- `resolution_number` (nullable)
- `expire` (date)
- `status` (enum `0|1`)
- `softDeletes`

### `terminals`

- `id`
- `name` (unique)
- `numbering_range_id` (FK -> `numbering_ranges.id`)
- `factus_numbering_range_id` (nullable)
- `status` (enum `0|1`)
- `timestamps`

Relación:
- `terminals` <-> `users` (many-to-many, sync/syncWithoutDetaching en `TerminalService`).
- `cash_openings` y `cash_closings` referencian `terminal_id`.

## 6. Impuestos, tributos y precios

### `tributes`

- `id`
- `name`
- `description`
- `api_tribute_id` (nullable, manejado en `TributeService`)
- `status` (0/1)
- `timestamps`

### `tax_rates`

- `id`
- `name`
- `rate` (decimal(4,2) en la migración original)
- `has_percentage` (boolean, default 1)
- `default` (boolean, default 0)
- `status` (enum `0|1`)
- `tribute_id` (nullable FK -> `tributes.id`)
- `timestamps`

Relación:
- `tax_rates` <-> `products` vía pivot `product_tax_rate` con `value`.

### `product_tax_rate` (pivot)

- `id`
- `product_id` (FK -> `products.id`)
- `tax_rate_id` (FK -> `tax_rates.id`)
- `value` (unsignedInteger, default 0)
- `timestamps`

### `categories`

- `id`
- `name` (unique)
- `timestamps`

### `products`

Campos principales (derivados de migraciones y uso en `ProductService`):

- `id`
- `barcode` (unique)
- `reference` (unique)
- `name`
- `description` (nullable; agregado en migraciones adicionales)
- `cost` (integer)
- `price` (integer)
- `stock` (integer, default 0)
- `quantity` (integer; unidades x producto cuando tiene presentaciones)
- `units` (bigint; unidades cuando tiene presentaciones)
- `has_inventory` (enum `0|1`; 0 tiene inventario, 1 no)
- `has_presentations` (enum `0|1`; 0 cuenta con presentaciones, 1 no)
- `top` (enum `0|1`)
- `status` (enum `0|1`)
- `category_id` (nullable FK -> `categories.id`)
- `cloudinary_public_id` (usado por accessors, no se lista columna exacta en migraciones leídas)

Relaciones:
- `products` -> `category` (belongsTo)
- `products` -> `presentations` (hasMany)
- `products` <-> `tax_rates` (belongsToMany con pivot `product_tax_rate` y campo `value`)

### `presentations`

- `id`
- `name`
- `price`
- `quantity`
- `status` (0/1)
- `product_id` (FK -> `products.id`)
- `timestamps`

## 7. Clientes y ventas

### `customers`

Campos principales usados por `CustomerService`:

- `id`
- `no_identification` (unique)
- `names`
- `identification_document_id` (FK -> `identification_documents.id`)
- `dv` (string(1), nullable)
- `legal_organization` (enum; ver `App\\Enums\\LegalOrganization`)
- `tribute` (enum; ver `App\\Enums\\CustomerTributes`)
- `direction` (nullable)
- `phone` (nullable)
- `email` (nullable)
- `top` (enum 0/1)
- `status` (enum 0/1)
- `timestamps`

### `payment_methods`

- `id`
- `name`
- `status` (enum `0|1`)
- `code` (nullable; para enviar a Factus)
- `timestamps`

### `orders` (venta rápida “mesa”)

Columnas (derivadas de migraciones):

- `id`
- `name` (antes `name_order`, renombrado)
- `products` (json)
- `customer` (json)
- `total` (unsignedInteger)
- `is_active` (boolean)
- `delivery_address` (nullable string)
- `timestamps`

Relación:
- `Order.products` y `Order.customer` se guardan como JSON; no hay tabla detalle separada.

## 8. Facturas y facturación electrónica

### `bills`

Campos (base + migraciones de evolución):

- `id`
- `subtotal` (unsignedInteger)
- `iva` (unsignedInteger; renombrado desde `tax`)
- `inc` (unsignedInteger; añadido en migraciones)
- `has_iva` (boolean)
- `has_inc` (boolean)
- `discount` (unsignedInteger)
- `total` (unsignedInteger)
- `tip` (unsignedInteger, default 0)
- `cost` (unsignedInteger, default 0)
- `status` (enum `0|1` con comentario “0 activa y 1 anulada” en migración original)
- `customer_id` (FK)
- `user_id` (FK)
- `payment_method_id` (FK)
- `terminal_id` (FK -> `terminals.id`, default 1)
- `numbering_range_id` (nullable FK)
- `number` (string, nullable, unique)
- `reference_code` (string, nullable, unique; añadido)
- `observation` (text, nullable)
- `pdf_url` (text/nullable, añadido)
- `created_at/updated_at`

Relaciones:
- `bills` -> `detail_bills` (hasMany)
- `bills` -> `customer`, `user`, `paymentMethod`, `terminal`, `numberingRange` (belongsTo)
- `bills` -> `finance` (hasOne)
- `bills` -> `electronic_bill` (hasOne `ElectronicBill`)
- `bills` -> `electronic_credit_note` (hasOne `ElectronicCreditNote`)
- `bills` -> `document_taxes` (morphMany `DocumentTax`)

### `detail_bills`

- `id`
- `name` (string)
- `rate` (decimal(4,2) con comentario “porcentaje del impuesto”)
- `amount` (unsignedInteger)
- `discount` (unsignedInteger)
- `tribute` (string, añadida)
- `tax` (unsignedInteger)
- `price` (unsignedInteger)
- `total` (unsignedInteger)
- `cost` (unsignedInteger, default 0)
- `presentation` (json)
- `bill_id` (FK -> `bills.id`)
- `product_id` (FK -> `products.id`)
- `timestamps`

Relaciones:
- `detail_bills` -> `bill` (belongsTo)
- `detail_bills` -> `product` (belongsTo)
- `detail_bills` -> `document_taxes` (morphMany)

### `electronic_bills`

- `id`
- `number` (string)
- `qr_image` (text nullable)
- `cufe` (string nullable)
- `numbering_range` (json nullable)
- `is_validated` (boolean default 0)
- `bill_id` (FK -> `bills.id`)
- `timestamps`

### `electronic_credit_notes`

- `id`
- `number` (string)
- `qr_image` (text nullable)
- `cude` (string nullable; typo en migración)
- `is_validated` (boolean default 0)
- `bill_id` (FK -> `bills.id`)
- `timestamps`

## 9. Impuestos calculados por documento

### `document_taxes`

- `id`
- `tribute_name`
- `tax_amount` (unsignedDecimal(12))
- `morphs('document_taxeable')`

### `document_tax_rates`

- `id`
- `has_percentage` (boolean)
- `rate` (unsignedDecimal(8,2))
- `taxable_amount` (unsignedDecimal(12))
- `tax_amount` (unsignedDecimal(12))
- `document_tax_id` (FK -> `document_taxes.id`)

## 10. Finanzas (vencimientos/financiación de facturas)

### `finances`

- `id`
- `bill_id` (FK)
- `status` (enum '0'|'1'; 1 pendiente, 0 pagado)
- `timestamps`

### `detail_finances`

- `id`
- `value` (unsignedInteger)
- `finance_id` (FK)
- `timestamps`

## 11. Caja y ventas diarias

### `cash_openings`

- `id`
- `initial_cash`
- `initial_coins`
- `tarjeta_credito` (nullable, default 0)
- `tarjeta_debito` (default 0)
- `cheques` (default 0)
- `otros` (default 0)
- `total_initial`
- `observations` (nullable)
- `user_id`
- `terminal_id`
- `is_active` (boolean default true)
- `opened_at` (timestamp default current)

### `cash_closings`

- `id`
- `base`
- `cash`
- `debit_card`
- `credit_card`
- `transfer`
- `financed`
- `outputs`
- `cash_register`
- `price`
- `observations` (nullable)
- `terminal_id` (nullable/default en migraciones adicionales)
- `user_id`
- `cash_opening_id` (nullable FK, añadido en migración adicional)
- `timestamps`

### `daily_sales`

- `id`
- `creation_date` (date)
- `terminal` (string)
- `from` (unsignedInteger)
- `to` (unsignedInteger)
- `subtotal_amount`
- `discount_amount`
- `inc_amount`
- `iva_amount`
- `exempt_amount`
- `excluded_amount`
- `total_amount`
- `timestamps`

### `daily_sale_details`

- `id`
- `total` (unsignedInteger)
- `payment_method_id` (FK)
- `timestamps`

## 12. Inventario: remisiones, transferencias, movimientos

### `warehouses`

- `id`
- `name`
- `address` (nullable)
- `phone` (nullable)
- `timestamps`

### `inventory_remissions`

- `id`
- `warehouse_id` (FK)
- `user_id` (FK)
- `folio`
- `remission_date` (dateTime)
- `concept`
- `note` (nullable)
- `timestamps`

### `remission_details`

- `id`
- `remission_id` (FK -> `inventory_remissions`)
- `product_id` (FK -> `products`)
- `quantity` (decimal(12,3))
- `unit_cost` (decimal(12,2))
- `total_cost` (decimal(14,2))
- `timestamps`

### `warehouse_transfers`

- `id`
- `origin_warehouse_id` (FK)
- `destination_warehouse_id` (FK)
- `user_id` (nullable FK)
- `transfer_date` (date)
- `status` (`pending|completed|cancelled`, default `pending`)
- `folio` (unique)
- `note` (nullable)
- `timestamps`

### `warehouse_transfer_details`

- `id`
- `warehouse_transfer_id` (FK)
- `product_id` (FK)
- `quantity` (decimal(10,2))
- `unit_cost` (decimal(10,2))
- `total_cost` (decimal(12,2))
- `timestamps`

### `stock_movements`

- `id`
- `warehouse_id` (FK)
- `remission_id` (nullable FK)
- `movement_type` (`IN|OUT`)
- `user_id` (nullable FK)
- `stock_movements_date` (date)
- `folio` (unique)
- `concept` (nullable)
- `note` (nullable)
- `timestamps`

### `stock_movements_detail`

- `id`
- `stock_movements_id` (FK)
- `product_id` (FK)
- `movement_type` (`IN|OUT`, default IN)
- `quantity` (decimal(10,2))
- `unit_cost` (decimal(10,2) nullable)
- `total_cost` (decimal(10,2) nullable)
- `timestamps`

## 13. Compras, egresos y nómina

### `purchases`

- `id`
- `total`
- `status` (0 activo, 1 anulado)
- `provider_id` (FK)
- `timestamps`

### `purchase_details`

- `id`
- `amount`
- `cost`
- `total`
- `product_id` (FK)
- `purchase_id` (FK)
- `timestamps`

### `outputs`

- `id`
- `date` (date)
- `reason`
- `price` (nullable)
- `description` (nullable)
- `user_id` (FK)
- `timestamps`

### `payrolls`

- `id`
- `price`
- `description`
- `staff_id` (FK)
- `user_id` (FK)
- `timestamps`

## 14. Notas para implementación React

1. Los campos monetarios del sistema son enteros/decimales según migraciones (p.ej. impuestos y costos en detalle).
2. Algunos campos se guardan como JSON (`orders.products`, `orders.customer`, `detail_bills.presentation`, `electronic_bills.numbering_range`).
3. En inventario existe una lógica condicional `has_inventory` y `has_presentations` que cambia cómo se decrementa/valida stock.

