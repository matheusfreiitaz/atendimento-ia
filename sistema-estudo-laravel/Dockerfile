FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libssl-dev pkg-config \
    && docker-php-ext-install pdo_mysql zip \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
