FROM php:8.2-cli

# Install dependency dan ekstensi PostgreSQL untuk Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring bcmath

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 10000
CMD php artisan serve --host 0.0.0.0 --port 10000
