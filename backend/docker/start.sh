#!/bin/sh
set -e

cd /var/www/html

# Ensure storage and bootstrap/cache are writable
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Wait for database to be ready (for EC2 deployments)
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database connection..."
    until php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.(getenv('DB_PORT')?:3306), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null; do
        echo "Database not ready, waiting 2 seconds..."
        sleep 2
    done
    echo "Database connection established."
fi

# Run migrations (retry a few times so DB is ready; non-fatal so container still starts)
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Running migrations..."
    migrate_attempt=1
    migrate_max=5
    while [ "$migrate_attempt" -le "$migrate_max" ]; do
      if php artisan migrate --force --no-interaction 2>&1; then
        echo "Migrations completed."
        break
      fi
      echo "Migrations attempt $migrate_attempt/$migrate_max failed."
      if [ "$migrate_attempt" -lt "$migrate_max" ]; then
        echo "Waiting 3s before retry..."
        sleep 3
      else
        echo "Migrations did not succeed (continuing anyway)."
      fi
      migrate_attempt=$((migrate_attempt + 1))
    done
fi

# Clear any stale caches before rebuilding (remove all PHP cache files)
rm -rf bootstrap/cache/*.php 2>/dev/null || true
mkdir -p bootstrap/cache 2>/dev/null || true
chmod -R 775 bootstrap/cache 2>/dev/null || true

# Clear Laravel caches
php artisan config:clear --no-interaction 2>/dev/null || true
php artisan cache:clear --no-interaction 2>/dev/null || true
php artisan route:clear --no-interaction 2>/dev/null || true
php artisan view:clear --no-interaction 2>/dev/null || true
php artisan optimize:clear --no-interaction 2>/dev/null || true

# Rebuild caches for production (skip if errors to avoid breaking the app)
# Only cache if we're in production AND routes/api.php exists
if [ "${APP_ENV:-local}" = "production" ]; then
    if [ -f "routes/api.php" ]; then
        php artisan config:cache --no-interaction 2>&1 || echo "Config cache failed, continuing..."
        php artisan route:cache --no-interaction 2>&1 || echo "Route cache failed, continuing..."
        php artisan view:cache --no-interaction 2>&1 || echo "View cache failed, continuing..."
    else
        echo "Warning: routes/api.php not found, skipping route cache"
        php artisan config:cache --no-interaction 2>&1 || echo "Config cache failed, continuing..."
        php artisan view:cache --no-interaction 2>&1 || echo "View cache failed, continuing..."
    fi
fi

# Create storage link
php artisan storage:link --no-interaction 2>/dev/null || true

# Start supervisor (manages PHP-FPM and Nginx)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
