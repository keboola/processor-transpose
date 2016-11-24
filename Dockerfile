FROM php:7

RUN apt-get update && apt-get install -y \
        git \
        unzip \
   --no-install-recommends && rm -r /var/lib/apt/lists/*

COPY ./php/php.ini /usr/local/etc/php/php.ini
COPY . /code

WORKDIR /code

RUN curl -sS https://getcomposer.org/installer | php \
  && mv ./composer.phar /usr/local/bin/composer

RUN composer install --no-interaction

ENTRYPOINT php /code/src/main.php --data=/data
