<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
        $objQuery = new SC_Query($this->getDSN($dsn), true, true);
        list($db_type) = split(":", $dsn);
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
     * テーブルの存在チェックを行う SQL 文を返す.
     *
     * @return string テーブルの存在チェックを行う SQL 文
     */
    function getTableExistsSql() {
        return "  SELECT relname "
             . "    FROM pg_class "
             . "   WHERE (relkind = 'r' OR relkind = 'v') "
             . "     AND relname = ? "
             . "GROUP BY relname";
    }

    /**
     * インデックスの検索結果を配列で返す.
     *
     * @param string $index_name インデックス名
     * @param string $table_name テーブル名（PostgreSQL では使用しない）
     * @return array インデックスの検索結果の配列
     */
    function getTableIndex($index_name, $table_name = "") {
        $objQuery = new SC_Query("", true, true);
        return $objQuery->getAll("SELECT relname FROM pg_class WHERE relname = ?",
                                 array($index_name));
    }

    /**
     * インデックスを作成する.
     *
     * @param string $index_name インデックス名
     * @param string $table_name テーブル名
     * @param string $col_name カラム名
     * @param integer $length 作成するインデックスのバイト長
     * @return void
     */
    function createTableIndex($index_name, $table_name, $col_name, $length = 0) {
        $objQuery = new SC_Query($dsn, true, true);
        $objQuery->query("CREATE INDEX ? ON ? (?)", array($index_name, $table_name, $col_name));
    }

    /**
     * テーブルのカラム一覧を取得する.
     *
     * @param string $table_name テーブル名
     * @return array テーブルのカラム一覧の配列
     */
    function sfGetColumnList($table_name) {
        $objQuery = new SC_Query();
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
     * @param string $expression 検索文字列
     * @return array テーブル名の配列
     */
    function findTableNames($expression = "") {
        $objQuery = new SC_Query();
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
