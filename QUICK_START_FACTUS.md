# 🚀 Guía Rápida de Inicio - Facturación Electrónica con Factus

## ⚡ Inicio Rápido (5 minutos)

### Paso 1: Obtener Credenciales de Factus

Contacta con Factus y solicita:
- Client ID
- Client Secret
- Email
- Password
- URL de la API (sandbox o producción)

---

### Paso 2: Configurar Credenciales

```bash
php artisan tinker
```

Pega y modifica con tus datos:

```php
$config = App\Models\FactusConfiguration::first();
$config->api = [
    'url' => 'https://api-sandbox.factus.com.co/',  // O la URL que te den
    'client_id' => 'TU_CLIENT_ID_AQUI',
    'client_secret' => 'TU_CLIENT_SECRET_AQUI',
    'email' => 'tu_email@example.com',
    'password' => 'tu_password'
];
$config->is_api_enabled = true;  // Cambiar a false para deshabilitar
$config->save();

// Limpiar caché
Cache::forget('api_configuration');
Cache::forget('is_api_enabled');
exit
```

---

### Paso 3: Probar Conexión

```bash
php artisan factus:test-connection
```

Si ves "✅ AUTENTICACIÓN EXITOSA", estás listo.

---

### Paso 4: Limpiar Caché

```bash
php artisan cache:clear
php artisan config:clear
```

---

### Paso 5: ¡Crear tu Primera Factura Electrónica!

1. Ve a **"Vender"** en tu aplicación
2. Selecciona un cliente (debe tener email y teléfono)
3. Agrega productos (deben tener código de referencia)
4. Click en **"Facturar"**

El sistema automáticamente:
- ✅ Valida la factura con Factus
- ✅ Obtiene el CUFE
- ✅ Guarda el código QR
- ✅ Guarda los datos electrónicos

---

## 🔧 Configuración Adicional

### Configurar Rango de Numeración de Factus (Opcional)

Si tu terminal tiene un rango de numeración específico en Factus:

```bash
php artisan tinker
```

```php
$terminal = App\Models\Terminal::find(1);  // Cambiar ID según tu terminal
$terminal->factus_numbering_range_id = 123;  // ID del rango en Factus
$terminal->save();
exit
```

---

## 📥 Descargar Documentos Oficiales

### Desde el Código

```php
use App\Services\Factus\ElectronicBillService;

// Descargar PDF
$pdfContent = ElectronicBillService::downloadPdf($bill);

// Descargar XML
$xmlContent = ElectronicBillService::downloadXml($bill);
```

### Desde el Navegador

```
PDF: /admin/facturas-electronicas/{bill_id}/pdf
XML: /admin/facturas-electronicas/{bill_id}/xml
```

---

## 🐛 Troubleshooting Rápido

### ❌ "Error al refrescar el token de acceso"

**Solución:**
```bash
php artisan factus:test-connection
```

---

### ❌ "El cliente no tiene email configurado"

**Solución:** Edita el cliente y agrega un email válido.

---

### ❌ "El producto no tiene código de referencia"

**Solución:** Edita el producto y agrega un código en el campo `reference`.

---

### ❌ La facturación no se ejecuta

**Solución:**
```bash
php artisan cache:clear
php artisan config:clear

# Verificar que está habilitada
php artisan tinker --execute="dd(App\Services\FactusConfigurationService::isApiEnabled());"
```

---

## 🔍 Ver Logs en Tiempo Real

```bash
# Todos los logs
tail -f storage/logs/laravel.log

# Solo facturación electrónica
tail -f storage/logs/laravel.log | grep -i "factus\|electronic"

# Solo errores
tail -f storage/logs/laravel.log | grep "ERROR"
```

---

## ⚙️ Comandos Útiles

```bash
# Probar conexión con Factus
php artisan factus:test-connection

# Limpiar todos los cachés
php artisan cache:clear
php artisan config:clear

# Ver configuración actual
php artisan tinker --execute="print_r(App\Models\FactusConfiguration::first()->toArray());"

# Ver última factura electrónica
php artisan tinker --execute="print_r(App\Models\ElectronicBill::latest()->first()->toArray());"

# Habilitar facturación electrónica
php artisan tinker --execute="App\Models\FactusConfiguration::first()->update(['is_api_enabled' => true]); Cache::forget('is_api_enabled'); echo 'Habilitada';"

# Deshabilitar facturación electrónica
php artisan tinker --execute="App\Models\FactusConfiguration::first()->update(['is_api_enabled' => false]); Cache::forget('is_api_enabled'); echo 'Deshabilitada';"
```

---

## 📚 Documentación Completa

Para más detalles, consulta:
- **`ELECTRONIC_BILLING_DOCUMENTATION.md`** - Documentación completa
- **`IMPLEMENTATION_SUMMARY.md`** - Resumen de implementación

---

## ✅ Checklist de Configuración

Antes de usar facturación electrónica, verifica:

- [ ] Credenciales de Factus configuradas
- [ ] Conexión probada con `php artisan factus:test-connection`
- [ ] Caché limpiado
- [ ] Facturación electrónica habilitada
- [ ] Clientes con email y teléfono
- [ ] Productos con código de referencia
- [ ] Productos con impuestos configurados

---

## 🎉 ¡Listo!

Ya puedes empezar a facturar electrónicamente.

**¿Necesitas ayuda?**
- Revisa los logs: `tail -f storage/logs/laravel.log`
- Ejecuta el diagnóstico: `php artisan factus:test-connection`
- Consulta la documentación completa

---

**Última actualización**: Octubre 7, 2025

