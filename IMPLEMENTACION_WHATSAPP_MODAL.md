# Implementación de Modal de WhatsApp para Envío de Facturas

## 📋 Descripción General

Se ha implementado un flujo completo para que los usuarios puedan enviar el PDF de las facturas por WhatsApp de manera opcional durante el proceso de facturación.

## 🎯 Características Implementadas

### 1. Modal de Confirmación Interactivo
- **Paso 1**: Pregunta al usuario si desea recibir el PDF por WhatsApp
- **Paso 2**: Captura el número de teléfono del destinatario
- **Validación**: Verifica que el número tenga al menos 10 dígitos
- **Estados de carga**: Muestra feedback visual durante el envío

### 2. Flujo Condicional
- **Si elige "Sí"**: Solicita el número de teléfono y envía el PDF
- **Si elige "No"**: Continúa con el proceso normal de facturación sin interrupciones

### 3. Integración con Webhook N8N
- **Endpoint**: Configurado en `config/services.php` bajo `n8n.whatsapp_webhook_url`
- **Payload enviado**:
  ```json
  {
    "numberDestino": 3153382089,
    "fileName": "https://res.cloudinary.com/dxktixdby/raw/upload/v1234567890/pos-images/pdfs/bill_123_1234567890.pdf"
  }
  ```
- **Timeout configurable**: Definido en variable de entorno `N8N_TIMEOUT`
- **URL del PDF**: Se envía el enlace público permanente almacenado en la base de datos

### 4. Sistema de Almacenamiento de PDF URLs
- **Campo en BD**: `bills.pdf_url` (tipo TEXT, nullable)
- **Almacenamiento automático**: Cuando el Job `UploadBillPdfToCloudinary` se ejecuta
- **Reutilización**: El servicio de WhatsApp verifica primero si existe el URL en BD
- **Beneficios**:
  - ✅ No se sube el PDF múltiples veces a Cloudinary
  - ✅ URL público permanente y accesible
  - ✅ Mejor rendimiento al enviar por WhatsApp
  - ✅ Trazabilidad del PDF de cada factura

## 📂 Archivos Modificados/Creados

### Nuevos Archivos
1. **`resources/views/components/whatsapp-modal.blade.php`**
   - Componente modal reutilizable con Alpine.js
   - Dos pasos: confirmación y captura de teléfono
   - Validación de formato telefónico

2. **`database/migrations/2025_10_16_120602_add_pdf_url_to_bills_table.php`**
   - Agrega campo `pdf_url` a la tabla `bills`
   - Almacena el enlace público del PDF en Cloudinary

### Archivos Modificados

#### Backend (Livewire)
1. **`app/Services/WhatsappPdfService.php`**
   - Actualizado para aceptar número de teléfono personalizado
   - Cambiado payload de `Fileurl` a `fileName` para coincidir con API de N8N
   - **OPTIMIZACIÓN**: Ahora verifica si el PDF ya fue subido (usando `bill->pdf_url`)
   - Solo sube a Cloudinary si no existe el URL en BD
   - Guarda el URL en BD para futuras referencias

2. **`app/Models/Bill.php`**
   - Agregado campo `pdf_url` al fillable
   - Almacena el enlace público de Cloudinary

3. **`app/Jobs/UploadBillPdfToCloudinary.php`**
   - Ahora guarda el `pdf_url` en la base de datos después de subir
   - Logs mejorados para tracking del URL

4. **`app/Http/Livewire/Admin/DirectSale/Create.php`**
   - Agregado método `sendBillViaWhatsapp($billId, $phoneNumber)`
   - Emite evento `bill-created` con el ID de la factura
   - Importación de `WhatsappPdfService`

5. **`app/Http/Livewire/Admin/Bills/Create.php`**
   - Agregado método `sendBillViaWhatsapp($billId, $phoneNumber)`
   - Emite evento `bill-created` con el ID de la factura
   - Importación de `WhatsappPdfService`
   - Método `uploadPdfToCloudinary` ahora guarda el URL en BD

#### Frontend (Vistas)
1. **`resources/views/livewire/admin/direct-sale/create.blade.php`**
   - Incluye componente `<x-whatsapp-modal />`
   - JavaScript para manejar el flujo del modal antes de facturar
   - Intercepta el evento `store-bill` para mostrar modal primero
   - Escucha evento `bill-created` para capturar el ID de la factura

2. **`resources/views/livewire/admin/bills/create.blade.php`**
   - Incluye componente `<x-whatsapp-modal />`
   - JavaScript para manejar el flujo del modal después de crear factura
   - Escucha evento `bill-created` y abre modal automáticamente

## 🔧 Configuración

### Variables de Entorno
Asegúrate de tener estas variables en tu archivo `.env`:

```env
# Webhook de N8N para WhatsApp
N8N_WHATSAPP_WEBHOOK_URL=https://n8n-vwj1.onrender.com/webhook/factura
N8N_TIMEOUT=10
```

### Configuración en `config/services.php`
```php
'n8n' => [
    'whatsapp_webhook_url' => env('N8N_WHATSAPP_WEBHOOK_URL', 'https://n8n-vwj1.onrender.com/webhook/factura'),
    'timeout' => env('N8N_TIMEOUT', 10),
],
```

## 🔄 Flujo de Funcionamiento

### En Venta Directa (DirectSale)
1. Usuario agrega productos al carrito
2. Hace clic en "Facturar"
3. **NUEVO**: Se muestra modal de WhatsApp
4. Usuario elige:
   - **Sí**: Ingresa número → Se crea factura → Se sube PDF a Cloudinary → Se envía por WhatsApp
   - **No**: Se crea factura normalmente sin envío por WhatsApp
5. Proceso continúa normal (descarga de ticket, etc.)

### En Creación de Factura (Bills/Create)
1. Usuario completa formulario de factura
2. Hace clic en "Guardar"
3. Se crea la factura y se sube PDF a Cloudinary
4. **NUEVO**: Se muestra modal de WhatsApp automáticamente
5. Usuario elige:
   - **Sí**: Ingresa número → Se envía por WhatsApp
   - **No**: Continúa normalmente
6. Se muestra ticket de impresión

## 🎨 Interfaz de Usuario

### Modal - Paso 1 (Confirmación)
```
┌─────────────────────────────────────┐
│  🟢 Enviar PDF por WhatsApp         │
├─────────────────────────────────────┤
│  ¿Desea recibir el PDF de la       │
│  factura por WhatsApp?              │
│                                     │
│  [Sí, enviar] [No, continuar...]   │
└─────────────────────────────────────┘
```

### Modal - Paso 2 (Número de Teléfono)
```
┌─────────────────────────────────────┐
│  📱 Número de WhatsApp              │
├─────────────────────────────────────┤
│  Ingrese el número de teléfono      │
│  ┌─────────────────────────────┐   │
│  │ 3001234567                  │   │
│  └─────────────────────────────┘   │
│  Formato: puede incluir código      │
│  de país (+57) o solo el número     │
│                                     │
│  [Confirmar y enviar]  [Atrás]     │
└─────────────────────────────────────┘
```

## 🔍 Validaciones

### Número de Teléfono
- **Mínimo**: 10 dígitos
- **Formato aceptado**: 
  - `3001234567`
  - `+573001234567`
  - `300 123 4567`
- **Limpieza automática**: Se eliminan espacios y caracteres especiales excepto `+`

## 📊 Logging

El sistema registra logs detallados en todas las etapas:

```php
// Inicio del proceso
Log::info('📱 DirectSale::sendBillViaWhatsapp - Iniciando', [
    'bill_id' => $billId,
    'phone' => $phoneNumber
]);

// Éxito
Log::info('✅ DirectSale::sendBillViaWhatsapp - Enviado exitosamente', [
    'bill_id' => $billId,
    'phone' => $phoneNumber,
    'file_url' => $result['file_url'] ?? 'N/A'
]);

// Error
Log::error('❌ DirectSale::sendBillViaWhatsapp - Error inesperado', [
    'bill_id' => $billId,
    'error' => $th->getMessage(),
    'line' => $th->getLine()
]);
```

## 🛠️ Manejo de Errores

### Errores Controlados
1. **Factura no encontrada**: Muestra mensaje de error al usuario
2. **Error en webhook N8N**: Registra warning y notifica al usuario
3. **Número inválido**: Muestra mensaje en el modal sin cerrar

### Errores No Bloqueantes
- Si falla el envío por WhatsApp, la factura ya está creada
- El usuario recibe notificación pero el proceso continúa
- Se registran todos los errores en logs para debugging

## 🧪 Testing Manual

### Caso 1: Envío Exitoso
1. Crear una factura
2. En el modal, seleccionar "Sí, enviar"
3. Ingresar número válido: `3001234567`
4. Verificar:
   - ✅ Se cierra el modal
   - ✅ Se muestra mensaje de éxito
   - ✅ El PDF se recibe en WhatsApp
   - ✅ El ticket se descarga normalmente

### Caso 2: Omitir WhatsApp
1. Crear una factura
2. En el modal, seleccionar "No, continuar sin enviar"
3. Verificar:
   - ✅ Se cierra el modal inmediatamente
   - ✅ El proceso continúa normalmente
   - ✅ El ticket se descarga

### Caso 3: Número Inválido
1. Crear una factura
2. Seleccionar "Sí, enviar"
3. Ingresar número corto: `123`
4. Hacer clic en "Confirmar"
5. Verificar:
   - ✅ Se muestra mensaje de error
   - ✅ El modal NO se cierra
   - ✅ Puede corregir el número

## 🔐 Seguridad

### Validaciones del Lado del Servidor
- Verificación de existencia de factura
- Validación de formato telefónico
- Timeout configurado para evitar cuelgues

### Validaciones del Lado del Cliente
- Validación de formato antes de enviar
- Prevención de envíos duplicados
- Estados de carga para evitar clicks múltiples

## 📱 Responsividad

El modal es completamente responsive:
- **Desktop**: Modal centrado con ancho máximo de 512px
- **Tablet**: Se ajusta al ancho de la pantalla con padding
- **Mobile**: Modal ocupa el ancho completo con padding mínimo

## 🚀 Mejoras Futuras Posibles

1. **Autocompletar teléfono**: Usar el teléfono del cliente si está disponible
2. **Historial de envíos**: Registrar en BD los envíos exitosos
3. **Reintentos automáticos**: Si falla, permitir reintentar
4. **Vista previa**: Mostrar preview del PDF antes de enviar
5. **Múltiples destinatarios**: Permitir enviar a varios números

## 📞 Soporte

Si encuentras problemas:
1. Revisa los logs en `storage/logs/laravel.log`
2. Verifica la configuración en `.env`
3. Confirma que el webhook de N8N está activo
4. Verifica que Cloudinary está configurado correctamente

## ✅ Checklist de Implementación

- [x] Servicio WhatsApp actualizado para aceptar número personalizado
- [x] Componente modal creado y estilizado
- [x] Integración en DirectSale (Venta Rápida)
- [x] Integración en Bills/Create (Crear Factura)
- [x] Validaciones de número telefónico
- [x] Manejo de errores
- [x] Logging completo
- [x] Documentación
- [x] Payload actualizado según API de N8N (`fileName` en lugar de `Fileurl`)
- [x] **Campo `pdf_url` agregado a la tabla `bills`**
- [x] **Almacenamiento automático del URL público del PDF**
- [x] **Optimización: reutilización del PDF ya subido**
- [x] **Modal centrado correctamente en pantalla**

---

**Fecha de implementación**: Octubre 2025  
**Versión**: 1.0.0

