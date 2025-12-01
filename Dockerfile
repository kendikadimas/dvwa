FROM php:8.2-apache

# Install required extensions
RUN docker-php-ext-install mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

WORKDIR /var/www/html
