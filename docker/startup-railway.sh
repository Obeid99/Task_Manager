#!/bin/bash

# Set default environment variables for Railway
export MESSENGER_TRANSPORT_DSN=${MESSENGER_TRANSPORT_DSN:-doctrine://default}
export APP_ENV=${APP_ENV:-prod}
export APP_SECRET=${APP_SECRET:-railway_secret_key_2024}

# Check if DATABASE_URL is set (Railway should provide this)
if [ -z "$DATABASE_URL" ]; then
    echo "WARNING: DATABASE_URL not set. App will run without database."
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

# Run database setup (only if database is available)
if [ "$SKIP_DB" = false ]; then
    echo "Setting up database..."
    php bin/console doctrine:database:create --if-not-exists
    php bin/console doctrine:schema:update --force
    echo "Creating admin user..."
    php bin/console app:create-admin --no-interaction 2>/dev/null || echo "Admin user already exists"
else
    echo "Skipping database setup (no database connection)"
fi

# Clear cache
echo "Clearing cache..."
php bin/console cache:clear --no-warmup

# Create directories and set permissions
echo "Setting up directories and permissions..."
mkdir -p /var/www/html/var/cache /var/www/html/var/log /var/www/html/var/sessions
chown -R www-data:www-data /var/www/html/var
chmod -R 777 /var/www/html/var

echo "Startup complete!"

# Start Apache
exec apache2-foreground
