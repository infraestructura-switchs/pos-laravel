#!/bin/bash

# ============================================
# Script de Deployment en VPS - Nginx Multi-Tenant
# ============================================
# Archivo: deploy-vps.sh
# Descripción: Deploy automático en servidor VPS con Nginx
# Uso: bash scripts/deploy-vps.sh

set -e  # Salir si hay error

echo "============================================"
echo "  Deployment VPS - Nginx Multi-Tenant"
echo "============================================"
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Variables
PROJECT_DIR="/var/www/html"
NGINX_CONFIG="/etc/nginx/sites-available/laravel-multitenant"
PHP_VERSION="8.2"

# ============================================
# Funciones
# ============================================

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${CYAN}ℹ $1${NC}"
}

check_root() {
    if [ "$EUID" -ne 0 ]; then
        print_error "Este script debe ejecutarse como root o con sudo"
        exit 1
    fi
}

check_dependencies() {
    print_info "Verificando dependencias..."
    
    local deps=("nginx" "php" "mysql" "composer" "git")
    local missing=()
    
    for dep in "${deps[@]}"; do
        if ! command -v $dep &> /dev/null; then
            missing+=($dep)
        fi
    done
    
    if [ ${#missing[@]} -ne 0 ]; then
        print_error "Dependencias faltantes: ${missing[*]}"
        print_info "Instala las dependencias primero:"
        print_info "sudo apt install nginx mysql-server php$PHP_VERSION-fpm php$PHP_VERSION-mysql php$PHP_VERSION-mbstring php$PHP_VERSION-xml php$PHP_VERSION-bcmath php$PHP_VERSION-curl php$PHP_VERSION-zip php$PHP_VERSION-gd git composer -y"
        exit 1
    fi
    
    print_success "Todas las dependencias instaladas"
}

# ============================================
# PASO 1: Verificaciones iniciales
# ============================================

echo ""
echo -e "${YELLOW}PASO 1: Verificaciones iniciales${NC}"
echo ""

check_root
check_dependencies

# ============================================
# PASO 2: Actualizar código
# ============================================

echo ""
echo -e "${YELLOW}PASO 2: Actualizando código${NC}"
echo ""

if [ -d "$PROJECT_DIR/.git" ]; then
    print_info "Actualizando desde repositorio Git..."
    cd $PROJECT_DIR
    
    # Guardar cambios locales si hay
    git stash
    
    # Obtener última versión
    git pull origin main
    
    print_success "Código actualizado"
else
    print_warning "No es un repositorio Git. Saltando actualización."
fi

# ============================================
# PASO 3: Instalar/actualizar dependencias
# ============================================

echo ""
echo -e "${YELLOW}PASO 3: Instalando dependencias${NC}"
echo ""

cd $PROJECT_DIR

if [ -f "composer.json" ]; then
    print_info "Instalando dependencias de Composer..."
    composer install --no-dev --optimize-autoloader --no-interaction
    print_success "Dependencias de Composer instaladas"
else
    print_error "composer.json no encontrado"
    exit 1
fi

# ============================================
# PASO 4: Configurar permisos
# ============================================

echo ""
echo -e "${YELLOW}PASO 4: Configurando permisos${NC}"
echo ""

print_info "Configurando propietario..."
chown -R www-data:www-data $PROJECT_DIR

print_info "Configurando permisos de escritura..."
chmod -R 775 $PROJECT_DIR/storage
chmod -R 775 $PROJECT_DIR/bootstrap/cache

print_success "Permisos configurados"

# ============================================
# PASO 5: Configurar Nginx
# ============================================

echo ""
echo -e "${YELLOW}PASO 5: Configurando Nginx${NC}"
echo ""

if [ -f "$PROJECT_DIR/conf/nginx/nginx-multitenant-production.conf" ]; then
    print_info "Copiando configuración de Nginx..."
    
    # Backup de configuración anterior si existe
    if [ -f "$NGINX_CONFIG" ]; then
        cp $NGINX_CONFIG ${NGINX_CONFIG}.backup.$(date +%Y%m%d_%H%M%S)
        print_info "Backup de configuración anterior creado"
    fi
    
    # Copiar nueva configuración
    cp $PROJECT_DIR/conf/nginx/nginx-multitenant-production.conf $NGINX_CONFIG
    
    # Crear symlink si no existe
    if [ ! -L "/etc/nginx/sites-enabled/laravel-multitenant" ]; then
        ln -s $NGINX_CONFIG /etc/nginx/sites-enabled/laravel-multitenant
        print_info "Symlink creado"
    fi
    
    # Remover configuración default si existe
    if [ -L "/etc/nginx/sites-enabled/default" ]; then
        rm /etc/nginx/sites-enabled/default
        print_info "Configuración default removida"
    fi
    
    # Probar configuración
    print_info "Probando configuración de Nginx..."
    if nginx -t; then
        print_success "Configuración de Nginx válida"
    else
        print_error "Error en configuración de Nginx"
        exit 1
    fi
else
    print_error "Archivo de configuración Nginx no encontrado"
    exit 1
fi

# ============================================
# PASO 6: Ejecutar migraciones
# ============================================

echo ""
echo -e "${YELLOW}PASO 6: Ejecutando migraciones${NC}"
echo ""

cd $PROJECT_DIR

# Verificar conexión a base de datos
print_info "Verificando conexión a base de datos..."
if php artisan db:show &> /dev/null; then
    print_success "Conexión a base de datos exitosa"
    
    # Ejecutar migraciones
    print_info "Ejecutando migraciones..."
    php artisan migrate --force
    print_success "Migraciones ejecutadas"
    
    # Migraciones de tenants
    print_info "Ejecutando migraciones de tenants..."
    php artisan tenants:migrate --force
    print_success "Migraciones de tenants ejecutadas"
else
    print_error "No se pudo conectar a la base de datos"
    print_warning "Verifica la configuración en .env"
    exit 1
fi

# ============================================
# PASO 7: Optimizar aplicación
# ============================================

echo ""
echo -e "${YELLOW}PASO 7: Optimizando aplicación${NC}"
echo ""

cd $PROJECT_DIR

print_info "Limpiando cachés..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

print_info "Generando cachés optimizados..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimizar autoload de Composer
composer dump-autoload --optimize

print_success "Aplicación optimizada"

# ============================================
# PASO 8: Compilar assets (si es necesario)
# ============================================

echo ""
echo -e "${YELLOW}PASO 8: Verificando assets${NC}"
echo ""

if [ -f "$PROJECT_DIR/package.json" ]; then
    if [ -d "$PROJECT_DIR/node_modules" ]; then
        print_info "Node modules encontrados"
        
        # Preguntar si desea compilar
        read -p "¿Desea compilar assets? (s/n): " compile_assets
        
        if [ "$compile_assets" = "s" ] || [ "$compile_assets" = "S" ]; then
            print_info "Instalando dependencias npm..."
            npm ci
            
            print_info "Compilando assets..."
            npm run build
            
            print_success "Assets compilados"
        else
            print_info "Compilación de assets omitida"
        fi
    else
        print_warning "Node modules no encontrados. Saltando compilación de assets."
    fi
else
    print_info "No hay package.json. Saltando assets."
fi

# ============================================
# PASO 9: Reiniciar servicios
# ============================================

echo ""
echo -e "${YELLOW}PASO 9: Reiniciando servicios${NC}"
echo ""

print_info "Reiniciando PHP-FPM..."
systemctl restart php${PHP_VERSION}-fpm
print_success "PHP-FPM reiniciado"

print_info "Recargando Nginx..."
systemctl reload nginx
print_success "Nginx recargado"

# Opcional: Reiniciar supervisor si existe
if command -v supervisorctl &> /dev/null; then
    print_info "Reiniciando workers de queue..."
    supervisorctl restart all
    print_success "Workers reiniciados"
fi

# ============================================
# PASO 10: Verificaciones finales
# ============================================

echo ""
echo -e "${YELLOW}PASO 10: Verificaciones finales${NC}"
echo ""

# Verificar que Nginx está corriendo
if systemctl is-active --quiet nginx; then
    print_success "Nginx está corriendo"
else
    print_error "Nginx no está corriendo"
    exit 1
fi

# Verificar que PHP-FPM está corriendo
if systemctl is-active --quiet php${PHP_VERSION}-fpm; then
    print_success "PHP-FPM está corriendo"
else
    print_error "PHP-FPM no está corriendo"
    exit 1
fi

# Verificar permisos
if [ -w "$PROJECT_DIR/storage" ]; then
    print_success "Permisos de storage correctos"
else
    print_warning "Posible problema con permisos de storage"
fi

# ============================================
# RESUMEN
# ============================================

echo ""
echo "============================================"
echo -e "${GREEN}  DEPLOYMENT COMPLETADO${NC}"
echo "============================================"
echo ""

print_success "La aplicación ha sido desplegada exitosamente"
echo ""

print_info "Información del deployment:"
echo "  - Directorio: $PROJECT_DIR"
echo "  - PHP Version: $PHP_VERSION"
echo "  - Nginx Config: $NGINX_CONFIG"
echo ""

print_info "Próximos pasos:"
echo "  1. Verificar que la aplicación esté accesible"
echo "  2. Revisar logs: tail -f /var/log/nginx/error.log"
echo "  3. Revisar logs Laravel: tail -f $PROJECT_DIR/storage/logs/laravel.log"
echo ""

print_info "URLs de acceso:"
# Obtener dominio del .env
if [ -f "$PROJECT_DIR/.env" ]; then
    CENTRAL_DOMAIN=$(grep "^CENTRAL_DOMAIN=" $PROJECT_DIR/.env | cut -d '=' -f2)
    if [ -n "$CENTRAL_DOMAIN" ]; then
        echo "  - Dominio central: http://$CENTRAL_DOMAIN"
        echo "  - Ejemplo tenant: http://testempresa.$CENTRAL_DOMAIN"
    fi
fi
echo ""

echo "============================================"
echo ""

exit 0


