#!/bin/sh

WRK_DIR=`pwd`

echo "remove obsolete files..."
rm -rf $WRK_DIR/.setttings
rm -rf $WRK_DIR/.buildpath
rm -rf $WRK_DIR/.project
rm -rf $WRK_DIR/test
rm -rf $WRK_DIR/templates
rm -rf $WRK_DIR/release.sh
rm -rf $WRK_DIR/html/test
rm -rf $WRK_DIR/data/downloads/module/*
rm -rf $WRK_DIR/data/downloads/module2/*
find $WRK_DIR -name "dummy" -delete
find $WRK_DIR -name "info.php" -delete
find $WRK_DIR -name ".svn" -type d -print0 | xargs -0 rm -rf
# find $WRK_DIR -iname "*.bak" -delete

echo "set permissions..."
chmod 666 $WRK_DIR/data/install.php
chmod -R 777 $WRK_DIR/html/install/temp
chmod -R 777 $WRK_DIR/html/user_data
chmod -R 777 $WRK_DIR/html/upload
chmod -R 777 $WRK_DIR/data/cache
chmod -R 777 $WRK_DIR/data/downloads
chmod -R 777 $WRK_DIR/data/Smarty
chmod -R 777 $WRK_DIR/data/class
chmod -R 777 $WRK_DIR/data/logs
chmod -R 777 $WRK_DIR/data/upload

echo "finished."
