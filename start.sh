#!/usr/bin/env bash

# Exit on error
set -o errexit

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Clear caches
echo "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate application key if it doesn't exist
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Start the application
echo "Starting the application..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
