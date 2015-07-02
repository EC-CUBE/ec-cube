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

# ---------------------------------------------------------------------
# Constants
# ---------------------------------------------------------------------
BASE_DIR=${BASE_DIR:-$(cd $(dirname $0) && pwd)}
CONFIG_DIR="${BASE_DIR}/app/config/eccube"
DIST_DIR="${BASE_DIR}/src/Eccube/Resource/config"
SQL_DIR="${BASE_DIR}/src/Eccube/Resource/sql"

# config.yml
CONFIG_YML="${CONFIG_DIR}/config.yml"
CONFIG_YML_DIST="${DIST_DIR}/config.yml.dist"
# db.yml
DATABASE_YML="${CONFIG_DIR}/database.yml"
DATABASE_YML_DIST="${DIST_DIR}/database.yml.dist"
# mail.yml
MAIL_YML="${CONFIG_DIR}/mail.yml"
MAIL_YML_DIST="${DIST_DIR}/mail.yml.dist"
# path.yml
PATH_YML="${CONFIG_DIR}/path.yml"
PATH_YML_DIST="${DIST_DIR}/path.yml.dist"
# constant.yml
CONSTANT_YML="${CONFIG_DIR}/constant.yml"
CONSTANT_YML_DIST="${DIST_DIR}/constant.yml.dist"

# ---------------------------------------------------------------------
# Configuration
# ---------------------------------------------------------------------
export ADMIN_MAIL=${ADMIN_MAIL:-"admin@example.com"}
export SHOP_NAME=${SHOP_NAME:-"EC-CUBE SHOP"}
export ROOT_DIR=${BASE_DIR}
export ROOT_URLPATH=${ROOT_URLPATH:-"/ec-cube/html"}
export ADMIN_ROUTE=${ADMIN_ROUTE:-"admin"}
export USER_DATA_ROUTE=${USER_DATA_ROUTE:-"user_data"}
export TEMPLATE_CODE=${TEMPLATE_CODE:-"default"}
export FORCE_SSL=${FORCE_SSL:-"0"}

export ADMINPASS="f6b126507a5d00dbdbb0f326fe855ddf84facd57c5603ffdf7e08fbb46bd633c"
export AUTH_MAGIC="droucliuijeanamiundpnoufrouphudrastiokec"

export DBSERVER=${DBSERVER-"127.0.0.1"}
export DBNAME=${DBNAME:-"cube3_dev"}
export DBUSER=${DBUSER:-"cube3_dev_user"}
export DBPASS=${DBPASS:-"password"}

export MAIL_BACKEND=${MAILER_BACKEND:-"smtp"}
export MAIL_HOST=${MAIL_HOST:-"localhost"}
export MAIL_PORT=${MAIL_PORT:-"25"}
export MAIL_USER=${MAIL_USER:-""}
export MAIL_PASS=${MAIL_PASS:-""}

DBTYPE=$1;
GET_COMPOSER=$2;

case "${DBTYPE}" in
"pgsql" )
    #-- DB Seting Postgres
    PSQL=psql
    PGUSER=postgres
    DROPDB=dropdb
    CREATEDB=createdb
    export DBPORT=5432
    export DBDRIVER=pdo_pgsql
;;
"mysql" )
    #-- DB Seting MySQL
    MYSQL=mysql
    ROOTUSER=root
    ROOTPASS=${DBPASS}
    DBSERVER=${DBSERVER}
    export DBPORT=3306
    export DBDRIVER=pdo_mysql
;;
* )
    echo "argument is invaid."
    echo ""
    echo "Usage: $0 [mysql|pgsql]"
    exit
    ;;
esac

# ---------------------------------------------------------------------
# Functions
# ---------------------------------------------------------------------
adjust_directory_permissions()
{
    chmod -R go+w "./html"
    chmod go+w "./app"
    chmod -R go+w "./app/template"
    chmod -R go+w "./app/cache"
    chmod -R go+w "./app/config"
    chmod go+w "./app/log"
    chmod go+w "./app/Plugin"
}

get_optional_sql()
{
    echo "INSERT INTO dtb_member (member_id, login_id, password, salt, work, del_flg, authority, creator_id, rank, update_date, create_date,name,department) VALUES (2, 'admin', '${ADMINPASS}', '${AUTH_MAGIC}', 1, 0, 0, 1, 1, current_timestamp, current_timestamp,'管理者','EC-CUBE SHOP');"
    echo "INSERT INTO dtb_base_info (id, shop_name, email01, email02, email03, email04, update_date, option_product_tax_rule) VALUES (1, '${SHOP_NAME}', '${ADMIN_MAIL}', '${ADMIN_MAIL}', '${ADMIN_MAIL}', '${ADMIN_MAIL}', current_timestamp, 0);"
}

render_config_template()
{
    printf "cat <<EOL\n`cat ${1}`\nEOL\n" | sh
}

# ---------------------------------------------------------------------
# Install
# ---------------------------------------------------------------------

# ---------------------------------
# Update Permissions
# ---------------------------------

echo "update permissions..."
adjust_directory_permissions

# ---------------------------------
# Create Configs
# ---------------------------------

echo "creating  ${CONFIG_YML}"
render_config_template ${CONFIG_YML_DIST} > ${CONFIG_YML}

echo  "eccube_install: 1" >> ${CONFIG_YML}

echo "creating  ${DATABASE_YML}"
render_config_template ${DATABASE_YML_DIST} > ${DATABASE_YML}

echo "creating  ${MAIL_YML}"
render_config_template ${MAIL_YML_DIST} > ${MAIL_YML}

echo "creating  ${PATH_YML}"
render_config_template ${PATH_YML_DIST} > ${PATH_YML}

#echo "creating  ${CONSTANT_YML}"
#echo "# overwrite or define new constant. " > ${CONSTANT_YML}
#echo "# see also ${CONSTANT_YML_DIST} " >> ${CONSTANT_YML}

# ---------------------------------
# Install Composer
# ---------------------------------

case "${GET_COMPOSER}" in
"none" )
echo "not get composer..."
;;
* )
echo "get composer..."
curl -sS https://getcomposer.org/installer | php

echo "install composer..."
php ./composer.phar install --dev --no-interaction
;;
esac

# ---------------------------------
# Setup Database
# ---------------------------------

case "${DBTYPE}" in
"pgsql" )
    # PostgreSQL
    echo "dropdb..."
    sudo -u ${PGUSER} ${DROPDB} ${DBNAME}

    echo "createdb..."
    sudo -u ${PGUSER} ${CREATEDB} -U ${DBUSER} ${DBNAME}

    echo "create table..."
    ./vendor/bin/doctrine orm:schema-tool:create

    echo "insert data..."
    sudo -u ${PGUSER} ${PSQL} -U ${DBUSER} -f ${SQL_DIR}/insert_data_pgsql.sql ${DBNAME}

    echo "execute optional SQL..."
    get_optional_sql | sudo -u ${PGUSER} ${PSQL} -U ${DBUSER} ${DBNAME}
;;
"mysql" )
    DBPASS=`echo $DBPASS | tr -d " "`
    if [ -n ${DBPASS} ]; then
        PASSOPT="--password=$DBPASS"
        CONFIGPASS=$DBPASS
    fi

    # MySQL
    echo "dropdb..."
    ${MYSQL} -u ${ROOTUSER} ${PASSOPT} -e "drop database \`${DBNAME}\`"

    echo "createdb..."
    ${MYSQL} -u ${ROOTUSER} ${PASSOPT} -e "create database \`${DBNAME}\` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;"

    #echo "grant user..."
    ${MYSQL} -u ${ROOTUSER} ${PASSOPT} -e "GRANT ALL ON \`${DBNAME}\`.* TO '${DBUSER}'@'%' IDENTIFIED BY '${DBPASS}'"

    echo "create table..."
    ./vendor/bin/doctrine orm:schema-tool:create

    echo "insert data..."
    ${MYSQL} -u ${DBUSER} ${PASSOPT} --default-character-set=utf8  ${DBNAME} < ${SQL_DIR}/insert_data_mysql.sql

    echo "execute optional SQL..."
    get_optional_sql | ${MYSQL} -u ${DBUSER} ${PASSOPT} ${DBNAME}
;;
esac

# DB migrator
php app/console migrations:migrate  --no-interaction

# Application cache clear
php app/console cache:clear --all

echo "Finished Successful!"
