# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 專案概述

Seagon 是一個基於 Laravel 12 的 LINE 聊天機器人，在 LINE 群組中提供互動功能。主要整合 LINE Bot SDK 和 OpenAI API。

## 常用指令

```bash
# Docker 開發
make build          # 建置 Docker 映像
make rebuild        # 重建映像（無快取）
make up             # 啟動容器並查看日誌
make down           # 停止容器並移除資料卷
make server         # 本地啟動開發伺服器

# 測試
php artisan test                          # 執行所有測試
./vendor/bin/phpunit tests/Unit           # 只跑單元測試
./vendor/bin/phpunit tests/Feature        # 只跑功能測試
./vendor/bin/phpunit tests/Unit/FooTest.php  # 跑單一測試檔

# 模擬訊息（本地測試用）
php artisan simulate                      # 模擬 LINE 訊息

# 資產編譯
npm run dev         # 開發模式編譯
npm run watch       # 監視模式
npm run production  # 生產模式編譯

# Composer
composer install    # 安裝 PHP 依賴
```

## 架構

### 訊息處理管道

核心入口為 `app/Http/Controllers/Line/Messaging.php`，接收 LINE Webhook POST 請求（`/api/line/messaging`）。訊息進入後通過 **Laravel Pipeline** 依序經過 12 個業務中介軟體處理：

```
AntiFraud → Image → Charm → Talk → Quotation → Inspire → Slot → Theory → Stock → Secret → NathanRecord → ChatGPT
```

每個中介軟體位於 `app/Seagon/Middleware/`，各自負責一種回應邏輯。中介軟體若命中條件則回覆訊息並終止管道，否則傳遞給下一層。

### 關鍵目錄

- `app/Seagon/Middleware/` — 12 個業務中介軟體，各功能的核心邏輯
- `app/Seagon/` — 輔助類別（Quotation 語錄、Inspire 情話、Slot 隨機詞、Random 等）
- `app/Console/Commands/` — Artisan 命令（simulate、reply、talk、discord）
- `routes/api.php` — API 路由（`/check` 健康檢查、`/line/messaging` Webhook）

### 安全機制

群組白名單控制：只有 `LINE_MESSAGING_GROUP_ALLOW_LIST` 中的群組 ID 才會處理訊息，其他返回 204。

### 環境變數

關鍵 `.env` 設定：
- `LINE_MESSAGEING_TOGGLE` — 訊息處理開關
- `LINE_MESSAGING_GROUP_ALLOW_LIST` — 允許的群組 ID（逗號分隔）
- `OPENAI_API_KEY` — OpenAI API 金鑰
- LINE Bot 相關認證密鑰

## 部署

- **Docker**：基礎映像 `php:8.4-apache`，含 PHP 8.4 + Apache

## 程式碼風格

- StyleCI Laravel preset（`.styleci.yml`）
- 縮排 4 空格，UTF-8，LF 換行（`.editorconfig`）
