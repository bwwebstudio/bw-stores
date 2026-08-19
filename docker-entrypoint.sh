#!/bin/bash

# Railway dynamic port
PORT="${PORT:-8080}"
export PORT

echo "========================================="
echo " Starting BW Store SaaS on Railway"
echo " Target Port: $PORT"
echo "========================================="

# Ensure directories exist and have full write permissions
mkdir -p /var/www/html/storage/logs \
         /var/www/html/storage/cache \
         /var/www/html/storage/sessions \
         /var/www/html/public/uploads

chmod -R 777 /var/www/html/storage /var/www/html/public/uploads 2>/dev/null || true
chown -R www-data:www-data /var/www/html/storage /var/www/html/public/uploads 2>/dev/null || true

# Run database migrations and seeders safely (non-blocking)
echo "Checking database connection and running migrations..."
php /var/www/html/database/migrate.php migrate 2>&1 || echo "Migration notice: continuing startup..."
php /var/www/html/database/migrate.php seed 2>&1 || echo "Seeding notice: continuing startup..."

# Dynamically configure Apache ports and virtual host for Railway's assigned PORT
echo "Listen $PORT" > /etc/apache2/ports.conf
echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf
a2enconf servername 2>/dev/null || true

cat <<EOF > /etc/apache2/sites-available/000-default.conf
<VirtualHost *:${PORT}>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html

    <Directory /var/www/html>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <FilesMatch "^\.(env|git|htaccess)|composer\.(json|lock)|phpunit\.xml">
        Require all denied
    </FilesMatch>

    ErrorLog /proc/self/fd/2
    CustomLog /proc/self/fd/1 combined
</VirtualHost>
EOF

a2ensite 000-default.conf 2>/dev/null || true
a2enmod rewrite headers 2>/dev/null || true

# Test Apache syntax and start server
echo "Testing Apache configuration..."
if apache2 -t 2>/dev/null; then
    echo "========================================="
    echo " Apache web server starting on port $PORT"
    echo "========================================="
    exec apache2 -D FOREGROUND
else
    echo "========================================="
    echo " Starting PHP built-in server on port $PORT"
    echo "========================================="
    exec php -S 0.0.0.0:$PORT /var/www/html/index.php
fi
