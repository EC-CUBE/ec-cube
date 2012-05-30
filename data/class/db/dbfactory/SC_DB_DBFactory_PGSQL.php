<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * PostgreSQL 固有の処理をするクラス.
 *
 * このクラスを直接インスタンス化しないこと.
 * 必ず SC_DB_DBFactory クラスを経由してインスタンス化する.
 * また, SC_DB_DBFactory クラスの関数を必ずオーバーライドしている必要がある.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 * @version $Id:SC_DB_DBFactory_PGSQL.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class SC_DB_DBFactory_PGSQL extends SC_DB_DBFactory {

    /**
     * DBのバージョンを取得する.
     *
     * @param string $dsn データソース名
     * @return string データベースのバージョン
     */
    function sfGetDBVersion($dsn = '') {
        $objQuery =& SC_Query_Ex::getSingletonInstance($dsn);
        $val = $objQuery->getOne('select version()');
        $arrLine = explode(' ' , $val);
        return $arrLine[0] . ' ' . str_replace(',', '', $arrLine[1]);
    }

    /**
     * MySQL 用の SQL 文に変更する.
     *
     * DB_TYPE が PostgreSQL の場合は何もしない
     *
     * @access private
     * @param string $sql SQL 文
     * @return string MySQL 用に置換した SQL 文
     */
    function sfChangeMySQL($sql) {
        return $sql;
    }

    /**
     * 昨日の売上高・売上件数を算出する SQL を返す.
     *
     * @param string $method SUM または COUNT
     * @return string 昨日の売上高・売上件数を算出する SQL
     */
    function getOrderYesterdaySql($method) {
        return 'SELECT '.$method.'(total) FROM dtb_order '
               . 'WHERE del_flg = 0 '
               . "AND to_char(create_date,'YYYY/MM/DD') = to_char(CURRENT_TIMESTAMP - interval '1 days','YYYY/MM/DD') "
               . 'AND status <> ' . ORDER_CANCEL;
    }

    /**
     * 当月の売上高・売上件数を算出する SQL を返す.
     *
     * @param string $method SUM または COUNT
     * @return string 当月の売上高・売上件数を算出する SQL
     */
    function getOrderMonthSql($method) {
        return 'SELECT '.$method.'(total) FROM dtb_order '
               . 'WHERE del_flg = 0 '
               . "AND to_char(create_date,'YYYY/MM') = ? "
               . "AND to_char(create_date,'YYYY/MM/DD') <> to_char(CURRENT_TIMESTAMP,'YYYY/MM/DD') "
               . 'AND status <> ' . ORDER_CANCEL;
    }

    /**
     * 昨日のレビュー書き込み件数を算出する SQL を返す.
     *
     * @return string 昨日のレビュー書き込み件数を算出する SQL
     */
    function getReviewYesterdaySql() {
        return 'SELECT COUNT(*) FROM dtb_review AS A '
               . 'LEFT JOIN dtb_products AS B '
               . 'ON A.product_id = B.product_id '
               . 'WHERE A.del_flg=0 '
               . 'AND B.del_flg = 0 '
               . "AND to_char(A.create_date, 'YYYY/MM/DD') = to_char(CURRENT_TIMESTAMP - interval '1 days','YYYY/MM/DD') "
               . "AND to_char(A.create_date,'YYYY/MM/DD') != to_char(CURRENT_TIMESTAMP,'YYYY/MM/DD')";
    }

    /**
     * メール送信履歴の start_date の検索条件の SQL を返す.
     *
     * @return string 検索条件の SQL
     */
    function getSendHistoryWhereStartdateSql() {
        return "start_date BETWEEN current_timestamp + '- 5 minutes' AND current_timestamp + '5 minutes'";
    }

    /**
     * ダウンロード販売の検索条件の SQL を返す.
     *
     * @param string $dtb_order_alias
     * @return string 検索条件の SQL
     */
    function getDownloadableDaysWhereSql($dtb_order_alias = 'dtb_order') {
        $baseinfo = SC_Helper_DB_Ex::sfGetBasisData();
        //downloadable_daysにNULLが入っている場合(無期限ダウンロード可能時)もあるので、NULLの場合は0日に補正
        $downloadable_days = $baseinfo['downloadable_days'];
        // FIXME 怪しい比較「== null」
        if ($downloadable_days == null || $downloadable_days == '') {
            $downloadable_days = 0;
        }
        $sql = <<< __EOS__
            (
                SELECT
                    CASE
                        WHEN (SELECT d1.downloadable_days_unlimited FROM dtb_baseinfo d1) = 1 AND $dtb_order_alias.payment_date IS NOT NULL THEN 1
                        WHEN DATE(CURRENT_TIMESTAMP) <= DATE($dtb_order_alias.payment_date + interval '$downloadable_days days') THEN 1
                        ELSE 0
                    END
            )
__EOS__;
        return $sql;
    }

    /**
     * 売上集計の期間別集計のSQLを返す
     *
     * @param mixed $type
     * @return string 検索条件のSQL
     */
    function getOrderTotalDaysWhereSql($type) {
        switch ($type) {
            case 'month':
                $format = 'MM';
                break;
            case 'year':
                $format = 'YYYY';
                break;
            case 'wday':
                $format = 'Dy';
                break;
            case 'hour':
                $format = 'HH24';
                break;
            default:
                $format = 'YYYY-MM-DD';
                break;
        }

        return "to_char(create_date, '".$format."') AS str_date,
            COUNT(order_id) AS total_order,
            SUM(CASE WHEN order_sex = 1 THEN 1 ELSE 0 END) AS men,
            SUM(CASE WHEN order_sex = 2 THEN 1 ELSE 0 END) AS women,
            SUM(CASE WHEN customer_id <> 0 AND order_sex = 1 THEN 1 ELSE 0 END) AS men_member,
            SUM(CASE WHEN customer_id <> 0 AND order_sex = 2 THEN 1 ELSE 0 END) AS women_member,
            SUM(CASE WHEN customer_id = 0 AND order_sex = 1 THEN 1 ELSE 0 END) AS men_nonmember,
            SUM(CASE WHEN customer_id = 0 AND order_sex = 2 THEN 1 ELSE 0 END) AS women_nonmember,
            SUM(total) AS total,
            AVG(total) AS total_average";
    }

    /**
     * 売上集計の年代別集計の年代抽出部分のSQLを返す
     *
     * @return string 年代抽出部分の SQL
     */
    function getOrderTotalAgeColSql() {
        return 'TRUNC(CAST(EXTRACT(YEAR FROM AGE(create_date, order_birth)) AS INT), -1)';
    }

    /**
     * 文字列連結を行う.
     *
     * @param array $columns 連結を行うカラム名
     * @return string 連結後の SQL 文
     */
    function concatColumn($columns) {
        $sql = '';
        $i = 0;
        $total = count($columns);
        foreach ($columns as $column) {
            $sql .= $column;
            if ($i < $total -1) {
                $sql .= ' || ';
            }
            $i++;
        }
        return $sql;
    }

    /**
     * テーブルを検索する.
     *
     * 引数に部分一致するテーブル名を配列で返す.
     *
     * @deprecated SC_Query::listTables() を使用してください
     * @param string $expression 検索文字列
     * @return array テーブル名の配列
     */
    function findTableNames($expression = '') {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql = '   SELECT c.relname AS name, '
            .  '     CASE c.relkind '
            .  "     WHEN 'r' THEN 'table' "
            .  "     WHEN 'v' THEN 'view' END AS type "
            .  '     FROM pg_catalog.pg_class c '
            .  'LEFT JOIN pg_catalog.pg_namespace n '
            .  '       ON n.oid = c.relnamespace '
            .  "    WHERE c.relkind IN ('r','v') "
            .  "      AND n.nspname NOT IN ('pg_catalog', 'pg_toast') "
            .  '      AND pg_catalog.pg_table_is_visible(c.oid) '
            .  '      AND c.relname LIKE ?'
            .  ' ORDER BY 1,2;';
        $arrColList = $objQuery->getAll($sql, array('%' . $expression . '%'));
        $arrColList = SC_Utils_Ex::sfSwapArray($arrColList, false);
        return $arrColList[0];
    }

    /**
     * 文字コード情報を取得する
     *
     * @return array 文字コード情報
     */
    function getCharSet() {
        // 未実装
        return array();
    }

    /**
     * 擬似表を表すSQL文(FROM 句)を取得する
     *
     * @return string
     */
    function getDummyFromClauseSql() {
        return '';
    }
}
