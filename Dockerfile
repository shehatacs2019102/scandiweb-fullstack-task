FROM php:8.2-cli

WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip git \
    && docker-php-ext-install pdo_mysql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /var/lib/apt/lists/*

COPY back-end/ .

RUN composer install --no-dev --optimize-autoloader

ENV PORT=10000
EXPOSE 10000

CMD php -d variables_order=EGPCS -S 0.0.0.0:$PORT -t public
