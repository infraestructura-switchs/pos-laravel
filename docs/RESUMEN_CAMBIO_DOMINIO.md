# ⚡ Resumen Rápido: Cambio de Dominio

Guía ultra-rápida para cambiar el dominio de tu aplicación multi-tenant.

## 🎯 En 5 Pasos

### 1. Editar `.env`
```env
CENTRAL_DOMAIN=tudominio.com
APP_URL=http://tudominio.com
CENTRAL_DOMAINS=tudominio.com,www.tudominio.com
```

### 2. Editar archivo `hosts` (Windows)
```
# C:\Windows\System32\drivers\etc\hosts
127.0.0.1       tudominio.com
127.0.0.1       www.tudominio.com
127.0.0.1       empresa1.tudominio.com
127.0.0.1       empresa2.tudominio.com
```

### 3. Editar Apache Virtual Hosts
```apache
# C:\xampp\apache\conf\extra\httpd-vhosts.conf

<VirtualHost *:80>
    ServerName tudominio.com
    ServerAlias www.tudominio.com
    DocumentRoot "C:/ruta/proyecto/public"
    <Directory "C:/ruta/proyecto/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName tudominio.com
    ServerAlias *.tudominio.com
    DocumentRoot "C:/ruta/proyecto/public"
    <Directory "C:/ruta/proyecto/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 4. Ejecutar Comandos
```bash
# Limpiar cachés
php artisan config:clear
php artisan cache:clear
ipconfig /flushdns

# Actualizar dominios de tenants en BD
php fix_tenants_domains.php

# Reiniciar Apache
net stop Apache2.4
net start Apache2.4

# Si usas Vite
npm run dev
```

### 5. Probar
```
http://tudominio.com
http://empresa1.tudominio.com
```

---

## 📁 Archivos que Debes Modificar

| Archivo | Qué Cambiar | ¿Obligatorio? |
|---------|-------------|---------------|
| `.env` | `CENTRAL_DOMAIN=tudominio.com` | ✅ SÍ |
| `C:\Windows\System32\drivers\etc\hosts` | Agregar dominios → 127.0.0.1 | ✅ SÍ (desarrollo) |
| `C:\xampp\apache\conf\extra\httpd-vhosts.conf` | ServerName y ServerAlias | ✅ SÍ |

---

## 🔧 Funciones Helper Disponibles

```php
// Obtener dominio central
centralDomain() 
// → "tudominio.com"

centralDomain(withProtocol: true) 
// → "http://tudominio.com"

centralDomain(withWww: true, withProtocol: true) 
// → "http://www.tudominio.com"

// Verificar si es tenant
isTenantDomain() 
// → true si estamos en empresa1.tudominio.com

// Obtener subdominio
tenantSubdomain() 
// → "empresa1" (si estamos en empresa1.tudominio.com)
```

---

## ❓ Preguntas Rápidas

**¿Tengo que modificar archivos PHP?**  
❌ NO. Todo usa la función `centralDomain()` que lee del `.env`

**¿Funciona en producción?**  
✅ SÍ. Solo cambia el archivo `hosts` por DNS real.

**¿Se actualizan automáticamente los tenants?**  
⚠️ Debes ejecutar `php fix_tenants_domains.php` para actualizar la BD.

**¿Puedo tener múltiples dominios?**  
✅ Puedes agregar aliases en `config/tenancy.php`

---

## 🆘 Problemas Comunes

### Error: "No se puede acceder al sitio"
```bash
# Verificar archivo hosts
notepad C:\Windows\System32\drivers\etc\hosts

# Debe contener:
127.0.0.1    tudominio.com
```

### Apache no inicia
```powershell
# Verificar sintaxis
C:\xampp\apache\bin\httpd.exe -t

# Ver logs
C:\xampp\apache\logs\error.log
```

### Assets (CSS/JS) no cargan
```bash
# Limpiar cachés
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Verificar que CENTRAL_DOMAIN esté correcto en .env
```

---

## 📚 Documentación Completa

- [Cambiar Dominio (Guía Detallada)](CAMBIAR_DOMINIO.md)
- [Cómo Funciona Apache y Hosts](COMO_FUNCIONA_APACHE_HOSTS.md)
- [Diagrama de Flujo](DIAGRAMA_FLUJO_MULTITENANT.md)

---

## ✅ Checklist

- [ ] Editar `.env` con nuevo dominio
- [ ] Actualizar archivo `hosts`
- [ ] Modificar `httpd-vhosts.conf`
- [ ] Ejecutar `php artisan config:clear`
- [ ] Ejecutar `php fix_tenants_domains.php`
- [ ] Reiniciar Apache
- [ ] Limpiar caché DNS: `ipconfig /flushdns`
- [ ] Probar dominio central: `http://tudominio.com`
- [ ] Probar tenant: `http://empresa1.tudominio.com`

---

## 💡 Consejo Pro

Usa el script automatizado para configuración inicial:

```powershell
# Ejecutar como Administrador
.\setup_multitenant.ps1
```

Este script hace todo automáticamente (excepto cambiar el dominio en `.env`).

---

**¿Todo funcionando?** 🎉

Si tienes problemas, revisa la [documentación completa](CAMBIAR_DOMINIO.md) o los [logs de Apache](C:\xampp\apache\logs\error.log).

