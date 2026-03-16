FROM php:8.4-apache

# 啟用 Apache mod_rewrite
RUN a2enmod rewrite

# 設定 DocumentRoot 指向 Laravel public 目錄
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

RUN apt-get update -y && apt-get install -y --no-install-recommends \
        unzip \
        libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql sockets \
    && apt-get autoremove -y && rm -r /var/lib/apt/lists/*

# Install Composer v2
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# 安裝程式依賴套件
COPY composer.* ./
RUN composer install --no-dev --no-scripts --optimize-autoloader && composer clear-cache

# 複製程式碼
COPY . .
RUN composer run post-autoload-dump

# 設定目錄權限
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
