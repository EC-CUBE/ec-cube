#!/bin/bash
#
# 1 つのターゲットを実行する。DB を戻してから、zap/targets.json の設定で autorun.sh を呼ぶ。
#
# usage: LANE=<n> run.sh [-p <policy>] <target>
#   -p  Sequence (既定) または Smoke
#
# 結果は zap/local/out/<policy>/<target>/ に出力する。

set -uo pipefail

POLICY=Sequence
if [[ ${1:-} == -p ]]; then POLICY=$2; shift 2; fi
TARGET=${1:?usage: LANE=<n> $0 [-p <policy>] <target>}

source "$(dirname "$0")/env.sh"

# 空の項目を詰めないよう | 区切りで取り出す
settings=$(jq -r --arg t "${TARGET}" \
    '.[] | select(.target == $t) | [.before_script // "", .context // "", (.thread_per_host // "" | tostring)] | join("|")' \
    zap/targets.json)
if [[ -z ${settings} ]]; then
    echo "${TARGET} は zap/targets.json にありません" >&2
    exit 2
fi
IFS='|' read -r BEFORE CONTEXT THREADS <<< "${settings}"

"${ZAP_LOCAL}/restore.sh" || { echo "lane ${LANE}: DB を戻せませんでした" >&2; exit 1; }

exec zap/bin/autorun.sh -t "${TARGET}" -o "${ZAP_LOCAL}/out/${POLICY}/${TARGET}" -p "${POLICY}" \
    ${BEFORE:+-b "${BEFORE}"} ${CONTEXT:+-c "${CONTEXT}"} ${THREADS:+-n "${THREADS}"}
