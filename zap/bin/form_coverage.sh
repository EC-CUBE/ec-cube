#!/bin/bash
#
# 画面のフォームの項目と、シナリオが送った項目を突き合わせる。
# ルートの照合 (coverage.sh) はメソッドと URL の一致だけを見るため、送っていない項目や通っていない分岐は分からない。
#
# usage: form_coverage.sh <結果のディレクトリ>...
#   結果のディレクトリ: autorun.sh の出力先 (forms.tsv と params.tsv がある)。複数のターゲットをまとめて照合する
#   例: zap/bin/form_coverage.sh zap/local/out/Sequence/*/
#
# フォームは zap/scripts/record_forms.js、送った項目は zap/scripts/record_params.js が記録したもの。
# どれかのターゲットで送っていれば送ったものとする。パスは param_counts.sh と同じく {id}・{token} にまとめる。
#
# 出力はタブ区切り: <種類> <フォームのメソッド> <送信先> <項目> <フォームのある画面 (1 つ)>
#   種類: フォーム (送信先へ一度も送っていない) / 項目 (送っていない項目) / 値 (mode・ボタンの値を送っていない)
#   クエリ文字列の付いたリンクは、フォームのメソッドを LINK として GET の送信と照合し、種類に「リンク」を付ける
# CSRF トークン (名前に _csrf か [_token] を含む項目) は比べない。
# 行ごとに番号の付いた項目 (一覧の行ごとの編集フォームの class_category_100[name]、明細の [0]) は、番号を {n} にまとめて比べる
# (price01 のように名前の一部の数字は別の項目のため、まとめない)。

set -euo pipefail

if [[ $# == 0 ]]; then
    echo "usage: $0 <結果のディレクトリ>..." >&2
    exit 2
fi

{
    for d in "$@"; do [[ -f ${d}/params.tsv ]] && sed 's/^/P\t/' "${d}/params.tsv"; done
    for d in "$@"; do [[ -f ${d}/forms.tsv ]] && sed 's/^/F\t/' "${d}/forms.tsv"; done
} | LC_ALL=C awk -F'\t' -v OFS='\t' '
    function norm(path,    n, i, seg, out) {
        n = split(path, seg, "/"); out = ""
        for (i = 2; i <= n; i++) {
            if (seg[i] ~ /^[0-9]+$/) seg[i] = "{id}"
            else if (length(seg[i]) >= 20 && seg[i] ~ /^[A-Za-z0-9_-]+$/) seg[i] = "{token}"
            out = out "/" seg[i]
        }
        return out == "" ? "/" : out
    }
    function rows(name) {
        gsub(/_[0-9]+%5[Bb]/, "_{n}%5B", name)
        gsub(/%5[Bb][0-9]+%5[Dd]/, "%5B{n}%5D", name)
        return name
    }
    function skip(name) {
        return tolower(name) ~ /(_csrf|%5b_token%5d)/
    }
    function decode(name,    hx, ch) {
        while (match(name, /%[0-9A-Fa-f][0-9A-Fa-f]/)) {
            hx = tolower(substr(name, RSTART + 1, 2))
            ch = sprintf("%c", (index("0123456789abcdef", substr(hx, 1, 1)) - 1) * 16 + index("0123456789abcdef", substr(hx, 2, 1)) - 1)
            name = substr(name, 1, RSTART - 1) ch substr(name, RSTART + 3)
        }
        return name
    }
    # 送った項目: <メソッド> <パス> <状態コード> <リダイレクト先> <名前=ハッシュ ...>
    $1 == "P" {
        k = $2 " " norm($3)
        sent[k] = 1
        n = split($6, ps, " ")
        for (i = 1; i <= n; i++) {
            j = index(ps[i], "=")
            names[k, rows(substr(ps[i], 1, j - 1))] = 1
            values[k, rows(substr(ps[i], 1, j - 1)) substr(ps[i], j)] = 1
        }
        next
    }
    # フォーム: <画面のメソッド> <画面のパス> <フォームのメソッド> <送信先のパス> <項目 ...>
    $1 == "F" {
        link = $4 == "LINK"
        k = (link ? "GET" : $4) " " $5
        kind = link ? "リンク" : "フォーム"
        if (!(k in sent)) {
            if (!((k, "form") in done)) { done[k, "form"] = 1; print kind, $4, $5, "", $2 " " $3 }
            next
        }
        n = split($6, fs, " ")
        for (i = 1; i <= n; i++) {
            j = index(fs[i], "="); name = rows(j ? substr(fs[i], 1, j - 1) : fs[i])
            t = index(fs[i], "~"); item = name (j ? substr(fs[i], j, (t ? t : length(fs[i]) + 1) - j) : ""); value = t ? substr(fs[i], t + 1) : ""
            if (skip(name)) continue
            if (!((k, name) in names)) {
                if (!((k, name) in done)) { done[k, name] = 1; print (link ? "リンクの項目" : "項目"), $4, $5, decode(name), $2 " " $3 }
            } else if (j && !((k, item) in values)) {
                if (!((k, item) in done)) { done[k, item] = 1; print (link ? "リンクの値" : "値"), $4, $5, decode(name) "=" decode(value), $2 " " $3 }
            }
        }
    }
' | sort -t$'\t' -k1,1 -k3,3 -k4,4
