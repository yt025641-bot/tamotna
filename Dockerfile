FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd zip

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Copy composer files first
COPY composer.json composer.lock* ./

# Install dependencies (ignore errors if composer.lock doesn't exist)
RUN composer install --no-dev --optimize-autoloader --no-scripts || true

# Copy application files
COPY . .

# Create DB_CON.php with environment variables
RUN echo '<?php\n\
$DB_HOST = getenv("DB_HOST") ?: "localhost";\n\
$DB_PORT = getenv("DB_PORT") ?: "3306";\n\
$DB_USER = getenv("DB_USER") ?: "root";\n\
$DB_PASSWORD = getenv("DB_PASSWORD") ?: "";\n\
$DB_NAME = getenv("DB_NAME") ?: "railway";\n\
\n\
$con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME, $DB_PORT);\n\
if (!$con) {\n\
    error_log("DB connection failed: " . mysqli_connect_error());\n\
    die("Database connection failed. Please check your configuration.");\n\
}\n\
mysqli_set_charset($con, "utf8mb4");\n\
?>' > /var/www/html/DB_CON.php

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Use PORT environment variable
ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Expose port
EXPOSE 80

# Start script
RUN echo '#!/bin/bash\n\
PORT=${PORT:-80}\n\
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf\n\
sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf\n\
apache2-foreground' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]
