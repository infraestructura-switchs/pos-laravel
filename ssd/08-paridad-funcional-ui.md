# 08. Paridad Funcional y de Interfaz (React vs Sistema Actual)

Objetivo: que la nueva app en React se sienta y funcione **muy similar** al producto actual (Laravel + Livewire), no solo que “tenga los mismos módulos”.

## 1. Criterios de similitud obligatorios

## Funcionalidad (100% paridad)

- Mismos módulos visibles por rol/módulo habilitado.
- Mismas reglas de negocio en ventas, inventario, caja y facturación electrónica.
- Mismos estados de operación (activo/anulado, abierta/cerrada, validada/no validada).
- Mismos cálculos de importes (subtotal, descuento, impuestos por tributo, total).
- Mismos flujos de error (mensajes de validación y bloqueos de negocio).

## Interfaz/UX (alta paridad)

- Misma estructura de navegación:
  - sidebar + topbar
  - accesos rápidos (factura, vender, venta rápida, productos)
  - menú por grupos (Facturación, Inventario, etc.)
- Misma jerarquía visual de páginas:
  - encabezado de secciones
  - tarjetas por bloque de captura
  - tablas desktop + tarjetas mobile
- Misma interacción en formularios:
  - validación inline
  - estados de carga (`Guardando`, panel de “Enviando factura”)
  - confirmaciones y alertas

## 2. Patrones visuales detectados en la UI actual

Tomado de:

- `resources/views/layouts/app.blade.php`
- `resources/views/livewire/admin/menu.blade.php`
- `resources/views/livewire/admin/bills/create.blade.php`

Patrones a replicar:

- **Layout fijo**:
  - topbar fija superior
  - sidebar fija lateral (modo compacto para “venta rápida/vender”)
- **Responsive real**:
  - mobile: menú hamburguesa + overlay
  - desktop: sidebar expandida/compacta
- **Facturación**:
  - cards por sección (`Información del cliente`, `Información del producto`, `Observaciones`)
  - tabla de items en desktop
  - cards de items en mobile
  - resumen de totales al final (valor bruto, descuento, impuestos, total)
- **Microinteracciones**:
  - dropdown de presentaciones
  - botones contextuales agregar/actualizar/cancelar
  - iconografía consistente (ico/ti)

## 3. Especificación de paridad por pantalla crítica

## 3.1 Menú y navegación

- Debe existir el mismo árbol de navegación.
- Los grupos deben abrir/cerrar igual (accordion).
- Debe ocultar/mostrar ítems según permisos y módulos.
- En móvil, debe mantener overlay + panel deslizante.

## 3.2 Crear factura

- Flujo visual:
  1. Selección cliente
  2. Selección producto + presentación + cantidad + descuentos
  3. Lista de productos agregados
  4. Forma de pago / crédito / fecha vencimiento
  5. Observaciones
  6. Resumen total
  7. Guardar
- Comportamiento:
  - mismos bloqueos de negocio al guardar
  - mismas validaciones visibles en UI
  - misma respuesta post-guardado (impresión/flujo electrónica)

## 3.3 Caja (apertura/cierre)

- Acciones accesibles desde topbar e interfaz principal.
- Deben reflejar estado actual de terminal/caja abierta.
- Mensajes y bloqueos similares cuando no hay terminal o no hay apertura.

## 3.4 Inventario

- Mantener vistas diferenciadas: bodegas, entradas/salidas, remisiones, transferencias.
- Conservar flujos de captura con detalle por producto/cantidad/costo.

## 4. Matriz de aceptación (Definition of Done)

Una pantalla React se considera “similar” solo si cumple:

- [ ] Paridad de campos (mismos campos editables y de solo lectura).
- [ ] Paridad de reglas (mismas validaciones y restricciones).
- [ ] Paridad de estados (carga, éxito, error, vacío).
- [ ] Paridad responsive (desktop/table/mobile).
- [ ] Paridad de navegación (misma ruta y acceso por menú).
- [ ] Paridad de permisos/módulos (visibilidad y acceso).
- [ ] Paridad de mensajes críticos de negocio.

## 5. Plan sugerido para construir similaridad

1. Implementar primero layout global (topbar/sidebar/responsive).
2. Migrar pantallas críticas en este orden:
   - `vender` / `facturas/nueva` / `facturas`
   - productos/clientes/terminales
   - caja
   - inventario
3. Integrar permisos/módulos antes de cerrar QA.
4. Ejecutar QA comparativo “pantalla a pantalla” con la matriz de aceptación.

## 6. Recomendación práctica

Para mantener similitud alta y controlar alcance:

- usar el mismo naming de módulos y rutas en frontend.
- mantener componentes reutilizables de UI:
  - `CardSection`
  - `DataTableResponsive`
  - `TotalsSummary`
  - `PermissionGate`
  - `ModuleGate`
- no cambiar el flujo de captura hasta completar paridad funcional.

