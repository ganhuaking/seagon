FROM ghcr.io/104lab/lazy-octane:8.1-alpine

# 全域設定
WORKDIR /usr/app/src

RUN docker-php-ext-install -j $(getconf _NPROCESSORS_ONLN) sockets

# Install Composer v2
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# 安裝程式依賴套件
COPY composer.* ./
RUN composer install --no-dev --no-scripts && composer clear-cache

# 複製程式碼
COPY . .
RUN composer run post-autoload-dump

CMD ["php", "artisan", "octane:start", "--host", "0.0.0.0"]
