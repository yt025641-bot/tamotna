FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx \
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

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json ./
RUN composer install --no-dev --optimize-autoloader --no-scripts || true

# Copy all files
COPY . .

# Create DB_CON.php
RUN echo '<?php\n\
$DB_HOST = getenv("DB_HOST") ?: "localhost";\n\
$DB_PORT = getenv("DB_PORT") ?: "3306";\n\
$DB_USER = getenv("DB_USER") ?: "root";\n\
$DB_PASSWORD = getenv("DB_PASSWORD") ?: "";\n\
$DB_NAME = getenv("DB_NAME") ?: "railway";\n\
$con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME, $DB_PORT);\n\
if (!$con) {\n\
    error_log("DB Error: " . mysqli_connect_error());\n\
    die("Database connection failed");\n\
}\n\
mysqli_set_charset($con, "utf8mb4");\n\
?>' > /var/www/html/DB_CON.php

# Nginx config
RUN echo 'server {\n\
    listen $PORT;\n\
    root /var/www/html;\n\
    index index.php index.html;\n\
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
    }\n\
    location ~ \.php$ {\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_index index.php;\n\
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n\
        include fastcgi_params;\n\
    }\n\
}' > /etc/nginx/sites-available/default

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Start script
RUN echo '#!/bin/bash\n\
PORT=${PORT:-8080}\n\
sed -i "s/\$PORT/$PORT/g" /etc/nginx/sites-available/default\n\
php-fpm -D\n\
nginx -g "daemon off;"' > /start.sh && chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
