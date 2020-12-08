FROM php:8.0-cli

RUN apt-get update && apt-get install zip libzip-dev unzip git asciinema -y
RUN docker-php-ext-install zip

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

ARG RUN_USER_ID=33
ARG RUN_USER_NAME=www-data
RUN useradd -m -u ${RUN_USER_ID} ${RUN_USER_NAME} || echo "User exists, it's ok."

USER ${RUN_USER_ID}
