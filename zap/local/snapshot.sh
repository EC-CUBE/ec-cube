#!/bin/bash
#
# 現在の DB を eccubedb_clean として保存する。restore.sh はこの DB から作り直す。
# setup.sh から、データの定期削除を起動する前に呼ぶ。
#
# usage: LANE=<n> snapshot.sh

set -euo pipefail
source "$(dirname "$0")/env.sh"

docker compose exec -T -e PGUSER=dbuser postgres psql -d postgres -v ON_ERROR_STOP=1 -q \
    -c "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = 'eccubedb' AND pid <> pg_backend_pid()" \
    -c "DROP DATABASE IF EXISTS eccubedb_clean" \
    -c "CREATE DATABASE eccubedb_clean TEMPLATE eccubedb" < /dev/null > /dev/null
