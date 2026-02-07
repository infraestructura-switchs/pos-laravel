FROM richarvey/nginx-php-fpm:3.1.6

COPY . .

# Image config
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1

# Laravel config
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Dar permisos
RUN chown -R nginx:nginx /var/www/html \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

# Limpiar cache de Laravel
RUN php /var/www/html/artisan config:clear || true \
 && php /var/www/html/artisan cache:clear || true \
 && php /var/www/html/artisan route:clear || true \
 && php /var/www/html/artisan view:clear || true

# Crear archivo de prueba
RUN echo "Nginx routing is working (generated in Dockerfile)" > /var/www/html/public/test.txt

EXPOSE 80

CMD ["/start.sh"]
