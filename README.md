# 🏪 Sistema POS Multi-Tenant

> Sistema de punto de venta multi-tenant desarrollado con Laravel, Livewire, Docker y Nginx.

[![Laravel](https://img.shields.io/badge/Laravel-9.x-red.svg)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-2.x-blue.svg)](https://laravel-livewire.com)
[![Docker](https://img.shields.io/badge/Docker-Enabled-blue.svg)](https://www.docker.com)

---

## 🚀 Inicio Rápido

### Requisitos

- Docker Desktop con WSL2
- Windows 10/11
- Git

### Instalación

```bash
# 1. Clonar repositorio
git clone <repository-url>
cd app-pos-laravel

# 2. Iniciar Docker
.\iniciar_docker_wsl.ps1

# 3. Configurar aplicación
docker compose -f docker-compose.nginx.yml exec php bash
bash crear_env.sh
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan livewire:publish --assets
exit

# 4. Configurar hosts (como administrador)
# Editar: C:\Windows\System32\drivers\etc\hosts
# Agregar: 127.0.0.1  adminpos.dokploy.movete.cloud

# 5. Acceder
# URL: http://adminpos.dokploy.movete.cloud/login
# Usuario: superadmin@gmail.com
# Contraseña: 12345678
```

---

## 📚 Documentación

Para documentación completa, ver:

- **[DOCUMENTACION.md](./DOCUMENTACION.md)** - Guía completa de instalación, configuración y troubleshooting
- **[ARQUITECTURA_MULTITENANT.md](./ARQUITECTURA_MULTITENANT.md)** - Detalles de arquitectura multi-tenant

---

## 🏗️ Arquitectura

### Multi-Tenancy

- **Dominio Central:** `adminpos.dokploy.movete.cloud` (Administración global)
- **Tenants:** `empresa.dokploy.movete.cloud` (Empresas independientes)
- **Sub-Tenants:** `sucursal.empresa.dokploy.movete.cloud` (Sucursales/franquicias)

### Tecnologías

- **Backend:** Laravel 9.x
- **Frontend:** Livewire 2.x, TailwindCSS, AlpineJS
- **Base de datos:** MySQL 8.0
- **Caché:** Redis
- **Web Server:** Nginx
- **Containers:** Docker + Docker Compose

---

## 📋 Comandos Útiles

```bash
# Docker
docker compose -f docker-compose.nginx.yml up -d        # Iniciar
docker compose -f docker-compose.nginx.yml down         # Detener
docker compose -f docker-compose.nginx.yml logs -f php  # Ver logs

# Laravel
docker compose -f docker-compose.nginx.yml exec php php artisan optimize:clear  # Limpiar cachés
docker compose -f docker-compose.nginx.yml exec php php artisan tenants:list    # Ver tenants

# Base de datos
docker compose -f docker-compose.nginx.yml exec mysql mysql -uroot -proot_password pos_central
```

---

## 🐛 Problemas Comunes

### Error: Livewire no carga (404)

```bash
docker compose -f docker-compose.nginx.yml exec php php artisan livewire:publish --assets
```

### Error: Lentitud

```bash
# Verificar que SESSION_DRIVER=redis en .env
# Limpiar logs grandes
echo '' > storage/logs/laravel.log
php artisan config:cache
```

### Error: Puerto 80 ocupado (Apache/XAMPP)

```powershell
.\detener_apache.ps1
```

Ver más soluciones en [DOCUMENTACION.md](./DOCUMENTACION.md)

---

## 📁 Estructura del Proyecto

```
app-pos-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Livewire/        # Componentes Livewire
│   │   └── Middleware/
│   ├── Models/
│   └── Services/
├── config/
│   ├── tenancy.php          # Configuración multi-tenant
│   └── livewire.php
├── docker/
│   ├── php/Dockerfile       # Imagen PHP customizada
│   └── nginx/
├── routes/
│   ├── web.php              # Rutas dominio central
│   ├── tenant.php           # Rutas para tenants
│   └── admin.php
├── docker-compose.nginx.yml
└── .env
```

---

## 🔐 Credenciales por Defecto

### Super Admin (Dominio Central)
- **Email:** `superadmin@gmail.com`
- **Password:** `12345678`

### Admin (Por Tenant)
- **Email:** Email proporcionado al crear el tenant
- **Password:** Password proporcionado al crear el tenant

---

## ⚡ Optimizaciones Aplicadas

✅ Sesiones en Redis (5-10x más rápido)  
✅ Opcache habilitado  
✅ Precarga de permisos (evita N+1)  
✅ Cache de configuración  
✅ Assets de Livewire optimizados

---

## 📞 Soporte

- Ver logs: `storage/logs/laravel.log`
- Documentación completa: [DOCUMENTACION.md](./DOCUMENTACION.md)
- Arquitectura: [ARQUITECTURA_MULTITENANT.md](./ARQUITECTURA_MULTITENANT.md)

---

## 📄 Licencia

[MIT License](./LICENSE)

---

**Desarrollado con ❤️ usando Laravel, Livewire, Docker y Nginx**
