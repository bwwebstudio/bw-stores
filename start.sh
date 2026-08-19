#!/bin/bash

# ===========================================
# BW Store - Start Script
# Works on Railway (Nixpacks / Procfile) and local containers
# ===========================================

PORT="${PORT:-8080}"
export PORT

echo "========================================="
echo " Starting BW Store on Port: $PORT"
echo "========================================="

# Ensure directories exist
mkdir -p storage/logs storage/cache storage/sessions public/uploads
chmod -R 777 storage public/uploads 2>/dev/null || true

# Run migrations & seeders safely
echo "Checking database connection and running migrations..."
php database/migrate.php migrate || echo "Migration notice: continuing startup..."
php database/migrate.php seed || echo "Seeding notice: continuing startup..."

# Start web server
echo "Starting PHP Server on 0.0.0.0:${PORT}..."
exec php -S 0.0.0.0:${PORT} index.php
