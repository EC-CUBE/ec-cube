#!/bin/bash
#
# zap/plugins.txt のプラグインを clone して EC-CUBE のコンテナへ導入し、有効化する。
#
# usage: install_plugins.sh [-c] <clone 先ディレクトリ>
#   -c  clone だけ行う (複数のレーンを並列に準備する前に 1 回だけ実行する)
#
# docker compose は呼び出し元の環境変数 (COMPOSE_FILE / COMPOSE_PROJECT_NAME) で実行する。
# ブランチは ZAP_PLUGIN_BRANCH で変更できる (既定は 4.4)。

set -euo pipefail

CLONE_ONLY=
if [[ ${1:-} == -c ]]; then CLONE_ONLY=1; shift; fi
REPOS=$(realpath -m "${1:?clone 先ディレクトリを指定してください}")
BRANCH=${ZAP_PLUGIN_BRANCH:-4.4}

cd "$(dirname "$0")/../.." || exit 1
mapfile -t PLUGINS < <(grep -vE '^\s*(#|$)' zap/plugins.txt)

mkdir -p "${REPOS}"
for p in "${PLUGINS[@]}"; do
    IFS=: read -r repo pkg code <<< "${p}"
    # clone 済みなら触らない (複数のレーンが同時に導入するため、clone 先へは書き込まない)
    [[ -d ${REPOS}/${repo} ]] && continue
    git clone --quiet --depth 1 --branch "${BRANCH}" "https://github.com/EC-CUBE/${repo}.git" "${REPOS}/${repo}.tmp"
    # extra.id が無いと eccube:composer:require が失敗する
    jq '.extra.id //= 0' "${REPOS}/${repo}.tmp/composer.json" > "${REPOS}/${repo}.tmp/composer.json.new"
    mv "${REPOS}/${repo}.tmp/composer.json.new" "${REPOS}/${repo}.tmp/composer.json"
    mv "${REPOS}/${repo}.tmp" "${REPOS}/${repo}"
done
[[ -n ${CLONE_ONLY} ]] && exit 0

# Web サーバーのユーザーは clone 元に書き込めないため、symlink ではなくコンテナ内へコピーして導入する
docker compose exec -T -u 0:0 ec-cube sh -c 'rm -rf /var/www/repos && mkdir -p /var/www/repos'
for p in "${PLUGINS[@]}"; do
    IFS=: read -r repo pkg code <<< "${p}"
    docker compose cp "${REPOS}/${repo}" "ec-cube:/var/www/repos/${repo}" > /dev/null
done
docker compose exec -T -u 0:0 ec-cube chown -R www-data:www-data /var/www/repos

run() { docker compose exec -T -u www-data:www-data ec-cube "$@"; }
for p in "${PLUGINS[@]}"; do
    IFS=: read -r repo pkg code <<< "${p}"
    echo "=== ${code}"
    run composer config "repositories.${pkg}" \
        "{\"type\":\"path\",\"url\":\"/var/www/repos/${repo}\",\"options\":{\"symlink\":false}}"
    run bin/console eccube:composer:require "ec-cube/${pkg}"
    run bin/console eccube:plugin:enable --code "${code}"
    # 前のプラグインを有効化した時点のコンテナキャッシュが残ると、次の有効化が失敗することがある
    run bin/console cache:clear > /dev/null
done
