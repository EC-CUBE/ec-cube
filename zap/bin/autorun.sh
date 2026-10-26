#!/bin/bash
#
# 1 つのターゲットについて、自動化プランを生成して ZAP で実行し、レポートを回収して結果を判定する。
#
# usage: autorun.sh -t <target> -o <出力先> [-s <scenario>] [-b <before_script>] [-c <context>] [-n <thread_per_host>] [-p <policy>]
#   -s  シナリオ (zap/scripts/<scenario>.zst)。省略時はターゲットと同じ名前
#   -b  事前に実行するスクリプト。カンマで区切って複数を指定できる
#   -e  Web サーバーの APP_ENV (インストーラは install)。スキャンの間だけ Apache の SetEnv で切り替え、終わったら戻す。
#       コンテナを作り直すと導入したプラグインやパッチが消えるため、環境変数ではなく Apache の設定で切り替える
#   -p  Sequence (既定) または Smoke
#
# 出力先には plan.yml / zap.log / ZAP-Report-<target>.html / alerts.json / access.log / attacks.tsv /
# params.tsv / param_counts.tsv / forms.tsv を置く。
# attacks.tsv はシナリオのリクエストごとに EC-CUBE が受けたリクエストの件数 (zap/bin/attack_counts.sh)。
# param_counts.tsv は攻撃が書き換えた項目ごとの件数 (zap/bin/param_counts.sh)。
# forms.tsv は画面のフォーム (zap/scripts/record_forms.js)。送っていない項目は zap/bin/form_coverage.sh で照合する。
# 終了コードは check_results.sh の判定に従う。
# docker compose は呼び出し元の環境変数 (COMPOSE_FILE / COMPOSE_PROJECT_NAME) で実行する。
# ZAP_INJECTABLE で攻撃する場所 (scanner.injectable) を変えられる。省略時は ZAP の既定 (クエリ文字列と本文の 3)。
# クエリ文字列 1・本文 2・Cookie 4・ヘッダー 8・URL のパス 16 の和 (例: Cookie とヘッダーも攻撃する 15)
# ZAP は -config の値を設定ファイル (~/.ZAP/config.xml) に保存し、次の起動でも使う。同じ ZAP で続けて実行すると
# 前のターゲットの値が残るため、並列数 (-n、省略時は 8) と攻撃する場所は省略時も毎回渡す。
# 並列数の 8 は、GitHub Actions のランナー (4 コア) での ZAP の既定 (コア数の 2 倍) に合わせたもの

set -uo pipefail

TARGET='' SCENARIO='' APP_ENV_OVERRIDE='' OUT='' BEFORE='' CONTEXT='' THREADS='' POLICY=Sequence
while getopts "t:s:e:o:b:c:n:p:" OPT; do
    case ${OPT} in
        e) APP_ENV_OVERRIDE=${OPTARG} ;;
        t) TARGET=${OPTARG} ;;
        s) SCENARIO=${OPTARG} ;;
        o) OUT=${OPTARG} ;;
        b) BEFORE=${OPTARG} ;;
        c) CONTEXT=${OPTARG} ;;
        n) THREADS=${OPTARG} ;;
        p) POLICY=${OPTARG} ;;
        *) exit 2 ;;
    esac
done
if [[ -z ${TARGET} || -z ${OUT} ]]; then
    echo "usage: $0 -t <target> -o <出力先> [-s <scenario>] [-b <before_script>] [-c <context>] [-n <thread_per_host>] [-p <policy>]" >&2
    exit 2
fi
OUT=$(realpath -m "${OUT}")
SCENARIO=${SCENARIO:-${TARGET}}
cd "$(dirname "$0")/../.." || exit 1
mkdir -p "${OUT}"

# Sequence は、同梱の Sequence ポリシーから一部のルールを OFF にした EC-CUBE ポリシー (prepare_zap.sh が作る) で実行する
POLICY_ARG=${POLICY}
[[ ${POLICY} == Sequence ]] && POLICY_ARG=EC-CUBE
zap/generate_automation_config.sh -t "${TARGET}" -s "${SCENARIO}" -o "${OUT}/plan.generated.yml" \
    ${BEFORE:+-b "${BEFORE}"} ${CONTEXT:+-c "${CONTEXT}"} ${POLICY_ARG:+-p "${POLICY_ARG}"} || exit 1

# zst はコンテナ内へコピーしてから読ませる (Docker Desktop のバインドマウント越しに読むと遅いため)。
# 生成したプランは作業ツリーに残さない
sed 's#/zap/wrk/scripts/#/tmp/scripts/#' "${OUT}/plan.generated.yml" > "${OUT}/plan.yml"
rm -f "${OUT}/plan.generated.yml"
docker compose exec -T -u 0:0 zap sh -c \
    'rm -rf /tmp/scripts /tmp/report /tmp/alerts.json /tmp/params.tsv /tmp/forms.tsv && cp -r /zap/wrk/scripts /tmp/scripts && chown -R zap /tmp/scripts' || exit 1
docker compose exec -T zap sh -c 'cat > /tmp/plan.yml' < "${OUT}/plan.yml" || exit 1

if [[ -n ${APP_ENV_OVERRIDE} ]]; then
    set_app_env() {
        local conf=/etc/apache2/conf-enabled/zz-zap-app-env.conf
        if [[ -n $1 ]]; then
            docker compose exec -T -u 0:0 ec-cube sh -c "echo 'SetEnv APP_ENV $1' > ${conf} && apache2ctl -k graceful"
        else
            docker compose exec -T -u 0:0 ec-cube sh -c "rm -f ${conf} && apache2ctl -k graceful"
        fi
        sleep 3
    }
    trap 'set_app_env ""' EXIT
    set_app_env "${APP_ENV_OVERRIDE}" || exit 1
fi

SINCE=$(date -u +%Y-%m-%dT%H:%M:%SZ)
# connection.httpStateEnabled: before_script (standalone) でログインしたセッションをシナリオの再生へ引き継ぐ
docker compose exec -T zap /zap/zap.sh -cmd \
    -config anticsrf.tokens.token.name=_csrf_token \
    -config anticsrf.tokens.token.enabled=true \
    -config connection.httpStateEnabled=true \
    -config scanner.threadPerHost="${THREADS:-8}" \
    -config scanner.injectable="${ZAP_INJECTABLE:-3}" \
    -configfile /zap/wrk/options.properties \
    -autorun /tmp/plan.yml < /dev/null 2>&1 | tee "${OUT}/zap.log"

docker compose cp zap:/tmp/report/. "${OUT}/" > /dev/null 2>&1
docker compose cp zap:/tmp/alerts.json "${OUT}/alerts.json" > /dev/null 2>&1
docker compose cp zap:/tmp/params.tsv "${OUT}/params.tsv" > /dev/null 2>&1 \
    && zap/bin/param_counts.sh "${OUT}/params.tsv" > "${OUT}/param_counts.tsv"
docker compose cp zap:/tmp/forms.tsv "${OUT}/forms.tsv" > /dev/null 2>&1
docker compose logs --since "${SINCE}" --no-log-prefix ec-cube > "${OUT}/access.log" 2> /dev/null
if zap/bin/attack_counts.sh "zap/scripts/${SCENARIO}.zst" "${OUT}/access.log" > "${OUT}/attacks.tsv" \
    && [[ ${POLICY} == Sequence ]]; then
    # パラメータがあるのに正常な応答 (2xx/3xx) が再生の分程度しか無いリクエストは、攻撃が画面の処理まで届いていない。
    # 3xx の大半がログイン画面・購入エラー画面・2 段階認証の画面へ戻されているものも同じ (ログインのリクエスト自身は除く)
    awk -F'\t' -v target="${TARGET}" -v gha="${GITHUB_ACTIONS:-}" '
        $5 == "あり" {
            split($6, r, "/")
            split($7, top, ", "); dest = top[1]; n = dest; sub(/ [0-9]+$/, "", dest); sub(/^.* /, "", n)
            dead = $3 !~ /\/login$/ && dest ~ /(\/login|\/shopping\/error|\/two_factor_auth\/)/ && $1 >= 20 && n + 0 >= $1 * 0.9
            if (r[1] + r[2] <= 3 || dead) {
                msg = sprintf("%s: 攻撃が届いていない可能性 %s %s (ステップ %s、%d 件、応答 2xx/3xx/4xx/5xx = %s%s)",
                    target, $2, $3, $4, $1, $6, dead ? "、3xx の行き先 " $7 : "")
                print (gha != "" ? "::notice::" : "[notice] ") msg
            }
        }' "${OUT}/attacks.tsv"
fi

# exec にすると -e で切り替えた APP_ENV を戻す trap が動かないため、終了コードを引き継いで終わる
zap/bin/check_results.sh "${OUT}/zap.log" "${POLICY}" "${TARGET}"
exit $?
