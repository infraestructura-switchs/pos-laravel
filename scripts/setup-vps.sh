#!/bin/bash

# ============================================
# Script de Configuración Inicial VPS
# ============================================
# Archivo: setup-vps.sh
# Descripción: Configuración inicial de un VPS para Laravel Multi-Tenant con Nginx
# Uso: bash scripts/setup-vps.sh

set -e  # Salir si hay error

echo "============================================"
echo "  Configuración Inicial VPS"
echo "  Laravel Multi-Tenant + Nginx"
echo "============================================"
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

print_success() { echo -e "${GREEN}✓ $1${NC}"; }
print_error() { echo -e "${RED}✗ $1${NC}"; }
print_warning() { echo -e "${YELLOW}⚠ $1${NC}"; }
print_info() { echo -e "${CYAN}ℹ $1${NC}"; }

# Verificar que se ejecuta como root
if [ "$EUID" -ne 0 ]; then
    print_error "Este script debe ejecutarse como root o con sudo"
    exit 1
fi

# Variables
PHP_VERSION="8.2"
MYSQL_ROOT_PASSWORD=""
PROJECT_DIR="/var/www/html"
REPO_URL=""

# ============================================
# PASO 1: Actualizar sistema
# ============================================

echo ""
echo -e "${YELLOW}PASO 1: Actualizando sistema${NC}"
echo ""

print_info "Actualizando lista de paquetes..."
apt update

print_info "Actualizando paquetes instalados..."
apt upgrade -y

print_success "Sistema actualizado"

# ============================================
# PASO 2: Instalar dependencias básicas
# ============================================

echo ""
echo -e "${YELLOW}PASO 2: Instalando dependencias básicas${NC}"
echo ""

print_info "Instalando utilidades básicas..."
apt install -y \
    software-properties-common \
    curl \
    wget \
    git \
    unzip \
    zip \
    htop \
    vim \
    certbot \
    python3-certbot-nginx

print_success "Dependencias básicas instaladas"

# ============================================
# PASO 3: Instalar Nginx
# ============================================

echo ""
echo -e "${YELLOW}PASO 3: Instalando Nginx${NC}"
echo ""

print_info "Instalando Nginx..."
apt install -y nginx

print_info "Iniciando Nginx..."
systemctl start nginx
systemctl enable nginx

print_success "Nginx instalado y en ejecución"

# ============================================
# PASO 4: Instalar PHP
# ============================================

echo ""
echo -e "${YELLOW}PASO 4: Instalando PHP $PHP_VERSION${NC}"
echo ""

print_info "Agregando repositorio de PHP..."
add-apt-repository ppa:ondrej/php -y
apt update

print_info "Instalando PHP y extensiones..."
apt install -y \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-soap \
    php${PHP_VERSION}-redis

print_info "Configurando PHP-FPM..."
systemctl start php${PHP_VERSION}-fpm
systemctl enable php${PHP_VERSION}-fpm

print_success "PHP $PHP_VERSION instalado"

# ============================================
# PASO 5: Configurar PHP
# ============================================

echo ""
echo -e "${YELLOW}PASO 5: Configurando PHP${NC}"
echo ""

PHP_INI="/etc/php/${PHP_VERSION}/fpm/php.ini"

print_info "Ajustando configuración de PHP..."

# Backup del archivo original
cp $PHP_INI ${PHP_INI}.backup

# Configuraciones
sed -i "s/upload_max_filesize = .*/upload_max_filesize = 50M/" $PHP_INI
sed -i "s/post_max_size = .*/post_max_size = 50M/" $PHP_INI
sed -i "s/memory_limit = .*/memory_limit = 512M/" $PHP_INI
sed -i "s/max_execution_time = .*/max_execution_time = 300/" $PHP_INI
sed -i "s/max_input_time = .*/max_input_time = 300/" $PHP_INI

# Habilitar OPcache
sed -i "s/;opcache.enable=.*/opcache.enable=1/" $PHP_INI
sed -i "s/;opcache.memory_consumption=.*/opcache.memory_consumption=256/" $PHP_INI
sed -i "s/;opcache.max_accelerated_files=.*/opcache.max_accelerated_files=10000/" $PHP_INI

print_info "Reiniciando PHP-FPM..."
systemctl restart php${PHP_VERSION}-fpm

print_success "PHP configurado"

# ============================================
# PASO 6: Instalar MySQL
# ============================================

echo ""
echo -e "${YELLOW}PASO 6: Instalando MySQL${NC}"
echo ""

print_info "Instalando MySQL Server..."
apt install -y mysql-server

print_info "Iniciando MySQL..."
systemctl start mysql
systemctl enable mysql

print_success "MySQL instalado"

# Configurar MySQL
echo ""
read -p "¿Desea configurar MySQL de forma segura ahora? (s/n): " configure_mysql

if [ "$configure_mysql" = "s" ] || [ "$configure_mysql" = "S" ]; then
    print_info "Ejecutando mysql_secure_installation..."
    mysql_secure_installation
fi

# ============================================
# PASO 7: Instalar Composer
# ============================================

echo ""
echo -e "${YELLOW}PASO 7: Instalando Composer${NC}"
echo ""

print_info "Descargando Composer..."
curl -sS https://getcomposer.org/installer | php

print_info "Moviendo Composer a /usr/local/bin..."
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

print_success "Composer instalado"

# ============================================
# PASO 8: Instalar Node.js y npm (opcional)
# ============================================

echo ""
read -p "¿Desea instalar Node.js y npm? (s/n): " install_node

if [ "$install_node" = "s" ] || [ "$install_node" = "S" ]; then
    echo ""
    echo -e "${YELLOW}PASO 8: Instalando Node.js${NC}"
    echo ""
    
    print_info "Agregando repositorio de NodeSource..."
    curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
    
    print_info "Instalando Node.js y npm..."
    apt install -y nodejs
    
    print_success "Node.js y npm instalados"
    node --version
    npm --version
else
    print_info "Instalación de Node.js omitida"
fi

# ============================================
# PASO 9: Configurar firewall
# ============================================

echo ""
echo -e "${YELLOW}PASO 9: Configurando firewall${NC}"
echo ""

print_info "Configurando UFW..."
ufw --force enable
ufw allow OpenSSH
ufw allow 'Nginx Full'

print_success "Firewall configurado"

# ============================================
# PASO 10: Clonar proyecto (opcional)
# ============================================

echo ""
read -p "¿Desea clonar el proyecto ahora? (s/n): " clone_project

if [ "$clone_project" = "s" ] || [ "$clone_project" = "S" ]; then
    echo ""
    echo -e "${YELLOW}PASO 10: Clonando proyecto${NC}"
    echo ""
    
    read -p "URL del repositorio Git: " REPO_URL
    
    if [ -n "$REPO_URL" ]; then
        print_info "Clonando proyecto..."
        
        # Crear directorio si no existe
        mkdir -p /var/www
        
        # Clonar
        git clone $REPO_URL $PROJECT_DIR
        
        print_info "Configurando permisos..."
        chown -R www-data:www-data $PROJECT_DIR
        chmod -R 775 $PROJECT_DIR/storage 2>/dev/null || true
        chmod -R 775 $PROJECT_DIR/bootstrap/cache 2>/dev/null || true
        
        print_success "Proyecto clonado"
    else
        print_warning "URL no proporcionada. Omitiendo clonación."
    fi
else
    print_info "Clonación de proyecto omitida"
fi

# ============================================
# PASO 11: Configurar base de datos
# ============================================

echo ""
read -p "¿Desea crear la base de datos ahora? (s/n): " create_db

if [ "$create_db" = "s" ] || [ "$create_db" = "S" ]; then
    echo ""
    echo -e "${YELLOW}PASO 11: Configurando base de datos${NC}"
    echo ""
    
    read -p "Nombre de la base de datos [pos_central]: " DB_NAME
    DB_NAME=${DB_NAME:-pos_central}
    
    read -p "Usuario de la base de datos [laravel]: " DB_USER
    DB_USER=${DB_USER:-laravel}
    
    read -sp "Contraseña del usuario: " DB_PASSWORD
    echo ""
    
    if [ -n "$DB_PASSWORD" ]; then
        print_info "Creando base de datos..."
        
        mysql -u root <<MYSQL_SCRIPT
CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON *.* TO '$DB_USER'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
MYSQL_SCRIPT
        
        print_success "Base de datos configurada"
        print_info "Base de datos: $DB_NAME"
        print_info "Usuario: $DB_USER"
    else
        print_warning "Contraseña no proporcionada. Omitiendo creación de BD."
    fi
fi

# ============================================
# PASO 12: Configurar .env (si el proyecto fue clonado)
# ============================================

if [ -d "$PROJECT_DIR" ] && [ -f "$PROJECT_DIR/.env.example" ]; then
    echo ""
    read -p "¿Desea configurar el archivo .env? (s/n): " config_env
    
    if [ "$config_env" = "s" ] || [ "$config_env" = "S" ]; then
        echo ""
        echo -e "${YELLOW}PASO 12: Configurando .env${NC}"
        echo ""
        
        cd $PROJECT_DIR
        
        if [ ! -f ".env" ]; then
            print_info "Copiando .env.example a .env..."
            cp .env.example .env
        fi
        
        read -p "Dominio central (ej: adminpos.dokploy.movete.cloud): " CENTRAL_DOMAIN
        
        if [ -n "$CENTRAL_DOMAIN" ]; then
            print_info "Configurando .env..."
            
            # Actualizar valores en .env
            sed -i "s/^APP_ENV=.*/APP_ENV=production/" .env
            sed -i "s/^APP_DEBUG=.*/APP_DEBUG=false/" .env
            sed -i "s|^APP_URL=.*|APP_URL=http://$CENTRAL_DOMAIN|" .env
            sed -i "s/^CENTRAL_DOMAIN=.*/CENTRAL_DOMAIN=$CENTRAL_DOMAIN/" .env
            
            if [ -n "$DB_NAME" ]; then
                sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
            fi
            if [ -n "$DB_USER" ]; then
                sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
            fi
            if [ -n "$DB_PASSWORD" ]; then
                sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env
            fi
            
            print_success ".env configurado"
        fi
    fi
fi

# ============================================
# PASO 13: Instalar dependencias y configurar proyecto
# ============================================

if [ -d "$PROJECT_DIR" ] && [ -f "$PROJECT_DIR/composer.json" ]; then
    echo ""
    read -p "¿Desea instalar dependencias y configurar el proyecto? (s/n): " setup_project
    
    if [ "$setup_project" = "s" ] || [ "$setup_project" = "S" ]; then
        echo ""
        echo -e "${YELLOW}PASO 13: Configurando proyecto${NC}"
        echo ""
        
        cd $PROJECT_DIR
        
        print_info "Instalando dependencias de Composer..."
        composer install --no-dev --optimize-autoloader --no-interaction
        
        print_info "Generando clave de aplicación..."
        php artisan key:generate --force
        
        print_info "Ejecutando migraciones..."
        php artisan migrate --force
        
        print_info "Optimizando aplicación..."
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        
        print_success "Proyecto configurado"
    fi
fi

# ============================================
# RESUMEN
# ============================================

echo ""
echo "============================================"
echo -e "${GREEN}  CONFIGURACIÓN INICIAL COMPLETADA${NC}"
echo "============================================"
echo ""

print_success "El servidor VPS ha sido configurado exitosamente"
echo ""

print_info "Software instalado:"
echo "  ✓ Nginx"
echo "  ✓ PHP $PHP_VERSION + extensiones"
echo "  ✓ MySQL"
echo "  ✓ Composer"
if [ "$install_node" = "s" ] || [ "$install_node" = "S" ]; then
    echo "  ✓ Node.js + npm"
fi
echo ""

print_info "Próximos pasos:"
echo ""
echo "1. Configurar DNS wildcard en tu proveedor:"
echo "   Tipo A: adminpos -> IP_DEL_SERVIDOR"
echo "   Tipo A: *.adminpos -> IP_DEL_SERVIDOR"
echo ""
echo "2. Copiar configuración de Nginx:"
echo "   sudo cp $PROJECT_DIR/conf/nginx/nginx-multitenant-production.conf /etc/nginx/sites-available/laravel-multitenant"
echo "   sudo ln -s /etc/nginx/sites-available/laravel-multitenant /etc/nginx/sites-enabled/"
echo "   sudo rm /etc/nginx/sites-enabled/default"
echo "   sudo nginx -t"
echo "   sudo systemctl reload nginx"
echo ""
echo "3. Configurar SSL (opcional pero recomendado):"
echo "   sudo certbot --nginx -d adminpos.dokploy.movete.cloud -d *.adminpos.dokploy.movete.cloud"
echo ""
echo "4. Crear un tenant de prueba:"
echo "   cd $PROJECT_DIR"
echo "   php artisan tenant:create testempresa test@example.com"
echo ""
echo "5. Acceder a la aplicación:"
if [ -n "$CENTRAL_DOMAIN" ]; then
    echo "   http://$CENTRAL_DOMAIN"
    echo "   http://testempresa.$CENTRAL_DOMAIN"
fi
echo ""

print_warning "IMPORTANTE: Configura las credenciales de producción en .env"
print_warning "IMPORTANTE: Asegúrate de que el firewall permita tráfico HTTP/HTTPS"
echo ""

echo "============================================"
echo ""

exit 0


