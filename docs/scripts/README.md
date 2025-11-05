# Scripts PowerShell del Proyecto

Este directorio contiene la documentacion de todos los scripts PowerShell (.ps1) disponibles en el proyecto.

## Lista de Scripts

### Scripts Multi-Tenant

#### `verify_multitenant.ps1`
**Descripcion:** Verifica que toda la configuracion multi-tenant este correcta.

**Uso:**
```powershell
.\verify_multitenant.ps1
```

**Permisos requeridos:** Ninguno (solo lectura)

**Que verifica:**
- PHP instalado y version
- Apache corriendo
- Archivo hosts configurado
- Virtual Hosts de Apache configurados
- Sintaxis de Apache
- Paquete Tenancy instalado
- Archivos de configuracion de Tenancy
- Conectividad de dominios
- Archivo .env

**Salida:** Muestra estado [OK], [ERROR] o [WARNING] para cada verificacion.

---

#### `check_hosts.ps1`
**Descripcion:** Lista todos los dominios relacionados con dokploy.movete.cloud en el archivo hosts de Windows.

**Uso:**
```powershell
.\check_hosts.ps1
```

**Permisos requeridos:** Ninguno (solo lectura)

**Funcionalidad:**
- Lee el archivo hosts
- Busca todos los dominios que contienen "dokploy.movete.cloud"
- Muestra tabla con IP, dominio y numero de linea
- Categoriza entre dominio principal y subdominios
- Muestra recomendaciones si faltan dominios

**Salida:** Tabla de dominios encontrados y resumen.

---

#### `add_tenant_subdomain.ps1`
**Descripcion:** Agrega un nuevo subdominio al archivo hosts de Windows.

**Uso:**
```powershell
.\add_tenant_subdomain.ps1 -Subdomain "nombre-tenant"
```

**Permisos requeridos:** Administrador (requiere escribir en hosts)

**Parametros:**
- `-Subdomain` (obligatorio): Nombre del subdominio sin el dominio base (ej: "empresa1")

**Funcionalidad:**
- Valida formato del subdominio (solo letras, numeros y guiones)
- Verifica si el subdominio ya existe
- Agrega entrada `127.0.0.1 nombre-tenant.dokploy.movete.cloud` al archivo hosts
- Limpia cache DNS con `ipconfig /flushdns`

**Ejemplo:**
```powershell
.\add_tenant_subdomain.ps1 -Subdomain "mi-empresa"
# Agrega: 127.0.0.1 mi-empresa.dokploy.movete.cloud
```

---

#### `setup_multitenant.ps1`
**Descripcion:** Script completo de configuracion inicial del entorno multi-tenant.

**Uso:**
```powershell
.\setup_multitenant.ps1
```

**Permisos requeridos:** Administrador

**Proceso automatizado:**
1. Instala paquete Tenancy via Composer
2. Publica archivos de configuracion con `php artisan tenancy:install`
3. Ejecuta migraciones con `php artisan migrate`
4. Verifica configuracion de Apache (httpd.conf)
5. Crea/actualiza Virtual Hosts en httpd-vhosts.conf
6. Configura archivo hosts de Windows
7. Limpia cache DNS
8. Verifica sintaxis de Apache
9. Opcionalmente reinicia Apache

**IMPORTANTE:** Este script hace backup de archivos antes de modificarlos.

**Notas:**
- Crea backups con timestamp antes de modificar archivos
- Pide confirmacion antes de reiniciar Apache
- Si algo falla, puedes restaurar desde los backups

---

### Scripts de Mantenimiento

#### `clear_cache_simple.ps1`
**Descripcion:** Limpia todos los caches de Laravel.

**Uso:**
```powershell
.\clear_cache_simple.ps1
```

**Permisos requeridos:** Ninguno

**Comandos ejecutados:**
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan event:clear
```

**Uso recomendado:** Despues de cambios en configuracion, rutas o vistas.

---

#### `rebuild_assets.ps1`
**Descripcion:** Recompila los assets (CSS/JS) usando Vite.

**Uso:**
```powershell
.\rebuild_assets.ps1
```

**Permisos requeridos:** Ninguno

**Proceso:**
1. Instala dependencias de Node (`npm install`)
2. Limpia caches de Laravel
3. Compila assets con `npm run build`
4. Muestra archivos generados

**Uso recomendado:** 
- Despues de cambios en CSS/JS
- Cuando los estilos no se cargan en el navegador
- Despues de instalar nuevas dependencias de frontend

---

#### `diagnose_styles.ps1`
**Descripcion:** Diagnostica problemas con los estilos y assets.

**Uso:**
```powershell
.\diagnose_styles.ps1
```

**Permisos requeridos:** Ninguno

**Verificaciones:**
- Existencia de manifest.json
- Rutas de archivos CSS/JS en manifest
- Existencia de archivos CSS/JS compilados
- Tamaño de archivos
- APP_ENV en .env
- Scripts en package.json
- Si Vite dev server esta corriendo

**Uso recomendado:** Cuando los estilos no cargan o hay errores 404 de assets.

---

#### `check_files.ps1`
**Descripcion:** Lista archivos CSS/JS compilados y muestra contenido de manifest.json.

**Uso:**
```powershell
.\check_files.ps1
```

**Permisos requeridos:** Ninguno

**Salida:**
- Lista de archivos CSS en public/build/assets/
- Lista de archivos JS en public/build/assets/
- Contenido completo de manifest.json
- APP_ENV actual

**Uso recomendado:** Para verificar que los assets se compilaron correctamente.

---

### Scripts de WhatsApp

#### `check_whatsapp_status.ps1`
**Descripcion:** Verifica la configuracion del webhook de WhatsApp.

**Uso:**
```powershell
.\check_whatsapp_status.ps1
```

**Permisos requeridos:** Ninguno

**Verificaciones:**
1. URL del webhook en .env (variable N8N_WHATSAPP_WEBHOOK_URL)
2. Logs recientes de WhatsApp
3. Cache de configuracion
4. Muestra comandos disponibles para debugging

**Uso recomendado:** Cuando WhatsApp no envia mensajes o hay problemas con webhooks.

---

### Scripts de Base de Datos

#### `run_migration.ps1`
**Descripcion:** Ejecuta una migracion especifica de Factro.

**Uso:**
```powershell
.\run_migration.ps1
```

**Permisos requeridos:** Ninguno

**Funcionalidad:**
- Configura charset UTF-8
- Ejecuta migracion de tabla factro_configurations
- Muestra mensaje de confirmacion

**Nota:** Este script esta configurado para una migracion especifica. Modifica el script para otras migraciones.

---

## Resumen Rapido

| Script | Permisos | Proposito |
|--------|----------|-----------|
| `verify_multitenant.ps1` | Usuario | Verificar config multi-tenant |
| `check_hosts.ps1` | Usuario | Listar dominios en hosts |
| `add_tenant_subdomain.ps1` | Admin | Agregar subdominio a hosts |
| `setup_multitenant.ps1` | Admin | Setup completo multi-tenant |
| `clear_cache_simple.ps1` | Usuario | Limpiar caches Laravel |
| `rebuild_assets.ps1` | Usuario | Recompilar assets |
| `diagnose_styles.ps1` | Usuario | Diagnosticar estilos |
| `check_files.ps1` | Usuario | Verificar archivos compilados |
| `check_whatsapp_status.ps1` | Usuario | Verificar webhook WhatsApp |
| `run_migration.ps1` | Usuario | Ejecutar migracion especifica |

## Convenciones de Salida

Todos los scripts usan colores y prefijos consistentes:

- **[OK]** - Verde: Operacion exitosa
- **[ERROR]** - Rojo: Error o problema
- **[WARNING]** - Amarillo: Advertencia o accion recomendada

## Ejecutar Scripts

### Metodo 1: Desde PowerShell
```powershell
cd C:\ruta\al\proyecto
.\nombre_script.ps1
```

### Metodo 2: Con permisos de administrador
1. Click derecho en PowerShell
2. "Ejecutar como administrador"
3. Navegar al proyecto
4. Ejecutar script

### Metodo 3: Desde el proyecto
Si estas en la carpeta del proyecto:
```powershell
.\setup_multitenant.ps1
```

## Solucion de Problemas

### Error: "No se puede cargar porque la ejecucion de scripts esta deshabilitada"

Ejecuta en PowerShell como administrador:
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Error: "No se encuentra el archivo"

Asegurate de estar en el directorio raiz del proyecto Laravel donde esta el archivo `.env`.

### Script no hace lo esperado

Revisa:
1. Permisos requeridos (algunos necesitan Admin)
2. Que las rutas en el script coincidan con tu instalacion (ej: C:\xampp)
3. Que tengas PHP y Composer en el PATH

## Notas Importantes

1. **Backups:** Los scripts que modifican archivos del sistema (hosts, Apache) crean backups automaticos.
2. **Rutas hardcodeadas:** Algunos scripts asumen XAMPP en `C:\xampp`. Modifica si tienes otra instalacion.
3. **Sin emojis:** Todos los scripts fueron limpiados para evitar errores en consolas que no soportan UTF-8.

## Contribuir

Si agregas nuevos scripts:
1. Elimina todos los emojis
2. Usa prefijos [OK], [ERROR], [WARNING]
3. Agrega comentarios en espanol
4. Documenta en este README
5. Prueba en PowerShell 5.1 y superior

