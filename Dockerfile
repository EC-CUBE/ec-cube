ARG TAG=8.1-apache-bullseye
FROM php:${TAG}

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

# PHP 8.5 以降は opcache がコアに静的組み込みされ共有モジュール (.so) を生成しないため、
# EXT_INSTALL_ARGS から opcache を外す。インストール対象はワークフロー側でバージョン別に制御する。
ARG GD_OPTIONS="--with-freetype --with-jpeg --with-webp"
ARG EXT_INSTALL_ARGS="zip gd mysqli pdo_mysql opcache intl"

RUN docker-php-ext-install -j$(nproc) pgsql pdo_pgsql \
  && docker-php-ext-configure gd ${GD_OPTIONS} \
  && docker-php-ext-install -j$(nproc) ${EXT_INSTALL_ARGS} \
  ;

RUN pecl install apcu && echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apc.ini

RUN mkdir -p ${APACHE_DOCUMENT_ROOT} \
  && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
  && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
  ;

RUN a2enmod rewrite headers ssl
# Enable SSL
RUN ln -s /etc/apache2/sites-available/default-ssl.conf /etc/apache2/sites-enabled/default-ssl.conf
# see https://stackoverflow.com/questions/73294020/docker-couldnt-create-the-mpm-accept-mutex/73303983#73303983
RUN echo "Mutex posixsem" >> /etc/apache2/apache2.conf
EXPOSE 443

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# Override with custom configuration settings
COPY dockerbuild/php.ini $PHP_INI_DIR/conf.d/
COPY dockerbuild/docker-php-entrypoint /usr/local/bin/

RUN curl -sS https://getcomposer.org/installer \
  | php \
  && mv composer.phar /usr/bin/composer

RUN composer config -g repos.packagist composer https://packagist.jp

COPY . ${APACHE_DOCUMENT_ROOT}
WORKDIR ${APACHE_DOCUMENT_ROOT}

RUN find ${APACHE_DOCUMENT_ROOT} \( -path ${APACHE_DOCUMENT_ROOT}/vendor -prune \) -or -print0 \
  | xargs -0 chown www-data:www-data \
  && find ${APACHE_DOCUMENT_ROOT} \( -path ${APACHE_DOCUMENT_ROOT}/vendor -prune \) -or \( -type d -print0 \) \
  | xargs -0 chmod g+s \
  ;

HEALTHCHECK --interval=10s --timeout=5s --retries=30 CMD pgrep apache
