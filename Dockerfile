# Dockerfile para aplicación Laravel
FROM php:8.2-fpm

# Instalar dependencias del sistema y extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libjpeg-dev libwebp-dev libonig-dev libxml2-dev \
    zip curl ca-certificates && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath zip gd sockets && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer (usa la imagen oficial de composer como fuente)
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar composer para aprovechar cache de capas
COPY composer.json composer.lock ./
RUN if [ -f composer.lock ]; then composer install --no-interaction --prefer-dist --optimize-autoloader; fi || true

# Copiar el resto del código
COPY . .

# Copiar entrypoint y dar permisos
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Ajustes de permisos iniciales
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 9000

CMD ["/usr/local/bin/entrypoint.sh"]

