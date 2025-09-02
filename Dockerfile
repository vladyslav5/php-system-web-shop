FROM php:8.2-fpm-alpine

RUN apk update && apk add \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    shadow \
    libpq-dev

RUN docker-php-ext-install pdo pdo_pgsql pgsql

WORKDIR /var/www/html

COPY . /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN composer install  --optimize-autoloader


