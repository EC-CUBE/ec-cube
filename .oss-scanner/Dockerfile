# Anthropic OSS Scanner 用のビルド環境（4.4）。
# スキャナはこの Dockerfile をネットワークありでビルドし、以降はネットワークなしで解析する。
# テストに必要なもの（dev 依存・DB スキーマ・フィクスチャ・UCP の schema）はすべてここで用意する。
# DB は単一コンテナで完結させるため SQLite を使う（CI の sqlite3 構成と同じ）。
FROM php:8.2-cli-bookworm

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    ca-certificates curl git unzip \
    libfreetype6-dev libicu-dev libjpeg62-turbo-dev libpng-dev libpq-dev libzip-dev libwebp-dev zlib1g-dev \
  && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
  && docker-php-ext-install -j"$(nproc)" pgsql pdo_pgsql zip gd mysqli pdo_mysql opcache intl \
  && pecl install apcu && echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apc.ini

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
  && echo "memory_limit=-1" > "$PHP_INI_DIR/conf.d/zz-memory.ini" \
  && echo "apc.enable_cli=1" >> "$PHP_INI_DIR/conf.d/zz-memory.ini"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# scanner contract: the checkout lives inside the image, at /src
COPY . /src
WORKDIR /src

ENV APP_ENV=test \
    DATABASE_URL=sqlite:///var/eccube.db \
    DATABASE_SERVER_VERSION=3 \
    DATABASE_CHARSET=utf8 \
    MAILER_DSN=null://null \
    ECCUBE_AUTH_MAGIC=oss-scanner-0000000000000000000000000000000000000000000000000000

# dev 依存（PHPUnit など）を含めて入れる
RUN composer install --no-interaction -o --apcu-autoloader

# SQLite は CREATE DATABASE が無いため schema:create だけで DB ファイルが作られる
RUN bin/console doctrine:schema:create \
  && bin/console eccube:fixtures:load

# Agent Commerce (UCP) のスキーマ契約テスト用。実行時はネットワークが無いのでここで取得する
RUN git clone --filter=blob:none --branch v2026-04-08 --single-branch --quiet \
    https://github.com/Universal-Commerce-Protocol/ucp.git var/agent-commerce-spec/ucp

# 動作確認。失敗してもビルドは止めない（スキャナの手引きどおり）
RUN (vendor/bin/phpunit --exclude-group cache-clear --exclude-group cache-clear-install \
      --exclude-group update-schema-doctrine --exclude-group plugin-service --exclude-group mcp \
      --exclude-group architecture tests/Eccube/Tests/Service/TaxRuleServiceTest.php \
     || echo "WARNING: phpunit reported failures")
