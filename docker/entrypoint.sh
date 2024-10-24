#!/bin/sh

echo "Starting entrypoint.sh script..."

# Run run.sh in the background to avoid blocking
bash /run.sh &

echo "Run.sh script executed."

php artisan view:clear
echo "View cache cleared."

php artisan config:clear
echo "Config cache cleared."

php artisan cache:clear
echo "Application cache cleared."

php artisan migrate --force
echo "Migrations completed."

php artisan db:seed --force
echo "Database seeded."

#service cron start
#echo "Cron service started."

# Set permissions for the cache data directory
if [ -d "/app/storage/framework/cache/data" ]; then
    chmod -R 777 /app/storage/framework/cache/data/*
    echo "Permissions set for /app/storage/framework/cache/data."
else
    echo "/app/storage/framework/cache/data directory does not exist."
fi

apache2-foreground
echo "Apache started."