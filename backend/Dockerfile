# ---- Composer ----
FROM composer:2 AS composer
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

COPY . .
RUN composer dump-autoload --optimize

# ---- Runtime ----
FROM php:8.2-fpm-alpine AS runtime

# PHP extensions Laravel typically needs
RUN apk add --no-cache \
    nginx \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    icu-dev \
    linux-headers \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    zip \
    intl \
    gd \
    opcache

# Nginx config
RUN mkdir -p /etc/nginx/conf.d
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
# App
WORKDIR /var/www/html
COPY --from=composer /app .
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Laravel storage and cache must be writable
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Render uses PORT (often 10000); start script substitutes it into nginx config
ENV PORT=8080
EXPOSE 8080

CMD ["/start.sh"]
