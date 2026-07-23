FROM php:8.3-apache

COPY . /var/www/html/

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN chmod a+w api/files/*/*.txt

EXPOSE 80
