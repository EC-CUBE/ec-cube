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
 * DBに依存した処理を抽象化するファクトリークラス.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 * @version $Id:SC_DB_DBFactory.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class SC_DB_DBFactory {

    /**
     * DB_TYPE に応じた DBFactory インスタンスを生成する.
     *
     * @param string $db_type 任意のインスタンスを返したい場合は DB_TYPE 文字列を指定
     * @return mixed DBFactory インスタンス
     */
    function getInstance($db_type = DB_TYPE) {
        switch ($db_type) {
            case 'mysql':
                return new SC_DB_DBFactory_MYSQL();

            case 'pgsql':
                return new SC_DB_DBFactory_PGSQL();

            default:
                return new SC_DB_DBFactory();
        }
    }

    /**
     * データソース名を取得する.
     *
     * 引数 $dsn が空でデータソースが定義済みの場合はDB接続パラメータの連想配列を返す
     * DEFAULT_DSN が未定義の場合は void となる.
     * $dsn が空ではない場合は, $dsn の値を返す.
     *
     * @param mixed $dsn データソース名
     * @return mixed データソース名またはDB接続パラメータの連想配列
     */
    function getDSN($dsn = '') {
        if (empty($dsn)) {
            if (defined('DEFAULT_DSN')) {
                $dsn = array('phptype'  => DB_TYPE,
                             'username' => DB_USER,
                             'password' => DB_PASSWORD,
                             'protocol' => 'tcp',
                             'hostspec' => DB_SERVER,
                             'port'     => DB_PORT,
                             'database' => DB_NAME
                             );
            } else {
                return '';
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
    function sfGetDBVersion($dsn = '') { return null; }

    /**
     * MySQL 用の SQL 文に変更する.
     *
     * @param string $sql SQL 文
     * @return string MySQL 用に置換した SQL 文
     */
    function sfChangeMySQL($sql) { return null; }

    /**
     * 昨日の売上高・売上件数を算出する SQL を返す.
     *
     * @param string $method SUM または COUNT
     * @return string 昨日の売上高・売上件数を算出する SQL
     */
    function getOrderYesterdaySql($method) { return null; }

    /**
     * 当月の売上高・売上件数を算出する SQL を返す.
     *
     * @param string $method SUM または COUNT
     * @return string 当月の売上高・売上件数を算出する SQL
     */
    function getOrderMonthSql($method) { return null; }

    /**
     * 昨日のレビュー書き込み件数を算出する SQL を返す.
     *
     * @return string 昨日のレビュー書き込み件数を算出する SQL
     */
    function getReviewYesterdaySql() { return null; }

    /**
     * メール送信履歴の start_date の検索条件の SQL を返す.
     *
     * @return string 検索条件の SQL
     */
    function getSendHistoryWhereStartdateSql() { return null; }

    /**
     * ダウンロード販売の検索条件の SQL を返す.
     *
     * @return string 検索条件の SQL
     */
    function getDownloadableDaysWhereSql() { return null; }

    /**
     * 文字列連結を行う.
     *
     * @param array $columns 連結を行うカラム名
     * @return string 連結後の SQL 文
     */
    function concatColumn($columns) { return null; }

    /**
     * テーブルを検索する.
     *
     * 引数に部分一致するテーブル名を配列で返す.
     *
     * @deprecated SC_Query::listTables() を使用してください
     * @param string $expression 検索文字列
     * @return array テーブル名の配列
     */
    function findTableNames($expression = '') { return array(); }

    /**
     * インデックス作成の追加定義を取得する
     *
     * 引数に部分一致するテーブル名を配列で返す.
     *
     * @param string $table 対象テーブル名
     * @param string $name 対象カラム名
     * @return array インデックス設定情報配列
     */
    function sfGetCreateIndexDefinition($table, $name, $definition) { return $definition; }

    /**
     * 各 DB に応じた SC_Query での初期化を行う
     *
     * @param SC_Query $objQuery SC_Query インスタンス
     * @return void
     */
    function initObjQuery(SC_Query &$objQuery) {
    }
}
