FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
      wget \
      git

RUN apt-get install -y libzip-dev libicu-dev && docker-php-ext-install pdo zip intl opcache

# PHP ext for MySQL / MariaDB
RUN docker-php-ext-install pdo_mysql

# Xdebug
RUN pecl install xdebug-2.9.2 && docker-php-ext-enable xdebug

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Symfony tool
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
  mv /root/.symfony/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www

EXPOSE 9000