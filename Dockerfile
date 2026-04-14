FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && a2enmod rewrite

COPY . /var/www/html/
COPY apache.conf /etc/apache2/conf-available/custom.conf
RUN a2enconf custom

EXPOSE 80