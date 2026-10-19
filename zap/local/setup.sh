#!/bin/bash
#
# レーンを起動し、スキャンできる状態にする。複数のレーンは並列に準備する。
#
# usage: setup.sh [-r] [-n] <レーン番号>...
#   -r  既存のコンテナとボリュームを削除して作り直す
#   -n  スキャン用のパッチ (zap/patches) を当てない
#   例: zap/local/setup.sh 1 2 3 4
#
# 1. コンテナを起動する (EC-CUBE は APP_ENV=prod)
# 2. バンドル版プラグインを導入する (zap/plugins.txt、clone 先は zap/local/repos)
# 3. ログインのスロットリングとレートリミッタを緩める
# 4. スキャン用のパッチを当て (-n で省略)、ZAP のアドオンを導入し、Smoke ポリシーを作る
# 5. この時点の DB を eccubedb_clean として保存する (restore.sh でターゲットごとに戻す)
# 6. スキャン中に作られたデータとファイルの定期削除を起動する
#
# ログは zap/local/out/setup-<レーン番号>.log (ZAP_DB=mysql のときは setup-mysql-<レーン番号>.log) に出力する。

set -uo pipefail

RECREATE= NO_PATCH=
while getopts rn OPT; do
    case ${OPT} in
        r) RECREATE=1 ;;
        n) NO_PATCH=1 ;;
        *) exit 2 ;;
    esac
done
shift $((OPTIND - 1))
if [[ $# == 0 ]]; then
    echo "usage: $0 [-r] [-n] <レーン番号>..." >&2
    exit 2
fi

source "$(dirname "$0")/env.sh"
mkdir -p "${ZAP_LOCAL}/out"
# 同じ番号の PostgreSQL と MySQL のレーンでログを上書きし合わないよう、MySQL は名前を分ける
LOG_PREFIX=
[[ ${ZAP_DB} != pgsql ]] && LOG_PREFIX=${ZAP_DB}-

# 並列に clone しないよう、先に 1 回だけ clone する
zap/bin/install_plugins.sh -c "${ZAP_LOCAL}/repos" || exit 1

setup_lane() {
    export LANE=$1
    source "${ZAP_LOCAL}/env.sh"
    set -e
    if [[ -n ${RECREATE} ]]; then
        docker compose down -v
    fi
    docker compose up -d --wait
    zap/bin/install_plugins.sh "${ZAP_LOCAL}/repos"
    zap/bin/prepare_ec-cube.sh
    if [[ -z ${NO_PATCH} ]]; then
        zap/bin/apply_patches.sh
    fi
    zap/bin/prepare_zap.sh
    "${ZAP_LOCAL}/snapshot.sh"
    zap/bin/start_cleanup.sh
}

lanes=("$@")
pids=()
for lane in "${lanes[@]}"; do
    ( setup_lane "${lane}" ) > "${ZAP_LOCAL}/out/setup-${LOG_PREFIX}${lane}.log" 2>&1 &
    pids+=($!)
done

status=0
for i in "${!lanes[@]}"; do
    if wait "${pids[$i]}"; then
        echo "lane ${lanes[$i]}: ok"
    else
        echo "lane ${lanes[$i]}: 失敗 (zap/local/out/setup-${LOG_PREFIX}${lanes[$i]}.log を参照)"
        status=1
    fi
done
exit ${status}
