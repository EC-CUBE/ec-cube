#!/bin/bash
#
# 1 つのターゲットについて、自動化プランを生成して ZAP で実行し、レポートを回収して結果を判定する。
#
# usage: autorun.sh -t <target> -o <出力先> [-b <before_script>] [-c <context>] [-n <thread_per_host>] [-p <policy>]
#   -p  Sequence (既定) または Smoke
#
# 出力先には plan.yml / zap.log / ZAP-Report-<target>.html / alerts.json を置く。
# 終了コードは check_results.sh の判定に従う。
# docker compose は呼び出し元の環境変数 (COMPOSE_FILE / COMPOSE_PROJECT_NAME) で実行する。

set -uo pipefail

TARGET='' OUT='' BEFORE='' CONTEXT='' THREADS='' POLICY=Sequence
while getopts "t:o:b:c:n:p:" OPT; do
    case ${OPT} in
        t) TARGET=${OPTARG} ;;
        o) OUT=${OPTARG} ;;
        b) BEFORE=${OPTARG} ;;
        c) CONTEXT=${OPTARG} ;;
        n) THREADS=${OPTARG} ;;
        p) POLICY=${OPTARG} ;;
        *) exit 2 ;;
    esac
done
if [[ -z ${TARGET} || -z ${OUT} ]]; then
    echo "usage: $0 -t <target> -o <出力先> [-b <before_script>] [-c <context>] [-n <thread_per_host>] [-p <policy>]" >&2
    exit 2
fi
OUT=$(realpath -m "${OUT}")
cd "$(dirname "$0")/../.." || exit 1
mkdir -p "${OUT}"

# 省略時は sequence-activeScan の既定 (同梱の Sequence ポリシー) を使うため、Sequence は渡さない
POLICY_ARG=
[[ ${POLICY} != Sequence ]] && POLICY_ARG=${POLICY}
zap/generate_automation_config.sh -t "${TARGET}" \
    ${BEFORE:+-b "${BEFORE}"} ${CONTEXT:+-c "${CONTEXT}"} ${POLICY_ARG:+-p "${POLICY_ARG}"} || exit 1

# zst はコンテナ内へコピーしてから読ませる (Docker Desktop のバインドマウント越しに読むと遅いため)。
# 生成したプランは作業ツリーに残さない
sed 's#/zap/wrk/scripts/#/tmp/scripts/#' "zap/automation/${TARGET}.yml" > "${OUT}/plan.yml"
rm -f "zap/automation/${TARGET}.yml"
docker compose exec -T -u 0:0 zap sh -c \
    'rm -rf /tmp/scripts /tmp/report /tmp/alerts.json && cp -r /zap/wrk/scripts /tmp/scripts && chown -R zap /tmp/scripts' || exit 1
docker compose exec -T zap sh -c 'cat > /tmp/plan.yml' < "${OUT}/plan.yml" || exit 1

# connection.httpStateEnabled: before_script (standalone) でログインしたセッションをシナリオの再生へ引き継ぐ
docker compose exec -T zap /zap/zap.sh -cmd \
    -config anticsrf.tokens.token.name=_csrf_token \
    -config anticsrf.tokens.token.enabled=true \
    -config connection.httpStateEnabled=true \
    ${THREADS:+-config scanner.threadPerHost=${THREADS}} \
    -configfile /zap/wrk/options.properties \
    -autorun /tmp/plan.yml < /dev/null 2>&1 | tee "${OUT}/zap.log"

docker compose cp zap:/tmp/report/. "${OUT}/" > /dev/null 2>&1
docker compose cp zap:/tmp/alerts.json "${OUT}/alerts.json" > /dev/null 2>&1

exec zap/bin/check_results.sh "${OUT}/zap.log" "${POLICY}" "${TARGET}"
