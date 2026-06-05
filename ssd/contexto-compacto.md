# Contexto Compacto - POS Multi-Tenant

## Qué es

Aplicación POS multi-tenant en Laravel 9 + Livewire 2, con API REST (Sanctum), orientada a operación comercial por empresa/sucursal:

- ventas y facturación
- inventario
- compras y egresos
- caja (apertura/cierre)
- clientes/proveedores/usuarios/roles
- facturación electrónica (Factus/Factro)

## Arquitectura

- **Central domain**: administra tenants.
- **Tenant domains**: operación aislada por empresa (DB por tenant).
- **Autenticación**:
  - web: sesión (`auth`)
  - API: token Bearer (`auth:sanctum`)
- **Autorización**:
  - roles/permisos (Spatie)
  - módulos habilitados (`module:*`)

## Núcleo funcional

1. **Venta/factura**
   - valida terminal + inventario
   - calcula totales/impuestos
   - guarda factura + detalle
   - actualiza stock/unidades
   - opcional: valida factura electrónica

2. **Anulación**
   - crea nota crédito electrónica (si aplica)
   - revierte inventario
   - marca estado de factura

3. **Inventario**
   - productos con y sin presentaciones
   - manejo por stock/unidades según `has_inventory` y `has_presentations`
   - remisiones, movimientos y transferencias entre bodegas

## API actual (lista para React, parcial)

Sí expone:

- auth, products, categories, tax_rates, tributes, presentations
- users, roles, terminals, customers
- bills (incluye endpoints auxiliares de cálculo y validación electrónica)
- company, health, product images

No expone de forma REST completa en `routes/api.php`:

- caja (`cash_openings`, `cash_closings`)
- inventario operativo (`inventory_remissions`, `stock_movements`, `warehouse_transfers`)
- compras/egresos/nomina (como API dedicada completa)

## Riesgos/ajustes para clonar en React

- Estandarizar contrato de errores (algunos controladores responden distinto).
- Cerrar gap de endpoints para inventario/caja.
- Revisar consistencia de `status` en facturas (documentado en SSD detallada).
- Definir endpoint de permisos/módulos para pintar menú y proteger vistas en frontend.

## Ruta de lectura sugerida

1. `ssd/contexto-compacto.md` (este archivo)
2. `ssd/06-api-rest.md`
3. `ssd/04-modelos-datos.md`
4. `ssd/07-mapeo-react.md`

