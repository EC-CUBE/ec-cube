#!/bin/sh

######################################################################
#
# EC-CUBE の再インストールを行う shell スクリプト
#
# ※ PostgreSQL 専用
#
# 1. 既存の EC-CUBE サイトを移動(PREFIX.YYYYMMDD)
# 2. SVNリポジトリより checkout(tags/EC_CUBE_VERSION)
# 3. パーミッション変更
# 4. html/install/sql 配下の SQL を実行
# 5. 管理者権限をアップデート
# 6. data/install.php を生成
#
# 使い方
#
# # ./ec_cube_install.sh /install/path/to/eccube eccube-2.4.1
#
# この場合の DocumentRoot は, /install/path/to/eccube/html になります.
#
# 開発コミュニティの関連スレッド
# http://xoops.ec-cube.net/modules/newbb/viewtopic.php?topic_id=4918&forum=14&post_id=23090#forumpost23090
#
#######################################################################

PREFIX=${PREFIX:-"$1"}
EC_CUBE_VERSION=${EC_CUBE_VERSION:-"$2"}

ADMIN_MAIL=${ADMIN_MAIL:-"shop@ec-cube.net"}
SHOP_NAME=${SHOP_NAME:-"ロックオンのふとんやさん"}
INSTALL_PHP="data/install.php"
SITE_URL=${SITE_URL:-"http://demo2.ec-cube.net/"}
SSL_URL=${SSL_URL:-"http://demo2.ec-cube.net/"}
DOMAIN_NAME=${DOMAIN_NAME:-""}

TODAY=`date "+%Y%m%d"`

SVN=svn
SVN_USER=guest
SVN_PASSWD=lh1jNhUn

REPOSITORY="https://svn.ec-cube.net/open/tags/"

PGUSER=postgres
DROPDB=dropdb
CREATEDB=createdb
PSQL=psql

DBSERVER="127.0.0.1"
DBNAME=demo2_db
DBUSER=demo2_db_user
DBPASS=password
DBPORT=5432

OPTIONAL_SQL_FILE=optional.sql


echo "PREFIX=${PREFIX}"
echo "EC_CUBE_VERSION=${EC_CUBE_VERSION}"

if [ -d ${PREFIX} ]
then
    echo "backup old version..."
    mv ${PREFIX} "${PREFIX}.${TODAY}"
fi

if [ -f ${OPTIONAL_SQL_FILE} ]
then
    echo "remove optional SQL"
    rm ${OPTIONAL_SQL_FILE}
fi

echo "checkout sources from svn.ec-cube.net..."
${SVN} checkout --username ${SVN_USER} --password ${SVN_PASSWD} \
    "${REPOSITORY}/${EC_CUBE_VERSION}" ${PREFIX}

echo "update permissions..."
chmod -R 777 "${PREFIX}/data/cache"
chmod -R 777 "${PREFIX}/data/class"
chmod -R 777 "${PREFIX}/data/Smarty"
chmod -R 777 "${PREFIX}/data/logs"
chmod -R 777 "${PREFIX}/data/downloads"
chmod -R 777 "${PREFIX}/html/install/temp"
chmod -R 777 "${PREFIX}/html/user_data"
chmod -R 777 "${PREFIX}/html/cp"
chmod -R 777 "${PREFIX}/html/upload"

#echo "dropdb..."
#su ${PGUSER} -c "${DROPDB} ${DBNAME}"

#echo "createdb..."
#su ${PGUSER} -c "${CREATEDB} -U ${DBUSER} ${DBNAME}"

SQL_DIR="${PREFIX}/html/install/sql"

echo "drop view..."
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${SQL_DIR}/drop_view.sql ${DBNAME}"

echo "drop tables..."
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${SQL_DIR}/drop_table.sql ${DBNAME}"
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -c 'DROP TABLE dtb_session;' ${DBNAME}"
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -c 'DROP TABLE dtb_module;' ${DBNAME}"
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -c 'DROP TABLE dtb_campaign_order;' ${DBNAME}"
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -c 'DROP TABLE dtb_mobile_kara_mail;' ${DBNAME}"
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -c 'DROP TABLE dtb_mobile_ext_session_id;' ${DBNAME}"
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -c 'DROP TABLE dtb_site_control;' ${DBNAME}"
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -c 'DROP TABLE dtb_trackback;' ${DBNAME}"

echo "create table..."
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${SQL_DIR}/create_table_pgsql.sql ${DBNAME}"

echo "create_view..."
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${SQL_DIR}/create_view.sql ${DBNAME}"

echo "adding tables..."
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${SQL_DIR}/add/dtb_campaign_order_pgsql.sql ${DBNAME}"
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${SQL_DIR}/add/dtb_mobile_ext_session_id_pgsql.sql ${DBNAME}"
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${SQL_DIR}/add/dtb_mobile_kara_mail_pgsql.sql ${DBNAME}"
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${SQL_DIR}/add/dtb_module_pgsql.sql ${DBNAME}"
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${SQL_DIR}/add/dtb_session_pgsql.sql ${DBNAME}"
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${SQL_DIR}/add/dtb_site_control_pgsql.sql ${DBNAME}"
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${SQL_DIR}/add/dtb_trackback_pgsql.sql ${DBNAME}"

echo "insert data..."
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${SQL_DIR}/insert_data.sql ${DBNAME}"

echo "create optional SQL..."
echo "INSERT INTO dtb_member (member_id, login_id, password, authority, creator_id) VALUES ('1', 'admin', '2c19f4a742398150cecc80b3e76b673a35b8c19c', '0', '0');" >> ${OPTIONAL_SQL_FILE}
echo "INSERT INTO dtb_baseinfo (shop_name, email01, email02, email03, email04, email05, top_tpl, product_tpl, detail_tpl, mypage_tpl) VALUES ('${SHOP_NAME}', '${ADMIN_MAIL}', '${ADMIN_MAIL}', '${ADMIN_MAIL}', '${ADMIN_MAIL}', '${ADMIN_MAIL}', 'default1', 'default1', 'default1', 'default1');" >> ${OPTIONAL_SQL_FILE}
echo "execute optional SQL..."
su ${PGUSER} -c "${PSQL} -U ${DBUSER} -f ${OPTIONAL_SQL_FILE} ${DBNAME}"

echo "copy images..."
cp -rv "${PREFIX}/html/install/save_image" "${PREFIX}/html/upload/"

echo "creating ${INSTALL_PHP}..."
cat > "${PREFIX}/${INSTALL_PHP}" <<EOF
<?php
define ('ECCUBE_INSTALL', 'ON');
define ('HTML_PATH', '${PREFIX}/html/');
define ('SITE_URL', '${SITE_URL}');
define ('SSL_URL', '${SSL_URL}');
define ('URL_DIR', '/');
define ('DOMAIN_NAME', '${DOMAIN_NAME}');
define ('DB_TYPE', 'pgsql');
define ('DB_USER', '${DBUSER}');
define ('DB_PASSWORD', '${DBPASS}');
define ('DB_SERVER', '${DBSERVER}');
define ('DB_NAME', '${DBNAME}');
define ('DB_PORT', '${DBPORT}');
define ('DATA_PATH', '${PREFIX}/data/');
define ('MOBILE_HTML_PATH', HTML_PATH . 'mobile/');
define ('MOBILE_SITE_URL', SITE_URL . 'mobile/');
define ('MOBILE_SSL_URL', SSL_URL . 'mobile/');
define ('MOBILE_URL_DIR', URL_DIR . 'mobile/');
?>
EOF

echo "Finished Successful!"