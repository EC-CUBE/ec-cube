#!/bin/bash

chromedriver --url-base=/wd/hub &
CDPID="$!"
trap "kill ${CDPID}" exit
php -S localhost:8000 &

vendor/bin/codecept -vvv run acceptance --env pgsql,chrome-headless "$@"
