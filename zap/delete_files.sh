#!/bin/bash

LATEST_FILE=$(find /var/www/html/html/ -printf '%T+ %p\n' | sort -r | head -n 1 | cut -d' ' -f 2)

while true
do
    find /var/www/html/html/ -newer $LATEST_FILE -mmin +0.1 -type f -exec rm {} +
    sleep 10
done