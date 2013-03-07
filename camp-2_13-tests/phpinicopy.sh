#!/bin/sh

if [ ! $1 ]
then
ECCUBE_DIR=`pwd`
else
ECCUBE_DIR=$1
fi

for dir in *
do
if [ -d $dir ]
then
cp -f $ECCUBE_DIR/php.ini $dir
cd $dir
$ECCUBE_DIR/${0##*/} $ECCUBE_DIR
cd ..
fi
done

