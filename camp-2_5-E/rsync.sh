#!/bin/bash

if [[ $1 = "run" ]] 
 then 
  OPTION='-Cauvt  --exclude ".svn"'
 else 
  echo $1
  OPTION='-Cauvn --exclude ".svn"'
fi 
echo rsync ${OPTION} data /srv/www/vhosts/eccube.miningbrownie.jp
rsync ${OPTION} data /srv/www/vhosts/eccube.miningbrownie.jp
echo rsync ${OPTION} html /srv/www/vhosts/eccube.miningbrownie.jp
rsync ${OPTION} html /srv/www/vhosts/eccube.miningbrownie.jp




