FROM composer:2.6 AS build

WORKDIR /app

# Copier d'abord tous les fichiers
COPY . .

# Mettre à jour les packages problématiques puis installer
RUN composer update lcobucci/clock lcobucci/jwt --no-interaction
RUN composer clear-cache 
RUN composer install --no-dev --optimize-autoloader --no-interaction

FROM php:8.2-apache

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip git curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Activer le module rewrite d'Apache
RUN a2enmod rewrite

# Copier l'application depuis le stage de build
COPY --from=build /app /var/www/html

# Définir les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Copier la configuration Apache
COPY ./docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
CMD ["apache2-foreground"]