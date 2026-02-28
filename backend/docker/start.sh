#!/bin/sh
set -e

# Render sets PORT; default 8080 for local Docker
PORT="${PORT:-8080}"
export PORT
sed -i "s/__PORT__/$PORT/g" /etc/nginx/nginx.conf

cd /var/www/html

# Ensure storage and bootstrap/cache are writable
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Run migrations (non-fatal so container starts even if DB is temporarily unavailable)
echo "Running migrations..."
if php artisan migrate --force --no-interaction 2>&1; then
  echo "Migrations completed."
else
  echo "Migrations failed or skipped (continuing anyway)."
fi

# Clear any stale caches before rebuilding (avoids boot errors from old discovery/config)
php artisan config:clear --no-interaction 2>/dev/null || true
php artisan cache:clear --no-interaction 2>/dev/null || true

# Rebuild caches (safe after AuthServiceProvider / sections fix)
php artisan config:cache --no-interaction || true
php artisan route:cache --no-interaction || true
php artisan view:cache --no-interaction || true
php artisan storage:link --no-interaction 2>/dev/null || true

# Start PHP-FPM in background, then Nginx in foreground (keeps container alive)
php-fpm &
exec nginx -g 'daemon off;'
