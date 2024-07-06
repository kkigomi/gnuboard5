FROM php:7.3.20-fpm

WORKDIR /root
RUN apt-get update

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libcurl4 \
    libcurl4-openssl-dev \
    zlib1g-dev \
    libicu-dev \
    libmemcached-dev \
    memcached \
    libwebp-dev \
    build-essential \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libxpm-dev \
    zip \
    libzip-dev \
    unzip

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath mysqli opcache zip
RUN pecl install redis && docker-php-ext-enable redis

# Adjusted GD configuration for PHP 7.3
RUN docker-php-ext-configure gd \
        --with-freetype-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ \
        --with-webp-dir=/usr/include/

RUN docker-php-ext-install gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

EXPOSE 9000
