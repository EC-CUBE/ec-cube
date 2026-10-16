#!/bin/bash
#
# EC-CUBE のルートのうち、どの Zest シナリオも通らないものを一覧にする。
#
# usage: coverage.sh [-a] <routes.json>
#   routes.json: bin/console debug:router --format=json の出力 (- で標準入力)
#   -a: 通るルートも含めて全件を出力する
#
#   例: LANE=1 zap/local/compose.sh exec -T ec-cube bin/console debug:router --format=json \
#         | zap/bin/coverage.sh -
#
# シナリオのリクエストの URL とメソッドがルートに一致すれば「通る」とする。POST の _method は
# そのメソッドとして扱う。URL 中の {{変数}} は、いくつかの候補値のどれかでルートの制約を満たせば一致とする。
# 「通る」は画面へのリクエストがあるというだけで、能動スキャンの攻撃が効いたことは意味しない。
# zap/coverage_exclude.txt に書いたルートは対象外として数えない。
#
# 出力はタブ区切り: <通る|通らない|対象外> <area> <method> <path> [<対象外の理由>]
# 最後に area ごとの集計を標準エラーへ出力する。

set -euo pipefail

cd "$(dirname "$0")/.."

ALL=
while getopts a OPT; do
    case $OPT in
        a) ALL=1 ;;
        *) exit 1 ;;
    esac
done
shift $((OPTIND - 1))

ROUTES=${1:?usage: coverage.sh [-a] <routes.json>}
[[ ${ROUTES} == - ]] && ROUTES=/dev/stdin

requests=$(jq -c '
    .statements[]? | select(.elementType == "ZestRequest")
    | . as $s
    | {
        method: ((($s.data // "") | capture("(^|&)_method=(?<m>[A-Za-z]+)").m // $s.method) | ascii_upcase),
        url: ((.url // .urlToken) | sub("^https?://[^/]+"; "") | sub("\\?.*$"; ""))
      }' scripts/*.zst | jq -s 'unique')

excludes=$(grep -v -e '^#' -e '^$' coverage_exclude.txt | jq -R -s '
    split("\n") | map(select(length > 0) | split("\t") | {re: .[0], reason: (.[1] // "")})')

rows=$(jq -r --argjson reqs "${requests}" --argjson excludes "${excludes}" '
    # 変数の候補値。数値の ID、英字のコード、cart_key のような「英数字_英数字」を試す
    ["1", "a", "a_1"] as $cands
    | to_entries[]
    | select((.key | startswith("_")) | not)
    | .value as $r
    | ($r.path | test("^/(_|\\.well-known)")) as $internal
    | select($internal | not)
    # pathRegex は PCRE の {^...$}sDu 形式。jq (Oniguruma) は (?P<name>) を解釈しないため (?<name>) に直す
    | ($r.pathRegex | capture("^\\{(?<re>.*)\\}[a-zA-Z]*$").re | gsub("\\(\\?P<"; "(?<")) as $re
    | ($r.method | if . == "ANY" then null else split("|") end) as $methods
    | (if ($r.defaults | type) == "object" then ($r.defaults._controller // "" | if type == "string" then . else tostring end) else "" end) as $ctrl
    | (if ($ctrl | test("^Plugin\\\\")) then "plugin"
       elif ($ctrl | test("^Eccube\\\\Controller\\\\Admin\\\\")) then "admin"
       else "front" end) as $area
    | (first($excludes[] | select(. as $e | $r.path | test($e.re))) // null) as $ex
    | (any($reqs[]; . as $q
        | ($methods == null or ($methods | index($q.method)))
        and any($cands[]; . as $c | $q.url | gsub("\\{\\{[^}]+\\}\\}"; $c) | test($re)))) as $hit
    | (if $ex then "対象外" elif $hit then "通る" else "通らない" end) as $status
    | [$status, $area, $r.method, $r.path, ($ex.reason // "")] | @tsv
' "${ROUTES}" | sort -t$'\t' -k1,1 -k2,2 -k4,4)

if [[ -n ${ALL} ]]; then
    printf '%s\n' "${rows}"
else
    printf '%s\n' "${rows}" | grep -v $'^通る\t' || true
fi

printf '%s\n' "${rows}" | awk -F'\t' '
    { n[$2, $1]++; a[$2] = 1 }
    END {
        for (k in a) printf "%-6s 通る %3d / 通らない %3d / 対象外 %3d\n", k, n[k, "通る"], n[k, "通らない"], n[k, "対象外"]
    }' | sort >&2
