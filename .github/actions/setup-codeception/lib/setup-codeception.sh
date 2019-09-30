#!/bin/bash

set -eo pipefail

sudo apt-fast install -y build-essential debconf-utils screen google-chrome-stable
sudo apt-fast install -y unzip xvfb autogen autoconf libtool pkg-config nasm libgconf-2-4
wget -c -nc --retry-connrefused --tries=0 http://chromedriver.storage.googleapis.com/2.43/chromedriver_linux64.zip
unzip -o -q chromedriver_linux64.zip
export DISPLAY=:99
./chromedriver --url-base=/wd/hub &
echo ">>> Started chrome-driver"
sudo Xvfb -ac :99 -screen 0 1280x1024x24 > /dev/null 2>&1 &
echo ">>> Started xvfb"

if [[ $1 == '--reset' ]]; then
    rm -rf app/Plugin/* html
    git checkout app/Plugin html
    find app/proxy/entity -name '*.php' -delete
    if [[ -f ".maintenance" ]]; then
        rm .maintenance
    fi
    rm -rf var/cache
    git checkout composer.json composer.lock
    composer install --dev --no-interaction -o --apcu-autoloader
    bin/console doctrine:schema:drop --force --full-database --env=dev
    bin/console doctrine:schema:create --env=dev
    bin/console eccube:fixtures:load --env=dev
    exit
fi

php -S localhost:8000 &

exit 0
