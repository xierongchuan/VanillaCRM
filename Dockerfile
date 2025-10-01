FROM php:8.4.12-fpm

# 1) Системные библиотеки для сборки расширений и MySQL
RUN apt-get update -y \
  && apt-get install -y \
  git unzip zip \
  libzip-dev libonig-dev libxml2-dev \
  libpng-dev libjpeg-dev libfreetype6-dev \
  libmemcached-tools \
  default-libmysqlclient-dev mariadb-client \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2.1) Установка LTS-версии Node.js вместо latest (рекомендуется для Laravel)
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - \
  && apt-get install -y nodejs \
  && npm install -g vite@latest \
  && npm install -g laravel-vite-plugin@latest

# 2.2) Исправление проблем с Vite и Node.js 24
RUN npm install -g npm@latest \
  && npm config set fund false \
  && npm config set audit false \
  && npm cache clean --force

# 3) PHP-расширения: MySQL (MariaDB), GD и т.д.
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) \
  mbstring exif pcntl bcmath zip \
  pdo_mysql gd \
  && docker-php-ext-install mysqli

# 4) Redis через PECL
RUN pecl install redis-6.2.0 \
  && docker-php-ext-enable redis

# 5) Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6) Копирование composer.json и composer.lock в конте
WORKDIR /var/www/vanillacrm_src
COPY composer.json composer.lock ./

# 7) Копирование кодовой базы в контейнер
COPY . .

# 8) Создание нужных директорий и установка прав
RUN mkdir -p storage/logs storage/framework/sessions bootstrap/cache \
  && chown -R www-data:www-data storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache

# 9) Настройка PHP для загрузки больших файлов
RUN echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/docker-php-upload.ini \
  && echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/docker-php-upload.ini \
  && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/docker-php-upload.ini

# 10) Документирование порта PHP-FPM
EXPOSE 9000

# 11) Ждём запуска MariaDB и выполняем миграции, затем старт PHP-FPM
CMD ["bash", "-lc", "/wait-for-it.sh mariadb:3306 --timeout=30 --strict -- composer install --optimize-autoloader --no-dev --no-scripts && php artisan storage:link && php artisan migrate && exec php-fpm"]
