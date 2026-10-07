#!/bin/bash
#
# レーンの EC-CUBE にスキャン用のパッチ (zap/patches/*.patch) を当てる、または外す。
# パッチは restore.sh では戻らない。外すか、setup.sh -r でレーンを作り直す。
#
# usage: LANE=<n> patch.sh [-R] [<パッチ名>...]
#   例: LANE=7 zap/local/patch.sh
#       LANE=7 zap/local/patch.sh -R mypage-delivery-keep

set -euo pipefail
source "$(dirname "$0")/env.sh"

exec zap/bin/apply_patches.sh "$@"
