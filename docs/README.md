# 📚 Documentación del Proyecto POS Multi-Tenant

Bienvenido a la documentación completa del sistema POS con arquitectura multi-tenant.

## 🎯 Inicio Rápido

Si eres nuevo en el proyecto, te recomendamos seguir este orden:

1. 📖 [RESUMEN_CAMBIO_DOMINIO.md](RESUMEN_CAMBIO_DOMINIO.md) - Cambio rápido de dominio
2. 🔧 [COMO_FUNCIONA_APACHE_HOSTS.md](COMO_FUNCIONA_APACHE_HOSTS.md) - Entender cómo funciona todo
3. 📊 [DIAGRAMA_FLUJO_MULTITENANT.md](DIAGRAMA_FLUJO_MULTITENANT.md) - Visualización del flujo

## 🌐 Multi-Tenant y Dominios

### Documentación Principal

| Documento | Descripción | Nivel |
|-----------|-------------|-------|
| [RESUMEN_CAMBIO_DOMINIO.md](RESUMEN_CAMBIO_DOMINIO.md) | Guía rápida de 5 pasos para cambiar el dominio | ⚡ Rápido |
| [CAMBIAR_DOMINIO.md](CAMBIAR_DOMINIO.md) | Guía completa con ejemplos, funciones helper y FAQs | 📖 Completo |
| [COMO_FUNCIONA_APACHE_HOSTS.md](COMO_FUNCIONA_APACHE_HOSTS.md) | Explicación detallada del flujo hosts → Apache → Laravel → BD | 🎓 Técnico |
| [DIAGRAMA_FLUJO_MULTITENANT.md](DIAGRAMA_FLUJO_MULTITENANT.md) | Diagramas visuales del sistema multi-tenant | 📊 Visual |

### ¿Qué documento leer según tu necesidad?

**"Solo quiero cambiar el dominio rápido"**  
→ [RESUMEN_CAMBIO_DOMINIO.md](RESUMEN_CAMBIO_DOMINIO.md)

**"Quiero entender cómo usar las funciones helper"**  
→ [CAMBIAR_DOMINIO.md](CAMBIAR_DOMINIO.md) - Sección "Funciones Helper"

**"No entiendo cómo funciona el archivo hosts y Apache"**  
→ [COMO_FUNCIONA_APACHE_HOSTS.md](COMO_FUNCIONA_APACHE_HOSTS.md)

**"Quiero ver un diagrama del flujo completo"**  
→ [DIAGRAMA_FLUJO_MULTITENANT.md](DIAGRAMA_FLUJO_MULTITENANT.md)

**"Tengo un error y no sé qué pasa"**  
→ [COMO_FUNCIONA_APACHE_HOSTS.md](COMO_FUNCIONA_APACHE_HOSTS.md) - Sección "FAQ" y "Problemas Comunes"

## 📂 Otras Documentaciones

### Soluciones Multi-Tenant
[soluciones-multitenant/](soluciones-multitenant/)
- Soluciones específicas a problemas de multi-tenancy
- Assets y estilos en subdominios
- Configuraciones avanzadas

### Despliegue
[deploy/](deploy/)
- Despliegue en VPS
- Configuración de producción
- SSL/HTTPS
- Servidores web (Apache/Nginx)

### Scripts PowerShell
[scripts/](scripts/)
- Scripts de automatización
- Configuración de hosts
- Gestión de tenants
- Verificación del sistema

### Guías
[guias/](guias/)
- Guía para eliminar tenants
- Procedimientos específicos
- Buenas prácticas

### API
[api/](api/)
- Documentación de endpoints
- Autenticación
- Ejemplos de uso

### WhatsApp
[whatsapp/](whatsapp/)
- Integración con WhatsApp
- Webhooks
- Configuración de N8N

### UI
[ui/](ui/)
- Componentes de interfaz
- Estilos y temas
- Guías de diseño

### Resúmenes
[resumen/](resumen/)
- Resúmenes ejecutivos
- Arquitectura general
- Decisiones técnicas

## 🛠️ Herramientas y Scripts

### Scripts PowerShell Disponibles

| Script | Descripción | Uso |
|--------|-------------|-----|
| `setup_multitenant.ps1` | Configuración inicial completa del multi-tenant | `.\setup_multitenant.ps1` |
| `verify_multitenant.ps1` | Verifica la configuración multi-tenant | `.\verify_multitenant.ps1` |
| `add_tenant_subdomain.ps1` | Agrega subdominios al archivo hosts | `.\add_tenant_subdomain.ps1 -Subdomain empresa1` |
| `check_hosts.ps1` | Lista todos los dominios en el archivo hosts | `.\check_hosts.ps1` |

### Scripts PHP

| Script | Descripción | Uso |
|--------|-------------|-----|
| `fix_tenants_domains.php` | Actualiza dominios de tenants en la BD | `php fix_tenants_domains.php` |
| `verificar_tenants.php` | Lista todos los tenants existentes | `php verificar_tenants.php` |
| `eliminar_todos_tenants.php` | Elimina todos los tenants (⚠️ peligroso) | `php eliminar_todos_tenants.php` |

### Comandos Artisan Principales

```bash
# Crear un nuevo tenant
php artisan tenant:create

# Listar tenants
php artisan tenants:list

# Ejecutar migraciones en tenants
php artisan tenants:migrate

# Ejecutar seeders en tenants
php artisan tenants:seed

# Ejecutar comando en un tenant específico
php artisan tenants:run migracion --tenant=empresa1
```

## 🔧 Funciones Helper Globales

### Funciones de Dominio

```php
// Obtener el dominio central
centralDomain() // "dokploy.movete.cloud"
centralDomain(withWww: true) // "www.dokploy.movete.cloud"
centralDomain(withProtocol: true) // "http://dokploy.movete.cloud"

// Verificar si es un tenant
isTenantDomain() // true/false
isTenantDomain('empresa1.dokploy.movete.cloud') // true

// Obtener subdominio del tenant
tenantSubdomain() // "empresa1" (si estamos en empresa1.dokploy.movete.cloud)
```

### Funciones de Negocio

```php
// Formatear moneda
formatToCop(1000) // "$ 1,000"

// Obtener terminal del usuario
getTerminal() // Terminal instance

// Verificar si tiene terminal
hasTerminal() // true/false

// Verificar si terminal está abierta
hasTerminalOpen() // true/false
```

## 📊 Arquitectura del Sistema

### Estructura de Bases de Datos

```
pos_central (BD Central)
├── tenants (Información de tenants)
├── domains (Dominios de tenants)
└── users (Usuarios admin)

tenantempresa1 (BD Tenant 1)
├── users
├── products
├── sales
├── customers
└── ...

tenantempresa2 (BD Tenant 2)
├── users
├── products
├── sales
├── customers
└── ...
```

### Flujo de Peticiones

```
Navegador
  ↓
Archivo Hosts (127.0.0.1)
  ↓
Apache (VirtualHost con wildcard)
  ↓
Laravel (index.php)
  ↓
Middleware Tenancy (identifica tenant)
  ↓
Cambio de BD (tenantempresaX)
  ↓
Procesamiento de Ruta
  ↓
Respuesta al Usuario
```

## 🎓 Conceptos Clave

### Multi-Tenancy
Sistema donde **múltiples clientes (tenants)** comparten la **misma infraestructura** pero tienen **datos completamente aislados**.

### Wildcard DNS/VirtualHost
Configuración que permite capturar **todos los subdominios** con una sola regla:
```
*.dokploy.movete.cloud → Captura empresa1, empresa2, empresa3, etc.
```

### Archivo Hosts
Archivo local que mapea dominios a IPs **antes** de consultar DNS. Solo para desarrollo local.

### Dominio Central
El dominio base donde reside la aplicación principal y desde donde se sirven los assets.

### Tenant
Cada cliente/empresa que usa el sistema. Tiene su propio subdominio y base de datos.

## 🆘 Soporte y Resolución de Problemas

### Problemas Comunes

1. **"No se puede acceder al sitio"**
   - Verificar archivo hosts
   - Verificar que Apache esté corriendo
   - Ver [COMO_FUNCIONA_APACHE_HOSTS.md](COMO_FUNCIONA_APACHE_HOSTS.md#problemas-comunes)

2. **"Assets (CSS/JS) no cargan"**
   - Limpiar cachés: `php artisan config:clear`
   - Verificar `FixViteAssetsForTenants` middleware
   - Ver [CAMBIAR_DOMINIO.md](CAMBIAR_DOMINIO.md#preguntas-frecuentes)

3. **"Apache no inicia"**
   - Verificar sintaxis: `httpd.exe -t`
   - Revisar logs: `C:\xampp\apache\logs\error.log`
   - Ver [COMO_FUNCIONA_APACHE_HOSTS.md](COMO_FUNCIONA_APACHE_HOSTS.md#faq)

### Logs Importantes

```
# Logs de Laravel
storage/logs/laravel.log

# Logs de Apache (XAMPP)
C:\xampp\apache\logs\error.log
C:\xampp\apache\logs\access.log

# Logs específicos del proyecto
C:\xampp\apache\logs\dokploy.movete.cloud-error.log
C:\xampp\apache\logs\dokploy-tenants-error.log
```

### Comandos de Diagnóstico

```bash
# Verificar configuración multi-tenant
.\verify_multitenant.ps1

# Listar dominios en hosts
.\check_hosts.ps1

# Ver tenants en BD
php verificar_tenants.php

# Verificar sintaxis de Apache
C:\xampp\apache\bin\httpd.exe -t

# Limpiar cachés de Laravel
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## 📖 Glosario

| Término | Descripción |
|---------|-------------|
| **Tenant** | Cliente/empresa que usa el sistema |
| **Central Domain** | Dominio base (ej: dokploy.movete.cloud) |
| **Subdomain** | Dominio de un tenant (ej: empresa1.dokploy.movete.cloud) |
| **Virtual Host** | Configuración de Apache para múltiples sitios |
| **Wildcard** | Comodín que captura todos los subdominios (*) |
| **Hosts File** | Archivo que mapea dominios a IPs localmente |
| **DocumentRoot** | Carpeta raíz desde donde Apache sirve archivos |
| **Multi-Tenancy** | Arquitectura de múltiples clientes, un código |

## 🔗 Enlaces Útiles

- **Repositorio:** [GitHub](https://github.com/tu-repo)
- **Demo:** [https://test.hallpos.com.co/](https://test.hallpos.com.co/)
- **Sitio Web:** [https://hallpos.com.co](https://hallpos.com.co)
- **Documentación Tenancy:** [https://tenancyforlaravel.com/docs](https://tenancyforlaravel.com/docs)

## 📝 Contribuir a la Documentación

Si encuentras errores o quieres mejorar la documentación:

1. Los archivos de documentación están en `docs/`
2. Usa formato Markdown (.md)
3. Incluye ejemplos cuando sea posible
4. Mantén un tono claro y educativo

---

**¿No encuentras lo que buscas?** Revisa el [índice completo](#) o contacta al equipo de desarrollo.

---

<p align="center">
  <strong>Documentación generada para Hallpos POS Multi-Tenant</strong><br>
  Última actualización: Noviembre 2025
</p>

