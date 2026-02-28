# ---- Composer ----
# Use same patched PHP Alpine base as runtime (no composer:2 image = no extra vulnerabilities)
FROM php:8.2.29-cli-alpine AS composer
RUN apk add --no-cache git unzip curl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-scripts

# ---- Runtime ----
# Pin to patch version for security updates (8.2.29+ includes CVE fixes for PHP/pgsql/curl)
FROM php:8.2.29-fpm-alpine AS runtime
# Update index and upgrade existing packages to reduce vulnerabilities, then add deps
RUN apk update && apk upgrade --no-cache \
    && apk add --no-cache \
    nginx \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    postgresql-dev \
    linux-headers \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        zip \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        bcmath \
        gd \
        intl \
        opcache \
        exif

COPY docker/nginx.conf /etc/nginx/nginx.conf
WORKDIR /var/www/html
COPY --from=composer /app .

# Remove cached package discovery so Laravel re-discovers (avoids CollisionServiceProvider / dev deps)
RUN rm -f bootstrap/cache/packages.php bootstrap/cache/services.php

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

ENV PORT=8080
EXPOSE 8080

# Optional: Render can use this to know when the app is ready
HEALTHCHECK --interval=30s --timeout=5s --start-period=40s --retries=3 \
    CMD ["/bin/sh", "-c", "curl -sf http://127.0.0.1:${PORT}/up | grep -q '\"status\"' || exit 1"]

CMD ["/start.sh"]
