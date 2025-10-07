#!/usr/bin/env bash
# Exit on error
set -o errexit

# Install PHP dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Generate application key
php artisan key:generate --force

# Install Node.js dependencies and build assets
npm install
npm run build

# Optimize the application
php artisan optimize

# Cache routes and config
php artisan route:cache
php artisan config:cache
php artisan view:cache
