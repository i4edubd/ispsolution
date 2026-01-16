FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    mysql-client \
    nodejs \
    npm \
    bash

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Create storage directories and set permissions
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Expose port 8000
EXPOSE 8000

# Health check
HEALTHCHECK --interval=10s --timeout=3s --start-period=30s --retries=3 \
    CMD php -v || exit 1

# DEVELOPMENT: Start PHP built-in server
# PRODUCTION: Use php-fpm with nginx/Apache instead of artisan serve
# For production, change CMD to: CMD ["php-fpm"] and configure with nginx/Apache
CMD ["sh", "-c", "if [ ! -d vendor ]; then composer install --no-interaction --prefer-dist; fi && if [ ! -d node_modules ]; then npm ci; fi && php artisan serve --host=0.0.0.0 --port=8000"]
