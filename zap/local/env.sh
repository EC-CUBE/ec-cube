# shellcheck shell=bash
#
# ローカル実行の共通設定。各スクリプトから source する。
#
# LANE=<n> (1 以上) ごとに別の docker compose プロジェクト (eccube-zap-<n>) を使う。
# レーンごとに EC-CUBE・PostgreSQL・ZAP が独立しているため、複数のターゲットを並列に実行できる。

ZAP_ROOT=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
ZAP_LOCAL=${ZAP_ROOT}/zap/local
# 結果の出力先。条件を変えて同じターゲットを実行するときは、別の出力先を指定して上書きを避ける
ZAP_OUT=${ZAP_OUT:-${ZAP_LOCAL}/out}
LANE=${LANE:-1}
# DB は ZAP_DB で選ぶ (pgsql が既定、mysql)。MySQL のレーンは別の compose プロジェクトにし、同じ番号の PostgreSQL のレーンと共存させる
ZAP_DB=${ZAP_DB:-pgsql}

if [[ ${ZAP_DB} == pgsql ]]; then
    export COMPOSE_PROJECT_NAME=eccube-zap-${LANE}
else
    export COMPOSE_PROJECT_NAME=eccube-zap-${ZAP_DB}-${LANE}
fi
export COMPOSE_FILE=${ZAP_ROOT}/docker-compose.yml:${ZAP_ROOT}/docker-compose.${ZAP_DB}.yml:${ZAP_ROOT}/docker-compose.owaspzap.ci.yml:${ZAP_LOCAL}/docker-compose.local.yml:${ZAP_LOCAL}/docker-compose.local.${ZAP_DB}.yml

cd "${ZAP_ROOT}" || exit 1
