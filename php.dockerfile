FROM php:8.2-fpm-alpine

# Install PHP extensions for MySQL support
RUN docker-php-ext-install pdo pdo_mysql