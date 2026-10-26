# Anthropic OSS Scanner 用のビルド環境（4.2）。
# スキャナはこの Dockerfile をネットワークありでビルドし、以降はネットワークなしで解析する。
# テストに必要なもの（dev 依存・DB スキーマ・フィクスチャ）はすべてここで用意する。
# DB は単一コンテナで完結させるため SQLite を使う（CI の sqlite3 構成と同じ）。
FROM php:7.4-cli-bullseye

ENV DEBIAN_FRONTEND=noninteractive

# Debian 11 (bullseye) は deb.debian.org から外れたため、アーカイブへ向け直す
RUN printf 'deb http://archive.debian.org/debian bullseye main\ndeb http://archive.debian.org/debian-security bullseye-security main\n' > /etc/apt/sources.list \
  && rm -f /etc/apt/sources.list.d/* \
  && echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99archive

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

# 動作確認。失敗してもビルドは止めない（スキャナの手引きどおり）
RUN (vendor/bin/phpunit --exclude-group cache-clear,cache-clear-install,update-schema-doctrine,plugin-service \
      tests/Eccube/Tests/Service/TaxRuleServiceTest.php \
     || echo "WARNING: phpunit reported failures")
