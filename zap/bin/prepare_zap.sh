#!/bin/bash
#
# ZAP のアドオンを更新・導入し、Smoke ポリシーを作る。
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

zap -addonupdate
zap -addonuninstall sequence
zap -addoninstall sequence
zap -addoninstall automation
zap -addoninstall groovy

docker compose exec -T zap sh -c "sed \
    -e 's#<policy>Sequence</policy>#<policy>Smoke</policy>#' \
    -e 's#<level>[A-Z]*</level>#<level>OFF</level>#g' \
    -e 's#<enabled>true</enabled>#<enabled>false</enabled>#g' \
    -e '/<statsId>/d' -e '/<readonly>/d' -e '/<locked>/d' \
    /home/zap/.ZAP/policies/Sequence.policy > /home/zap/.ZAP/policies/Smoke.policy"
