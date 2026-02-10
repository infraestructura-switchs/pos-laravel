FROM php:8.3-fpm-alpine

# Instalar Nginx y dependencias del sistema
RUN apk add --no-cache \
    nginx \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    supervisor

# Instalar extensiones de PHP
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
#ENV APP_KEY base64:xezR3eGeEbhmx0sQjF6JKSoO72QyBE9ScmoUzMbgkwI=

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    &&  chmod -R 775  /var/www/html/storage/logs/laravel.log


# Copiar .env.example a .env si no existe
RUN cp -n .env.example .env || true

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Generar clave de aplicación
RUN php artisan key:generate --force || true

RUN apk add --no-cache \
    nodejs \
    npm

# Dar permisos
RUN chown -R nobody:nobody /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configurar Nginx
RUN mkdir -p /etc/nginx/http.d
COPY <<'EOF' /etc/nginx/http.d/default.conf
# ============================================
# Configuración Nginx Multi-Tenant - Docker
# ============================================
# Archivo: nginx-multitenant-docker.conf
# Uso: Para contenedores Docker

# Servidor para dominio central (sin subdominios)
server {
    listen 8080;
    server_name adminpos.dokploy.movete.cloud;
    
    root /var/www/html/public;
    index index.php index.html index.htm;
    
    charset utf-8;
    
    # Logs específicos para dominio central
    access_log /var/log/nginx/central-access.log;
    error_log /var/log/nginx/central-error.log;
    
    # Headers de seguridad
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    
    # Aumentar tamaños máximos
    client_max_body_size 100M;
    client_body_buffer_size 128k;
    
    # Bloquear acceso a archivos sensibles
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    location /.git {
        deny all;
        return 403;
    }
    
    # Favicon y robots
    location = /favicon.ico { 
        access_log off; 
        log_not_found off; 
    }
    
    location = /robots.txt  { 
        access_log off; 
        log_not_found off; 
    }
    
    # Assets estáticos con cache
    location ~* \.(jpg|jpeg|gif|png|css|js|ico|webp|tiff|ttf|svg|woff|woff2|eot)$ {
        expires 365d;
        add_header Cache-Control "public, no-transform";
        add_header Access-Control-Allow-Origin "*";
        access_log off;
    }
    
    # Directorio build (Vite assets)
    location /build/ {
        alias /var/www/html/public/build/;
        expires 365d;
        add_header Cache-Control "public, no-transform";
        add_header Access-Control-Allow-Origin "*";
        access_log off;
    }
    
    # Directorio vendor
    location /vendor/ {
        alias /var/www/html/public/vendor/;
        expires 365d;
        add_header Cache-Control "public, no-transform";
        add_header Access-Control-Allow-Origin "*";
        access_log off;
    }
    
    # Storage público
    location /storage/ {
        alias /var/www/html/storage/app/public/;
        expires 7d;
        add_header Cache-Control "public";
    }
    
    # Ruta principal - Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # Manejo de PHP
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        include fastcgi_params;
        
        # Aumentar timeouts para procesos largos
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
        
        # Buffer settings
        fastcgi_buffer_size 128k;
        fastcgi_buffers 256 16k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
    }
    
    # Denegar acceso a archivos PHP en storage
    location ~* ^/storage/.*\.php$ {
        deny all;
    }
}

# Servidor wildcard para TODOS los subdominios (tenants)
server {
    listen 8080;
    # Wildcard: captura cualquier subdominio
    server_name ~^(?<tenant>.+)\.adminpos\.dokploy\.movete\.cloud$;
    
    root /var/www/html/public;
    index index.php index.html index.htm;
    
    charset utf-8;
    
    # Logs específicos para tenants
    access_log /var/log/nginx/tenants-access.log;
    error_log /var/log/nginx/tenants-error.log;
    
    # Headers de seguridad
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    
    # Aumentar tamaños máximos
    client_max_body_size 100M;
    client_body_buffer_size 128k;
    
    # Pasar el subdominio como variable a PHP
    fastcgi_param TENANT_SUBDOMAIN $tenant;
    
    # Bloquear acceso a archivos sensibles
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    location /.git {
        deny all;
        return 403;
    }
    
    # Favicon y robots
    location = /favicon.ico { 
        access_log off; 
        log_not_found off; 
    }
    
    location = /robots.txt  { 
        access_log off; 
        log_not_found off; 
    }
    
    # Assets estáticos con cache
    location ~* \.(jpg|jpeg|gif|png|css|js|ico|webp|tiff|ttf|svg|woff|woff2|eot)$ {
        expires 365d;
        add_header Cache-Control "public, no-transform";
        add_header Access-Control-Allow-Origin "*";
        access_log off;
    }
    
    # Directorio build (Vite assets)
    location /build/ {
        alias /var/www/html/public/build/;
        expires 365d;
        add_header Cache-Control "public, no-transform";
        add_header Access-Control-Allow-Origin "*";
        access_log off;
    }
    
    # Directorio vendor
    location /vendor/ {
        alias /var/www/html/public/vendor/;
        expires 365d;
        add_header Cache-Control "public, no-transform";
        add_header Access-Control-Allow-Origin "*";
        access_log off;
    }
    
    # Storage por tenant
    location /storage/ {
        alias /var/www/html/storage/app/public/;
        expires 7d;
        add_header Cache-Control "public";
    }
    
    # Ruta principal - Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # Manejo de PHP
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        include fastcgi_params;
        
        # Aumentar timeouts para procesos largos
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
        
        # Buffer settings
        fastcgi_buffer_size 128k;
        fastcgi_buffers 256 16k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
    }
    
    # Denegar acceso a archivos PHP en storage
    location ~* ^/storage/.*\.php$ {
        deny all;
    }
}
EOF

# Configurar supervisor
COPY <<'EOF' /etc/supervisord.conf
[supervisord]
nodaemon=true
user=root
logfile=/dev/stdout
logfile_maxbytes=0
pidfile=/var/run/supervisord.pid

[program:php-fpm]
command=php-fpm -F
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=true

[program:nginx]
command=nginx -g 'daemon off;'
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=true
EOF

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
