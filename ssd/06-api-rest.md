# 06. API REST (contratos, payloads y respuestas)

La API REST se define en `routes/api.php`. La mayoría de recursos están bajo `middleware('auth:sanctum')`.

## 1. Convención de respuestas

- Éxito: `{"success": true, "message": "...", "data": ...}`
- Error: frecuentemente `success: false` con `message` y/o `errors` (varía por controlador).

## 2. Salud

- `GET /health` (pública)
  - Retorna JSON con `success`, `message` y `timestamp`.

## 3. Autenticación (Sanctum)

- `POST /auth/login` (pública)
  - Body:
    - `email`: string (email) requerido
    - `password`: string min 6 requerido
  - Respuesta:
    - `data.user`: usuario autenticado
    - `data.token`: token Bearer
    - `data.token_type`: "Bearer"
- `POST /auth/logout` (protegida)
  - Sin body (usa `request->user()`)
- `GET /auth/me` (protegida)
  - Retorna `data` con el usuario actual

## 4. Products

- `POST /products` (protegida)
  - Body (ver `ProductService::store`):
    - `barcode`: string requerida, unique
    - `reference`: string requerida, unique
    - `category_id`: int nullable
    - `name`: string min 3 max 250
    - `cost`: int min 0
    - `price`: int min 0
    - `has_inventory`: int 0/1
    - `stock`: int min 0
    - `units`: int min 0
    - `has_presentations`: int 0/1
    - `quantity`: int requerible SOLO si `has_presentations == 0` (por regla `exclude_if`)
    - `presentations`: array SOLO si `has_presentations == 0`, con min 1
      - cada item: definición exacta según `Presentation` (ver 06.1)
    - `tax_rates`: array min 1
      - `tax_rates.*.id`: int exists `tax_rates,id`
      - `tax_rates.*.value`: int/num min 0
  - Regla adicional:
    - `cost < price`
  - Respuesta: `{success:true, data:<id>}`
- `PUT /products/{id}` (protegida)
  - Body (ver `ProductService::update`):
    - Misma estructura base + `top`, `status`, y reglas para presentaciones.
    - `quantity/presentations` se requieren si `has_presentations == 0`.
- `GET /products/{id}` (protegida)
  - Respuesta: `data` con el producto
- `GET /products` (protegida)
  - Query params (según controlador):
    - `name`, `reference`, `status`, `has_inventory`
    - `sort_field`, `sort_order`, `per_page`
  - Respuesta: `data` paginado
- `POST /products/import` (protegida)
  - Form-data:
    - `file`: xlsx/xlsm max 30720 KB
  - Regla adicional:
    - Solo permitido si `Bill::count()==0` y `Purchase::count()==0`
- `GET /products/import/template` (protegida)
  - Respuesta: `data` = base64 del archivo
- `GET /products/export/excel` (protegida)
  - Respuesta: `data` = base64 del archivo

### Product Images

- `POST /products/images/upload-base64` (protegida)
  - Body:
    - `product_id` (required int exists `products.id`)
    - `file_name` (string)
    - `file_base64` (string; acepta `data:*` o base64 puro)
  - Respuesta: `data.public_id`
- `GET /products/images/{productId}` (protegida)
  - Respuesta: `data.url`
- `DELETE /products/images/{productId}` (protegida)
  - Respuesta: `{success:true, message:...}`

## 5. Categories

- `POST /categories`
  - Body: `name` (required, unique)
  - Respuesta: `{data:<id>}`
- `PUT /categories/{id}`
  - Body: `name` (required)
- `GET /categories/{id}`
- `GET /categories`
  - Filters: `name`, `per_page`, `order_by`, `order_dir`

## 6. Tax Rates

- `POST /tax_rates`
  - Body (ver `TaxRateService::create`):
    - `name`
    - `has_percentage` boolean
    - `rate` numeric min 0
    - `default` boolean
    - `tribute_id` exists `tributes.id`
  - Nota:
    - `status` no es obligatorio en `create` según reglas del servicio.
- `PUT /tax_rates/{id}`
  - Body:
    - `status` requerido (en `in:0,1`)
    - y otros campos opcionales.
- `GET /tax_rates/{id}`
- `GET /tax_rates`
  - Filters: `name`, `status`, `order_by`, `order_direction`, `per_page`

## 7. Tributes

- `POST /tributes`
  - Body:
    - `name` required
    - `description` required
    - `api_tribute_id` nullable
  - Respuesta: `{data:{id}}`
- `PUT /tributes/{id}`
  - Body:
    - `status` required (en `in:0,1`)
    - y campos opcionales
- `GET /tributes/{id}`
- `GET /tributes`
  - Filters: `name`, `status`, `api_tribute_id`, `order_by`, `order_dir`, `per_page`

## 8. Presentations

- `POST /presentations`
  - Body:
    - `name`, `price` (min 0), `quantity` (min 1), `product_id` exists
- `PUT /presentations/{id}`
  - Body opcional:
    - `name`, `price`, `quantity`, `status`, `product_id`
- `GET /presentations/{id}`
- `GET /presentations`
  - Filters: `name`, `price`, `quantity`, `status`, `product_id`, `orderBy`, `orderDir`, `perPage`

## 9. Roles

- `POST /roles` (requires auth:sanctum)
  - Body:
    - `name` required unique `roles.name`
    - `permissions` nullable array de `permissions.id`
  - Respuesta: `201` y `{"id": <id>}`
- `PUT /roles/{id}`
  - Body:
    - `name` required unique (except id)
    - `permissions` nullable array
  - Nota:
    - En `RoleService::update` no se puede editar el rol “Administrador”.
- `GET /roles/{id}` (retorna con permisos)
- `GET /roles` (filters: `name`, `guard_name`, `per_page`)

## 10. Users

- `POST /users`
  - Body:
    - `name`, `phone`, `email` (unique)
    - `role` (exists roles.name)
    - `password` min 8 max 250 + `confirmed`
    - `password_confirmation`
- `PUT /users/{id}`
  - Body (ver `UserService::update`):
    - `name`/`phone`/`email`/`status` opcionales
    - `password` nullable y `password_confirmation` está marcado como requerido por el validator (revisar al integrar).
    - `role` opcional, no editable si es “Administrador”.
- `GET /users/{id}`
- `GET /users` (filters: `name`, `phone`, `email`, `role`, `status`, `orderBy`, `orderDir`, `perPage`)

## 11. Terminals

- `GET /terminals/verify-terminal`
  - No body. Lanza error si el usuario no tiene terminales activas.
- `POST /terminals`
  - Body:
    - `name` required unique
    - `numbering_range_id` required exists
    - `factus_numbering_range_id` nullable
    - `users` nullable array de `users.id`
- `PUT /terminals/{id}`
  - Body opcional: `name`, `numbering_range_id`, `factus_numbering_range_id`, `status`, `users`
- `GET /terminals/{id}` (con usuarios)
- `GET /terminals` (filters: `name`, `status`, `numbering_range_id`, `factus_numbering_range_id`, `sort_by`, `sort_order`, `per_page`)

## 12. Customers

- `POST /customers`
  - Body (ver `CustomerService::store`):
    - `identification_document_id` required exists
    - `legal_organization` required enum
    - `tribute` required enum
    - `no_identification` required string unique (y regla `Identification`)
    - `dv` required_if document_id==6 (según validator)
    - `names` min 5 max 250
    - `direction`, `phone`, `email` opcionales (email/phone validados)
- `PUT /customers/{id}`
  - Body:
    - Mismas reglas con `no_identification` único ignorando id + `top` y `status` obligatorios por validator.
- `GET /customers/{id}`
- `GET /customers`
  - Filters: `name`, `document_number`, `email`, `status` (y paginate)
  - Nota:
    - el filtro usa `document_number` pero la columna se maneja como `no_identification` en migraciones.

## 13. Bills (facturas)

- `POST /bills`
  - Body (ver `BillService::create`):
    - `reference_code` nullable
    - `number` nullable
    - `cost` required int min 0
    - `tip` required int min 0
    - `subtotal` required int min 0
    - `discount` required int min 0
    - `total` required int min 0
    - `cash` required int min 0
    - `status` required in `0,1`
    - `observation` nullable string
    - `terminal_id` required exists
    - `customer_id` required exists
    - `user_id` required exists (importante para integrarlo)
    - `payment_method_id` required exists
    - `numbering_range_id` nullable exists
  - Respuesta: `{data:{id}}`
- `PUT /bills/{id}`
  - Body parcial; validaciones “sometimes|required”.
- `GET /bills/{id}`
- `GET /bills`
  - Filters: `reference_code`, `number`, `status`, `customer_id`, `terminal_id`, `user_id`, `payment_method_id`, `numbering_range_id`
  - + paginación con `per_page`, `order_by`, `order_dir`

### Operaciones auxiliares de cálculo/validación

- `POST /bills/get-unique-products`
  - Body: `{ "products": [ { "id": <productId> }, ... ] }`
  - Retorna productos únicos desde DB
- `POST /bills/add-cost`
  - Body:
    - `products`: array (items del frontend)
    - `products_db`: array (productos DB hidratables por `Product::hydrate`)
  - Retorna `data` con `products` actualizados
- `POST /bills/validate-inventory`
  - Body: `products`, `products_db`
  - Respuesta: `{success:true}` o error `400` con message de `CustomException`
- `POST /bills/calc-totales`
  - Body: `products`, `products_db`
- `POST /bills/update-stock`
  - Body: `products_db`
- `POST /bills/update-units-stock`
  - Body:
    - `product`: objeto con `id`, `presentation` e `amount` (según BillService usage)
    - `products_db`: array hidratable

### Validación electrónica / credit note

- `GET /bills/{id}/validate-electronic`
  - Ejecuta `BillService::validateElectronicBill`
- `GET /bills/{id}/store-credit-note`
  - Ejecuta `BillService::storeElectronicCreditNote`
- `GET /bills/{id}/validate-credit-note`
  - Ejecuta `BillService::validateElectronicCreditNote`

## 14. Company

- `GET /company/show`
  - Retorna `ModelsCompany::first()`
- `POST /company/update`
  - Body:
    - `logo` opcional (imagen png max 512 KB; dims max 500x250)
    - `company.nit`, `company.name`, `company.direction`, `company.phone`, `company.email`, `company.type_bill`, `company.barcode`
  - Nota:
    - El controlador no retorna un JSON explícito (por diseño actual). React debe tolerar respuesta vacía/200.

## 15. PDF upload de facturas a Cloudinary (API pública)

- `GET /pdf-upload/bill/{bill}`
  - No requiere Sanctum (por como está definido en `routes/api.php`).
  - Genera PDF (electrónico o estándar) y lo sube a Cloudinary.
  - Retorna:
    - `success: true`
    - `file_url` (secure_url si existe).

