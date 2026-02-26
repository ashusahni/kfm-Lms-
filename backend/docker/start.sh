#!/bin/sh
set -e

# Render sets PORT; default 8080 for local Docker
PORT="${PORT:-8080}"
export PORT

# Replace __PORT__ in nginx config with Render's PORT
sed -i "s/__PORT__/$PORT/g" /etc/nginx/nginx.conf

# Laravel: run migrations and caches on each deploy
cd /var/www/html
php artisan migrate --force --no-interaction || true
php artisan config:cache --no-interaction || true
php artisan route:cache --no-interaction || true
php artisan view:cache --no-interaction || true
php artisan storage:link --no-interaction 2>/dev/null || true

# Start PHP-FPM in background, then Nginx in foreground
php-fpm &
exec nginx -g 'daemon off;'
