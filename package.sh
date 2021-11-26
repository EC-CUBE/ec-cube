#!/usr/bin/env bash
set -o errexit

declare -r CURRENT_DIR=$PWD
declare -r TEMP_DIR="$(mktemp -d)"
declare -r WORKSPACE=$TEMP_DIR/ec-cube
declare -r PACKAGE_SUFFIX=${TAG_NAME:+-${TAG_NAME}}

cp -a $(dirname $0) $WORKSPACE

rm -rf $WORKSPACE/.editorconfig
rm -rf $WORKSPACE/.gitignore
rm -rf $WORKSPACE/.buildpath
rm -rf $WORKSPACE/.gitmodules
rm -rf $WORKSPACE/.php_cs.dist
rm -rf $WORKSPACE/phpunit.xml.dist
rm -rf $WORKSPACE/phpstan.neon.dist
rm -rf $WORKSPACE/app.json
rm -rf $WORKSPACE/Procfile
rm -rf $WORKSPACE/LICENSE.txt
rm -rf $WORKSPACE/README.md
rm -rf $WORKSPACE/codeception.yml
rm -rf $WORKSPACE/var/*
rm -rf $WORKSPACE/.env
rm -rf $WORKSPACE/codeception
rm -rf $WORKSPACE/tests
rm -rf $WORKSPACE/.github
rm -rf $WORKSPACE/zap
rm -rf $WORKSPACE/docker-compose.owaspzap.*
rm -rf $WORKSPACE/package.sh
rm -rf $WORKSPACE/app/PluginData/Api/oauth/private.key
rm -rf $WORKSPACE/app/PluginData/Api/oauth/public.key
find $WORKSPACE -name "dummy" -print0 | xargs -0 rm -rf
find $WORKSPACE -name ".git*" -and ! -name ".gitkeep" -print0 | xargs -0 rm -rf
find $WORKSPACE -name ".git*" -type d -print0 | xargs -0 rm -rf

echo "set permissions..."
chmod -R o+w $WORKSPACE

echo "complession files..."
(cd $TEMP_DIR; tar --preserve-permissions -czf ${CURRENT_DIR}/eccube${PACKAGE_SUFFIX}.tar.gz ec-cube)
(cd $TEMP_DIR; zip -ry ${CURRENT_DIR}/eccube${PACKAGE_SUFFIX}.zip ec-cube 1> /dev/null)
md5sum eccube${PACKAGE_SUFFIX}.tar.gz | awk '{ print $1 }' > eccube${PACKAGE_SUFFIX}.tar.gz.checksum.md5
md5sum eccube${PACKAGE_SUFFIX}.zip | awk '{ print $1 }' > eccube${PACKAGE_SUFFIX}.zip.checksum.md5
sha1sum eccube${PACKAGE_SUFFIX}.tar.gz | awk '{ print $1 }' > eccube${PACKAGE_SUFFIX}.tar.gz.checksum.sha1
sha1sum eccube${PACKAGE_SUFFIX}.zip | awk '{ print $1 }' > eccube${PACKAGE_SUFFIX}.zip.checksum.sha1
sha256sum eccube${PACKAGE_SUFFIX}.tar.gz | awk '{ print $1 }' > eccube${PACKAGE_SUFFIX}.tar.gz.checksum.sha256
sha256sum eccube${PACKAGE_SUFFIX}.zip | awk '{ print $1 }' > eccube${PACKAGE_SUFFIX}.zip.checksum.sha256

ls -al
