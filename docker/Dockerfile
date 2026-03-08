FROM php:8.5-cli

RUN apt update ; \
    apt install -y \
        zip \
    ; \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    ;
