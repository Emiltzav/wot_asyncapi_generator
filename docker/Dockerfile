FROM php:7.4-apache

LABEL maintainer="emiltzav"
LABEL name="Web of Things web server docker img"
LABEL version="0.1"
LABEL description="This is a sample Docker image for a WoT project."
EXPOSE 8080

# Apache configuration
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Install necessary PHP extensions and packages
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    wget \
    git \
    unzip --no-install-recommends && \
    apt-get clean

# Install PHP - MySQL extensions
RUN docker-php-ext-install zip pdo_mysql mysqli gd

# Install MongoDB PHP extension
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Copy PHP configuration and install script
COPY ./install-composer.sh ./
COPY ./php.ini /usr/local/etc/php/

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www

# Install EasyRdf via Composer
RUN composer require easyrdf/easyrdf

# Copy application source code to container
COPY . /var/www

# Set file permissions
RUN chown -R www-data:www-data /var/www

# Start Apache
CMD ["apache2-foreground"]