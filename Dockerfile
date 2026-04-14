FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libzip-dev zip unzip git \
    && docker-php-ext-install pdo_mysql mysqli gd zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader || true

# Set permissions
RUN chmod -R 755 /var/www/html

# Start PHP built-in server
CMD php -S 0.0.0.0:${PORT:-8080} -t /var/www/html
