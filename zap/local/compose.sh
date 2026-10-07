#!/bin/bash
#
# レーンの docker compose を操作する。
#
# usage: LANE=<n> compose.sh <docker compose のサブコマンド>...
#   例: LANE=2 zap/local/compose.sh ps
#       LANE=2 zap/local/compose.sh logs ec-cube --tail=100

source "$(dirname "$0")/env.sh"
exec docker compose "$@"
