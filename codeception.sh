#!/bin/bash

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

./chromedriver --url-base=/wd/hub &
CDPID="$!"
trap "kill ${CDPID}" exit

mailcatcher
php -S localhost:8000 &

vendor/bin/codecept -vvv run acceptance --env chrome,travis "$@"
