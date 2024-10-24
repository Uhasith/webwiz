* * * * * /usr/bin/su -s /bin/sh -c "/usr/local/bin/php /var/www/artisan schedule:run" www-data >> /var/www/storage/logs/worker.log 2>&1
#*/1 * * * *  echo "timestamp: $(date '+\%Y-\%m-\%d \%H:\%M:\%S')"  >> /var/newfile.yaml
