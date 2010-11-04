<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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


// {{{ requires
require_once(CLASS_PATH . "db/SC_DB_DBFactory.php");

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
    function sfGetDBVersion($dsn = "") {
        $objQuery =& SC_Query::getSingletonInstance();
        $val = $objQuery->getOne("select version()");
        $arrLine = split(" " , $val);
        return $arrLine[0] . " " . $arrLine[1];
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
    function sfChangeMySQL($sql){
        return $sql;
    }

    /**
     * 昨日の売上高・売上件数を算出する SQL を返す.
     *
     * @param string $method SUM または COUNT
     * @return string 昨日の売上高・売上件数を算出する SQL
     */
    function getOrderYesterdaySql($method) {
        return "SELECT ".$method."(total) FROM dtb_order "
              . "WHERE del_flg = 0 "
                . "AND to_char(create_date,'YYYY/MM/DD') = to_char(now() - interval '1 days','YYYY/MM/DD') "
                . "AND status <> " . ORDER_CANCEL;
    }

    /**
     * 当月の売上高・売上件数を算出する SQL を返す.
     *
     * @param string $method SUM または COUNT
     * @return string 当月の売上高・売上件数を算出する SQL
     */
    function getOrderMonthSql($method) {
        return "SELECT ".$method."(total) FROM dtb_order "
              . "WHERE del_flg = 0 "
                . "AND to_char(create_date,'YYYY/MM') = ? "
                . "AND to_char(create_date,'YYYY/MM/DD') <> to_char(now(),'YYYY/MM/DD') "
                . "AND status <> " . ORDER_CANCEL;
    }

    /**
     * 昨日のレビュー書き込み件数を算出する SQL を返す.
     *
     * @return string 昨日のレビュー書き込み件数を算出する SQL
     */
    function getReviewYesterdaySql() {
        return "SELECT COUNT(*) FROM dtb_review AS A "
          . "LEFT JOIN dtb_products AS B "
                 . "ON A.product_id = B.product_id "
              . "WHERE A.del_flg=0 "
                . "AND B.del_flg = 0 "
                . "AND to_char(A.create_date, 'YYYY/MM/DD') = to_char(now() - interval '1 days','YYYY/MM/DD') "
                . "AND to_char(A.create_date,'YYYY/MM/DD') != to_char(now(),'YYYY/MM/DD')";
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
    function getDownloadableDaysWhereSql() {
        $baseinfo = SC_Helper_DB_Ex::sf_getBasisData();
        //downloadable_daysにNULLが入っている場合(無期限ダウンロード可能時)もあるので、NULLの場合は0日に補正
        $downloadable_days = $baseinfo['downloadable_days'];
        if($downloadable_days ==null || $downloadable_days == "")$downloadable_days=0;
        return "(SELECT CASE WHEN (SELECT d1.downloadable_days_unlimited FROM dtb_baseinfo d1) = 1 AND o.payment_date IS NOT NULL THEN 1 WHEN DATE(NOW()) <= DATE(o.payment_date + '". $downloadable_days ." days') THEN 1 ELSE 0 END)";
    }

    /**
     * 文字列連結を行う.
     *
     * @param array $columns 連結を行うカラム名
     * @return string 連結後の SQL 文
     */
    function concatColumn($columns) {
        $sql = "";
        $i = 0;
        $total = count($columns);
        foreach ($columns as $column) {
            $sql .= $column;
            if ($i < $total -1) {
                $sql .= " || ";
            }
            $i++;
        }
        return $sql;
    }

    /**
     * テーブルのカラム一覧を取得する.
     *
     * @deprecated SC_Query::listTableFields() を使用してください
     * @param string $table_name テーブル名
     * @return array テーブルのカラム一覧の配列
     */
    function sfGetColumnList($table_name) {
        $objQuery =& SC_Query::getSingletonInstance();
        $sql = "  SELECT a.attname "
             . "    FROM pg_class c, pg_attribute a "
             . "   WHERE c.relname=? "
             . "     AND c.oid=a.attrelid "
             . "     AND a.attnum > 0 "
             . "     AND not a.attname "
             . "    LIKE '........pg.dropped.%........' "
             . "ORDER BY a.attnum";
        $arrColList = $objQuery->getAll($sql, array($table_name));
        $arrColList = SC_Utils_Ex::sfswaparray($arrColList);
        return $arrColList["attname"];
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
    function findTableNames($expression = "") {
        $objQuery =& SC_Query::getSingletonInstance();
        $sql = "   SELECT c.relname AS name, "
            .  "     CASE c.relkind "
            .  "     WHEN 'r' THEN 'table' "
            .  "     WHEN 'v' THEN 'view' END AS type "
            .  "     FROM pg_catalog.pg_class c "
            .  "LEFT JOIN pg_catalog.pg_namespace n "
            .  "       ON n.oid = c.relnamespace "
            .  "    WHERE c.relkind IN ('r','v') "
            .  "      AND n.nspname NOT IN ('pg_catalog', 'pg_toast') "
            .  "      AND pg_catalog.pg_table_is_visible(c.oid) "
            .  "      AND c.relname LIKE ?"
            .  " ORDER BY 1,2;";
        $arrColList = $objQuery->getAll($sql, array("%" . $expression . "%"));
        $arrColList = SC_Utils_Ex::sfswaparray($arrColList, false);
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
}
?>
