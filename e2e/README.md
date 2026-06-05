# E2E + Visual Regression (Playwright)

Este proyecto incluye pruebas automáticas para validar:

1. **Funcionalidad base** (login, rutas críticas, render de facturación).
2. **Similitud visual pantalla a pantalla** (comparación pixel a pixel de screenshots).

## Requisitos

- Aplicación Laravel levantada y accesible.
- URL base configurada en `E2E_BASE_URL` (default: `http://127.0.0.1:8000`).
- Usuario válido para login:
  - `E2E_USER_EMAIL` (default: `superadmin@gmail.com`)
  - `E2E_USER_PASSWORD` (default: `12345678`)

## Comandos

- Ejecutar suite completa:
  - `npm run test:e2e`
- Actualizar snapshots visuales (cuando un cambio visual es esperado):
  - `npm run test:e2e:update`
- UI runner:
  - `npm run test:e2e:ui`

## Archivos principales

- `playwright.config.ts` - configuración global.
- `e2e/functional-smoke.spec.ts` - pruebas funcionales rápidas.
- `e2e/visual-parity.spec.ts` - pruebas visuales por pantalla.

## Cómo funciona la comparación visual

- La primera ejecución con `--update-snapshots` crea la línea base.
- Las siguientes ejecuciones comparan pantalla actual vs baseline.
- Si hay cambios de color/layout fuera del umbral, la prueba falla.

