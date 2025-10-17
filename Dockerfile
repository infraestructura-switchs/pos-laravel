# Use a production-ready PHP-FPM image with Alpine for a smaller size
FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    # Other dependencies your app needs
    libpng \
    libjpeg-turbo \
    libwebp \
    libzip \
    freetype \
    icu-dev \
    git \
    npm \
    curl \
    unzip

# Install common PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    opcache \
    zip \
    exif \
    pcntl \
    mbstring \
    gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code into the container
COPY . .

# Run Composer installation, optimized for production
RUN composer install --no-dev --optimize-autoloader

# Run npm production build for your assets
RUN npm ci && npm run build

# Set permissions for the application
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000
EXPOSE 9000

# Set the entry point to run PHP-FPM and supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]


CMD ["/start.sh"]
