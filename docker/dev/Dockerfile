FROM php:7.4-cli-alpine

# Install xdebug
RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
  && pecl install xdebug \
  && docker-php-ext-enable xdebug \
  && apk del .phpize-deps

WORKDIR /var/www/html