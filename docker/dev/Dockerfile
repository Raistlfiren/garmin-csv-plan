FROM php:7.4-cli

# Install xdebug
#RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
#  && pecl install xdebug \
#  && docker-php-ext-enable xdebug \
#  && apk del .phpize-deps
RUN apt-get update && apt-get install git zip unzip -y \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install composer to run composer install with the specified PHP version of the project
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

WORKDIR /var/www/html