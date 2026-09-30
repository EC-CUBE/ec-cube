#!/bin/bash
#
# DB を eccubedb_clean から作り直し、前のターゲットの影響を消す。
# BaseInfo 等は結果キャッシュに残るため cache pool も消す。スキャンがメンテナンスモードを
# 有効にすることがあるため .maintenance も消す。
#
# usage: LANE=<n> restore.sh

set -euo pipefail
source "$(dirname "$0")/env.sh"

docker compose exec -T -e PGUSER=dbuser postgres psql -d postgres -v ON_ERROR_STOP=1 -q \
    -c "DROP DATABASE IF EXISTS eccubedb WITH (FORCE)" \
    -c "CREATE DATABASE eccubedb TEMPLATE eccubedb_clean" < /dev/null > /dev/null
docker compose exec -T -u www-data:www-data ec-cube \
    sh -c 'rm -f .maintenance && bin/console cache:pool:clear --all > /dev/null' < /dev/null
