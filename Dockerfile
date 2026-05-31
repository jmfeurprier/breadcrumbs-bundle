ARG PHP_VERSION=8.5

FROM php:${PHP_VERSION}-cli-alpine

WORKDIR /app

RUN apk add --no-cache unzip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
