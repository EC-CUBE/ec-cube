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
 * MySQL 固有の処理をするクラス.
 *
 * このクラスを直接インスタンス化しないこと.
 * 必ず SC_DB_DBFactory クラスを経由してインスタンス化する.
 * また, SC_DB_DBFactory クラスの関数を必ずオーバーライドしている必要がある.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 * @version $Id:SC_DB_DBFactory_MYSQL.php 15267 2007-08-09 12:31:52Z nanasess $
 */
class SC_DB_DBFactory_MYSQL extends SC_DB_DBFactory {

    /** SC_Query インスタンス */
    var $objQuery;

    /**
     * DBのバージョンを取得する.
     *
     * @param string $dsn データソース名
     * @return string データベースのバージョン
     */
    function sfGetDBVersion($dsn = '') {
        $objQuery =& SC_Query_Ex::getSingletonInstance($dsn);
        $val = $objQuery->getOne('select version()');
        return 'MySQL ' . $val;
    }

    /**
     * MySQL 用の SQL 文に変更する.
     *
     * @access private
     * @param string $sql SQL 文
     * @return string MySQL 用に置換した SQL 文
     */
    function sfChangeMySQL($sql) {
        // 改行、タブを1スペースに変換
        $sql = preg_replace("/[\r\n\t]/",' ',$sql);
        // ILIKE検索をLIKE検索に変換する
        $sql = $this->sfChangeILIKE($sql);
        // RANDOM()をRAND()に変換する
        $sql = $this->sfChangeRANDOM($sql);
        // TRUNCをTRUNCATEに変換する
        $sql = $this->sfChangeTrunc($sql);
        // ARRAY_TO_STRINGをGROUP_CONCATに変換する
        $sql = $this->sfChangeArrayToString($sql);
        return $sql;
    }

    /**
     * 文字コード情報を取得する
     *
     * @return array 文字コード情報
     */
    function getCharSet() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrRet = $objQuery->getAll("SHOW VARIABLES LIKE 'char%'");
        return $arrRet;
    }

    /**
     * 昨日の売上高・売上件数を算出する SQL を返す.
     *
     * @param string $method SUM または COUNT
     * @return string 昨日の売上高・売上件数を算出する SQL
     */
    function getOrderYesterdaySql($method) {
        return 'SELECT ' . $method . '(total) FROM dtb_order '
               . 'WHERE del_flg = 0 '
               . 'AND cast(create_date as date) = DATE_ADD(current_date, interval -1 day) '
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
               . "AND date_format(create_date, '%Y/%m') = ? "
               . "AND date_format(create_date, '%Y/%m/%d') <> date_format(CURRENT_TIMESTAMP, '%Y/%m/%d') "
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
               . 'WHERE A.del_flg = 0 '
               . 'AND B.del_flg = 0 '
               . 'AND cast(A.create_date as date) = DATE_ADD(current_date, interval -1 day) '
               . 'AND cast(A.create_date as date) != current_date';
    }

    /**
     * メール送信履歴の start_date の検索条件の SQL を返す.
     *
     * @return string 検索条件の SQL
     */
    function getSendHistoryWhereStartdateSql() {
        return 'start_date BETWEEN date_add(CURRENT_TIMESTAMP,INTERVAL -5 minute) AND date_add(CURRENT_TIMESTAMP,INTERVAL 5 minute)';
    }

    /**
     * ダウンロード販売の検索条件の SQL を返す.
     *
     * @param string $dtb_order_alias
     * @return string 検索条件の SQL
     */
    function getDownloadableDaysWhereSql($dtb_order_alias = 'dtb_order') {
        return '(SELECT IF((SELECT d1.downloadable_days_unlimited FROM dtb_baseinfo d1)=1, 1, DATE(CURRENT_TIMESTAMP) <= DATE(DATE_ADD(' . $dtb_order_alias . '.payment_date, INTERVAL (SELECT downloadable_days FROM dtb_baseinfo) DAY))))';
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
                $format = '%m';
                break;
            case 'year':
                $format = '%Y';
                break;
            case 'wday':
                $format = '%a';
                break;
            case 'hour':
                $format = '%H';
                break;
            default:
                $format = '%Y-%m-%d';
                break;
        }

        return " date_format(create_date, '".$format."') AS str_date,
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
        return 'TRUNC((YEAR(create_date) - YEAR(order_birth)) - (RIGHT(create_date, 5) < RIGHT(order_birth, 5)), -1)';
    }

    /**
     * 文字列連結を行う.
     *
     * @param array $columns 連結を行うカラム名
     * @return string 連結後の SQL 文
     */
    function concatColumn($columns) {
        $sql = 'concat(';
        $i = 0;
        $total = count($columns);
        foreach ($columns as $column) {
            $sql .= $column;
            if ($i < $total -1) {
                $sql .= ', ';
            }
            $i++;
        }
        $sql .= ')';
        return $sql;
    }

    /**
     * テーブルを検索する.
     *
     * 引数に部分一致するテーブル名を配列で返す.
     *
     * @param string $expression 検索文字列
     * @return array テーブル名の配列
     */
    function findTableNames($expression = '') {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql = 'SHOW TABLES LIKE '. $objQuery->quote('%' . $expression . '%');
        $arrColList = $objQuery->getAll($sql);
        $arrColList = SC_Utils_Ex::sfSwapArray($arrColList, false);
        return $arrColList[0];
    }

    /**
     * ILIKE句 を LIKE句へ変換する.
     *
     * @access private
     * @param string $sql SQL文
     * @return string 変換後の SQL 文
     */
    function sfChangeILIKE($sql) {
        $changesql = preg_replace('/(^|[^\w])ILIKE([^\w]|$)/i', '$1LIKE$2', $sql);
        return $changesql;
    }

    /**
     * RANDOM() を RAND() に変換する.
     *
     * @access private
     * @param string $sql SQL文
     * @return string 変換後の SQL 文
     */
    function sfChangeRANDOM($sql) {
        $changesql = preg_replace('/(^|[^\w])RANDOM\(/i', '$1RAND(', $sql);
        return $changesql;
    }

    /**
     * TRUNC() を TRUNCATE() に変換する.
     *
     * @access private
     * @param string $sql SQL文
     * @return string 変換後の SQL 文
     */
    function sfChangeTrunc($sql) {
        $changesql = preg_replace('/(^|[^\w])TRUNC([^\w]|$)/i', '$1TRUNCATE$2', $sql);
        return $changesql;
    }

    /**
     * ARRAY_TO_STRING(ARRAY(A),B) を GROUP_CONCAT() に変換する.
     *
     * @access private
     * @param string $sql SQL文
     * @return string 変換後の SQL 文
     */
    function sfChangeArrayToString($sql) {
        if (strpos(strtoupper($sql), 'ARRAY_TO_STRING') !== FALSE) {
            preg_match_all('/ARRAY_TO_STRING.*?\(.*?ARRAY\(.*?SELECT (.+?) FROM (.+?) WHERE (.+?)\).*?\,.*?\'(.+?)\'.*?\)/is', $sql, $match, PREG_SET_ORDER);

            foreach ($match as $item) {
                $replace = 'GROUP_CONCAT(' . $item[1] . ' SEPARATOR \'' . $item[4] . '\') FROM ' . $item[2] . ' WHERE ' . $item[3];
                $sql = str_replace($item[0], $replace, $sql);
            }
        }
        return $sql;
    }

    /**
     * インデックス作成の追加定義を取得する
     *
     * 引数に部分一致するテーブル名を配列で返す.
     *
     * @param string $table 対象テーブル名
     * @param string $name 対象カラム名
     * @return array インデックス設定情報配列
     */
    function sfGetCreateIndexDefinition($table, $name, $definition) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrTblInfo = $objQuery->getTableInfo($table);
        foreach ($arrTblInfo as $fieldInfo) {
            if (array_key_exists($fieldInfo['name'], $definition['fields'])) {
                if ($fieldInfo['nativetype'] == 'text') {
                    // TODO: text型フィールドの場合に255文字以内決めうちでインデックス列のサイズとして
                    //       指定して良いか確認は必要。
                    $definition['fields'][$fieldInfo['name']]['length'] = '255';
                }
            }
        }
        return $definition;
    }

    /**
     * 擬似表を表すSQL文(FROM 句)を取得する
     *
     * @return string
     */
    function getDummyFromClauseSql() {
        return 'FROM DUAL';
    }

    /**
     * 各 DB に応じた SC_Query での初期化を行う
     *
     * @param SC_Query $objQuery SC_Query インスタンス
     * @return void
     */
    function initObjQuery(SC_Query &$objQuery) {
        $objQuery->exec('SET SESSION storage_engine = InnoDB');
        $objQuery->exec("SET SESSION sql_mode = 'ANSI'");
        $objQuery->exec("SET TIME_ZONE = ?", array(date('P')));
    }
}
