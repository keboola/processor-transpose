#!/bin/bash
echo "Starting tests" >&1
php --version \
    && composer --version \
    && composer install \
    && /code/vendor/bin/phpcs --standard=psr2 -n --ignore=vendor --extensions=php . \
    && /code/vendor/bin/phpunit
