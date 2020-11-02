#######################################################
# ベース環境(base)
#   各環境のベースとして、基本動作に必要なツール類をインストール
#   中間ステージとしての利用
#######################################################
FROM php:7.3-apache-stretch as base
ENV CONTAINER_BUILD_STAGE Base

ENV APACHE_DOCUMENT_ROOT /var/www/html

RUN apt-get update \
  && apt-get install --no-install-recommends -y \
    apt-transport-https \
    apt-utils \
    build-essential \
    curl \
    debconf-utils \
    gcc \
    gnupg2 \
    libfreetype6-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libpq-dev \
    libzip-dev \
    locales \
    ssl-cert \
    unzip \
    zlib1g-dev \
  # 次ステージでもaptを使用する可能性があるため、中間イメージのサイズは増えるが、cleanは行わない
  # && apt-get clean \
  # && rm -rf /var/lib/apt/lists/* \
  && echo "en_US.UTF-8 UTF-8" >/etc/locale.gen \
  && locale-gen \
  ;

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install -j$(nproc) zip gd mysqli pdo_mysql opcache intl pgsql pdo_pgsql \
  ;

RUN pecl install apcu && echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apc.ini

RUN mkdir -p ${APACHE_DOCUMENT_ROOT} \
  && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
  && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
  ;

RUN a2enmod rewrite headers ssl
# Enable SSL
RUN ln -s /etc/apache2/sites-available/default-ssl.conf /etc/apache2/sites-enabled/default-ssl.conf
EXPOSE 443

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# Override with custom configuration settings
COPY dockerbuild/php.ini $PHP_INI_DIR/conf.d/



#######################################################
# ビルダー環境(builder)
#   ビルドツール類をインストール
#   ソースコード・成果物は含まない
#   中間ステージとしての利用
#######################################################
FROM base as builder
ENV CONTAINER_BUILD_STAGE Builder

# nodeツールインストール
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash - \
  && apt-get install -y nodejs \
  # 次ステージでもaptを使用する可能性があるため、中間イメージのサイズは増えるが、cleanは行わない
  # && apt-get clean \
  ;

# composerツールインストール
RUN curl -sS https://getcomposer.org/installer \
  | php \
  && mv composer.phar /usr/bin/composer \
  && composer selfupdate --1 \
  && composer config -g repos.packagist composer https://packagist.jp \
  && composer global require hirak/prestissimo

RUN chown www-data:www-data /var/www



#######################################################
# 成果物ビルド環境(build-artifacts)
#   ビルダー環境をベースにソースコード搭載・成果物ビルドまで実施した環境
#   中間ステージとしての利用
#######################################################
FROM builder as build-artifacts
ENV CONTAINER_BUILD_STAGE "Build Artifact"

# vendor配下のパーミッション設定を先行実施
RUN mkdir -p ${APACHE_DOCUMENT_ROOT}/vendor \
  && mkdir -p ${APACHE_DOCUMENT_ROOT}/var \
  && chown www-data:www-data ${APACHE_DOCUMENT_ROOT}/vendor \
  && chmod g+s ${APACHE_DOCUMENT_ROOT}/vendor

# 全体コピー前にcomposer installを先行完了させる(docker cache利用によるリビルド速度向上)
USER www-data
COPY composer.json ${APACHE_DOCUMENT_ROOT}/composer.json
COPY composer.lock ${APACHE_DOCUMENT_ROOT}/composer.lock
RUN composer install \
  --no-scripts \
  --no-autoloader \
  -d ${APACHE_DOCUMENT_ROOT} \
  ;

# ----- ファイル変更時、以後のステップにはキャッシュが効かなくなる ------
USER root
COPY . ${APACHE_DOCUMENT_ROOT}
WORKDIR ${APACHE_DOCUMENT_ROOT}

RUN find ${APACHE_DOCUMENT_ROOT} \( -path ${APACHE_DOCUMENT_ROOT}/vendor -prune \) -or -print0 \
  | xargs -0 chown www-data:www-data \
  && find ${APACHE_DOCUMENT_ROOT} \( -path ${APACHE_DOCUMENT_ROOT}/vendor -prune \) -or \( -type d -print0 \) \
  | xargs -0 chmod g+s \
  ;

USER www-data
RUN composer dumpautoload -o --apcu

RUN if [ ! -f ${APACHE_DOCUMENT_ROOT}/.env ]; then \
        cp -p .env.dist .env \
        ; fi

# trueを指定した場合、DBマイグレーションやECCubeのキャッシュ作成をスキップする。
# ビルド時点でDBを起動出来ない場合等に指定が必要となる。
ARG SKIP_INSTALL_SCRIPT_ON_DOCKER_BUILD=false

RUN if [ ! -f ${APACHE_DOCUMENT_ROOT}/var/eccube.db ] && [ ! ${SKIP_INSTALL_SCRIPT_ON_DOCKER_BUILD} = "true" ]; then \
        composer run-script installer-scripts && composer run-script auto-scripts \
        ; fi


#######################################################
# ランタイム環境(runtime)
#   コンテナによるアプリ配布を想定した環境
#   composer/node等使用不可
#######################################################
FROM base as runtime
ENV CONTAINER_BUILD_STAGE Runtime
USER root

RUN apt-get clean \
  && rm -rf /var/lib/apt/lists/* \
  ;

COPY --from=build-artifacts /var/www/html /var/www/html



#######################################################
# 開発環境(develop)
#   ビルダー環境に開発用ツール類を追加した環境
#   通常の開発に使用する
#######################################################
FROM builder as develop
ENV CONTAINER_BUILD_STAGE Develop
USER root
# 開発ツールインストール
RUN apt-get install --no-install-recommends -y \
    git \
    vim \
    sudo \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* \
  ;

# 各ファイル変更の度に上記aptが実行される事を防ぐため、成果物を含まない環境をベースとし、ビルド成果物はbuild-artifactsから取得する
COPY --from=build-artifacts /var/www/html /var/www/html
