#!/bin/sh
set -e

echo "Starting Laravel entrypoint on Render (SQLite)..."

# SQLite setup
echo "Using SQLite - No TCP database server to wait for."

mkdir -p database
touch database/database.sqlite 2>/dev/null || true
chmod 664 database/database.sqlite 2>/dev/null || true

# Clear old caches first
echo "Clearing Laravel caches..."
php artisan optimize:clear

# Run migrations
echo "Running migrations..."
php artisan migrate:fresh --force

# Generate Passport keys
echo "Generating Passport keys..."
php artisan passport:keys --force

# Create Passport Personal Client only if not exists
if ! php artisan tinker --execute="exit(App\Models\OauthPersonalAccessClient::count() > 0 ? 0 : 1);"; then
  echo "Creating Passport Personal Client..."
  php artisan passport:client --personal --name='Personal Access Client' --no-interaction
fi

# Optimize Laravel
echo "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Entrypoint complete! Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf