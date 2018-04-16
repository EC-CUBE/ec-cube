#!/bin/bash

chromedriver --url-base=/wd/hub &
CDPID="$!"
trap "kill ${CDPID}" exit

mailcatcher
php -S localhost:8000 &

vendor/bin/codecept -vvv run acceptance --env chrome-headless,travis "$@"
