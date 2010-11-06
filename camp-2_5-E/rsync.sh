#!/bin/bash

if [[ $1 = "run" ]] 
 then 
  OPTION='-Cauvt  --exclude ".svn" -e ssh '
 else 
  echo $1
  OPTION='-Cauvn  --exclude ".svn" -e ssh '
fi 
echo rsync ${OPTION} data root@172.17.1.71:/srv/www/vhosts/eccube.miningbrownie.jp
rsync ${OPTION} data root@172.17.1.71:/srv/www/vhosts/eccube.miningbrownie.jp
echo rsync ${OPTION} html root@172.17.1.71:/srv/www/vhosts/eccube.miningbrownie.jp
rsync ${OPTION} html root@172.17.1.71:/srv/www/vhosts/eccube.miningbrownie.jp

