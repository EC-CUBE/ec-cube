#!/bin/bash
#
# 能動スキャンの攻撃を、書き換えられた項目ごとに数える。
# attacks.tsv (attack_counts.sh) はリクエスト単位のため、2xx が多くても特定の項目への攻撃がすべて弾かれていることがある。
#
# usage: param_counts.sh <params.tsv>
#   params.tsv: zap/scripts/record_params.js が書き出したもの
#
# リクエストはメソッドとパスでまとめる (数字だけの階層は {id}、20 文字以上の英数字の階層は {token} にまとめる)。
# まとめたリクエストの中で項目ごとに最も多い値を元の値とみなし、値が異なる項目を攻撃された項目とする。
# 同じパスに複数のステップがある場合 (確認と完了の mode が違うなど) は、ステップ 1 の元の値と 2 項目以上違う
# リクエストから、ステップ 2 の元の値を求める (3 まで)。各リクエストは、違う項目が最も少ないステップに数える。
# 異なる項目が無いリクエストは再生 (または攻撃の対象外の項目への攻撃) として「(元の値)」に数える。
# 2 つ以上の項目が異なるリクエストは、それぞれの項目に数え、件数を「複数」の列に出す。
# CSRF トークン (名前に _csrf か [_token] を含む項目) は再生のたびに値が変わり、攻撃もされないため比べない。
# 2xx のうち、応答に入力エラーがあったもの (入力の検証で止まり、処理に届いていない) を「入力エラー」の列に出す。
#
# 出力はタブ区切り: <件数> <メソッド> <パス> <ステップ> <項目> <応答の内訳 2xx/3xx/4xx/5xx> <入力エラー> <複数>
#                   <3xx の行き先 (多い順に 3 件)>

set -euo pipefail

TSV=${1:?usage: param_counts.sh <params.tsv>}

LC_ALL=C awk -F'\t' -v OFS='\t' -v STEPS=3 '
    function key(method, path) {
        gsub(/\/[0-9]+/, "/{id}", path)
        gsub(/\/[A-Za-z0-9_-]{20,}/, "/{token}", path)
        return method " " path
    }
    function skip(name) {
        return tolower(name) ~ /(_csrf|%5b_token%5d)/
    }
    # 行の項目を nm[] / hv[] に読む (CSRF トークンを除く)。項目の数を返す
    function parse(line,    n, i, j, m) {
        n = split(line, ps, " "); m = 0
        for (i = 1; i <= n; i++) {
            j = index(ps[i], "=")
            if (skip(substr(ps[i], 1, j - 1))) continue
            nm[++m] = substr(ps[i], 1, j - 1); hv[m] = substr(ps[i], j + 1)
        }
        return m
    }
    # ステップ t の元の値と違う項目の数 (ステップに無い項目、ステップにあってリクエストに無い項目も数える)
    function dist(k, t, m,    i, dd, found) {
        dd = 0; found = 0
        for (i = 1; i <= m; i++) {
            if ((k, t, nm[i]) in mode) { found++; if (hv[i] != mode[k, t, nm[i]]) dd++ } else dd++
        }
        return dd + nnames[k, t] - found
    }
    # ステップ t の元の値を求めるため、値の出現数を数える
    function tally(k, t, m,    i) {
        has[k, t] = 1
        for (i = 1; i <= m; i++) {
            cnt[k, t, nm[i], hv[i]]++
            if (cnt[k, t, nm[i], hv[i]] > best[k, t, nm[i]]) {
                if (!((k, t, nm[i]) in mode)) nnames[k, t]++
                best[k, t, nm[i]] = cnt[k, t, nm[i], hv[i]]; mode[k, t, nm[i]] = hv[i]
            }
        }
    }
    FNR == 1 { pass++ }
    # 1〜STEPS 回目: 前のどのステップとも 2 項目以上違うリクエストから、次のステップの元の値を求める
    pass <= STEPS {
        k = key($1, $2); m = parse($5)
        for (t = 1; t < pass; t++) if (has[k, t] && dist(k, t, m) < 2) next
        tally(k, pass, m)
        next
    }
    # 最後: 違う項目が最も少ないステップに割り当て、そのステップの元の値と異なる項目に数える
    {
        k = key($1, $2); m = parse($5)
        bt = 1; bd = -1
        for (t = 1; t <= STEPS; t++) if (has[k, t]) { dd = dist(k, t, m); if (bd < 0 || dd < bd) { bd = dd; bt = t } }
        nd = 0
        for (i = 1; i <= m; i++) if (!((k, bt, nm[i]) in mode) || hv[i] != mode[k, bt, nm[i]]) diff[++nd] = nm[i]
        if (nd == 0) diff[++nd] = "(元の値)"
        st = substr($3, 1, 1)
        dest = ""
        if (st == 3 && $4 != "") {
            dest = $4
            sub(/^https?:\/\/[^\/]+/, "", dest)
            sub(/\?.*/, "", dest)
            gsub(/\/[0-9]+/, "/{id}", dest)
        }
        for (i = 1; i <= nd; i++) {
            r = k SUBSEP bt SUBSEP diff[i]
            if (!(r in c)) order[++nr] = r
            c[r]++; s[r, st]++
            if (st == 2 && $6 == "E") fe[r]++
            if (nd > 1) multi[r]++
            if (dest != "") { if (!((r, dest) in d)) { ndest[r]++; dn[r, ndest[r]] = dest } d[r, dest]++ }
        }
    }
    END {
        for (x = 1; x <= nr; x++) {
            r = order[x]
            split(r, kp, SUBSEP); split(kp[1], mp, " ")
            top = ""
            for (t = 1; t <= 3; t++) {
                b = 0
                for (j = 1; j <= ndest[r]; j++) if (!((r, j) in used) && d[r, dn[r, j]] > b) { b = d[r, dn[r, j]]; bj = j }
                if (b == 0) break
                used[r, bj] = 1
                top = top (top == "" ? "" : ", ") dn[r, bj] " " b
            }
            name = kp[3]
            # 名前の URL エンコードを戻す (表示用。mawk には strtonum が無いため 16 進数を自前で読む)
            while (match(name, /%[0-9A-Fa-f][0-9A-Fa-f]/)) {
                hx = tolower(substr(name, RSTART + 1, 2))
                ch = sprintf("%c", (index("0123456789abcdef", substr(hx, 1, 1)) - 1) * 16 + index("0123456789abcdef", substr(hx, 2, 1)) - 1)
                name = substr(name, 1, RSTART - 1) ch substr(name, RSTART + 3)
            }
            print c[r], mp[1], mp[2], kp[2], name, (s[r, 2] + 0) "/" (s[r, 3] + 0) "/" (s[r, 4] + 0) "/" (s[r, 5] + 0), fe[r] + 0, multi[r] + 0, top
        }
    }
' "${TSV}" "${TSV}" "${TSV}" "${TSV}" | sort -t$'\t' -k2,4 -k1,1nr
