#!/bin/bash

LATEST_FILE=$(find /var/www/html/html/ -printf '%T+ %p\n' | sort -r | head -n 1 | cut -d' ' -f 2)
# 次のディレクトリのファイルは、後のステップの再生まで残す。
# 能動スキャンが終わるまで後のステップは再生されないため、すぐに消すと再生が失敗する
# - 商品画像の一時ファイル: アップロードの次の商品登録が入力エラーになる
# - 返品申請の添付ファイル: シナリオの冒頭で作るため、管理画面のダウンロードが 404 になる
KEEP_DIRS=(
    /var/www/html/html/upload/temp_image
    /var/www/html/html/upload/refund_request/save
)

prune=()
for dir in "${KEEP_DIRS[@]}"; do
    prune+=(-path "${dir}" -prune -o)
done

while true
do
    find /var/www/html/html/ "${prune[@]}" -newer $LATEST_FILE -mmin +0.1 -type f -exec rm {} +
    find "${KEEP_DIRS[@]}" -newer $LATEST_FILE -mmin +60 -type f -exec rm {} +
    sleep 10
done
