FROM php:8.2-apache

# Install system deps and PHP extensions commonly needed (adjust as required)
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libzip-dev zlib1g-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# Enable mod_rewrite for .htaccess / pretty URLs
RUN a2enmod rewrite

# Copy application code
COPY . /var/www/html
WORKDIR /var/www/html

# Install Composer from official image and run install
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN if [ -f composer.json ]; then composer install --no-dev --no-interaction --optimize-autoloader; fi || true

# Entrypoint will adjust Apache to listen on $PORT if provided
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
CMD ["/usr/local/bin/docker-entrypoint.sh"]
