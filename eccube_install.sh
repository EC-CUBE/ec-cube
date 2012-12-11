#!/bin/sh

######################################################################
#
# EC-CUBE のインストールを行う shell スクリプト
#
#
# #処理内容
# 1. パーミッション変更
# 2. html/install/sql 配下の SQL を実行
# 3. 管理者権限をアップデート
# 4. data/config/config.php を生成
#
# 使い方
#
# # ./ec_cube_install.sh mysql
#
#
# 開発コミュニティの関連スレッド
# http://xoops.ec-cube.net/modules/newbb/viewtopic.php?topic_id=4918&forum=14&post_id=23090#forumpost23090
#
#######################################################################

#######################################################################
# Configuration 
#-- Shop Configuration
CONFIG_PHP="data/config/config.php"
ADMIN_MAIL=${ADMIN_MAIL:-"admin@example.com"}
SHOP_NAME=${SHOP_NAME:-"EC-CUBE SHOP"}
HTTP_URL=${HTTP_URL:-"http://test.local"}
HTTPS_URL=${HTTPS_URL:-"http://test.local/"}
DOMAIN_NAME=${DOMAIN_NAME:-""}
ADMINPASS="f6b126507a5d00dbdbb0f326fe855ddf84facd57c5603ffdf7e08fbb46bd633c"
AUTH_MAGIC="droucliuijeanamiundpnoufrouphudrastiokec"

DBTYPE=$1;

case "${DBTYPE}" in
"pgsql" ) 
    #-- DB Seting Postgres
    PSQL=psql
    PGUSER=postgres
    DROPDB=dropdb
    CREATEDB=createdb
    DBSERVER="127.0.0.1"
    DBNAME=cube212_dev
    DBUSER=cube212_dev_user
    DBPASS=password
    DBPORT=5432
;;
"mysql" ) 
    #-- DB Seting MySQL
    MYSQL=mysql
    ROOTUSER=root
    ROOTPASS=arigato36
    DBSERVER="127.0.0.1"
    DBNAME=cube212_dev
    DBUSER=cube212_dev_user
    DBPASS=password
    DBPORT=3306
;;
* ) echo "ERROR:: argument is invaid"
exit
;;
esac


#######################################################################
# Install 

echo "PREFIX=${PREFIX}"
echo "EC_CUBE_VERSION=${EC_CUBE_VERSION}"

#-- Update Permissions
echo "update permissions..."
chmod -R 777 "./html"
chmod 755 "./data"
chmod -R 777 "./data/Smarty"
chmod -R 777 "./data/cache"
chmod -R 777 "./data/class"
chmod -R 755 "./data/class_extends"
chmod 777 "./data/config"
chmod -R 777 "./data/download"
chmod -R 777 "./data/downloads"
chmod 755 "./data/fonts"
chmod 755 "./data/include"
chmod 777 "./data/logs"
chmod -R 777 "./data/module"
chmod 755 "./data/smarty_extends"
chmod 777 "./data/upload"
chmod 777 "./data/upload/csv"

#-- Setup Database
SQL_DIR="./html/install/sql"
OPTIONAL_SQL_FILE=optional.sql
if [ -f ${OPTIONAL_SQL_FILE} ]
then
    echo "remove optional SQL"
    rm ${OPTIONAL_SQL_FILE}
fi

echo "create optional SQL..."
echo "INSERT INTO dtb_member (member_id, login_id, password, salt, work, del_flg, authority, creator_id, rank, update_date) VALUES (2, 'admin', '${ADMINPASS}', '${AUTH_MAGIC}', '1', '0', '0', '0', '1', current_timestamp);" >> ${OPTIONAL_SQL_FILE}
echo "INSERT INTO dtb_baseinfo (id, shop_name, email01, email02, email03, email04, email05, top_tpl, product_tpl, detail_tpl, mypage_tpl, update_date) VALUES (1, '${SHOP_NAME}', '${ADMIN_MAIL}', '${ADMIN_MAIL}', '${ADMIN_MAIL}', '${ADMIN_MAIL}', '${ADMIN_MAIL}', 'default1', 'default1', 'default1', 'default1', current_timestamp);" >> ${OPTIONAL_SQL_FILE}


case "${DBTYPE}" in
"pgsql" ) 
    # PostgreSQL
    echo "dropdb..."
    su ${PGUSER} -c "${DROPDB} ${DBNAME}"
    echo "createdb..."
    su ${PGUSER} -c "${CREATEDB} -U ${DBUSER} ${DBNAME}"
    echo "create table..."
    su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${SQL_DIR}/create_table_pgsql.sql ${DBNAME}"
    echo "insert data..."
    su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${SQL_DIR}/insert_data.sql ${DBNAME}"
    echo "execute optional SQL..."
    su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${OPTIONAL_SQL_FILE} ${DBNAME}"
;;
"mysql" ) 
    # MySQL
    echo "dropdb..."
    ${MYSQL} -u ${ROOTUSER} -p${ROOTPASS} -e "drop database ${DBNAME}"
    echo "createdb..."
    ${MYSQL} -u ${ROOTUSER} -p${ROOTPASS} -e "create database ${DBNAME}"
    #echo "grant user..."
    #${MYSQL} -u ${ROOTUSER} -p${ROOTPASS} -e "GRANT ALL ON ${DBNAME}.* TO '${DBUSER}'@'%' IDENTIFIED BY '${DBPASS}'"
    echo "create table..."
    ${MYSQL} -u ${DBUSER} -p${DBPASS} ${DBNAME} < ${SQL_DIR}/create_table_mysql.sql
    echo "insert data..."
    ${MYSQL} -u ${DBUSER} -p${DBPASS} ${DBNAME} < ${SQL_DIR}/insert_data.sql
    echo "execute optional SQL..."
    ${MYSQL} -u ${DBUSER} -p${DBPASS} ${DBNAME} < ${OPTIONAL_SQL_FILE}
;;
esac

#-- Setup Initial Data

echo "copy images..."
cp -rv "./html/install/save_image" "./html/upload/"

echo "creating ${CONFIG_PHP}..."
cat > "./${CONFIG_PHP}" <<EOF
<?php
define('ECCUBE_INSTALL', 'ON');
define('HTTP_URL', '${HTTP_URL}');
define('HTTPS_URL', '${HTTPS_URL}');
define('ROOT_URLPATH', '/');
define('DOMAIN_NAME', '${DOMAIN_NAME}');
define('DB_TYPE', '${DBTYPE}');
define('DB_USER', '${DBUSER}');
define('DB_PASSWORD', '${DBPASS}');
define('DB_SERVER', '${DBSERVER}');
define('DB_NAME', '${DBNAME}');
define('DB_PORT', '${DBPORT}');
define('ADMIN_DIR', 'admin/');
define('ADMIN_FORCE_SSL', FALSE);
define('ADMIN_ALLOW_HOSTS', 'a:0:{}');
define('AUTH_MAGIC', '${AUTH_MAGIC}');
define('PASSWORD_HASH_ALGOS', 'sha256');
define('MAIL_BACKEND', 'mail');
define('SMTP_HOST', '');
define('SMTP_PORT', '');
define('SMTP_USER', '');
define('SMTP_PASSWORD', '');

EOF

echo "Finished Successful!"