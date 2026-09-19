#!/bin/sh

set -e

echo "Starting Laravel Marketplace..."

# Clear cached configuration
php artisan config:clear

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed marketplace categories
echo "Seeding marketplace categories..."
php artisan db:seed --class=CategorySeeder --force

# Cache Laravel configuration
echo "Caching Laravel configuration..."
php artisan config:cache

# Start Apache
echo "Starting Apache..."
exec apache2-foreground