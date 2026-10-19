#!/bin/bash

cd $(dirname $0)

# -s はシナリオ (zap/scripts/<scenario>.zst)。省略時はターゲットと同じ名前。
# 同じシナリオを条件を変えて別のターゲットとして実行するときに使う (zap/targets.json の scenario)
# -b は事前に実行するスクリプト。カンマで区切って複数を指定でき、書いた順に実行する
while getopts "t:s:c:b:n:p:o:" OPT
do
    case $OPT in
        t) ZA_TARGET=${OPTARG} ;;
        s) ZA_SCENARIO=${OPTARG} ;;
        c) ZA_CONTEXT=${OPTARG} ;;
        b) ZA_BEFORE_SCRIPT=${OPTARG} ;;
        n) ZA_THREAD_PER_HOST=${OPTARG} ;;
        p) ZA_POLICY=${OPTARG} ;;
        o) ZA_OUTPUT=${OPTARG} ;;
    esac
done

ZA_THREAD_PER_HOST=${ZA_THREAD_PER_HOST:-10}
ZA_SCENARIO=${ZA_SCENARIO:-${ZA_TARGET}}

if [[ -z "${ZA_CONTEXT}" ]]; then
    # 名前に admin を含まないが、管理画面だけを操作するターゲットも admin コンテキストで実行する
    if [[ ${ZA_SCENARIO} =~ 'admin' || ${ZA_SCENARIO} == plugin_related_product || ${ZA_SCENARIO} == plugin_sales_report ]]; then
        ZA_CONTEXT=admin
    elif [[ ${ZA_SCENARIO} == mypage_* ]]; then
        # ユーザーを持たないコンテキストでは sequence-activeScan がセッションを引き継がないため,
        # 会員でログインした状態を ZAP の認証で維持する
        ZA_CONTEXT=front_login
    else
        ZA_CONTEXT=default
    fi
fi

# ユーザーと forced user はコンテキストから決める (-c で明示した場合も同じ)
case ${ZA_CONTEXT} in
    admin)
        ZA_USER=admin
        ZA_FORCE_ADMIN_CONFIG="
  - type: script
    parameters:
      action: add
      type: standalone
      name: forceuser
      file: /zap/wrk/scripts/forceuser.groovy

  - type: script
    parameters:
      action: run
      type: standalone
      name: forceuser
"
        ;;
    front_login)
        ZA_USER=customer
        ;;
esac

ZA_BEFORE_SCRIPT=$(echo ${ZA_BEFORE_SCRIPT} | sed 's/ //g')
# 管理画面のシナリオはログイン済みのセッションで再生する
if [[ -z ${ZA_BEFORE_SCRIPT} && ${ZA_CONTEXT} == admin ]]; then
    ZA_BEFORE_SCRIPT=admin_login.zst
fi

echo "
CONTEXT: ${ZA_CONTEXT}
USER: ${ZA_USER}
THREAD_PER_HOST: ${ZA_THREAD_PER_HOST}
TARGET: ${ZA_TARGET}
SCENARIO: ${ZA_SCENARIO}
BEFORE_SCRIPT: ${ZA_BEFORE_SCRIPT}
POLICY: ${ZA_POLICY:-Sequence}
"

# 省略時は sequence-activeScan の既定 (同梱の Sequence ポリシー) を使う
if [[ -n ${ZA_POLICY} ]]; then
    ZA_POLICY_CONFIG="      policy: ${ZA_POLICY}"
fi

# Smoke では、再生で入力エラーの画面が返ったリクエストを Form error: としてログに出す。
# 再生と攻撃はどちらも能動スキャンとして送られ区別できないため、攻撃を送らない Smoke に限る
if [[ ${ZA_POLICY} == Smoke ]]; then
    ZA_FORM_ERRORS_CONFIG="
  - type: script
    parameters:
      action: add
      type: httpsender
      name: report_form_errors
      file: /zap/wrk/scripts/report_form_errors.js"
fi

ZA_BEFORE_SCRIPT_CONFIG=
i=0
for script in ${ZA_BEFORE_SCRIPT//,/ }; do
    i=$((i + 1))
    ZA_BEFORE_SCRIPT_CONFIG="${ZA_BEFORE_SCRIPT_CONFIG}
  - type: script
    parameters:
      action: add
      type: standalone
      name: before_script_${i}
      file: /zap/wrk/scripts/${script}
  - type: script
    parameters:
      action: run
      type: standalone
      name: before_script_${i}"
done

TEMPLATE=$(sed 's/"/\\"/g' automation/template.yml)
# -o で出力先を指定できる (省略時は automation/<target>.yml)。同じターゲットを並列に生成しても衝突しないように使う
eval "echo \"${TEMPLATE}\"" > "${ZA_OUTPUT:-automation/${ZA_TARGET}.yml}"