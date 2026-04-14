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
    libzip-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd zip

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader || true

# Update DB_CON.php to use environment variables
RUN echo '<?php\n\
$DB_HOST = getenv("DB_HOST") ?: "localhost";\n\
$DB_PORT = getenv("DB_PORT") ?: "3306";\n\
$DB_USER = getenv("DB_USER") ?: "root";\n\
$DB_PASSWORD = getenv("DB_PASSWORD") ?: "";\n\
$DB_NAME = getenv("DB_NAME") ?: "railway";\n\
\n\
$con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME, $DB_PORT);\n\
if (!$con) {\n\
    error_log("Database connection failed: " . mysqli_connect_error());\n\
    die("Database connection failed");\n\
}\n\
mysqli_set_charset($con, "utf8mb4");\n\
?>' > /var/www/html/DB_CON.php

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configure Apache for Railway PORT
RUN echo '<VirtualHost *:${PORT}>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html\n\
    <Directory /var/www/html>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

EXPOSE ${PORT}

CMD sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf && apache2-foreground
