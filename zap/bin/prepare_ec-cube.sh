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
# MCP のレート制限 (IP ごとに 1 分 60 件、クライアントごとに 1 分 300 件) は、能動スキャンの攻撃の大半を 429 で止める
run sed -i -e 's/limit: 60$/limit: 100000/' -e 's/limit: 300$/limit: 100000/' app/config/eccube/packages/mcp_rate_limiter.yaml
# eccube-api4 の動的クライアント登録 (/register) は IP ごとに 1 時間 20 件までのため、攻撃が 429 で止まる
API_CONFIG=app/Plugin/Api44/Resource/config/services.yaml
if run test -f "${API_CONFIG}"; then
    run sed -i -e 's/limit: 20$/limit: 100000/' -e 's/limit: 200$/limit: 100000/' "${API_CONFIG}"
fi
# インストール後のプラグインの有効化 (/install/plugin*) は、インストーラが書き出すトランザクションのファイルの
# トークンで認可する。install_plugins.zst が ECCUBE-CSRF-TOKEN に入れて送るトークンで置く (期限は 2100 年)。
# インストーラと同じく末尾に改行を付けない (改行があるとトークンが一致しない)。
# ローカルでは、ターゲットごとに zap/local/restore.sh が置き直す
run sh -c 'printf %s 4102444800:zap-install-plugin-token > var/.httransaction'
run bin/console cache:clear > /dev/null
run bin/console debug:container --parameter eccube_login_throttling_max_attempts
run bin/console debug:container --parameter eccube_login_throttling_interval
