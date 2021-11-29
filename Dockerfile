FROM php:7.4-apache-bullseye

ENV APACHE_DOCUMENT_ROOT /var/www/html

RUN apt update \
  && apt upgrade -y \
  && apt install --no-install-recommends -y \
    apt-transport-https \
    apt-utils \
    build-essential \
    curl \
    debconf-utils \
    gcc \
    git \
    vim \
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
    libwebp-dev \
  && apt upgrade -y ca-certificates \
  && apt clean \
  && rm -rf /var/lib/apt/lists/* \
  && echo "en_US.UTF-8 UTF-8" >/etc/locale.gen \
  && locale-gen \
  ;

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
  && docker-php-ext-install -j$(nproc) zip gd mysqli pdo_mysql opcache intl pgsql pdo_pgsql \
  ;

RUN pecl install apcu && echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apc.ini

RUN curl -sL https://deb.nodesource.com/setup_12.x | bash - \
  && apt update \
  && apt install -y nodejs \
  && apt clean \
  ;

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
COPY dockerbuild/docker-php-entrypoint /usr/local/bin/

RUN chown www-data:www-data /var/www \
  && mkdir -p ${APACHE_DOCUMENT_ROOT}/vendor \
  && mkdir -p ${APACHE_DOCUMENT_ROOT}/var \
  && chown www-data:www-data ${APACHE_DOCUMENT_ROOT}/vendor \
  && chmod g+s ${APACHE_DOCUMENT_ROOT}/vendor \
  && chown www-data:www-data ${APACHE_DOCUMENT_ROOT}/var

RUN curl -sS https://getcomposer.org/installer \
  | php \
  && mv composer.phar /usr/bin/composer

# 全体コピー前にcomposer installを先行完了させる(docker cache利用によるリビルド速度向上)

RUN composer config -g repos.packagist composer https://packagist.jp
COPY composer.json ${APACHE_DOCUMENT_ROOT}/composer.json
COPY composer.lock ${APACHE_DOCUMENT_ROOT}/composer.lock
RUN chown www-data:www-data ${APACHE_DOCUMENT_ROOT}/composer.*

USER www-data
RUN composer install \
  --no-scripts \
  --no-autoloader \
  --no-plugins \
  -d ${APACHE_DOCUMENT_ROOT} \
  ;

##################################################################
# ファイル変更時、以後のステップにはキャッシュが効かなくなる
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

USER root
