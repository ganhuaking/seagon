FROM ghcr.io/104lab/lazy-octane:8.2

# 全域設定
WORKDIR /usr/app/src

RUN apt-get update -y && apt-get install -y --no-install-recommends \
        unzip \
    && apt-get autoremove -y && rm -r /var/lib/apt/lists/*

RUN docker-php-ext-install -j $(getconf _NPROCESSORS_ONLN) sockets

# Install Composer v2
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# 安裝程式依賴套件
COPY composer.* ./
RUN composer install --no-dev --no-scripts --optimize-autoloader && composer clear-cache

# 複製程式碼
COPY . .
RUN composer run post-autoload-dump

CMD ["php", "artisan", "octane:start", "--host", "0.0.0.0"]
