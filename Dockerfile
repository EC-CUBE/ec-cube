FROM php:7.3-apache-stretch

ENV APACHE_DOCUMENT_ROOT /var/www/html

RUN apt-get update \
  && apt-get install --no-install-recommends -y \
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
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* \
  && echo "en_US.UTF-8 UTF-8" >/etc/locale.gen \
  && locale-gen \
  ;

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install -j$(nproc) zip gd mysqli pdo_mysql opcache intl pgsql pdo_pgsql \
  ;

RUN pecl install apcu && echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apc.ini

RUN curl -sL https://deb.nodesource.com/setup_12.x | bash - \
  && apt-get install -y nodejs \
  && apt-get clean \
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

COPY . ${APACHE_DOCUMENT_ROOT}

WORKDIR ${APACHE_DOCUMENT_ROOT}

RUN curl -sS https://getcomposer.org/installer \
  | php \
  && mv composer.phar /usr/bin/composer \
  && composer selfupdate --1 \
  && composer config -g repos.packagist composer https://packagist.jp \
  && composer global require hirak/prestissimo \
  && chown www-data:www-data /var/www \
  && mkdir -p ${APACHE_DOCUMENT_ROOT}/var \
  && chown -R www-data:www-data ${APACHE_DOCUMENT_ROOT} \
  && find ${APACHE_DOCUMENT_ROOT} -type d -print0 \
  | xargs -0 chmod g+s \
  ;

USER www-data

RUN composer install \
  --no-scripts \
  --no-autoloader \
  -d ${APACHE_DOCUMENT_ROOT} \
  ;

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
