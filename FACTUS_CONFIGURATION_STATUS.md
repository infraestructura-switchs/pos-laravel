# Estado de Facturación Electrónica con Factus

## 🔴 ESTADO ACTUAL: DESHABILITADO

**Fecha**: Octubre 7, 2025 - 20:10 hrs  
**Motivo**: Credenciales de sandbox inválidas

---

## ⚠️ IMPORTANTE

**NO HABILITAR** la facturación electrónica hasta que se cumplan TODAS estas condiciones:

### Requisitos para Habilitar:

1. ✅ **Credenciales Válidas de Factus**
   - [ ] Client ID correcto
   - [ ] Client Secret correcto
   - [ ] Email y password válidos
   - [ ] Prueba exitosa con `php artisan factus:test-connection`

2. ✅ **Sin Facturas Pendientes en Factus**
   - [ ] Verificar que no hay facturas pendientes en el servidor de Factus
   - [ ] Resolver cualquier factura pendiente antes de continuar

3. ✅ **Configuración Completa**
   - [ ] Rango de numeración configurado en Factus
   - [ ] `factus_numbering_range_id` asignado a terminales
   - [ ] Clientes con email y teléfono
   - [ ] Productos con referencias

---

## 🚫 Problemas Conocidos

### Problema 1: "Se encontró una factura pendiente por enviar a la DIAN"

**Error:**
```
Se encontró una factura pendiente por enviar a la DIAN
```

**Causa:**  
Hay una factura en el servidor de Factus que no se completó el proceso de validación con la DIAN.

**Solución:**
1. Contactar con soporte de Factus
2. Solicitar que eliminen/completen la factura pendiente
3. O usar otras credenciales (cuenta nueva)

### Problema 2: "invalid_client"

**Error:**
```
Error: invalid_client
Descripción: Client authentication failed
```

**Causa:**  
Las credenciales de sandbox expiraron o son inválidas.

**Solución:**
1. Obtener nuevas credenciales de Factus
2. Actualizar en la configuración
3. Probar con `php artisan factus:test-connection`

---

## 📝 Comandos Útiles

### Verificar Estado Actual
```bash
php artisan tinker --execute="echo App\Services\FactusConfigurationService::isApiEnabled() ? 'HABILITADO' : 'DESHABILITADO';"
```

### Deshabilitar Facturación Electrónica
```bash
php artisan tinker --execute="App\Models\FactusConfiguration::first()->update(['is_api_enabled' => false]); Cache::forget('is_api_enabled'); echo 'Deshabilitado';"
php artisan cache:clear
```

### Habilitar Facturación Electrónica (Solo cuando todo esté listo)
```bash
php artisan tinker --execute="App\Models\FactusConfiguration::first()->update(['is_api_enabled' => true]); Cache::forget('is_api_enabled'); echo 'Habilitado';"
php artisan cache:clear
```

### Probar Conexión
```bash
php artisan factus:test-connection
```

---

## 📧 Contacto Factus

Cuando necesites resolver problemas con Factus:

1. **Soporte Técnico**: Contactar a Factus
2. **Solicitar**:
   - Eliminar facturas pendientes
   - Nuevas credenciales de sandbox
   - O activar cuenta de producción

---

## 📊 Historial de Cambios

| Fecha | Acción | Motivo | Estado |
|-------|--------|--------|--------|
| 2025-10-07 20:10 | Deshabilitado | Factura pendiente en Factus | 🔴 OFF |
| 2025-10-07 20:03 | Error detectado | "Factura pendiente DIAN" | ❌ Error |
| 2025-10-07 15:00 | Deshabilitado | Credenciales inválidas | 🔴 OFF |

---

## 🎯 Próximos Pasos

1. **Inmediato**: Mantener DESHABILITADO hasta resolver con Factus
2. **Contactar Factus**: Resolver factura pendiente
3. **Obtener nuevas credenciales**: Solicitar credenciales válidas
4. **Probar**: Usar `php artisan factus:test-connection`
5. **Habilitar**: Solo cuando todo esté verificado

---

**Última actualización**: Octubre 7, 2025 - 20:10 hrs

