# 07. Mapeo a una implementación React (insumo práctico)

## 1. Estructura recomendada en React

Propuesta de rutas/páginas (equivalentes a `routes/admin.php`):

- `/login`
- `/dashboard` (equivalente a `GET /` en admin.php)
- `/configuracion` (empresa)
- Catálogos:
  - `/clientes`
  - `/proveedores`
  - `/productos`
  - `/impuestos`
  - `/rangos-de-numeracion`
  - `/terminales`
  - `/roles-y-permisos`
  - `/usuarios`
  - `/modulos`
- Operación POS:
  - `/apertura-de-caja`
  - `/cierre-de-caja`
  - `/ventas-diarias`
  - `/facturas`
  - `/facturas/nueva`
  - `/facturas/:id`
  - `/vender`
  - `/ventas-rapidas/nueva`
- Inventario:
  - `/inventario-remisiones`
  - `/entrada-salidas`
  - `/transferencias`
- Compras/egresos/nómina:
  - `/compras`
  - `/egresos`
  - `/nomina`
  - `/empleados`
- Integraciones:
  - `/factus/conexion`
  - `/factro/conexion`

## 2. Autenticación y persistencia de token

Secuencia:

1. `POST /api/auth/login` con `email/password`.
2. Guardar `token` en una estrategia definida para SPA (ideal: memory o storage con controles).
3. En cada llamada API, adjuntar header:
   - `Authorization: Bearer <token>`

Logout:

- `POST /api/auth/logout` y limpiar token local.

## 3. Tenancy en React

El sistema original usa tenancy por dominio (Stancl).

Recomendación:

- Servir el frontend React desde el mismo dominio tenant/subdomain para que el backend “entienda” el tenant por host.
- Si el frontend está centralizado en un dominio único, considerar implementar un header o query param para “tenant”, y extender backend para que inicialice tenancy por valor.

## 4. Manejo de permisos/módulos en el frontend

Observación:

- En el backend, el gating `module:*` es para rutas web (`HasModule`).
- La API actual no muestra middleware `module:*` por endpoint; el control real para React debe:
  - o bien ejecutarse server-side dentro de cada endpoint API,
  - o bien replicarse en el frontend (menos confiable).

Plan recomendado:

1. Mantener autorización server-side para endpoints sensibles.
2. En React, renderizar menús/páginas según permisos/roles del usuario.
3. Implementar (si no existe) un endpoint API para:
   - listar módulos habilitados (`modules`)
   - listar permisos del usuario

## 5. Pantalla de Facturas (flujo con API)

### 5.1 Preparar ítems (cálculo y validación)

Secuencia recomendada desde React (usando los endpoints auxiliares):

1. Obtener productos únicos desde los ítems:
   - `POST /api/bills/get-unique-products` con `{ products: [{id,...}] }`
2. Añadir costo a ítems:
   - `POST /api/bills/add-cost` con `{ products, products_db }`
3. Validar inventario:
   - `POST /api/bills/validate-inventory` con `{ products, products_db }`
4. Calcular totales:
   - `POST /api/bills/calc-totales` con `{ products, products_db }`
5. Calcular impuestos/document_taxes (hoy el cálculo real está en servidor `DocumentTaxService`)
   - En React, o bien replicar lógica de impuestos (alto riesgo), o bien delegar creando una estrategia server-side:
     - crear una nueva ruta API para `calc-tax` por documento,
     - o crear temporalmente el `bill` y volver atrás (no recomendado).

### 5.2 Crear la factura

1. `POST /api/bills` con payload según `BillService::create`.
2. Payload recomendado:
   - usar el `user_id` de `/api/auth/me` (o desde token si el backend lo obtiene por auth; actualmente el validator lo exige en payload).
3. Luego:
   - `GET /api/bills/{id}/validate-electronic` si aplica.

### 5.3 Anulación de factura

En web, la anulación también crea nota crédito y valida electrónica.

En API no existe un endpoint único “cancel”. Propuesta (compuesta):

1. Actualizar bill a `status='1'`:
   - `PUT /api/bills/{billId}` con `status: '1'` (y campos requeridos por validator).
2. Crear nota crédito electrónica:
   - `GET /api/bills/{billId}/store-credit-note`
3. Validar nota crédito:
   - `GET /api/bills/{billId}/validate-credit-note`

## 6. Gap de API para inventario/caja

La API documentada en `routes/api.php` cubre principalmente:

- auth
- products/categorias/impuestos/tributos/presentaciones
- customers
- bills (incluye cálculos y validación electrónica)
- company
- roles/permissions (parcial)
- terminals

No se observan endpoints REST (en `routes/api.php`) para:

- `inventory_remissions` y `remission_details`
- `stock_movements` y `stock_movements_detail`
- `warehouse_transfers` y `warehouse_transfer_details`
- caja (cash_openings, cash_closings)

Para que React reemplace 1:1 la UX de Livewire, se recomienda:

- crear controladores y servicios API equivalentes a los Livewire CRUD (o exponer endpoints existentes si ya existen en el proyecto pero fuera de `routes/api.php`).

## 7. Carga de PDFs / imágenes

- Imágenes de productos:
  - `POST /api/products/images/upload-base64`
  - `GET /api/products/images/{productId}`
  - `DELETE /api/products/images/{productId}`
- PDF a Cloudinary:
  - `GET /api/pdf-upload/bill/{bill}` (pública)

## 8. Recomendación de implementación

1. Usar tipado fuerte (TypeScript) para payloads.
2. Centralizar validaciones del lado cliente con base en la SSD (y continuar validando server-side).
3. Para impuestos (DIAN/Factus/Factro), delegar el cálculo/validación al servidor mientras no exista una API explícita de “calculate taxes”.

## 9. Estado de la Migración (Phase 1: Mocks)

Se ha inicializado un proyecto base en `C:\Users\LENOVO\OneDrive - Arquitecsoft S.A.S\Arquitecsoft\Unidad_E\PROGRAMAS\ServidoresWeb\xampp8.2\htdocs\POS-React`.

### Gaps Críticos Identificados
- **Acciones de Menú:** Actualmente solo Dashboard y catálogos básicos están mapeados. Falta Inventario, Caja y Nómina.
- **Lógica de Negocio:** Los cálculos complejos de impuestos y validaciones de stock residen aún 100% en el backend.
- **Mocks Requeridos:** Se necesitan mocks detallados para los flujos de "Cerrar Caja" y "Crear Remisión".

### Estrategia de Entrega
1. Utilizar los archivos JSON en `src/mocks/data/` como fuente de verdad temporal.
2. Cada nueva funcionalidad en React debe ir acompañada de su correspondiente handler en MSW.
3. Mantener la paridad visual con el sistema Livewire (ver `08-paridad-funcional-ui.md`).
