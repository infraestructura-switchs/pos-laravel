#!/usr/bin/env bash
echo "Running composer"
composer install --no-dev --working-dir=/var/www/html

php artisan storage:link

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force

echo "Running NPM"
npm install && npm run build


echo "Running livewire..."
php artisan livewire:publish --assets