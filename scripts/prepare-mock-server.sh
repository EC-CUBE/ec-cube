#!/bin/bash

# Mockサーバ用のプラグインアーカイブを準備するスクリプト

set -e

# プロジェクトルートディレクトリ
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
REPOS_DIR="${PROJECT_ROOT}/repos"
PLUGINS_DIR="${PROJECT_ROOT}/codeception/_data/plugins"

echo "Preparing mock server plugins..."

# reposディレクトリを作成
mkdir -p "${REPOS_DIR}"

# InstallTestPlugin-1.0.0のアーカイブを作成
if [ -d "${PLUGINS_DIR}/InstallTestPlugin-1.0.0" ]; then
    echo "Creating InstallTestPlugin-1.0.0.tgz..."
    (cd "${PLUGINS_DIR}/InstallTestPlugin-1.0.0" && tar zcf "${REPOS_DIR}/InstallTestPlugin-1.0.0.tgz" *)
    echo "Created: ${REPOS_DIR}/InstallTestPlugin-1.0.0.tgz"
else
    echo "Warning: InstallTestPlugin-1.0.0 directory not found"
fi

# 既存のプラグインもアーカイブ化
for plugin_dir in "${PLUGINS_DIR}"/*-1.0.0; do
    if [ -d "$plugin_dir" ]; then
        plugin_name=$(basename "$plugin_dir")
        echo "Creating ${plugin_name}.tgz..."
        (cd "$plugin_dir" && tar zcf "${REPOS_DIR}/${plugin_name}.tgz" *)
        echo "Created: ${REPOS_DIR}/${plugin_name}.tgz"
    fi
done

echo "Mock server plugins prepared successfully!"
echo "Repository directory: ${REPOS_DIR}"
echo "Available plugins:"
ls -la "${REPOS_DIR}"/*.tgz 2>/dev/null || echo "No plugin archives found"
