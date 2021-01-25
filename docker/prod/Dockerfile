############### php #####################
FROM php:7.2-fpm-alpine AS php

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

#TODO: remove dev deps
RUN apk update \
    && apk --no-cache add --virtual build-dependencies autoconf build-base \
    && apk --no-cache add zlib-dev imagemagick-dev imagemagick yarn \
    && pecl install imagick opcache apcu redis \
    && docker-php-ext-enable imagick opcache apcu redis \
    && docker-php-ext-install pdo_mysql zip \
    && apk del build-dependencies


############### app_deps ################
FROM php AS app_deps

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY ./composer.json ./composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader

COPY . .

RUN composer dump-autoload --optimize --no-dev --classmap-authoritative


################ app ####################
FROM php AS app

RUN mkdir /app && chown www-data:www-data /app

WORKDIR /app

USER www-data

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=app_deps /app/docker/prod/php.ini $PHP_INI_DIR/conf.d/app.ini
COPY --chown=www-data:www-data --from=app_deps /app/bin /app/bin
COPY --chown=www-data:www-data --from=app_deps /app/config /app/config
COPY --chown=www-data:www-data --from=app_deps /app/migrations /app/migrations
COPY --chown=www-data:www-data --from=app_deps /app/public /app/public
COPY --chown=www-data:www-data --from=app_deps /app/src /app/src
#COPY --chown=www-data:www-data --from=app_deps /app/templates /app/templates
#COPY --chown=www-data:www-data --from=app_deps /app/translations /app/translations
COPY --chown=www-data:www-data --from=app_deps /app/vendor /app/vendor
COPY --chown=www-data:www-data --from=app_deps /app/composer.json /app/composer.json
COPY --chown=www-data:www-data --from=app_deps /app/composer.lock /app/composer.lock
COPY --chown=www-data:www-data ./docker/prod/entrypoint.sh /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]

CMD ["php-fpm"]


############### nginx ###################
FROM nginx:latest AS nginx

RUN mkdir -p /app/public && chown www-data:www-data /app

COPY --chown=www-data:www-data ./docker/prod/nginx.conf /etc/nginx/conf.d/default.conf
