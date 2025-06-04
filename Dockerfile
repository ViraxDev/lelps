FROM php:7.0-fpm-alpine

RUN apk add --no-cache bash

RUN curl -sS https://getcomposer.org/download/2.2.21/composer.phar -o /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer \
    && composer --version

WORKDIR /var/task