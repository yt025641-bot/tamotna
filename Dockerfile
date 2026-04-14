FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev zip unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mysqli zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Copy application
COPY . /var/www/html/

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader || true

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Configure Apache to listen on PORT
RUN echo 'Listen ${PORT}' > /etc/apache2/ports.conf && \
    echo '<VirtualHost *:${PORT}>' > /etc/apache2/sites-available/000-default.conf && \
    echo '    DocumentRoot /var/www/html' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    <Directory /var/www/html>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '</VirtualHost>' >> /etc/apache2/sites-available/000-default.conf

# Health check endpoint
RUN echo '<?php http_response_code(200); echo "OK"; ?>' > /var/www/html/health.php

CMD ["apache2-foreground"]
