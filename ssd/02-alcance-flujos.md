# 02. Alcance Funcional y Flujos de Usuario

## 1. Alcance funcional (módulos y pantallas principales)

El acceso a cada funcionalidad en el panel tenant se controla por middleware `module:<nombre>` y por permisos RBAC. Los endpoints web se definen en `routes/admin.php` (prefijo `administrador/` para tenants).

### Catálogos y administración operativa

- Catálogo de clientes: `GET /clientes`
- Catálogo de proveedores: `GET /proveedores`
- Bodegas/almacenes: `GET /almacenes`
- Productos: `GET /productos`
- Inventario: remisiones de inventario `GET /inventario-remisiones` (alias `GET /remisiones`)
- Inventario: movimientos de entrada/salida `GET /entrada-salidas`
- Inventario: transferencias entre bodegas `GET /transferencias`
- Impuestos: tax rates `GET /impuestos`
- Cierre/apertura de caja:
  - Apertura: `GET /apertura-de-caja`
  - Cierre: `GET /cierre-de-caja`
- Ventas diarias: `GET /ventas-diarias` y descarga PDF `GET /ventas-diarias/pdf/{dailySale?}`
- Rangos de numeración: `GET /rangos-de-numeracion`
- Terminales: `GET /terminales`
- Módulos habilitados: `GET /modulos`
- Configuración de la empresa (negocio): `GET /configuración`
- Usuarios del tenant: `GET /usuarios`
- Roles y permisos (RBAC): `GET /roles-y-permisos`
- Logs:
  - `GET /logs`
  - `GET /logs-file`

### Ventas y facturación

- Facturación (facturas “main”):
  - Listado: `GET /facturas`
  - Crear: `GET /facturas/nueva-factura`
  - Ver detalle: `GET /facturas/{bill}`
  - Información: `GET /facturas/informacion/{bill}`
  - PDF: `GET /facturas/pdf/{bill}`
  - Upload/WhatsApp de PDF:
    - `GET /facturas/pdf-upload/{bill}`
    - `GET /facturas/pdf-whatsapp/{bill}`
    - `POST /facturas/{bill}/whatsapp`
  - Descarga de PDF: `GET /facturas-download/{bill}`
- Facturación electrónica (en `Factus`/`Factro`):
  - PDF: `GET /facturas-electronicas/{bill}/pdf`
  - XML: `GET /facturas-electronicas/{bill}/xml`
  - Info: `GET /facturas-electronicas/{bill}/info`
- Venta rápida / mesa:
  - Crear: `GET /ventas-rapidas/nueva-venta`
- Venta directa (“vender”):
  - Crear: `GET /vender`
  - Descarga de factura: `GET /vender/facturas-download/{bill}`

### Compras, egresos y nómina

- Compras:
  - Listado: `GET /compras`
  - Crear: `GET /compras/agregar-compra`
  - Ver detalle: `GET /compra/{purchase}`
  - PDF: `GET /compra/pdf/{purchase}`
  - Descarga: `GET /compra-download/{purchase}`
- Egresos:
  - Listado: `GET /egresos`
  - Ver: `GET /egreso/{output}`
  - Upload PDF: `GET /egreso/pdf-upload/{output}`
  - WhatsApp: `GET /egreso/pdf-whatsapp/{output}`
  - Descarga: `GET /egreso-download/{output}`
- Finanzas (asociadas a facturas):
  - `GET /financiaciones`
- Nómina:
  - Empleados: `GET /empleados`
  - Listado nómina: `GET /nomina`
  - Ver: `GET /nomina/{payroll}`
  - Descarga: `GET /nomina-download/{payroll}`

## 2. Flujo de autenticación y tenancy

1. El usuario accede al dominio del sistema:
   - Central: crea y administra tenants.
   - Tenant: opera el POS de esa empresa.
2. `routes/tenant.php` inicializa tenancy por dominio usando `InitializeTenancyByDomain`.
3. En web se usa autenticación session (`auth`/`verified`) y RBAC por roles/permisos.
4. En API se usa `auth:sanctum` (token Bearer) para CRUD y operaciones de cálculo.

## 3. Flujo de venta (creación de factura)

El flujo principal de facturación está implementado en el componente Livewire:
`app/Http/Livewire/Admin/Bills/Create.php`.

1. Selección/validación:
   - `customer.id` obligatorio.
   - `products` (array) obligatorio.
   - `payment_method_id` obligatorio.
   - `finance` define si se crea financiación (due date).
2. Preparación de ítems:
   - Resolver costo por producto/presentación (`BillService::addCostToItems`).
   - Validar inventario (`BillService::validateInventory`).
   - Calcular totales por ítem (`BillService::calcTotales`).
   - Calcular impuestos por ítems y por documento (`DocumentTaxService`).
3. Persistencia transaccional:
   - Crear factura con `BillService::store`.
   - Crear detalle por ítem (`DetailBillService::store`).
   - Actualizar stock/unidades según configuración de inventario (`BillService::updateUnitsOrStock`, `updateStock`).
   - Si `finance === '1'`, crear relación `bill->finance()` con `due_date`.
4. Validación de facturación electrónica:
   - Si Factus o Factro están habilitados (`FactusConfigurationService::isApiEnabled`, `FactroConfigurationService::isApiEnabled`), se intenta validar con `BillService::validateElectronicBill`.
5. Al finalizar:
   - Se imprime ticket / se habilita PDF en función del flujo.

## 4. Flujo de anulación (cancelación) de factura

El flujo de anulación está en `app/Http/Livewire/Admin/Bills/Index.php::cancelBill`.

1. Si la factura ya está en estado “anulada” se retorna error.
2. Si factura electrónica existe y no está validada por DIAN (según `is_validated`), no se permite anular.
3. Se crea nota crédito electrónica con `BillService::storeElectronicCreditNote`.
4. Se actualiza el estado de la factura a `status = '1'`.
5. Se valida la nota crédito electrónica (`BillService::validateElectronicCreditNote`) y se hace commit.

## 5. Flujo de inventario (inventario con vs sin unidades)

El sistema maneja dos modos por producto:

- `has_inventory` = 0: el producto controla inventario (stock/unidades) y se valida decremento.
- `has_inventory` = 1: el producto no maneja inventario (no se valida/decrementa como stock).

Además, por `has_presentations`:

- Si tiene presentaciones (`has_presentations == 0`): el stock/decremento se hace por cantidad de presentaciones.
- Si no tiene presentaciones (`has_presentations == 1`): el stock/decremento se hace por `units`/`stock` directo (ver `BillService`).

## 6. Flujo de caja (apertura/cierre)

La caja está compuesta por:

- `cash_openings`: guarda efectivo inicial y estado `is_active`.
- `cash_closings`: guarda totales por método (cash, debit_card, credit_card, transfer, etc.) y referencia a `cash_opening_id`.

Las rutas para abrir/cerrar dependen del módulo `cierre de caja`.

## 7. Funcionalidades Pendientes por Migrar a React

Actualmente, las siguientes funcionalidades y acciones de menú están disponibles únicamente en el sistema Livewire original y **deben ser portadas a React**:

### Acciones de Menú y Navegación
- **Dashboard Principal:** Métricas en tiempo real y gráficos de ventas.
- **Gestión de Inventario Completa:** Remisiones, movimientos y transferencias.
- **Operaciones de Caja:** Apertura, cierre y arqueo detallado.
- **Nómina y Empleados:** Gestión de pagos e integración con Factus.
- **Configuración Avanzada:** Rangos de numeración, terminales y roles/permisos.

### Lógica de Negocio Requerida en React
- **Cálculo de Impuestos Dinámico:** Replicar la lógica de `DocumentTaxService` o consumir los mocks actualizados.
- **Integración Electrónica:** Flujos de validación DIAN vía Factus/Factro.
- **Impresión de Tickets:** Generación y descarga de PDFs desde el cliente.

> [!IMPORTANT]
> La Phase 1 se enfoca en establecer la cáscara (Shell) de la aplicación y los flujos críticos de Venta y Clientes usando Mocks.
