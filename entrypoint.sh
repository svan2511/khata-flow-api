#!/bin/sh
set -e

echo "========================================"
echo "Starting Laravel Application on Render"
echo "========================================"

# Clear old cached files
rm -f bootstrap/cache/*.php

# SQLite Setup
echo "Setting up SQLite..."
mkdir -p database
touch database/database.sqlite 2>/dev/null || true
chmod 664 database/database.sqlite

# Clear all caches
echo "Clearing Laravel caches..."
php artisan optimize:clear

# Run Migrations
echo "Running database migrations..."
php artisan migrate --force

# ================== PASSPORT KEYS SETUP ==================
echo "Setting up Laravel Passport..."

if [ -n "$PASSPORT_PRIVATE_KEY" ] && [ -n "$PASSPORT_PUBLIC_KEY" ]; then
    echo "✅ Using Passport keys from Environment Variables..."
    mkdir -p storage

    echo "$PASSPORT_PRIVATE_KEY" > storage/oauth-private.key
    echo "$PASSPORT_PUBLIC_KEY" > storage/oauth-public.key

    chmod 600 storage/oauth-private.key
    chmod 644 storage/oauth-public.key

    echo "Passport keys loaded from env successfully."
else
    echo "⚠️  No Passport keys found in env. Generating new keys..."
    php artisan passport:keys --force
fi

# Final Optimizations (After keys are set)
echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "========================================"
echo "✅ Laravel Entrypoint Completed Successfully!"
echo "========================================"

# Start Supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf