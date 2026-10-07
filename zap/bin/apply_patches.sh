#!/bin/bash
#
# スキャン用のパッチ (zap/patches/*.patch) を EC-CUBE のコンテナへ当てる。
#
# usage: apply_patches.sh [-R] [<パッチ名>...]
#   パッチ名は zap/patches/ のファイル名から .patch を除いたもの。省略時はすべて
#   -R  当てたパッチを外す
#
# 能動スキャンの攻撃で状態が消費され (登録済み、削除済み、使用済みの URL など)、後続の攻撃が
# 画面の処理まで届かなくなるのを防ぐため、保存や削除の処理を無効にする。
# スキャンする対象がリリースするコードと変わり、保存の時点で起きるエラー (制約違反など) は
# 検出できなくなるため、既定のスキャンでは当てない。
#
# docker compose は呼び出し元の環境変数 (COMPOSE_FILE / COMPOSE_PROJECT_NAME) で実行する。

set -euo pipefail
cd "$(dirname "$0")/../.." || exit 1

REVERSE=
while getopts R OPT; do
    case ${OPT} in
        R) REVERSE=-R ;;
        *) exit 2 ;;
    esac
done
shift $((OPTIND - 1))

if [[ $# -eq 0 ]]; then
    set -- $(basename -s .patch zap/patches/*.patch)
fi

patch_in_container() {
    docker compose exec -T -u www-data:www-data ec-cube patch -p1 --forward -s "$@" > /dev/null
}

for name in "$@"; do
    file=zap/patches/${name}.patch
    if [[ ! -f ${file} ]]; then
        echo "${file} がありません" >&2
        exit 1
    fi
    # 逆向きに当てられるなら当て済み。二重に当てると patch が .rej を残すため、先に判定する
    if patch_in_container --dry-run -R < "${file}"; then
        applied=1
    else
        applied=
    fi
    if [[ -z ${REVERSE} && -n ${applied} ]]; then
        echo "${name}: 当て済み"
    elif [[ -n ${REVERSE} && -z ${applied} ]]; then
        echo "${name}: 当たっていない"
    else
        patch_in_container ${REVERSE} < "${file}"
        if [[ -n ${REVERSE} ]]; then echo "${name}: 外しました"; else echo "${name}: 当てました"; fi
    fi
done
