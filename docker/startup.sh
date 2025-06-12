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

# Set permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/var
chmod -R 777 /var/www/html/var

echo "Startup complete!"

# Start Apache
exec apache2-foreground
