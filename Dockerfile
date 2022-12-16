FROM ghcr.io/mileschou/php-tester:8.1-alpine

# 全域設定
WORKDIR /source

# 安裝程式依賴套件
COPY composer.* ./
RUN composer install --no-dev --no-scripts && composer clear-cache

# 複製程式碼
COPY . .
RUN composer run post-autoload-dump

CMD ["php", "artisan", "serve", "--host", "0.0.0.0"]
