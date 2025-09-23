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

# 2) PHP-расширения: MySQL (MariaDB), GD и т.д.
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) \
  mbstring exif pcntl bcmath zip \
  pdo_mysql gd \
  && docker-php-ext-install mysqli

# 3) Redis через PECL
RUN pecl install redis-6.2.0 \
  && docker-php-ext-enable redis

# 4) Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5) Установка зависимостей приложения
WORKDIR /var/www/vanillacrm_src
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-scripts

# 6) Копирование кодовой базы в контейнер
COPY . .

# 7) Создание нужных директорий и установка прав
RUN mkdir -p storage/logs storage/framework/sessions bootstrap/cache \
  && chown -R www-data:www-data storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache

# 8) Линковка директорий
RUN php artisan storage:link

# 9) Документирование порта PHP-FPM
EXPOSE 9000

# 10) Ждём запуска MariaDB и выполняем миграции, затем старт PHP-FPM
CMD ["bash", "-lc", "/wait-for-it.sh mariadb:3306 --timeout=30 --strict -- php artisan migrate && exec php-fpm"]
