FROM php:8.4-fpm-alpine

# Установка системных зависимостей и PHP расширений
RUN apk add --no-cache \
    zip \
    unzip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/laravel
