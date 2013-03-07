#!/bin/sh

while read PROP
do
svn propdel -R $PROP ./
done <<EOF
svn:keywords
svn:executable
svn:mime-type
svn:eol-style
EOF

find . \( -name module -o -name .svn \) -prune -o -type f -print | xargs svn propset 'svn:keywords' 'Id'
svn propset -R 'svn:executable' *.sh
svn propset -R 'svn:eol-style' 'LF' ./
find . -name '*.php' | xargs svn propset -R 'svn:mime-type' 'text/x-httpd-php; charset=UTF-8'
find . -name '*.tpl' | xargs svn propset -R 'svn:mime-type' 'text/x-smarty-template; charset=UTF-8'
find . -name '*.gif' | xargs svn propset -R 'svn:mime-type' 'image/gif'
find . -name '*.jpg' | xargs svn propset -R 'svn:mime-type' 'image/jpeg'
find . -name '*.png' | xargs svn propset -R 'svn:mime-type' 'image/png'
find . -name '*.pdf' | xargs svn propset -R 'svn:mime-type' 'application/pdf'
