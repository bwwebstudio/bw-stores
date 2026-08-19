#!/bin/bash
set -e

PORT="${PORT:-80}"
export PORT

echo "========================================="
echo " Starting BW Store SaaS on Railway..."
echo " Configured Port: $PORT"
echo "========================================="

# Update Apache port listening to Railway's dynamic PORT
echo "Listen $PORT" > /etc/apache2/ports.conf
echo "ServerName localhost" >> /etc/apache2/apache2.conf 2>/dev/null || true

# Ensure storage and upload directories exist with write permissions
mkdir -p /var/www/html/storage/logs \
         /var/www/html/storage/cache \
         /var/www/html/storage/sessions \
         /var/www/html/public/uploads

chown -R www-data:www-data /var/www/html/storage /var/www/html/public/uploads 2>/dev/null || true
chmod -R 777 /var/www/html/storage /var/www/html/public/uploads 2>/dev/null || true

# Run Database Migrations & Seeders
echo "Checking database connection and running migrations..."
php database/migrate.php migrate || echo "Migration notice: continuing startup..."
php database/migrate.php seed || echo "Seeding notice: continuing startup..."

echo "========================================="
echo " Web server ready! Listening on port $PORT"
echo "========================================="

# Launch Apache in foreground
exec apache2-foreground
