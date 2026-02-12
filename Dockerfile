FROM php:8.3-fpm-alpine3.23

# Instalar Nginx y Supervisor
RUN apk add --no-cache \
    nginx \
    supervisor \
    bash \
    curl \
    git \
    nodejs \
    npm \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    zip \
    unzip

# Instalar extensiones PHP necesarias para Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    intl \
    gd \
    zip \
    bcmath \
    opcache

# ---------- Instalar Composer ----------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---------- Crear directorios ----------
RUN mkdir -p /var/www/html \
    /run/nginx \
    /var/log/supervisor \
    /var/www/errors \
    /var/www/html/src \
    /var/www/html/errors   

WORKDIR /var/www/html

COPY . .
#COPY conf/nginx/nginx-site.conf /var/www/html/index.

# Eliminar paquete viejo si existe (no falla si no está)
RUN composer remove hirak/prestissimo --no-update || true

# ---------- Instalar dependencias PHP ----------
RUN if [ -f composer.json ]; then \
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist; \
    fi

# ---------- Instalar dependencias Node ----------
RUN if [ -f package.json ]; then \
    npm install && npm run build; \
    fi    

# Image config
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
    && chmod -R 775 /var/www/html/storage/logs/laravel.log

# Instalar dependencias de Composer durante el build
##RUN composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

# Instalar dependencias del sistema y Node.js
RUN apk add --no-cache curl && \
    curl -fsSL https://unofficial-builds.nodejs.org/download/release/v20.19.0/node-v20.19.0-linux-x64-musl.tar.gz -o node.tar.gz && \
    tar -xzf node.tar.gz -C /usr/local --strip-components=1 && \
    rm node.tar.gz && \
    ln -sf /usr/local/bin/node /usr/bin/node && \
    ln -sf /usr/local/bin/npm /usr/bin/

# Actualizar npm a la versión específica 11.6.2
##RUN npm install -g npm@11.6.2

# Instalar extensión bcmath requerida
#RUN docker-php-ext-install bcmath

# DEBUG: Crear archivo de prueba y listar contenido
RUN echo "Nginx routing is working (generated in Dockerfile)" > /var/www/html/public/test.txt && \
    ls -la /var/www/html/public



# Add Scripts
ADD scripts/start.sh /start.sh
RUN sed -i 's/\r$//' /start.sh && chmod +x /start.sh
ADD scripts/pull /usr/bin/pull
ADD scripts/push /usr/bin/push
ADD scripts/letsencrypt-setup /usr/bin/letsencrypt-setup
ADD scripts/letsencrypt-renew /usr/bin/letsencrypt-renew
RUN chmod 755 /usr/bin/pull && chmod 755 /usr/bin/push && chmod 755 /usr/bin/letsencrypt-setup && chmod 755 /usr/bin/letsencrypt-renew && chmod 755 /start.sh

# copy in code
ADD dockerfiles_image/src/ /var/www/html/src
ADD dockerfiles_image/errors/ /var/www/errors  

EXPOSE 80 8080 443

WORKDIR "/var/www/html"
CMD ["/start.sh"]