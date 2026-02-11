FROM richarvey/nginx-php-fpm:3.1.6

COPY . .
#COPY conf/nginx/nginx-site.conf /var/www/html/index.html

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


RUN ls -la /var/www/html/storage/logs

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Attempt to chmod log file only if it exists, or just create it
RUN touch /var/www/html/storage/logs/laravel.log && chmod -R 775 /var/www/html/storage/logs/laravel.log

# Instalar dependencias de Composer durante el build

# Instalar extensión bcmath requerida
RUN docker-php-ext-install bcmath

# Instalar dependencias de Composer durante el build
RUN composer install --no-dev --optimize-autoloader --no-scripts --working-dir=/var/www/html

# Instalar dependencias del sistema y Node.js
RUN apk add --no-cache curl && \
    curl -fsSL https://unofficial-builds.nodejs.org/download/release/v20.19.0/node-v20.19.0-linux-x64-musl.tar.gz -o node.tar.gz && \
    tar -xzf node.tar.gz -C /usr/local --strip-components=1 && \
    rm node.tar.gz && \
    ln -sf /usr/local/bin/node /usr/bin/node && \
    ln -sf /usr/local/bin/npm /usr/bin/

# Actualizar npm a la versión específica 11.6.2
RUN npm install -g npm@11.6.2

# Instalar extensión bcmath requerida


# DEBUG: Crear archivo de prueba y listar contenido
RUN echo "Nginx routing is working (generated in Dockerfile)" > /var/www/html/public/test.txt && \
    ls -la /var/www/html/public

EXPOSE 80 8080 443 

CMD ["/start.sh"]
