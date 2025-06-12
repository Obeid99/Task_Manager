#!/bin/bash

# Check if DATABASE_URL is set
if [ -z "$DATABASE_URL" ]; then
    echo "WARNING: DATABASE_URL not set. Skipping database operations."
    SKIP_DB=true
else
    echo "DATABASE_URL found: ${DATABASE_URL}"
    SKIP_DB=false
fi

# Wait for database to be ready (with timeout)
if [ "$SKIP_DB" = false ]; then
    echo "Waiting for database to be ready..."
    TIMEOUT=30
    COUNTER=0
    until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
        echo "Database is not ready yet. Waiting... ($COUNTER/$TIMEOUT)"
        sleep 3
        COUNTER=$((COUNTER + 1))
        if [ $COUNTER -ge $TIMEOUT ]; then
            echo "Database connection timeout after 90 seconds. Continuing without database setup."
            SKIP_DB=true
            break
        fi
    done

    if [ "$SKIP_DB" = false ]; then
        echo "Database is ready!"
    fi
fi

# Run migrations (only if database is available)
if [ "$SKIP_DB" = false ]; then
    echo "Running database migrations..."
    php bin/console doctrine:migrations:migrate --no-interaction
else
    echo "Skipping database migrations (no database connection)"
fi

# Clear cache
echo "Clearing cache..."
php bin/console cache:clear --no-warmup

# Create admin user if it doesn't exist (only if database is available)
if [ "$SKIP_DB" = false ]; then
    echo "Checking for admin user..."
    php bin/console app:create-admin --no-interaction 2>/dev/null || echo "Admin user already exists or command not available"
else
    echo "Skipping admin user creation (no database connection)"
fi

# Create directories and set permissions
echo "Setting up directories and permissions..."
mkdir -p /var/www/html/var/cache /var/www/html/var/log /var/www/html/var/sessions
chown -R www-data:www-data /var/www/html/var
chmod -R 777 /var/www/html/var

echo "Startup complete!"

# Start Apache
exec apache2-foreground
