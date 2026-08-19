# ===========================================
# Dockerfile for BW Store (PHP 8.2 + Apache)
# Optimized for Railway & Production Cloud Deployment
# ===========================================

FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Configure & install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        gd \
        zip \
        opcache \
        bcmath

# Enable Apache modules
RUN a2enmod rewrite headers

# Install Composer from official Composer image
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy custom Apache site configuration
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Copy application files
COPY . /var/www/html

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Prepare storage and uploads folders
RUN mkdir -p storage/logs storage/cache storage/sessions public/uploads \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/public/uploads \
    && chmod -R 777 /var/www/html/storage /var/www/html/public/uploads \
    && chmod +x docker-entrypoint.sh start.sh

# Default port
EXPOSE 80 8080

# Set entrypoint
ENTRYPOINT ["/bin/bash", "/var/www/html/docker-entrypoint.sh"]
