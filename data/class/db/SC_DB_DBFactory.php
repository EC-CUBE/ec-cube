<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once($include_dir . "/../data/class/db/dbfactory/SC_DB_DBFactory_MYSQL.php");
require_once($include_dir . "/../data/class/db/dbfactory/SC_DB_DBFactory_PGSQL.php");

/**
 * DBに依存した処理を抽象化するファクトリークラス.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_DB_DBFactory {

    /**
     * DB_TYPE に応じた DBFactory インスタンスを生成する.
     *
     * @return mixed DBFactory インスタンス
     */
    function getInstance() {
        switch (DB_TYPE) {
        case "mysql":
            return new SC_DB_DBFactory_MYSQL();
            break;

        case "pgsql":
            return new SC_DB_DBFactory_PGSQL();
            break;

        default:
        }
    }

    /**
     * データソース名を取得する.
     *
     * 引数 $dsn が空の場合は, DEFAULT_DSN の値を返す.
     * DEFAULT_DSN が未定義の場合は void となる.
     * $dsn が空ではない場合は, $dsn の値を返す.
     *
     * @param string $dsn データソース名
     * @return void|string データソース名
     */
    function getDSN($dsn = "") {
        if(empty($dsn)) {
            if(defined('DEFAULT_DSN')) {
                $dsn = DEFAULT_DSN;
            } else {
                return;
            }
        }
        return $dsn;
    }

    /**
     * DBのバージョンを取得する.
     *
     * @param string $dsn データソース名
     * @return string データベースのバージョン
     */
    function sfGetDBVersion($dsn = "") { return null; }

    /**
     * MySQL 用の SQL 文に変更する.
     *
     * @param string $sql SQL 文
     * @return string MySQL 用に置換した SQL 文
     */
    function sfChangeMySQL($sql) { return null; }

    /**
     * テーブルの存在チェックを行う SQL 文を返す.
     *
     * @return string テーブルの存在チェックを行う SQL 文
     */
    function getTableExistsSql() { return null; }

    /**
     * インデックスの検索結果を配列で返す.
     *
     * @param string $index_name インデックス名
     * @param string $table_name テーブル名
     * @return array インデックスの検索結果の配列
     */
    function getTableIndex($index_name, $table_name = "") { return array(); }

    /**
     * インデックスを作成する.
     *
     * @param string $index_name インデックス名
     * @param string $table_name テーブル名
     * @param string $col_name カラム名
     * @param integer $length 作成するインデックスのバイト長
     * @return void
     */
    function createTableIndex($index_name, $table_name, $col_name, $length = 0) {}

    /**
     * テーブルのカラム一覧を取得する.
     *
     * @param string $table_name テーブル名
     * @return array テーブルのカラム一覧の配列
     */
    function sfGetColumnList($table_name) { array(); }
}
?>
