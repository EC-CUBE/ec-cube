<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework\DB;

use Eccube\Application;
use Eccube\Framework\Query;
use Eccube\Framework\DB\DBFactory\MysqlDBFactory;
use Eccube\Framework\DB\DBFactory\PgsqlDBFactory;
use Eccube\Framework\Util\Utils;

/**
 * DBに依存した処理を抽象化するファクトリークラス.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 */
class DBFactory
{
    /**
     * DB_TYPE に応じた DBFactory インスタンスを生成する.
     *
     * @param  string $db_type 任意のインスタンスを返したい場合は DB_TYPE 文字列を指定
     * @return DBFactory  DBFactory インスタンス
     */
    public static function getInstance($db_type = DB_TYPE)
    {
        switch ($db_type) {
            case 'mysql':
                return new MysqlDBFactory();

            case 'pgsql':
                return new PgsqlDBFactory();

            default:
                return new static();
        }
    }

    /**
     * データソース名を取得する.
     *
     * 引数 $dsn が空でデータソースが定義済みの場合はDB接続パラメータの連想配列を返す
     * DEFAULT_DSN が未定義の場合は void となる.
     * $dsn が空ではない場合は, $dsn の値を返す.
     *
     * @param  string $dsn データソース名
     * @return string データソース名またはDB接続パラメータの連想配列
     */
    public function getDSN($dsn = '')
    {
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
     * @param  string $dsn データソース名
     * @return string データベースのバージョン
     */
    public function sfGetDBVersion($dsn = '')
    {
        return null;
    }

    /**
     * MySQL 用の SQL 文に変更する.
     *
     * @param  string $sql SQL 文
     * @return string MySQL 用に置換した SQL 文
     */
    public function sfChangeMySQL($sql)
    {
        return null;
    }

    /**
     * 昨日の売上高・売上件数を算出する SQL を返す.
     *
     * @param  string $method SUM または COUNT
     * @return string 昨日の売上高・売上件数を算出する SQL
     */
    public function getOrderYesterdaySql($method)
    {
        return null;
    }

    /**
     * 当月の売上高・売上件数を算出する SQL を返す.
     *
     * @param  string $method SUM または COUNT
     * @return string 当月の売上高・売上件数を算出する SQL
     */
    public function getOrderMonthSql($method)
    {
        return null;
    }

    /**
     * 昨日のレビュー書き込み件数を算出する SQL を返す.
     *
     * @return string 昨日のレビュー書き込み件数を算出する SQL
     */
    public function getReviewYesterdaySql()
    {
        return null;
    }

    /**
     * メール送信履歴の start_date の検索条件の SQL を返す.
     *
     * @return string 検索条件の SQL
     */
    public function getSendHistoryWhereStartdateSql()
    {
        return null;
    }

    /**
     * ダウンロード販売の検索条件の SQL を返す.
     *
     * @return string 検索条件の SQL
     */
    public function getDownloadableDaysWhereSql()
    {
        return null;
    }

    /**
     * 文字列連結を行う.
     *
     * @param  string[]  $columns 連結を行うカラム名
     * @return string 連結後の SQL 文
     */
    public function concatColumn($columns)
    {
        return null;
    }

    /**
     * テーブルを検索する.
     *
     * 引数に部分一致するテーブル名を配列で返す.
     *
     * @deprecated Query::listTables() を使用してください
     * @param  string $expression 検索文字列
     * @return array  テーブル名の配列
     */
    public function findTableNames($expression = '')
    {
        return array();
    }

    /**
     * インデックス作成の追加定義を取得する
     *
     * 引数に部分一致するテーブル名を配列で返す.
     *
     * @param  string $table 対象テーブル名
     * @param  string $name  対象カラム名
     * @return array  インデックス設定情報配列
     */
    public function sfGetCreateIndexDefinition($table, $name, $definition)
    {
        return $definition;
    }

    /**
     * 各 DB に応じた Query での初期化を行う
     *
     * @param  Query $objQuery Query インスタンス
     * @return void
     */
    public function initObjQuery(Query &$objQuery)
    {
    }

    /**
     * テーブル一覧を取得する
     *
     * @return array テーブル名の配列
     */
    public function listTables(Query &$objQuery)
    {
        $objManager =& $objQuery->conn->loadModule('Manager');

        return $objManager->listTables();
    }

    /**
     * SQL 文に OFFSET, LIMIT を付加する。
     *
     * @param string 元の SQL 文
     * @param integer LIMIT
     * @param integer OFFSET
     * @return string 付加後の SQL 文
     */
    function addLimitOffset($sql, $limit = 0, $offset = 0)
    {
        if ($limit != 0) {
            $sql .= " LIMIT $limit";
        }
        if (strlen($offset) === 0) {
            $offset = 0;
        }
        $sql .= " OFFSET $offset";

        return $sql;
    }

    /**
     * 商品詳細の SQL を取得する.
     *
     * @param  string $where_products_class 商品規格情報の WHERE 句
     * @return string 商品詳細の SQL
     */
    public function alldtlSQL($where_products_class = '')
    {
        if (!Utils::isBlank($where_products_class)) {
            $where_products_class = 'AND (' . $where_products_class . ')';
        }
        /*
         * point_rate, deliv_fee は商品規格(dtb_products_class)ごとに保持しているが,
         * 商品(dtb_products)ごとの設定なので MAX のみを取得する.
         */
        $sql = <<< __EOS__
            (
                SELECT
                     dtb_products.*
                    ,T4.product_code_min
                    ,T4.product_code_max
                    ,T4.price01_min
                    ,T4.price01_max
                    ,T4.price02_min
                    ,T4.price02_max
                    ,T4.stock_min
                    ,T4.stock_max
                    ,T4.stock_unlimited_min
                    ,T4.stock_unlimited_max
                    ,T4.point_rate
                    ,T4.deliv_fee
                    ,dtb_maker.name AS maker_name
                FROM dtb_products
                    INNER JOIN (
                        SELECT product_id
                            ,MIN(product_code) AS product_code_min
                            ,MAX(product_code) AS product_code_max
                            ,MIN(price01) AS price01_min
                            ,MAX(price01) AS price01_max
                            ,MIN(price02) AS price02_min
                            ,MAX(price02) AS price02_max
                            ,MIN(stock) AS stock_min
                            ,MAX(stock) AS stock_max
                            ,MIN(stock_unlimited) AS stock_unlimited_min
                            ,MAX(stock_unlimited) AS stock_unlimited_max
                            ,MAX(point_rate) AS point_rate
                            ,MAX(deliv_fee) AS deliv_fee
                        FROM dtb_products_class
                        WHERE del_flg = 0 $where_products_class
                        GROUP BY product_id
                    ) AS T4
                        ON dtb_products.product_id = T4.product_id
                    LEFT JOIN dtb_maker
                        ON dtb_products.maker_id = dtb_maker.maker_id
            ) AS alldtl
__EOS__;

        return $sql;
    }
}
