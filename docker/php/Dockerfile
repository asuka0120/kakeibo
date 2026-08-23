FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
        git \
        curl \
        libpng-dev \
        libzip-dev \
        libxml2-dev \
        oniguruma-dev \
        icu-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        nodejs \
        npm \
        $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
