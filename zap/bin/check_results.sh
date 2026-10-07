#!/bin/bash
#
# ZAP の実行ログから結果を判定する。
#
# usage: check_results.sh <zap.log> <policy> <target>
#
# 自動化プランは Zest のアサーション失敗 (passed = false) や変数の抽出失敗 (Assign: failed) があっても
# 成功扱いになるため、ログから判定する。
#   - 自動化プランの失敗 (High のアラート、ジョブのエラー): 失敗
#   - 再生の失敗: Smoke では失敗。Sequence では能動スキャンの攻撃が前のステップの状態を変えて
#     再生が失敗することがあるため、警告に留める
#   - 再生で入力エラーの画面が返ったリクエスト (Form error:、Smoke のみ出力): 再生の失敗として扱う。
#     状態コードは 200 のため ZAP は成功とみなすが、後続の画面に攻撃が届かない
# ログは options.properties の view.locale=ja_JP により日本語で出力される。
#
# 最後に 1 行、タブ区切りで RESULT <target> <policy> <成功|失敗> <再生の失敗>/<再生の総数> high=<件数> を出力する。
# GitHub Actions ではアノテーションとジョブのサマリーも出力する。

set -u

LOG=$1 POLICY=$2 TARGET=$3

annotate() {
    if [[ -n ${GITHUB_ACTIONS:-} ]]; then
        echo "::$1::$2"
    else
        echo "[$1] $2" >&2
    fi
}

status=0
plan=成功
if ! grep -q '自動化プランが成功' "${LOG}"; then
    plan=失敗
    status=1
    annotate error "${TARGET}: 自動化プランが失敗しました (High のアラート、またはジョブのエラー)"
    sed -n '/自動化プランの失敗/,$p' "${LOG}" >&2
fi

high=$(grep -o 'High risk alert count \[[0-9]*' "${LOG}" | grep -o '[0-9]*$' | head -1)
total=$(grep -c '^Response:' "${LOG}")
failures=$(grep -E 'passed = false|Assign: failed|^Form error:' "${LOG}")
count=0
if [[ -n ${failures} ]]; then
    count=$(wc -l <<< "${failures}")
    level=warning
    if [[ ${POLICY} == Smoke ]]; then
        level=error
        status=1
    fi
    while IFS= read -r line; do annotate "${level}" "${TARGET}: ${line}"; done <<< "${failures}"
fi

if [[ -n ${GITHUB_STEP_SUMMARY:-} ]]; then
    {
        echo "### ${TARGET} (${POLICY})"
        echo "- 自動化プラン: ${plan}"
        echo "- High のアラート: ${high:-0}"
        echo "- 再生の失敗: ${count} / ${total}"
    } >> "${GITHUB_STEP_SUMMARY}"
fi

printf 'RESULT\t%s\t%s\t%s\t%s/%s\thigh=%s\n' "${TARGET}" "${POLICY}" "${plan}" "${count}" "${total}" "${high:-0}"
exit ${status}
