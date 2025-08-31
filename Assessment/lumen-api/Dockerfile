FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json .
RUN composer install --no-dev --no-interaction --prefer-dist

FROM php:8.2-cli-alpine
WORKDIR /app
RUN docker-php-ext-install pdo pdo_mysql
COPY --from=vendor /app/vendor ./vendor
COPY . .
EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public", "public/index.php"]
