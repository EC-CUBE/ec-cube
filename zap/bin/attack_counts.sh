#!/bin/bash
#
# シナリオのリクエストごとに、EC-CUBE が受けたリクエストの件数を数える。
# 能動スキャンの攻撃が届いていない (再生の分しか来ていない) リクエストを見つけるために使う。
#
# usage: attack_counts.sh <scenario.zst> <access.log>
#   access.log: スキャン中の EC-CUBE (Apache) のアクセスログ
#
# シナリオの URL の {{変数}} は任意の 1 階層に一致させ、クエリ文字列は無視する。
# 同じメソッドと URL のリクエストが複数のステップにある場合は、まとめて数える。
# before_script や再生のリクエストも含むため、件数が数件なら攻撃はほぼ届いていない。
# 件数が多くても 4xx ばかりなら、攻撃は画面の処理まで届かずに弾かれている (例: 使用済みの URL で 404)。
#
# 出力はタブ区切り: <件数> <メソッド> <URL> <ステップの番号> <パラメータの有無> <応答の内訳 2xx/3xx/4xx/5xx>

set -euo pipefail

ZST=${1:?usage: attack_counts.sh <scenario.zst> <access.log>}
LOG=${2:?usage: attack_counts.sh <scenario.zst> <access.log>}

jq -r '
    [.statements[]? | select(.elementType == "ZestRequest")
     | (.url // .urlToken) as $u
     | select($u | test("^https?://ec-cube(/|$)"))
     | {
         method: .method,
         path: ($u | sub("^https?://[^/]+"; "") | sub("\\?.*$"; "") | if . == "" then "/" else . end),
         params: (((.data // "") != "") or ($u | test("\\?"))),
         index: .index
       }]
    | group_by([.method, .path])[]
    | {
        method: .[0].method,
        path: .[0].path,
        params: any(.[]; .params),
        steps: (map(.index | tostring) | join(","))
      }
    # {{変数}} を任意の 1 階層に置き換えた ERE を作る
    | .re = ("^" + (.path
        | gsub("\\{\\{[^}]+\\}\\}"; "\u0001")
        | gsub("(?<c>[.+*?()\\[\\]{}|^$\\\\])"; "\\\(.c)")
        | gsub("\u0001"; "[^/]+")) + "$")
    | [.method, .path, .re, .steps, (if .params then "あり" else "なし" end)] | @tsv
' "${ZST}" | awk -F'\t' -v OFS='\t' '
    NR == FNR { m[NR] = $1; p[NR] = $2; re[NR] = $3; st[NR] = $4; pa[NR] = $5; n = NR; next }
    match($0, /"[A-Z]+ [^ ]+ HTTP\/[0-9.]+" [0-9]+/) {
        split(substr($0, RSTART + 1, RLENGTH - 1), f, /[ "]+/)
        path = f[2]
        sub(/\?.*/, "", path)
        k = substr(f[4], 1, 1)
        for (i = 1; i <= n; i++) if (f[1] == m[i] && path ~ re[i]) { c[i]++; s[i, k]++ }
    }
    END {
        for (i = 1; i <= n; i++)
            print c[i] + 0, m[i], p[i], st[i], pa[i], (s[i, 2] + 0) "/" (s[i, 3] + 0) "/" (s[i, 4] + 0) "/" (s[i, 5] + 0)
    }
' - "${LOG}" | sort -t$'\t' -k1,1n
