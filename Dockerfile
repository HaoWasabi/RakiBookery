FROM php:8.2-apache

# Install the PHP extensions required by MySQL and PayPal integrations.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libcurl4-openssl-dev \
    && docker-php-ext-install -j"$(nproc)" curl pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Serve only the public directory and route application URLs through index.php.
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

COPY src/ /var/www/html/

# The application stores uploaded book images in this directory.
RUN chown -R www-data:www-data /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/public/uploads

WORKDIR /var/www/html/public

EXPOSE 80

