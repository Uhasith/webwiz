FROM php:8.2-apache

WORKDIR /app


RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y supervisor cron
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash -

RUN apt-get install -y nodejs
RUN npm install -g npm@latest

COPY . /app/

RUN composer clear-cache
RUN composer install

RUN npm install

RUN (crontab -l ; echo "* * * * * /usr/local/bin/php /app/artisan schedule:run >> /cron_log.log") | crontab


COPY ./docker/server-supervisor.conf /etc/supervisor/conf.d/run.conf
COPY ./docker/server-start-script.sh /run.sh
COPY ./docker/entrypoint.sh /entrypoint.sh
COPY ./docker/php.ini /usr/local/etc/php/php.ini
COPY ./docker/000-default.conf  /etc/apache2/sites-available/000-default.conf
COPY ./docker/apache2.conf  /etc/apache2/apache2.conf


RUN chmod 777 /run.sh
RUN chmod 777 /entrypoint.sh

RUN npm run build


RUN mkdir -p /app/storage/logs
#RUN rm -rf /app/storage/framework
#RUN mkdir -p /app/storage/framework/sessions
#RUN mkdir -p /app/storage/framework/cache
#RUN mkdir -p /app/storage/framework/cache/data
#RUN mkdir -p /app/storage/framework/views
RUN mkdir -p /app/storage/logs
#RUN mkdir -p /app/storage/framework/cache/data
RUN rm -rf /app/storage/logs/*
RUN rm -rf /app/public/qanswer/css/generated/*
RUN touch /app/storage/logs/laravel.log
RUN chmod -R 777 /app/storage/*
RUN chmod -R 777 /app/public/*
RUN #chmod -R 777 /app/bootstrap/*
RUN #chmod -R 777 /app/storage/framework/cache/data

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache
RUN mkdir -p /app/storage/framework/cache/data && chown -R www-data:www-data /app/storage/framework/cache/data
#RUN chmod -R 775 /app/storage  /app/bootstrap/cache

#RUN chmod -R 777 /app/storage/framework/cache/data/*
RUN a2enmod rewrite
#temp
#RUN sed -i '$ d' /etc/ssl/openssl.cnf
#RUN /usr/local/bin/php /app/artisan config:clear

ENTRYPOINT ["/entrypoint.sh"]
