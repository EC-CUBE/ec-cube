#!/bin/sh

CURRENT_DIR=`pwd`
TMP_DIR=/tmp
SVN_PATH=file:///home/svn/open/branches/feature-module-update
ECCUBE_VERSION=2.0.0-beta
WRK_DIR=eccube-$ECCUBE_VERSION

if [ ! -d $TMP_DIR ]; then
    mkdir -p $TMP_DIR
fi

cd $TMP_DIR
svn export $SVN_PATH $WRK_DIR

echo "remove obsolete files..."
rm -rf $WRK_DIR/.setttings
rm -rf $WRK_DIR/.buildpath
rm -rf $WRK_DIR/.project
rm -rf $WRK_DIR/test
rm -rf $WRK_DIR/templates
rm -rf $WRK_DIR/convert.php
rm -rf $WRK_DIR/release.sh
rm -rf $WRK_DIR/html/test
rm -rf $WRK_DIR/data/downloads/module/*
rm -rf $WRK_DIR/data/downloads/module2/*
find ./$WRK_DIR -name "dummy" -delete
find ./$WRK_DIR -name "info.php" -delete

echo "set permissions..."
chmod 666 $WRK_DIR/data/install.php
chmod -R 777 $WRK_DIR/html/install/temp
chmod -R 777 $WRK_DIR/html/user_data
chmod -R 777 $WRK_DIR/html/upload
chmod -R 777 $WRK_DIR/html/cp
chmod -R 777 $WRK_DIR/data/cache
chmod -R 777 $WRK_DIR/data/downloads
chmod -R 777 $WRK_DIR/data/Smarty
chmod -R 777 $WRK_DIR/data/class
chmod -R 777 $WRK_DIR/data/logs
chmod -R 777 $WRK_DIR/html/cp

echo "complession files..."
tar cfp $WRK_DIR.tar $WRK_DIR
gzip -9 $WRK_DIR.tar
mv $WRK_DIR.tar.gz $CURRENT_DIR/
rm -rf $WRK_DIR

echo "finished successful!"
echo $CURRENT_DIR/$WRK_DIR.tar.gz
