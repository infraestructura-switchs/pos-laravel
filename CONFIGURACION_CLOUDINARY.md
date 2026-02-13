# ☁️ Configuración de Cloudinary

## ✅ Problema Resuelto

Las variables de entorno de Cloudinary no estaban configuradas en Docker, lo que causaba errores al subir imágenes de productos y PDFs de facturas.

---

## 🔧 Configuración Aplicada

### Variables Añadidas a `docker-compose.nginx.yml`

```yaml
environment:
  # Cloudinary Configuration
  - CLOUDINARY_CLOUD_NAME=dxktixdby
  - CLOUDINARY_API_KEY=672933399666117
  - CLOUDINARY_API_SECRET=4q1LKVcTy_CDxnWwSxHuJyixrrI
  - CLOUDINARY_SECURE=true
  - CLOUDINARY_FOLDER=pos-images
```

### Datos de tu Cuenta Cloudinary

- **Cloud Name**: `dxktixdby`
- **API Key**: `672933399666117`
- **API Secret**: `4q1LKVcTy_CDxnWwSxHuJyixrrI`
- **Folder**: `pos-images` (carpeta donde se guardan las imágenes y PDFs)

---

## 📦 ¿Qué se Sube a Cloudinary?

### 1. Imágenes de Productos
- **Ubicación en Cloudinary**: `pos-images/`
- **Formato**: JPG, PNG, WEBP
- **Transformaciones**: Resize automático a 800x800px
- **Optimización**: Calidad y formato automático

### 2. PDFs de Facturas
- **Ubicación en Cloudinary**: `pos-images/pdfs/` o `pos-images/bills/`
- **Formato**: PDF (raw file)
- **Nombre**: `bill_[id]_[timestamp].pdf`
- **Uso**: Envío por WhatsApp, descarga, almacenamiento

---

## 🚀 Reinicio de Docker Realizado

Los contenedores fueron reiniciados para aplicar las nuevas variables:

```bash
wsl docker compose -f docker-compose.nginx.yml down
wsl docker compose -f docker-compose.nginx.yml up -d
```

✅ Contenedores reiniciados:
- `laravel-php-fpm` ✅
- `laravel-nginx-multitenant` ✅
- `laravel-mysql` ✅
- `laravel-phpmyadmin` ✅
- `laravel-redis` ✅

---

## 🧪 Cómo Probar Cloudinary

### 1. Subir Imagen de Producto

1. Ve a **Productos** en el menú
2. Crea o edita un producto
3. Sube una imagen
4. La imagen se subirá automáticamente a Cloudinary
5. Verás la imagen optimizada en la lista de productos

### 2. Generar PDF de Factura

1. Realiza una venta
2. Ve a **Facturas**
3. Click en "Ver PDF" o "Descargar PDF"
4. El PDF se genera y se sube a Cloudinary automáticamente
5. El URL del PDF se guarda en la base de datos

### 3. Verificar en Cloudinary

Accede a tu panel de Cloudinary:
- **URL**: https://console.cloudinary.com/
- **Cloud Name**: `dxktixdby`

Verás las carpetas:
- `pos-images/` - Imágenes de productos
- `pos-images/pdfs/` - PDFs de facturas
- `pos-images/bills/` - Facturas generadas

---

## 🔍 Verificar Configuración

### Desde Terminal

```bash
wsl docker exec laravel-php-fpm php artisan tinker --execute="echo config('cloudinary.cloud_name');"
```

Debería mostrar: `dxktixdby`

### Verificar que las Variables Estén Cargadas

```bash
wsl docker exec laravel-php-fpm env | grep CLOUDINARY
```

Debería mostrar:
```
CLOUDINARY_CLOUD_NAME=dxktixdby
CLOUDINARY_API_KEY=672933399666117
CLOUDINARY_API_SECRET=4q1LKVcTy_CDxnWwSxHuJyixrrI
CLOUDINARY_SECURE=true
CLOUDINARY_FOLDER=pos-images
```

---

## 🐛 Errores que se Solucionaron

### Error 1: "Cloudinary credentials not configured"

**Causa**: Las variables de entorno no estaban en Docker
**Solución**: ✅ Agregadas a `docker-compose.nginx.yml`

### Error 2: "No se pudo subir la imagen"

**Causa**: API Key o Secret incorrectos
**Solución**: ✅ Configurados correctamente

### Error 3: "PDF no se genera con URL"

**Causa**: Cloudinary no estaba configurado, el PDF se generaba pero no se subía
**Solución**: ✅ Ahora se sube automáticamente y se guarda el URL en BD

---

## 📝 Servicios que Usan Cloudinary

### 1. CloudinaryService.php
```php
// Subir imagen
$cloudinary->uploadImage($filePath, $options);

// Subir PDF (raw file)
$cloudinary->uploadRaw($filePath, $options);

// Eliminar archivo
$cloudinary->deleteImage($publicId);
```

### 2. ImageService.php
- Gestiona imágenes de productos
- Sube, elimina y obtiene URLs optimizadas

### 3. UploadBillPdfToCloudinary.php (Job)
- Job en cola para subir PDFs de facturas
- Se ejecuta en segundo plano
- Guarda el URL en la tabla `bills`

### 4. WhatsappPdfService.php
- Genera PDF de factura
- Sube a Cloudinary
- Envía el URL por WhatsApp vía webhook

---

## 🔐 Seguridad

### API Secret

El API Secret (`4q1LKVcTy_CDxnWwSxHuJyixrrI`) es **privado** y solo debe estar en:
- Variables de entorno del servidor (Docker, .env)
- **NUNCA** en código frontend
- **NUNCA** en repositorios públicos

### Permisos en Cloudinary

Asegúrate de que tu cuenta Cloudinary tenga:
- ✅ Upload habilitado
- ✅ Transformaciones habilitadas
- ✅ Suficiente almacenamiento

---

## 📊 Límites de Cloudinary

### Plan Gratuito (Free):
- **Almacenamiento**: 25 GB
- **Bandwidth**: 25 GB/mes
- **Transformaciones**: 25,000/mes
- **Imágenes**: Ilimitadas (dentro del storage)

### Monitoreo

Revisa tu uso en: https://console.cloudinary.com/console/usage

---

## 🎯 Rutas de Prueba

### 1. Probar Subida de PDF (API Pública)

```
GET http://adminpos.dokploy.movete.cloud/api/pdf-upload/bill/{bill_id}
```

Ejemplo:
```
http://adminpos.dokploy.movete.cloud/api/pdf-upload/bill/1
```

Esto:
1. Genera el PDF de la factura
2. Lo sube a Cloudinary
3. Retorna el URL

### 2. Ver Configuración de Cloudinary

```bash
wsl docker exec laravel-php-fpm php artisan tinker
```

Luego:
```php
config('cloudinary')
```

---

## ✅ Checklist Final

- [x] Variables de Cloudinary añadidas a Docker
- [x] Contenedores reiniciados
- [x] API Key y Secret configurados
- [x] Cloud Name correcto
- [x] Carpeta `pos-images` configurada
- [x] Servicio CloudinaryService funcionando
- [x] Jobs de subida de PDF configurados

---

## 🚀 ¡Listo para Usar!

Ahora puedes:

1. ✅ **Subir imágenes de productos** - Se guardarán en Cloudinary
2. ✅ **Generar PDFs de facturas** - Se subirán automáticamente
3. ✅ **Enviar facturas por WhatsApp** - Con URL de Cloudinary
4. ✅ **Optimizar imágenes** - Cloudinary lo hace automáticamente
5. ✅ **Almacenamiento ilimitado** - Hasta el límite de tu plan

---

## 📞 Contacto Cloudinary

Si necesitas aumentar límites o resolver problemas:
- **Dashboard**: https://console.cloudinary.com/
- **Documentación**: https://cloudinary.com/documentation
- **Soporte**: https://support.cloudinary.com/

---

## 🔄 Si Cambias las Credenciales

Si en el futuro necesitas cambiar las credenciales de Cloudinary:

1. Edita `docker-compose.nginx.yml`
2. Cambia los valores de `CLOUDINARY_*`
3. Reinicia Docker:
```bash
wsl docker compose -f docker-compose.nginx.yml down
wsl docker compose -f docker-compose.nginx.yml up -d
```

