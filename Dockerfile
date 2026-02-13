FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo_mysql

# Keep Apache defaults; app is served from /var/www/html/crm
