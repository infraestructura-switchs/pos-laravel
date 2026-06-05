# SSD - Migración de Sistema POS (Laravel/Livewire -> React/Mocks)

Este documento detalla el proceso de migración del sistema POS actual (Laravel 9 + Livewire 2) hacia una arquitectura moderna en React. Actualmente se encuentra en la **Fase 1: Implementación de UI y Lógica de Cliente con Mocks**.

## Documentos

1. `01-contexto.md` - Contexto inicial y visión general del sistema.
2. `02-alcance-flujos.md` - Alcance funcional y flujos de usuario.
3. `03-arquitectura-seguridad.md` - Multi-tenancy, autenticación, permisos y configuración.
4. `04-modelos-datos.md` - Modelo de datos (entidades, relaciones y campos).
5. `05-reglas-negocio.md` - Reglas de negocio implementadas y validaciones críticas.
6. `06-api-rest.md` - Contratos REST (endpoints, payloads y respuestas).
7. `07-mapeo-react.md` - Cómo mapear pantallas/flujo y contratos a una implementación React.
8. `08-paridad-funcional-ui.md` - Criterios para asegurar similitud funcional y de interfaz con el sistema actual.

## Fuentes del documento

- Rutas web/API: `routes/web.php`, `routes/tenant.php`, `routes/admin.php`, `routes/api.php`
- Controladores/Services: `app/Http/Controllers/Api/*`, `app/Services/*`
- Reglas y utilidades: `app/Http/Middleware/*`, `app/Services/BillService.php`, `app/helpers.php`
- Esquema: migraciones en `database/migrations/*`

