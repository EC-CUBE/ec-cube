#!/bin/bash
#
# スキャン用に EC-CUBE の設定を変える。
# 能動スキャンはログインを大量に繰り返すため、ログインのスロットリングとレートリミッタを緩める。
#
# docker compose は呼び出し元の環境変数 (COMPOSE_FILE / COMPOSE_PROJECT_NAME) で実行する。

set -euo pipefail
cd "$(dirname "$0")/../.." || exit 1

run() { docker compose exec -T -u www-data:www-data ec-cube "$@"; }

run sed -i \
    -e 's/eccube_login_throttling_max_attempts: 5/eccube_login_throttling_max_attempts: 1024/' \
    -e "s/eccube_login_throttling_interval: '30 minutes'/eccube_login_throttling_interval: '1 minutes'/" \
    app/config/eccube/packages/eccube.yaml
run rm -f app/config/eccube/packages/prod/eccube_rate_limiter.yaml
run sed -i -e 's/30 min/1 min/g' app/config/eccube/packages/eccube_rate_limiter.yaml
run bin/console cache:clear > /dev/null
run bin/console debug:container --parameter eccube_login_throttling_max_attempts
run bin/console debug:container --parameter eccube_login_throttling_interval
