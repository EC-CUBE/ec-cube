#!/bin/bash
#
# Zest スクリプトの multipart/form-data のリクエストが、ZAP の能動スキャンで攻撃できる形かを確かめる。
#
# usage: lint_zst.sh [<scenario.zst>...]   (省略時は zap/scripts/*.zst)
#
# - boundary に大文字を含まないこと: ZAP は Content-Type を小文字にしてから boundary を取り出すため、
#   本文の boundary と一致せず、攻撃が 1 件も送られない
# - boundary の行の前の改行が CRLF であること: ZAP は CRLF で本文を分割する
#
# 問題のあるリクエストを出力し、1 件でもあれば終了コード 1 を返す。

set -euo pipefail

cd "$(dirname "$0")/../.."
if [[ $# == 0 ]]; then set -- zap/scripts/*.zst; fi

status=0
for zst in "$@"; do
    errors=$(jq -r '
        .. | objects | select(.elementType == "ZestRequest") as $r
        | ($r.headers // "" | capture("(?i)content-type:\\s*multipart/form-data;.*boundary=(?<b>[^\\r\\n;]+)")?) as $m
        | select($m != null)
        | ($m.b | gsub("(?<c>[.*+?^${}()|\\[\\]\\\\])"; "\\\(.c)")) as $quoted
        | ((if $m.b | test("[A-Z]") then "boundary に大文字を含む (\($m.b))" else empty end),
           (if ($r.data // "") | test("(?<!\\r)\\n--" + $quoted) then "boundary の行の前の改行が LF だけ" else empty end))
        | "\($r.method) \($r.url // $r.urlToken): \(.)"
    ' "${zst}" 2> /dev/null) || { echo "${zst}: JSON として読めません" >&2; status=1; continue; }
    if [[ -n ${errors} ]]; then
        sed "s#^#${zst}: #" <<< "${errors}" >&2
        status=1
    fi
done
exit ${status}
