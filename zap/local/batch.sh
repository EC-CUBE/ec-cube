#!/bin/bash
#
# 複数のターゲットを、複数のレーンで並列に実行する。
# zap/targets.json を共有のキューにして、各レーンが空き次第 1 本ずつ取って実行する。
#
# usage: batch.sh [-p <policy>] -l "<レーン番号>..." [<ターゲットの正規表現>]
#   例: zap/local/batch.sh -p Smoke -l "1 2 3 4"
#       zap/local/batch.sh -l "1 2" 'admin_product_.*'
#
# 結果の一覧は zap/local/out/<policy>/summary.tsv に出力する (出力先は ZAP_OUT で変更できる)。
# 各ターゲットの結果は zap/local/out/<policy>/<target>/ にある。

set -uo pipefail

POLICY=Sequence LANES=
while getopts "p:l:" OPT; do
    case ${OPT} in
        p) POLICY=${OPTARG} ;;
        l) LANES=${OPTARG} ;;
        *) exit 2 ;;
    esac
done
shift $((OPTIND - 1))
RE=${1:-.*}
if [[ -z ${LANES} ]]; then
    echo "usage: $0 [-p <policy>] -l \"<レーン番号>...\" [<ターゲットの正規表現>]" >&2
    exit 2
fi

source "$(dirname "$0")/env.sh"
OUT=${ZAP_OUT}/${POLICY}
QUEUE=${OUT}/queue.txt
SUMMARY=${OUT}/summary.tsv
mkdir -p "${OUT}"

jq -r --arg re "^(${RE})$" '.[] | select(.target | test($re)) | .target' zap/targets.json > "${QUEUE}"
echo "$(wc -l < "${QUEUE}") targets, lanes: ${LANES}"
: > "${SUMMARY}"

worker() {
    local lane=$1 target start result
    while true; do
        target=$(flock "${QUEUE}.lock" sh -c "head -1 '${QUEUE}'; sed -i 1d '${QUEUE}'")
        [[ -z ${target} ]] && break
        start=$(date +%s)
        result=$(LANE=${lane} "${ZAP_LOCAL}/run.sh" -p "${POLICY}" "${target}" 2> /dev/null | grep '^RESULT' | cut -f2-)
        [[ -z ${result} ]] && result=$(printf '%s\t%s\t実行できませんでした' "${target}" "${POLICY}")
        printf '%s\tlane%s\t%ss\n' "${result}" "${lane}" $(( $(date +%s) - start )) | tee -a "${SUMMARY}"
    done
}

for lane in ${LANES}; do
    worker "${lane}" &
done
wait
rm -f "${QUEUE}" "${QUEUE}.lock"
echo "summary: ${SUMMARY}"
