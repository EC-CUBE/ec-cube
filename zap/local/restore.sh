#!/bin/bash
#
# DB を eccubedb_clean から作り直し、前のターゲットの影響を消す。
# BaseInfo 等は結果キャッシュに残るため cache pool も消す。スキャンがメンテナンスモードを
# 有効にすることがあるため .maintenance も消す。
#
# usage: LANE=<n> restore.sh

set -euo pipefail
source "$(dirname "$0")/env.sh"

if [[ ${ZAP_DB} == mysql ]]; then
    docker compose exec -T -e MYSQL_PWD=root mysql sh -c \
        'mysql -u root -e "DROP DATABASE IF EXISTS eccubedb; CREATE DATABASE eccubedb" && mysqldump -u root --single-transaction --routines eccubedb_clean | mysql -u root eccubedb' < /dev/null > /dev/null
else
    docker compose exec -T -e PGUSER=dbuser postgres psql -d postgres -v ON_ERROR_STOP=1 -q \
        -c "DROP DATABASE IF EXISTS eccubedb WITH (FORCE)" \
        -c "CREATE DATABASE eccubedb TEMPLATE eccubedb_clean" < /dev/null > /dev/null
fi
docker compose exec -T -u www-data:www-data ec-cube \
    sh -c 'rm -f .maintenance && echo "4102444800:zap-install-plugin-token" > var/.httransaction && bin/console cache:pool:clear --all > /dev/null' < /dev/null
