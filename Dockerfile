FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libzip-dev zip unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mysqli zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader || true

RUN chown -R www-data:www-data /var/www/html

# Apache will read PORT from env
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2

EXPOSE 8080

CMD /bin/bash -c "source /etc/apache2/envvars && sed -i 's/Listen 80/Listen '\${PORT:-8080}'/g' /etc/apache2/ports.conf && sed -i 's/:80/:'\${PORT:-8080}'/g' /etc/apache2/sites-available/000-default.conf && apache2 -DFOREGROUND"
