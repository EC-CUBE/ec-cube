# shellcheck shell=bash
#
# ローカル実行の共通設定。各スクリプトから source する。
#
# LANE=<n> (1 以上) ごとに別の docker compose プロジェクト (eccube-zap-<n>) を使う。
# レーンごとに EC-CUBE・PostgreSQL・ZAP が独立しているため、複数のターゲットを並列に実行できる。

ZAP_ROOT=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
ZAP_LOCAL=${ZAP_ROOT}/zap/local
LANE=${LANE:-1}

export COMPOSE_PROJECT_NAME=eccube-zap-${LANE}
export COMPOSE_FILE=${ZAP_ROOT}/docker-compose.yml:${ZAP_ROOT}/docker-compose.pgsql.yml:${ZAP_ROOT}/docker-compose.owaspzap.ci.yml:${ZAP_LOCAL}/docker-compose.local.yml

cd "${ZAP_ROOT}" || exit 1
