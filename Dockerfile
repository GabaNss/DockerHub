FROM php:8.2-apache

RUN docker-php-ext-install opcache

COPY Trabalho/ /var/www/html/

RUN mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html/data \
    && chmod -R 775 /var/www/html/data

EXPOSE 80
