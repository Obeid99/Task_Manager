#!/bin/bash

# Wait for database to be ready
echo "Waiting for database to be ready..."
until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
    echo "Database is not ready yet. Waiting..."
    sleep 2
done

echo "Database is ready!"

# Run migrations
echo "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

# Clear cache
echo "Clearing cache..."
php bin/console cache:clear --no-warmup

# Create admin user if it doesn't exist
echo "Checking for admin user..."
php bin/console app:create-admin --no-interaction 2>/dev/null || echo "Admin user already exists or command not available"

# Create directories and set permissions
echo "Setting up directories and permissions..."
mkdir -p /var/www/html/var/cache /var/www/html/var/log /var/www/html/var/sessions
chown -R www-data:www-data /var/www/html/var
chmod -R 777 /var/www/html/var

echo "Startup complete!"

# Start Apache
exec apache2-foreground
