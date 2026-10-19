#!/bin/bash
#
# スキャン中に作られたデータとファイルを定期的に削除する処理を、コンテナ内でバックグラウンド起動する。
# 能動スキャンが大量に作るレコードやファイルで、画面が重くなるのを防ぐ。
#
# docker compose は呼び出し元の環境変数 (COMPOSE_FILE / COMPOSE_PROJECT_NAME) で実行する。

set -euo pipefail
cd "$(dirname "$0")/../.." || exit 1

# DB は起動しているサービスで判断する (docker-compose.pgsql.yml / docker-compose.mysql.yml)
if docker compose ps --services | grep -qx mysql; then
    docker compose cp zap/delete_data_mysql.sh mysql:/delete_data.sh > /dev/null
    docker compose exec -d -e MYSQL_PWD=secret mysql /delete_data.sh
else
    docker compose cp zap/delete_data.sh postgres:/ > /dev/null
    docker compose exec -d -e PGUSER=dbuser -e PGDATABASE=eccubedb postgres /delete_data.sh
fi
docker compose cp zap/delete_files.sh ec-cube:/ > /dev/null
docker compose exec -d ec-cube /delete_files.sh
