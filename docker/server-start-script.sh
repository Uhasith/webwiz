#!/bin/bash

role=${CONTAINER_ROLE:-app}

envVars=(
    APP_NAME
    APP_ENV
    APP_KEY
    APP_DEBUG
    APP_URL
    LOG_CHANNE
    LOG_DEPRECATIONS_CHANNEL
    LOG_LEVEL
    DB_CONNECTION
    DB_HOST
    DB_PORT
    DB_DATABASE
    DB_USERNAME
    DB_PASSWORD
    BROADCAST_DRIVER
    CACHE_DRIVER
    FILESYSTEM_DISK
    QUEUE_CONNECTION
    SESSION_DRIVER
    SESSION_LIFETIME
    MEMCACHED_HOST
    REDIS_HOST
    REDIS_PASSWORD
    REDIS_PORT
    MAIL_MAILER
    MAIL_HOST
    MAIL_PORT
    MAIL_USERNAME
    MAIL_PASSWORD
    MAIL_ENCRYPTION
    MAIL_FROM_ADDRESS
    MAIL_FROM_NAME
    AWS_ACCESS_KEY_ID
    AWS_SECRET_ACCESS_KEY
    AWS_S3_REGION
    AWS_S3_BUCKET
    AWS_END_POINT
    PUSHER_APP_ID
    PUSHER_APP_KEY
    PUSHER_APP_SECRET
    PUSHER_HOST
    PUSHER_PORT
    PUSHER_SCHEME
    PUSHER_APP_CLUSTER
    VITE_APP_NAME
    VITE_PUSHER_APP_KEY
    VITE_PUSHER_HOST
    VITE_PUSHER_PORT
    VITE_PUSHER_SCHEME
    VITE_PUSHER_APP_CLUSTER
    WEATHER_API_KEY
    EQUIPMENT_MOCK
    TEST_APIS
    WQD_PATH
    ANNUALLY_RANGE
    ROUTE_CACHE_AGE
    PAGE_CACHE_AGE
    CONTAINER_ROLE
    VITE_FIREBASE_API_KEY
    VITE_FIREBASE_AUTH_DOMAIN
    VITE_FIREBASE_PROJECT_ID
    VITE_FIREBASE_STORAGE_BUCKET
    VITE_FIREBASE_MESSAGING_SENDER_ID
    VITE_FIREBASE_APP_ID
    VITE_FIREBASE_MEASUREMENT_ID
    VITE_FIREBASE_VAPID_KEY
    HISTORY_DATA_PATH
)

> /app/.env
for envVar in "${envVars[@]}"; do
    echo -e "${envVar}=${!envVar}" >> /app/.env
done



touch /cron_log.log
chmod 777 /cron_log.log
if [ "$role" = "app" ]; then
    echo "Starting apache server"
    supervisord -c /etc/supervisor/supervisord.conf
    supervisorctl stop laravel-worker:*
    apachectl start && : >> /app/storage/logs/laravel.log && chmod 777 /app/storage/logs/laravel.log && tail -f /app/storage/logs/laravel.log
elif [ "$role" = "queue" ]; then
    echo "Running the queue"
    supervisord -c /etc/supervisor/supervisord.conf && : >> /app/storage/logs/laravel.log && chmod 777 /app/storage/logs/laravel.log && tail -f /app/storage/logs/laravel.log
elif [ "$role" = "scheduler" ]; then
    echo "Running the scheduler"
    supervisord -c /etc/supervisor/supervisord.conf
    supervisorctl stop laravel-worker:*
    cron && : >> /app/storage/logs/laravel.log && chmod 777 /app/storage/logs/laravel.log && tail -f /app/storage/logs/laravel.log
elif [ "$role" = "all" ]; then
    echo "Running all"
    : >> /app/storage/logs/laravel.log && chmod 777 /app/storage/logs/laravel.log
    echo "Running queues"
    supervisord -c /etc/supervisor/supervisord.conf
    echo "Running scheduler"
    cron
    echo "Running server"
    apachectl start && chmod 777 /app/storage/logs/laravel.log && tail -f /app/storage/logs/laravel.log
else
    echo "Could not match the container role \"$role\""
    exit 1
fi
