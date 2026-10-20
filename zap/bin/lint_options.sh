#!/bin/bash
#
# options.properties のアラートのフィルターの番号が、ファイルの中で 0 から順に並んでいるかを確かめる。
#
# usage: lint_options.sh [<options.properties>]   (省略時は zap/options.properties)
#
# ZAP は -configfile の値を 1 行ずつ設定に足すため、まだ無い番号は末尾の次の番号として作られる。
# 番号が飛んだり前後したりすると、フィルターの項目が別のフィルターに混ざる。ruleid を失ったフィルターがあると、
# それ以降のフィルターも読み込まれず、外したはずの誤検知が High として残る。
# ~/.ZAP に以前の設定が残っている環境では起きないため、ローカルでは気づけない。
#
# 番号が順に並んでいなければ、最初のずれを出力して終了コード 1 を返す。

set -euo pipefail

cd "$(dirname "$0")/../.."
file=${1:-zap/options.properties}

grep -o 'globalalertfilter\.filters\.filter\\([0-9]*\\)' "${file}" | grep -o '[0-9]*' | uniq \
    | awk -v file="${file}" '$1 != NR - 1 { printf "%s: フィルターの番号 %d の位置に %d がある (0 から順に並べる)\n", file, NR - 1, $1; bad = 1; exit }
                             END { exit bad }' >&2
