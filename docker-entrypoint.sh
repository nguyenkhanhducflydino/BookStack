#!/bin/sh
set -e

# Function to wait for database
wait_for_db() {
    echo "Waiting for database to be ready..."
    until php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; do
        echo "Database is not ready yet. Waiting 5 seconds..."
        sleep 5
    done
    echo "Database is ready!"
}

# Initialize public volume if empty
if [ ! -f /var/www/html/public/index.php ]; then
    echo "Initializing public directory..."
    cp -r /var/www/html/public_backup/* /var/www/html/public/
    chown -R www-data:www-data /var/www/html/public
fi

# Create storage link if it doesn't exist
if [ ! -L /var/www/html/public/storage ]; then
    echo "Creating storage link..."
    php artisan storage:link
fi

# Wait for database and run migrations
if [ -f /var/www/html/.env ]; then
    wait_for_db
    
    echo "Running database migrations..."
    php artisan migrate --force
    
    echo "Running database seeders..."
    php artisan db:seed --force || true
    
    echo "Optimizing application..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    echo "No .env file found, skipping migrate."
fi

# Execute the original command
exec "$@"
