#!/bin/bash

LATEST_FILE=$(find /var/www/html/html/ -printf '%T+ %p\n' | sort -r | head -n 1 | cut -d' ' -f 2)
# 商品画像の一時ファイルは、アップロードの次のステップの再生まで残す。
# アップロードの能動スキャンが終わるまで再生されないため、すぐに消すと商品登録が入力エラーになる
TEMP_IMAGE_DIR=/var/www/html/html/upload/temp_image

while true
do
    find /var/www/html/html/ -path "${TEMP_IMAGE_DIR}" -prune -o -newer $LATEST_FILE -mmin +0.1 -type f -exec rm {} +
    find "${TEMP_IMAGE_DIR}" -newer $LATEST_FILE -mmin +60 -type f -exec rm {} +
    sleep 10
done
