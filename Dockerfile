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

# Copiar .env.example a .env si no existe
RUN cp -n .env.example .env || true

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Generar clave de aplicación
RUN php artisan key:generate --force || true

# Dar permisos
RUN chown -R nobody:nobody /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configurar Nginx
RUN mkdir -p /etc/nginx/http.d
COPY <<'EOF' /etc/nginx/http.d/default.conf
server {
    listen 80;
    server_name _;
    root /var/www/html/public;
    index index.php index.html;

    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PHP_VALUE "upload_max_filesize=100M \n post_max_size=100M";
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
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

