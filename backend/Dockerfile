#!/bin/sh
set -e

echo "----------------------------------------"
echo "Starting Laravel application..."
echo "----------------------------------------"

# Wait a few seconds to ensure database is ready
echo "Waiting for database connection..."
sleep 5

# Run database migrations
echo "Running migrations..."
php artisan migrate --force || true

# Clear and cache config (prevents cached local configs)
echo "Clearing and caching configuration..."
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix storage permissions (just in case)
echo "Fixing permissions..."
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

# Start PHP-FPM in background
echo "Starting PHP-FPM..."
php-fpm -D

# Start Nginx in foreground (keeps container alive)
echo "Starting Nginx..."
nginx -g "daemon off;"