# Anthropic OSS Scanner 用のビルド環境（4.0）。
# スキャナはこの Dockerfile をネットワークありでビルドし、以降はネットワークなしで解析する。
# テストに必要なもの（dev 依存・PHPUnit・DB スキーマ・フィクスチャ）はすべてここで用意する。
# DB は単一コンテナで完結させるため SQLite を使う（Travis の sqlite3 構成と同じ）。
FROM php:7.3-cli-stretch

ENV DEBIAN_FRONTEND=noninteractive

# Debian 9 (stretch) は deb.debian.org から外れたため、アーカイブへ向け直す
RUN printf 'deb http://archive.debian.org/debian stretch main\ndeb http://archive.debian.org/debian-security stretch/updates main\n' > /etc/apt/sources.list \
  && rm -f /etc/apt/sources.list.d/* \
  && echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99archive

RUN apt-get update \
  && apt-get install -y --no-install-recommends --allow-unauthenticated \
    ca-certificates curl git unzip \
    libfreetype6-dev libicu-dev libjpeg62-turbo-dev libpng-dev libpq-dev libzip-dev libwebp-dev zlib1g-dev \
  && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype-dir=/usr --with-jpeg-dir=/usr --with-webp-dir=/usr \
  && docker-php-ext-install -j"$(nproc)" pgsql pdo_pgsql zip gd mysqli pdo_mysql opcache intl \
  && pecl install apcu-5.1.21 && echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apc.ini

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
  && echo "memory_limit=-1" > "$PHP_INI_DIR/conf.d/zz-memory.ini" \
  && echo "apc.enable_cli=1" >> "$PHP_INI_DIR/conf.d/zz-memory.ini"

# 4.0 の composer.lock は Composer 1 系のプラグインを含む。Composer 2.2 で、プラグインを動かさずに入れる
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# scanner contract: the checkout lives inside the image, at /src
COPY . /src
WORKDIR /src

ENV APP_ENV=test \
    DATABASE_URL=sqlite:///var/eccube.db \
    DATABASE_SERVER_VERSION=3 \
    MAILER_URL=null://localhost

# dev 依存（PHPUnit など）を含めて入れる
RUN composer install --no-interaction --no-scripts --no-plugins --ignore-platform-req=composer-plugin-api \
  && composer dumpautoload -o --no-plugins

# bin/phpunit（simple-phpunit）は初回に PHPUnit をダウンロードする。ネットワークなしの解析に備えてここで取得する
RUN bin/phpunit --version

# SQLite は CREATE DATABASE が無いため schema:create だけで DB ファイルが作られる
RUN bin/console doctrine:schema:create \
  && bin/console eccube:fixtures:load

# 動作確認。失敗してもビルドは止めない（スキャナの手引きどおり）
RUN (bin/phpunit --exclude-group cache-clear,cache-clear-install,update-schema-doctrine,plugin-service \
      tests/Eccube/Tests/Service/TaxRuleServiceTest.php \
     || echo "WARNING: phpunit reported failures")
