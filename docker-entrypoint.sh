#!/bin/sh
set -e

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

# Run database migrations
if [ -f /var/www/html/.env ]; then
    echo "Running database migrations..."
    php artisan migrate --force
else
    echo "No .env file found, skipping migrate."
fi

# Execute the original command
exec "$@"
