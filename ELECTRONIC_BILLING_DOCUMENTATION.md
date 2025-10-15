# Documentación de Facturación Electrónica con Factus

## 📋 Índice

1. [Resumen de la Implementación](#resumen-de-la-implementación)
2. [Arquitectura](#arquitectura)
3. [Configuración](#configuración)
4. [Flujo de Facturación](#flujo-de-facturación)
5. [API Endpoints](#api-endpoints)
6. [Modelos](#modelos)
7. [Servicios](#servicios)
8. [Comandos Artisan](#comandos-artisan)
9. [Manejo de Errores](#manejo-de-errores)
10. [Logging](#logging)
11. [Testing](#testing)
12. [Troubleshooting](#troubleshooting)

---

## Resumen de la Implementación

Se ha implementado un sistema completo de facturación electrónica integrado con la API de Factus que permite:

- ✅ Validar facturas con la DIAN a través de Factus
- ✅ Obtener CUFE (Código Único de Factura Electrónica)
- ✅ Descargar XML y PDF oficiales
- ✅ Generar códigos QR para las facturas
- ✅ Manejo robusto de errores
- ✅ Logging detallado para diagnóstico
- ✅ Comandos de utilidad para testing

---

## Arquitectura

### Componentes Principales

```
app/
├── Models/
│   ├── Bill.php                    # Modelo de factura
│   ├── ElectronicBill.php          # Modelo de factura electrónica
│   ├── FactusConfiguration.php     # Configuración de Factus
│   └── AccessToken.php             # Token de autenticación
│
├── Services/
│   ├── BillService.php             # Lógica de negocio de facturas
│   ├── FactusConfigurationService.php  # Gestión de configuración
│   └── Factus/
│       ├── HttpService.php         # Cliente HTTP para Factus
│       └── ElectronicBillService.php  # Lógica de facturación electrónica
│
├── Controllers/
│   └── Admin/
│       └── ElectronicBillController.php  # Endpoints de facturación electrónica
│
├── Livewire/
│   └── Admin/
│       └── DirectSale/
│           └── Create.php          # Componente de venta directa
│
├── Traits/
│   └── TokenTrait.php              # Gestión de tokens de autenticación
│
└── Console/
    └── Commands/
        └── TestFactusConnection.php  # Comando de diagnóstico
```

### Flujo de Datos

```
[Usuario crea factura] 
    ↓
[DirectSale::storeBill]
    ↓
[Bill guardada en BD]
    ↓
[BillService::validateElectronicBill]
    ↓
[ElectronicBillService::validate]
    ↓
[HttpService::apiHttp] → [API Factus]
    ↓
[Respuesta con CUFE, QR, etc.]
    ↓
[ElectronicBillService::saveElectronicBill]
    ↓
[ElectronicBill guardada en BD]
    ↓
[Usuario recibe confirmación]
```

---

## Configuración

### 1. Variables de Entorno

No se requieren variables de entorno. La configuración se almacena en la base de datos.

### 2. Configurar Credenciales de Factus

#### Opción A: Usando el script interactivo

```bash
php update_factus_config.php
```

El script te pedirá:
- URL de la API (sandbox o producción)
- Client ID
- Client Secret
- Email
- Password
- Si deseas habilitar la facturación electrónica

#### Opción B: Manualmente desde Tinker

```bash
php artisan tinker
```

```php
$config = App\Models\FactusConfiguration::first();
$config->api = [
    'url' => 'https://api-sandbox.factus.com.co/',  // Sandbox
    // 'url' => 'https://api.factus.com.co/',      // Producción
    'client_id' => 'tu_client_id',
    'client_secret' => 'tu_client_secret',
    'email' => 'tu_email@example.com',
    'password' => 'tu_password'
];
$config->is_api_enabled = true;
$config->save();

// Limpiar caché
Cache::forget('api_configuration');
Cache::forget('is_api_enabled');
```

### 3. Probar la Conexión

```bash
php artisan factus:test-connection
```

Este comando:
- ✅ Verifica que la configuración esté completa
- ✅ Prueba la autenticación con Factus
- ✅ Muestra el estado del token actual
- ✅ Permite actualizar el token si la prueba es exitosa

---

## Flujo de Facturación

### 1. Crear Factura

Cuando un usuario crea una factura en la vista "Vender":

1. **Validaciones previas**:
   - Terminal activo
   - Inventario disponible
   - Rango de numeración activo

2. **Crear factura en BD**:
   - Se crea el registro `Bill`
   - Se crean los `DetailBill`
   - Se calculan impuestos

3. **Validar con Factus** (si está habilitado):
   ```php
   BillService::validateElectronicBill($bill);
   ```

4. **Respuesta de Factus**:
   - Número de factura oficial
   - CUFE
   - Imagen QR (base64)
   - Rango de numeración usado

5. **Guardar datos electrónicos**:
   - Se crea/actualiza `ElectronicBill`
   - Se actualiza el número de la factura

### 2. Descargar Documentos

#### PDF Oficial

```php
// Desde el controlador
GET /admin/facturas-electronicas/{bill}/pdf

// Desde código
$pdfContent = ElectronicBillService::downloadPdf($bill);
```

#### XML Oficial

```php
// Desde el controlador
GET /admin/facturas-electronicas/{bill}/xml

// Desde código
$xmlContent = ElectronicBillService::downloadXml($bill);
```

---

## API Endpoints

### Rutas Disponibles

| Método | Ruta | Descripción |
|--------|------|-------------|
| `GET` | `/admin/facturas-electronicas/{bill}/pdf` | Descargar PDF oficial |
| `GET` | `/admin/facturas-electronicas/{bill}/xml` | Descargar XML oficial |
| `GET` | `/admin/facturas-electronicas/{bill}/info` | Información de la factura electrónica |

### Ejemplo de Uso

```javascript
// Descargar PDF
window.location.href = `/admin/facturas-electronicas/${billId}/pdf`;

// Descargar XML
window.location.href = `/admin/facturas-electronicas/${billId}/xml`;

// Obtener información
fetch(`/admin/facturas-electronicas/${billId}/info`)
    .then(response => response.json())
    .then(data => console.log(data));
```

---

## Modelos

### Bill

```php
// Verificar si es factura electrónica
$bill->is_electronic  // bool

// Verificar si está validada
$bill->is_validated  // bool

// Obtener factura electrónica
$bill->electronicBill  // ElectronicBill | null
```

### ElectronicBill

```php
$electronicBill = $bill->electronicBill;

// Propiedades
$electronicBill->number          // string - Número oficial
$electronicBill->cufe            // string - Código Único
$electronicBill->qr_image        // string - Base64 del QR
$electronicBill->is_validated    // boolean
$electronicBill->numbering_range // array

// Accessors
$electronicBill->has_qr_image  // boolean
$electronicBill->pdf_url       // string
$electronicBill->xml_url       // string
```

### FactusConfiguration

```php
$config = FactusConfiguration::first();

// Propiedades
$config->is_api_enabled  // boolean
$config->api             // array [url, client_id, client_secret, email, password]
```

---

## Servicios

### ElectronicBillService

#### Métodos Principales

```php
// Validar factura con Factus
$response = ElectronicBillService::validate($bill);

// Guardar respuesta en BD
ElectronicBillService::saveElectronicBill($responseData, $bill);

// Descargar documentos
$pdfContent = ElectronicBillService::downloadPdf($bill);
$xmlContent = ElectronicBillService::downloadXml($bill);

// Obtener URLs
$pdfUrl = ElectronicBillService::getPdfUrl($bill);
$xmlUrl = ElectronicBillService::getXmlUrl($bill);
```

#### Validaciones Implementadas

El servicio valida automáticamente:
- ✅ Factura con productos
- ✅ Productos con impuestos configurados
- ✅ Productos con código de referencia
- ✅ Cliente con tipo de documento
- ✅ Cliente con email (obligatorio para facturación electrónica)
- ✅ Cliente con teléfono (obligatorio para facturación electrónica)
- ✅ Método de pago configurado

### BillService

```php
// Validar factura electrónica
BillService::validateElectronicBill($bill);

// Este método:
// 1. Verifica si está habilitada la facturación electrónica
// 2. Valida con Factus
// 3. Guarda la respuesta
// 4. Maneja errores automáticamente
```

### FactusConfigurationService

```php
// Verificar si está habilitada
$enabled = FactusConfigurationService::isApiEnabled();

// Obtener configuración
$config = FactusConfigurationService::apiConfiguration();
// Retorna: ['url', 'client_id', 'client_secret', 'email', 'password']
```

---

## Comandos Artisan

### factus:test-connection

Prueba la conexión con Factus y valida las credenciales.

```bash
php artisan factus:test-connection
```

**Output:**
```
🔍 Probando conexión con Factus...

1. Verificando si la facturación electrónica está habilitada...
✅ La facturación electrónica está HABILITADA

2. Obteniendo configuración de Factus...
+---------------+------------------------------------+
| Campo         | Valor                              |
+---------------+------------------------------------+
| URL           | https://api-sandbox.factus.com.co/ |
| Client ID     | 9f561c53-d9b7-459b-9...            |
| Client Secret | WEN7SpeQc8G8qAzfYQpi...            |
| Email         | sandbox@factus.com.co              |
| Password      | ************                       |
+---------------+------------------------------------+

3. Verificando token existente...
   Token encontrado:
   - Creado: 2025-10-07 12:10:28
   - Expira: 2025-10-07 14:20:36
   ⚠️  El token ha EXPIRADO

4. Probando autenticación con Factus...
   Status Code: 200
   ✅ AUTENTICACIÓN EXITOSA
   - Token recibido: eyJ0eXAiOiJKV1QiLCJhbGc...
   - Expira en: 3600 segundos

¿Deseas actualizar el token en la base de datos? (s/n): s
✅ Token actualizado correctamente

🎉 ¡Prueba completada exitosamente!
```

---

## Manejo de Errores

### Tipos de Errores

#### 1. Errores de Configuración

```php
// No existe configuración
throw new CustomException('No se ha configurado la facturación electrónica...');

// Falta un campo requerido
throw new CustomException('Falta la configuración: email...');
```

#### 2. Errores de Autenticación

```php
// Credenciales inválidas
throw new CustomException('Error al autenticarse con la API de Factus. Verifique las credenciales...');

// Token expirado
// Se refresca automáticamente
```

#### 3. Errores de Validación

```php
// Cliente sin email
throw new CustomException('El cliente no tiene email configurado. El email es obligatorio...');

// Producto sin referencia
throw new CustomException("El producto '{$name}' no tiene código de referencia configurado");
```

#### 4. Errores de la API

```php
// Respuesta inválida
throw new CustomException('La respuesta de Factus no contiene los datos esperados');

// Error de la DIAN
// Se captura de ValidationException
```

### Ejemplo de Manejo en el Frontend

```php
// En DirectSale::Create
try {
    BillService::validateElectronicBill($bill);
} catch (CustomException $ce) {
    return $this->emit('error', $ce->getMessage());
} catch (ValidationException $ve) {
    foreach ($ve->errors() as $field => $messages) {
        foreach ($messages as $message) {
            $this->addError($field, $message);
        }
    }
} catch (\Throwable $th) {
    return $this->emit('error', 'Ha sucedido un error inesperado...');
}
```

---

## Logging

Todos los procesos están completamente logueados para facilitar el diagnóstico.

### Logs Principales

#### Proceso de Facturación

```
[14:56:58] LOG.info: 🚀 DirectSale::storeBill - Iniciando proceso de facturación
[14:56:58] LOG.info: 🔍 DirectSale::storeBill - Validando terminal e inventario
[14:56:58] LOG.info: 💾 DirectSale::storeBill - Creando factura en BD
[14:56:58] LOG.info: ✅ DirectSale::storeBill - Factura creada exitosamente
[14:56:58] LOG.info: 🔌 DirectSale::storeBill - Verificando facturación electrónica
[14:56:58] LOG.info: ⚡ DirectSale::storeBill - Facturación electrónica HABILITADA
```

#### Validación con Factus

```
[14:56:58] LOG.info: 🔐 DirectSale::validateElectronicBill - Iniciando validación
[14:56:58] LOG.info: 📤 ElectronicBillService::validate - Preparando datos para Factus
[14:56:58] LOG.info: 🔧 ElectronicBillService::prepareData - Iniciando preparación de datos
[14:56:58] LOG.info: ✅ ElectronicBillService::prepareData - Items preparados
[14:56:58] LOG.info: 📡 ElectronicBillService::validate - Enviando factura a Factus
[14:56:59] LOG.info: ✅ ElectronicBillService::validate - Respuesta recibida de Factus
[14:56:59] LOG.info: 💾 ElectronicBillService::saveElectronicBill - Guardando factura electrónica
[14:56:59] LOG.info: ✅ ElectronicBillService::saveElectronicBill - Factura electrónica creada
```

#### Autenticación

```
[14:56:58] LOG.info: 🔐 TokenTrait - Obteniendo nuevo token de acceso...
[14:56:58] LOG.info: 📡 TokenTrait - Enviando solicitud de autenticación a Factus
[14:56:59] LOG.info: 📥 TokenTrait - Respuesta recibida de Factus
[14:56:59] LOG.info: ✅ TokenTrait - Token de acceso creado exitosamente
```

### Ver Logs

```bash
# En tiempo real
tail -f storage/logs/laravel.log

# Filtrar por facturación electrónica
tail -f storage/logs/laravel.log | grep -i "factus\|electronic"

# Buscar errores
tail -f storage/logs/laravel.log | grep "ERROR"
```

---

## Testing

### Prerequisitos para Testing

1. **Tener credenciales válidas de Factus**
2. **Cliente de prueba configurado**:
   - Con email válido
   - Con teléfono
   - Con tipo de documento

3. **Productos configurados**:
   - Con código de referencia
   - Con impuestos

### Prueba Manual

1. **Habilitar facturación electrónica**:
```bash
php artisan tinker --execute="App\Models\FactusConfiguration::first()->update(['is_api_enabled' => true]);"
php artisan cache:clear
```

2. **Ir a "Vender"** y crear una factura

3. **Verificar logs**:
```bash
tail -f storage/logs/laravel.log
```

4. **Verificar en BD**:
```bash
php artisan tinker --execute="dd(App\Models\ElectronicBill::latest()->first());"
```

---

## Troubleshooting

### Problema: "Error al refrescar el token de acceso"

**Causa**: Credenciales inválidas o token expirado.

**Solución**:
```bash
php artisan factus:test-connection
```

---

### Problema: "El cliente no tiene email configurado"

**Causa**: El cliente no tiene email y es obligatorio para facturación electrónica.

**Solución**: Editar el cliente y agregar un email válido.

---

### Problema: "El producto no tiene código de referencia"

**Causa**: El producto no tiene el campo `reference` configurado.

**Solución**: Editar el producto y agregar una referencia única.

---

### Problema: La facturación electrónica no se ejecuta

**Causa**: Está deshabilitada o el caché no se ha limpiado.

**Solución**:
```bash
php artisan cache:clear
php artisan config:clear
php artisan tinker --execute="dd(App\Services\FactusConfigurationService::isApiEnabled());"
```

---

### Problema: "No hay rango de numeración de Factus configurado"

**Causa**: El terminal no tiene `factus_numbering_range_id`.

**Solución**:
1. Crear un rango de numeración en Factus
2. Obtener el ID del rango
3. Actualizar el terminal:
```bash
php artisan tinker
```
```php
$terminal = App\Models\Terminal::find(1);
$terminal->factus_numbering_range_id = 123;  // ID del rango en Factus
$terminal->save();
```

---

## Mejoras Futuras

### Recomendaciones

1. **Implementar cola de trabajos**:
   - Enviar facturas a Factus en segundo plano
   - Evitar timeouts en la UI

2. **Sincronización de rangos**:
   - Obtener rangos de numeración desde Factus automáticamente

3. **Notificaciones por email**:
   - Enviar PDF y XML al cliente por correo

4. **Interfaz de administración**:
   - Vista para consultar facturas electrónicas
   - Filtros y búsqueda
   - Reenvío de facturas

5. **Métricas y reportes**:
   - Dashboard de facturas electrónicas
   - Estadísticas de validación
   - Errores comunes

---

## Soporte

Para problemas o preguntas:

1. **Revisar logs**: `storage/logs/laravel.log`
2. **Ejecutar diagnóstico**: `php artisan factus:test-connection`
3. **Verificar configuración**: Ver sección [Configuración](#configuración)
4. **Revisar troubleshooting**: Ver sección [Troubleshooting](#troubleshooting)

---

**Fecha de última actualización**: Octubre 7, 2025
**Versión**: 1.0.0

