#!/bin/sh
set -e

echo "Starting Laravel entrypoint on Render (SQLite)..."

# Remove old cached configs
rm -f bootstrap/cache/*.php

# SQLite setup
echo "Using SQLite - No TCP database server to wait for."

mkdir -p database
touch database/database.sqlite 2>/dev/null || true
chmod 664 database/database.sqlite 2>/dev/null || true

# Clear caches
echo "Clearing Laravel caches..."
php artisan optimize:clear

# Run migrations
echo "Running migrations..."
php artisan migrate:fresh --force

# Install Passport
echo "Installing Passport..."
php artisan passport:install --force

# Optimize Laravel
echo "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Entrypoint complete! Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf