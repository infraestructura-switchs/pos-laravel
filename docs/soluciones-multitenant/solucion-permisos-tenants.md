# Solución consolidada: Permisos completos para nuevos tenants

## Objetivo
Que cada tenant nuevo quede inmediatamente operativo con todos los módulos, roles y permisos, y con datos base mínimos.

## Implementación vigente
En `app/Http/Controllers/TenantRegistrationController.php`:

1) Ejecutar seeders base antes de crear el usuario administrador.
2) Crear usuario admin del tenant.
3) Asignar rol "Administrador" y sincronizar todos los permisos.
4) Crear `Company` y cliente por defecto (Consumidor Final).

### Seeders en orden recomendado
- DepartmentSeeder
- CitySeeder
- CurrencySeeder
- InvoiceProviderSeeder
- TributeSeeder
- TaxRateSeeder
- PaymentMethodSeeder
- IdentificationDocumentSeeder
- PermissionSeeder
- RoleSeeder
- ModuleSeeder
- TerminalSeeder

### Verificación rápida
- Menú completo visible tras el primer login del tenant (Ventas, Vender, Productos, Clientes, Apertura de caja, Configuración).
- Logs con ejecución de seeders y asignación de permisos.

## Notas y diagnóstico
- Si faltan opciones de menú: revisar logs y que `PermissionSeeder`, `RoleSeeder` y `ModuleSeeder` se ejecuten sin errores.
- Si el usuario admin no tiene permisos: forzar reasignación con `syncPermissions(Permission::all())`.

## Comandos útiles
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```
