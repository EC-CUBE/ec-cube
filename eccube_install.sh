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
# Configurationの内容を自分の環境に併せて修正
# PostgreSQLの場合は、DBユーザーを予め作成しておいて
# # ./ec_cube_install.sh pgsql
# MySQLはMYSQLのRoot以外のユーザーで実行する場合は、128行目をコメントアウトして
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

DBSERVER=${DBSERVER-"127.0.0.1"}
DBNAME=${DBNAME:-"cube213_dev"}
DBUSER=${DBUSER:-"cube213_dev_user"}
DBPASS=${DBPASS:-"password"}

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
    DBPORT=5432
;;
"mysql" )
    #-- DB Seting MySQL
    MYSQL=mysql
    ROOTUSER=root
    ROOTPASS=$DBPASS
    DBSERVER="127.0.0.1"
    DBPORT=3306
;;
* ) echo "ERROR:: argument is invaid"
exit
;;
esac


#######################################################################
# Install

#-- Update Permissions
echo "update permissions..."
chmod -R go+w "./html"
chmod go+w "./data"
chmod -R go+w "./data/Smarty"
chmod -R go+w "./data/cache"
chmod -R go+w "./data/class"
chmod -R go+w "./data/class_extends"
chmod go+w "./data/config"
chmod -R go+w "./data/download"
chmod -R go+w "./data/downloads"
chmod go+w "./data/fonts"
chmod go+w "./data/include"
chmod go+w "./data/logs"
chmod -R go+w "./data/module"
chmod go+w "./data/smarty_extends"
chmod go+w "./data/upload"
chmod go+w "./data/upload/csv"

#-- Setup Database
SQL_DIR="./html/install/sql"
OPTIONAL_SQL_FILE=optional.sql
if [ -f ${OPTIONAL_SQL_FILE} ]
then
    echo "remove optional SQL"
    rm ${OPTIONAL_SQL_FILE}
fi

SEQUENCES="
dtb_best_products_best_id_seq
dtb_bloc_bloc_id_seq
dtb_category_category_id_seq
dtb_class_class_id_seq
dtb_classcategory_classcategory_id_seq
dtb_csv_no_seq
dtb_csv_sql_sql_id_seq
dtb_customer_customer_id_seq
dtb_deliv_deliv_id_seq
dtb_holiday_holiday_id_seq
dtb_kiyaku_kiyaku_id_seq
dtb_mail_history_send_id_seq
dtb_maker_maker_id_seq
dtb_member_member_id_seq
dtb_module_update_logs_log_id_seq
dtb_news_news_id_seq
dtb_order_order_id_seq
dtb_order_detail_order_detail_id_seq
dtb_other_deliv_other_deliv_id_seq
dtb_pagelayout_page_id_seq
dtb_payment_payment_id_seq
dtb_products_class_product_class_id_seq
dtb_products_product_id_seq
dtb_review_review_id_seq
dtb_send_history_send_id_seq
dtb_mailmaga_template_template_id_seq
dtb_plugin_plugin_id_seq
dtb_plugin_hookpoint_plugin_hookpoint_id_seq
dtb_api_config_api_config_id_seq
dtb_api_account_api_account_id_seq
dtb_tax_rule_tax_rule_id_seq
"

echo "create optional SQL..."
echo "INSERT INTO dtb_member (member_id, login_id, password, salt, work, del_flg, authority, creator_id, rank, update_date) VALUES (2, 'admin', '${ADMINPASS}', '${AUTH_MAGIC}', '1', '0', '0', '0', '1', current_timestamp);" >> ${OPTIONAL_SQL_FILE}
echo "INSERT INTO dtb_baseinfo (id, shop_name, email01, email02, email03, email04, top_tpl, product_tpl, detail_tpl, mypage_tpl, update_date) VALUES (1, '${SHOP_NAME}', '${ADMIN_MAIL}', '${ADMIN_MAIL}', '${ADMIN_MAIL}', '${ADMIN_MAIL}', 'default1', 'default1', 'default1', 'default1', current_timestamp);" >> ${OPTIONAL_SQL_FILE}

case "${DBTYPE}" in
"pgsql" )
    # PostgreSQL
    echo "dropdb..."
    sudo -u ${PGUSER} ${DROPDB} ${DBNAME}
    echo "createdb..."
    sudo -u ${PGUSER} ${CREATEDB} -U ${DBUSER} ${DBNAME}
    echo "create table..."
    sudo -u ${PGUSER} ${PSQL} -U ${DBUSER} -f ${SQL_DIR}/create_table_pgsql.sql ${DBNAME}
    echo "insert data..."
    sudo -u ${PGUSER} ${PSQL} -U ${DBUSER} -f ${SQL_DIR}/insert_data.sql ${DBNAME}
    for S in $SEQUENCES
    do
	echo "CREATE SEQUENCE $S START 10000;" >> ${OPTIONAL_SQL_FILE}
    done
    echo "execute optional SQL..."
    sudo -u ${PGUSER} ${PSQL} -U ${DBUSER} -f ${OPTIONAL_SQL_FILE} ${DBNAME}
;;
"mysql" )
    DBPASS=`echo $DBPASS | tr -d " "`
    if [ -n ${DBPASS} ]; then
	PASSOPT="--password=$DBPASS"
	CONFIGPASS=$DBPASS
    fi
    # MySQL
    echo "dropdb..."
    ${MYSQL} -u ${ROOTUSER} ${PASSOPT} -e "drop database ${DBNAME}"
    echo "createdb..."
    ${MYSQL} -u ${ROOTUSER} ${PASSOPT} -e "create database ${DBNAME}"
    #echo "grant user..."
    #${MYSQL} -u ${ROOTUSER} ${PASSOPT} -e "GRANT ALL ON ${DBNAME}.* TO '${DBUSER}'@'%' IDENTIFIED BY '${DBPASS}'"
    echo "create table..."
    ${MYSQL} -u ${DBUSER} ${PASSOPT} ${DBNAME} < ${SQL_DIR}/create_table_mysql.sql
    echo "insert data..."
    ${MYSQL} -u ${DBUSER} ${PASSOPT} ${DBNAME} < ${SQL_DIR}/insert_data.sql
    for S in $SEQUENCES
    do
	echo "CREATE TABLE $S ( sequence int(11) NOT NULL AUTO_INCREMENT, PRIMARY KEY (sequence)) ENGINE=MyISAM DEFAULT CHARSET=utf8; LOCK TABLES $S WRITE; INSERT INTO $S VALUES (10000); UNLOCK TABLES;" >> ${OPTIONAL_SQL_FILE}
    done
    echo "execute optional SQL..."
    ${MYSQL} -u ${DBUSER} ${PASSOPT} ${DBNAME} < ${OPTIONAL_SQL_FILE}
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
define('DB_PASSWORD', '${CONFIGPASS}');
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
