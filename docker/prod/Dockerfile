FROM php:7.4-cli-alpine

RUN set -xe \
    && apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS \
    && rm -rf /usr/share/php \
    && rm -rf /tmp/* \
    && apk del  .phpize-deps

COPY . /usr/src/garmin-csv-plan

RUN cd /usr/src/garmin-csv-plan \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && php composer.phar install

WORKDIR /usr/src/garmin-csv-plan