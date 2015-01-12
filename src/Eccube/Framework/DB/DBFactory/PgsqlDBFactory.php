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

namespace Eccube\Framework\DB\DBFactory;

use Eccube\Application;
use Eccube\Framework\Query;
use Eccube\Framework\DB\DBFactory;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;

/**
 * PostgreSQL 固有の処理をするクラス.
 *
 * このクラスを直接インスタンス化しないこと.
 * 必ず \Eccube\Framework\DB\DBFactory クラスを経由してインスタンス化する.
 * また, \Eccube\Framework\DB\DBFactory クラスの関数を必ずオーバーライドしている必要がある.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 */
class PgsqlDBFactory extends DBFactory
{
    /**
     * DBのバージョンを取得する.
     *
     * @param  string $dsn データソース名
     * @return string データベースのバージョン
     */
    public function sfGetDBVersion($dsn = '')
    {
        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query', $dsn);
        $val = $objQuery->getOne('select version()');
        $arrLine = explode(' ', $val);

        return $arrLine[0] . ' ' . str_replace(',', '', $arrLine[1]);
    }

    /**
     * MySQL 用の SQL 文に変更する.
     *
     * DB_TYPE が PostgreSQL の場合は何もしない
     *
     * @access private
     * @param  string $sql SQL 文
     * @return string MySQL 用に置換した SQL 文
     */
    public function sfChangeMySQL($sql)
    {
        return $sql;
    }

    /**
     * 昨日の売上高・売上件数を算出する SQL を返す.
     *
     * @param  string $method SUM または COUNT
     * @return string 昨日の売上高・売上件数を算出する SQL
     */
    public function getOrderYesterdaySql($method)
    {
        return 'SELECT '.$method.'(total) FROM dtb_order '
               . 'WHERE del_flg = 0 '
               . "AND to_char(create_date,'YYYY/MM/DD') = to_char(CURRENT_TIMESTAMP - interval '1 days','YYYY/MM/DD') "
               . 'AND status <> ' . ORDER_CANCEL;
    }

    /**
     * 当月の売上高・売上件数を算出する SQL を返す.
     *
     * @param  string $method SUM または COUNT
     * @return string 当月の売上高・売上件数を算出する SQL
     */
    public function getOrderMonthSql($method)
    {
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
    public function getReviewYesterdaySql()
    {
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
    public function getSendHistoryWhereStartdateSql()
    {
        return "start_date BETWEEN current_timestamp + '- 5 minutes' AND current_timestamp + '5 minutes'";
    }

    /**
     * ダウンロード販売の検索条件の SQL を返す.
     *
     * @param  string $dtb_order_alias
     * @return string 検索条件の SQL
     */
    public function getDownloadableDaysWhereSql($dtb_order_alias = 'dtb_order')
    {
        $baseinfo = Application::alias('eccube.helper.db')->getBasisData();
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
     * @param  mixed  $type
     * @return string 検索条件のSQL
     */
    public function getOrderTotalDaysWhereSql($type)
    {
        switch ($type) {
            case 'month':
                $format = 'YYYY-MM';
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
    public function getOrderTotalAgeColSql()
    {
        return 'TRUNC(CAST(EXTRACT(YEAR FROM AGE(create_date, order_birth)) AS INT), -1)';
    }

    /**
     * 文字列連結を行う.
     *
     * @param  string[]  $columns 連結を行うカラム名
     * @return string 連結後の SQL 文
     */
    public function concatColumn($columns)
    {
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
     * @deprecated Query::listTables() を使用してください
     * @param  string $expression 検索文字列
     * @return array  テーブル名の配列
     */
    public function findTableNames($expression = '')
    {
        $objQuery = Application::alias('eccube.query');
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
        $arrColList = Utils::sfSwapArray($arrColList, false);

        return $arrColList[0];
    }

    /**
     * 文字コード情報を取得する
     *
     * @return array 文字コード情報
     */
    public function getCharSet()
    {
        // 未実装
        return array();
    }

    /**
     * 擬似表を表すSQL文(FROM 句)を取得する
     *
     * @return string
     */
    public function getDummyFromClauseSql()
    {
        return '';
    }

    /**
     * テーブル一覧を取得する
     *
     * MDB2_Driver_Manager_pgsql#listTables の不具合回避を目的として独自実装している。
     * @return array テーブル名の配列
     */
    public function listTables(Query &$objQuery)
    {
        $col = 'tablename';
        $from = 'pg_tables';
        $where = "schemaname NOT IN ('pg_catalog', 'information_schema', 'sys')";

        return $objQuery->getCol($col, $from, $where);
    }

    /**
     * 商品詳細の SQL を取得する.
     *
     * PostgreSQL 用にチューニング。
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
        $sub_base = "FROM dtb_products_class WHERE del_flg = 0 AND product_id = dtb_products.product_id $where_products_class";
        $sql = <<< __EOS__
            (
                SELECT
                     dtb_products.*
                    ,dtb_maker.name AS maker_name
                    ,(SELECT MIN(product_code) $sub_base) AS product_code_min
                    ,(SELECT MAX(product_code) $sub_base) AS product_code_max
                    ,(SELECT MIN(price01) $sub_base) AS price01_min
                    ,(SELECT MAX(price01) $sub_base) AS price01_max
                    ,(SELECT MIN(price02) $sub_base) AS price02_min
                    ,(SELECT MAX(price02) $sub_base) AS price02_max
                    ,(SELECT MIN(stock) $sub_base) AS stock_min
                    ,(SELECT MAX(stock) $sub_base) AS stock_max
                    ,(SELECT MIN(stock_unlimited) $sub_base) AS stock_unlimited_min
                    ,(SELECT MAX(stock_unlimited) $sub_base) AS stock_unlimited_max
                    ,(SELECT MAX(point_rate) $sub_base) AS point_rate
                    ,(SELECT MAX(deliv_fee) $sub_base) AS deliv_fee
                FROM dtb_products
                    LEFT JOIN dtb_maker
                        ON dtb_products.maker_id = dtb_maker.maker_id
                WHERE EXISTS(SELECT * $sub_base)
            ) AS alldtl
__EOS__;

        return $sql;
    }
}
