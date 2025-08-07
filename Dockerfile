FROM composer:2.6 AS build

WORKDIR /app
COPY composer.json composer.lock ./

RUN composer update 

RUN composer update lcobucci/clock lcobucci/jwt
RUN composer clear-cache && composer install --no-dev --optimize-autoloader --ignore-platform-reqs


COPY . .

FROM php:8.2-fpm-alpine

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip git curl \
    && docker-php-ext-install pdo mbstring exif pcntl bcmath gd

RUN a2enmod rewrite


COPY --from=build /app /var/www/html


RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

COPY ./docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
CMD ["apache2-foreground"]
