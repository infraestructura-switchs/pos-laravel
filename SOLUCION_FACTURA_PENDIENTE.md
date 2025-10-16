# 🔧 Solución: "Se encontró una factura pendiente por enviar a la DIAN"

## ⚠️ Cuándo aparece este error

Este error aparece cuando:
- Intentas crear una nueva factura electrónica
- Factus detecta que hay una factura anterior sin completar
- La factura quedó en estado "pendiente" en el servidor de Factus

---

## 🎯 Soluciones Rápidas

### ✅ Opción 1: Completar la Factura Pendiente

**Cuando usar:** Cuando quieres que la factura pendiente se envíe a la DIAN

```bash
php artisan factus:complete-pending
```

**Qué hace:**
1. Consulta las facturas pendientes en Factus
2. Te muestra la información de cada una
3. Te pregunta si quieres completarlas
4. Intenta enviarlas a la DIAN

---

### 🗑️ Opción 2: Eliminar la Factura Pendiente

**Cuando usar:** Cuando la factura pendiente fue una prueba o tiene errores

```bash
# Modo interactivo (te pregunta cuál eliminar)
php artisan factus:delete-pending

# Eliminar una factura específica
php artisan factus:delete-pending SETI-123
```

**Qué hace:**
1. Consulta las facturas pendientes
2. Te permite seleccionar cuál eliminar
3. Elimina la factura de Factus
4. Libera el sistema para crear nuevas facturas

---

### 📋 Opción 3: Consultar Facturas en Factus

**Cuando usar:** Cuando quieres ver qué facturas tienes en Factus

```bash
# Ver todas las facturas
php artisan factus:list-bills

# Ver solo las pendientes
php artisan factus:list-bills --pending
```

**Qué hace:**
- Muestra todas tus facturas en Factus
- Indica el estado de cada una
- Muestra CUFE y fecha

---

## 🔄 Flujo Recomendado

### Cuando aparece el error:

1. **Verificar facturas pendientes**
   ```bash
   php artisan factus:list-bills --pending
   ```

2. **Decidir qué hacer:**
   
   **Si la factura es válida:**
   ```bash
   php artisan factus:complete-pending
   ```
   
   **Si la factura es de prueba/error:**
   ```bash
   php artisan factus:delete-pending
   ```

3. **Limpiar caché**
   ```bash
   php artisan cache:clear
   ```

4. **Reintentar crear factura**
   - Ir a "Vender"
   - Crear la factura nuevamente

---

## 🆘 Si los Comandos No Funcionan

Si los comandos fallan, es porque Factus no tiene esos endpoints. En ese caso:

### Plan B: Contactar Soporte de Factus

**Información para proporcionar:**
- Tu Client ID
- Número de la factura pendiente (si lo sabes)
- Fecha aproximada cuando se creó
- Solicitar: "Eliminar factura pendiente" o "Completar envío a DIAN"

**Contactos de Factus:**
- Soporte técnico: [Consultar en su sitio web]
- Email: soporte@factus.com.co (verificar)
- Portal de soporte: https://factus.com.co/soporte

---

## 🛡️ Prevención

### Para evitar facturas pendientes en el futuro:

1. **Verificar credenciales antes de facturar**
   ```bash
   php artisan factus:test-connection
   ```

2. **No interrumpir el proceso**
   - Espera a que termine de crear la factura
   - No refresques la página mientras está procesando

3. **Revisar logs si hay errores**
   ```bash
   tail -f storage/logs/laravel.log | grep "Factus\|Electronic"
   ```

4. **Mantener credenciales actualizadas**
   - Las credenciales de sandbox pueden expirar
   - Verificar regularmente con `factus:test-connection`

---

## 📊 Ejemplos de Uso

### Ejemplo 1: Completar Factura Pendiente

```bash
$ php artisan factus:complete-pending

🔍 Buscando facturas pendientes en Factus...

⚠️  Se encontraron 1 factura(s) pendiente(s)

Factura #1:
  Número: SETI-123
  Estado: pending
  Fecha: 2025-10-07 20:03:06

¿Deseas intentar completar esta factura? (yes/no) [yes]:
> yes

📤 Intentando completar factura...
✅ Factura completada exitosamente
```

### Ejemplo 2: Eliminar Factura Pendiente

```bash
$ php artisan factus:delete-pending

🔍 Buscando facturas pendientes...

⚠️  Se encontraron 1 factura(s) pendiente(s)

¿Cuál factura deseas eliminar?
  [0] SETI-123 - 2025-10-07 20:03:06

> 0

¿Estás seguro de eliminar la factura SETI-123? (yes/no) [no]:
> yes

🗑️  Eliminando factura SETI-123...
✅ Factura eliminada exitosamente
```

### Ejemplo 3: Listar Facturas

```bash
$ php artisan factus:list-bills --pending

🔍 Consultando facturas en Factus...

+----------+-------------+--------------------------------+---------------------+
| Número   | Estado      | CUFE                           | Fecha               |
+----------+-------------+--------------------------------+---------------------+
| SETI-123 | ⚠️ Pendiente | abc123def456...                | 2025-10-07 20:03:06 |
+----------+-------------+--------------------------------+---------------------+

Total: 1 facturas
```

---

## 🔍 Diagnóstico de Problemas

### Error: "No se pudo conectar con Factus"

**Solución:**
```bash
# 1. Verificar credenciales
php artisan factus:test-connection

# 2. Verificar caché
php artisan cache:clear

# 3. Verificar configuración
php artisan tinker --execute="dd(App\Services\FactusConfigurationService::apiConfiguration());"
```

### Error: "Endpoint no encontrado"

**Solución:**
- Factus puede no tener endpoints para gestionar pendientes
- Contactar directamente con soporte de Factus
- Usar el portal web de Factus si está disponible

### Error persiste después de eliminar

**Solución:**
```bash
# 1. Limpiar caché
php artisan cache:clear

# 2. Verificar que realmente se eliminó
php artisan factus:list-bills --pending

# 3. Esperar 5 minutos y reintentar
# (puede haber delay en la sincronización de Factus)
```

---

## 📝 Logs Importantes

Los comandos registran todo en los logs de Laravel:

```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Filtrar solo Factus
tail -f storage/logs/laravel.log | grep "Factus"
```

---

## ✅ Checklist Post-Solución

Después de resolver el problema, verifica:

- [ ] `php artisan factus:list-bills --pending` no muestra facturas
- [ ] `php artisan cache:clear` ejecutado
- [ ] Crear una factura de prueba funciona sin errores
- [ ] Los logs no muestran errores de Factus

---

## 🎯 Resumen Ejecutivo

| Acción | Comando | Cuándo Usar |
|--------|---------|-------------|
| Ver pendientes | `factus:list-bills --pending` | Siempre primero |
| Completar | `factus:complete-pending` | Factura válida |
| Eliminar | `factus:delete-pending` | Factura de prueba |
| Listar todas | `factus:list-bills` | Diagnóstico general |

---

**Última actualización**: Octubre 7, 2025  
**Versión**: 1.0.0

