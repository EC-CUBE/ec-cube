#!/bin/bash
#
# ZAP のアドオンを更新・導入し、EC-CUBE ポリシーと Smoke ポリシーを作る。
# EC-CUBE は同梱の Sequence ポリシーから、検出の見込みが無く時間だけかかるルールを OFF にしたもの (zap/README.md 参照)。
# 同梱のポリシーは読み取り専用のため、書き換えずに別のファイルとして作る。
# Smoke は同梱の Sequence ポリシーの全ルールを OFF にしたもので、能動スキャンをせず zst の再生だけを確認する。
#
# docker compose は呼び出し元の環境変数 (COMPOSE_FILE / COMPOSE_PROJECT_NAME) で実行する。

set -euo pipefail
cd "$(dirname "$0")/../.." || exit 1

# 一度に起動する ZAP がプロキシのポートを確保できずに終了することがある (Address already in use)。
# 原因は特定できていないが、再実行すると成功するため、3 回まで試す
zap() {
    local i
    for i in 1 2 3; do
        docker compose exec -T zap /zap/zap.sh -cmd "$@" && return 0
        echo "zap.sh -cmd $* が失敗しました (${i} 回目)" >&2
        sleep 5
    done
    return 1
}

# アドオンの一覧の取得 (Check for updates) に失敗すると、導入されないまま zap.sh は成功で終わる。
# 導入できたかをファイルで確かめ、無ければ導入し直す。
# <確かめるファイル> は省略すると、アドオンのファイル (ZAP の同梱か、ダウンロードしたもの)
install_addon() {
    local id=$1 check=${2:-} i
    for i in 1 2 3; do
        zap -addoninstall "${id}"
        if [[ -n ${check} ]]; then
            docker compose exec -T zap test -f "${check}" && return 0
        else
            docker compose exec -T zap sh -c "ls /zap/plugin/${id}-*.zap /home/zap/.ZAP/plugin/${id}-*.zap 2> /dev/null; true" | grep -q . && return 0
        fi
        echo "アドオン ${id} を導入できませんでした (${i} 回目)" >&2
        sleep 10
    done
    return 1
}

zap -addonupdate
# sequence は同梱の版を外して最新を入れる。同梱のファイルが残るため、アドオンが置くポリシーで確かめる
zap -addonuninstall sequence
install_addon sequence /home/zap/.ZAP/policies/Sequence.policy
install_addon automation
install_addon groovy

docker compose exec -T zap sh -c "sed \
    -e 's#<policy>Sequence</policy>#<policy>Smoke</policy>#' \
    -e 's#<level>[A-Z]*</level>#<level>OFF</level>#g' \
    -e 's#<enabled>true</enabled>#<enabled>false</enabled>#g' \
    -e '/<statsId>/d' -e '/<readonly>/d' -e '/<locked>/d' \
    /home/zap/.ZAP/policies/Sequence.policy > /home/zap/.ZAP/policies/Smoke.policy"

# EC-CUBE ポリシー。OFF にするルール: 40026 Cross Site Scripting (DOM Based)。足すルールは EXTRA_RULES
# <locked> は残す。locked のポリシーは列挙していないルールを無効にするが、外すと既定で有効になり、同梱の Sequence に無いルールまで動く
# 足すルール: 同梱の Sequence に無いルールのうち、EC-CUBE に効果のあるもの (zap/README.md 参照)。
# ZAP_EXTRA_RULES (カンマ区切りのルール ID) で上書きできる
EXTRA_RULES=${ZAP_EXTRA_RULES-0,10058,20017,20018,30002,40008,40009,40032,40034,40035,40044,90034}
extra=
for id in ${EXTRA_RULES//,/ }; do
    extra="${extra}<p${id}><enabled>true</enabled></p${id}>"
done
docker compose exec -T zap sh -c "sed \
    -e 's#<policy>Sequence</policy>#<policy>EC-CUBE</policy>#' \
    -e '/<p40026>/,/<\/p40026>/s#<enabled>true</enabled>#<enabled>false</enabled><level>OFF</level>#' \
    -e 's#</plugins>#${extra}</plugins>#' \
    -e '/<statsId>/d' -e '/<readonly>/d' \
    /home/zap/.ZAP/policies/Sequence.policy > /home/zap/.ZAP/policies/EC-CUBE.policy"

# Persistent ポリシー。格納型 XSS のルール (40014・40016・40017) だけを有効にする (zap/README.md の「検知できることの検証」)。
# 新規登録で作られるレコードに目印が押し出されないよう、既存のレコードを編集するだけのシナリオ (*_persistent) で使う
docker compose exec -T zap sh -c "sed \
    -e 's#<policy>Sequence</policy>#<policy>Persistent</policy>#' \
    -e 's#<enabled>true</enabled>#<enabled>false</enabled>#g' \
    -e 's#</plugins>#<p40014><enabled>true</enabled></p40014><p40016><enabled>true</enabled></p40016><p40017><enabled>true</enabled></p40017></plugins>#' \
    -e '/<statsId>/d' -e '/<readonly>/d' \
    /home/zap/.ZAP/policies/Sequence.policy > /home/zap/.ZAP/policies/Persistent.policy"
