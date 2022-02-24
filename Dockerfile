FROM php:8-apache

# Update
RUN apt-get -y update --fix-missing \
    && apt-get upgrade -y \
    && apt-get --no-install-recommends install -y apt-utils zlib1g-dev libzip-dev libicu-dev  \
       libcurl4 libcurl4-openssl-dev zip \
    && pecl install redis-5.3.4  \
    && docker-php-ext-enable redis \
    && docker-php-ext-install curl \
    && docker-php-ext-install zip \
    && docker-php-ext-install -j$(nproc) intl \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install gettext \
    && docker-php-ext-install exif \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite headers \
    && rm -rf /usr/src/*

ADD --chown=www-data:root ./ /var/www/app/
USER www-data