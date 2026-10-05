FROM php:8.2-cli

WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip git \
    && docker-php-ext-install pdo_mysql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /var/lib/apt/lists/*

COPY back-end/ .

RUN composer install --no-dev --optimize-autoloader --no-interaction

ENV PORT=10000
EXPOSE 10000

CMD sh -c "cp /etc/secrets/.env /app/.env 2>/dev/null; cp /etc/secrets/ca.pem /app/ca.pem 2>/dev/null; \
    php artisan config:cache \
    && php artisan lighthouse:cache \
    && php artisan migrate --force \
    && php artisan db:seed --force \
    && php artisan serve --host=0.0.0.0 --port=$PORT"
