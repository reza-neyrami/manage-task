FROM php:8.2

WORKDIR /Var/www/html
# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
    apt-get install -y \
    git \
    zip \
    curl \
    nginx

COPY ./nginx/default.conf /etc/nginx/conf.d/defaults.conf

# Copy custom php.ini file to the image
# COPY ./php.ini /usr/local/etc/php/

RUN docker-php-ext-install pdo_mysql exif pcntl
RUN apt-get update && \
    apt-get install -y libxml2-dev && \
    docker-php-ext-install xml
RUN docker-php-ext-install bcmath

