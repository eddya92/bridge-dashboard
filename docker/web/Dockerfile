FROM php:8-apache

ARG DEBIAN_FRONTEND=noninteractive
ARG XDEBUG_VERSION=3.1.1
ARG PECL_REDIS_VERSION=5.3.4

# Update
RUN apt-get -y update --fix-missing && \
    apt-get upgrade -y && \
    apt-get --no-install-recommends install -y apt-utils && \
    rm -rf /var/lib/apt/lists/*

# Install useful tools and install important libaries
RUN apt-get -y update && \
    apt-get -y --no-install-recommends install nano wget dialog iputils-ping && \
    apt-get -y --no-install-recommends install zlib1g-dev libzip-dev libicu-dev && \
    apt-get -y --no-install-recommends install --fix-missing apt-utils build-essential git curl libonig-dev && \
    apt-get -y --no-install-recommends install --fix-missing libcurl4 libcurl4-openssl-dev zip openssl && \
    rm -rf /var/lib/apt/lists/* && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install xdebug
RUN pecl install xdebug-${XDEBUG_VERSION} && \
    docker-php-ext-enable xdebug

# Install redis
RUN pecl install redis-${PECL_REDIS_VERSION} && \
    docker-php-ext-enable redis

# Other PHP Extensions
RUN docker-php-ext-install curl && \
#    docker-php-ext-install tokenizer && \
    docker-php-ext-install zip && \
    docker-php-ext-install -j$(nproc) intl && \
    docker-php-ext-install mbstring && \
    docker-php-ext-install gettext && \
    docker-php-ext-install exif

# Enable apache modules
RUN a2enmod rewrite headers

# Cleanup
RUN rm -rf /usr/src/*
