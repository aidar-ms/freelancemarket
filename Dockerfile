FROM php:7.2-apache

ENV WEB_DOCUMENT_ROOT  /app/web
ENV WEB_DOCUMENT_INDEX index.php

WORKDIR /app

ADD . /app

# -- confs
RUN apt-get update && \
    docker-php-ext-install pdo_mysql && \
    chown -R www-data:www-data /app && \
    chmod -R 755 /app/public && \
    a2enmod rewrite

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data

COPY vhost.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
