<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */


// {{{ requires
require_once($include_dir . "/../data/class/db/SC_DB_DBFactory.php"); // FIXME

/**
 * PostgreSQL 固有の処理をするクラス.
 *
 * このクラスを直接インスタンス化しないこと.
 * 必ず SC_DB_DBFactory クラスを経由してインスタンス化する.
 * また, SC_DB_DBFactory クラスの関数を必ずオーバーライドしている必要がある.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 * @version $Id$
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
}
?>
