FROM serversideup/php:8.4-fpm-nginx

USER root
# Add any extensions your app needs
RUN install-php-extensions intl bcmath exif gd

# Node, only needed to build frontend assets
RUN apt-get update \
 && apt-get install -y --no-install-recommends curl ca-certificates \
 && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
 && apt-get install -y nodejs \
 && rm -rf /var/lib/apt/lists/*

USER www-data
COPY --chown=www-data:www-data . /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-interaction \
 && npm ci \
 && npm run build \
 && rm -rf node_modules
