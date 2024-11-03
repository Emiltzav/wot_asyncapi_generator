FROM php:7.4-apache

LABEL maintainer="emiltzav"
LABEL name="MeetMyFarm docker img"
LABEL version="0.1"
LABEL description="This is a sample Docker image for a WoT project."
EXPOSE 8080

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

RUN apt-get update && apt-get install libzip-dev libpng-dev wget git unzip -y --no-install-recommends && apt-get clean

RUN docker-php-ext-install zip pdo_mysql mysqli gd

COPY ./install-composer.sh ./

COPY ./php.ini /usr/local/etc/php/

WORKDIR /var/www

RUN chown -R www-data:www-data /var/www

CMD ["apache2-foreground"]