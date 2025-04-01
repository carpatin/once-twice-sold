##############
# Base image #
##############
FROM php:8.3-fpm-alpine AS base

ARG UID
ARG GID

WORKDIR /srv/app

RUN set -eux; \
    apk add --no-cache git nano $PHPIZE_DEPS && \
    pecl install openswoole && \
    docker-php-ext-enable openswoole

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" && \
    rm "$PHP_INI_DIR/php.ini-production"
COPY docker/php/conf.d/app.ini $PHP_INI_DIR/conf.d/

RUN addgroup -S www-user -g $GID && \
    adduser -S www-user -G www-user -u $UID

ENTRYPOINT bash

##########################
# WebSocket server image #
##########################
FROM base AS websocket

USER www-user
ENTRYPOINT ["php", "/srv/app/websocket.php"]


#########################
# Web application image #
#########################
FROM base AS webapp

COPY docker/php/php-fpm.d/website.conf /usr/local/etc/php-fpm.d/website.conf
RUN mkdir /var/run/php && chown -R www-user:www-user /var/run/php
USER www-user
ENTRYPOINT php-fpm


#####################################
# Reverse proxy Caddy builder image #
#####################################
FROM caddy:2-builder-alpine AS caddy_builder

RUN xcaddy build


#############################
# Reverse proxy Caddy image #
#############################
FROM caddy:2 AS caddy

WORKDIR /srv/app
COPY --from=caddy_builder /usr/bin/caddy /usr/bin/caddy
COPY docker/caddy/Caddyfile /etc/caddy/Caddyfile
