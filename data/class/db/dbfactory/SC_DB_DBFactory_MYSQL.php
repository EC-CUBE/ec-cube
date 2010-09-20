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
    function sfGetDBVersion($dsn = "") {
        $objQuery =& SC_Query::getSingletonInstance();
        $val = $objQuery->getOne("select version()");
        return "MySQL " . $val;
    }

    /**
     * MySQL 用の SQL 文に変更する.
     *
     * @access private
     * @param string $sql SQL 文
     * @return string MySQL 用に置換した SQL 文
     */
    function sfChangeMySQL($sql){
        // 改行、タブを1スペースに変換
        $sql = preg_replace("/[\r\n\t]/"," ",$sql);
        // view表をインラインビューに変換する
        $sql = $this->sfChangeView($sql);
        // ILIKE検索をLIKE検索に変換する
        $sql = $this->sfChangeILIKE($sql);
        // RANDOM()をRAND()に変換する
        $sql = $this->sfChangeRANDOM($sql);
        // TRUNCをTRUNCATEに変換する
        $sql = $this->sfChangeTrunc($sql);
        return $sql;
    }

    /**
     * 文字コード情報を取得する
     *
     * @return array 文字コード情報
     */
    function getCharSet() {
        $objQuery =& SC_Query::getSingletonInstance();
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
        return "SELECT ".$method."(total) FROM dtb_order "
              . "WHERE del_flg = 0 "
                . "AND cast(create_date as date) = DATE_ADD(current_date, interval -1 day) "
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
                . "AND date_format(create_date, '%Y/%m') = ? "
                . "AND date_format(create_date, '%Y/%m/%d') <> date_format(now(), '%Y/%m/%d') "
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
              . "WHERE A.del_flg = 0 "
                . "AND B.del_flg = 0 "
                . "AND cast(A.create_date as date) = DATE_ADD(current_date, interval -1 day) "
                . "AND cast(A.create_date as date) != current_date";
    }

    /**
     * メール送信履歴の start_date の検索条件の SQL を返す.
     *
     * @return string 検索条件の SQL
     */
    function getSendHistoryWhereStartdateSql() {
        return "start_date BETWEEN date_add(now(),INTERVAL -5 minute) AND date_add(now(),INTERVAL 5 minute)";
    }

    /**
     * ダウンロード販売の検索条件の SQL を返す.
     *
     * @param string $dtb_order_alias
     * @return string 検索条件の SQL
     */
    function getDownloadableDaysWhereSql($dtb_order_alias) {
        return "(SELECT IF((SELECT d1.downloadable_days_unlimited FROM dtb_baseinfo d1)=1, 1, DATE(NOW()) <= DATE(DATE_ADD(" . $dtb_order_alias . ".commit_date, INTERVAL (SELECT downloadable_days FROM dtb_baseinfo) DAY))))";
    }

    /**
     * 文字列連結を行う.
     *
     * @param array $columns 連結を行うカラム名
     * @return string 連結後の SQL 文
     */
    function concatColumn($columns) {
        $sql = "concat(";
        $i = 0;
        $total = count($columns);
        foreach ($columns as $column) {
            $sql .= $column;
            if ($i < $total -1) {
                $sql .= ", ";
            }
            $i++;
        }
        $sql .= ")";
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
        $sql = "SHOW COLUMNS FROM " . $table_name;
        $arrColList = $objQuery->getAll($sql);
        $arrColList = SC_Utils_Ex::sfswaparray($arrColList);
        return $arrColList["Field"];
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
        $objQuery =& SC_Query::getSingletonInstance();
        $sql = "SHOW TABLES LIKE ". $objQuery->quote("%" . $expression . "%");
        $arrColList = $objQuery->getAll($sql);
        $arrColList = SC_Utils_Ex::sfswaparray($arrColList, false);
        return $arrColList[0];
    }

    /**
     * View の WHERE 句を置換する.
     *
     * @param string $target 置換対象の文字列
     * @param string $where 置換する文字列
     * @param array $arrval WHERE 句の要素の配列
     * @param string $option SQL 文の追加文字列
     * @return string 置換後の SQL 文
     */
    function sfViewWhere($target, $where = "", $arrval = array(), $option = ""){

        $arrWhere = split("[?]", $where);
        $where_tmp = " WHERE " . $arrWhere[0];
        for($i = 1; $i < count($arrWhere); $i++){
            $where_tmp .= SC_Utils_Ex::sfQuoteSmart($arrval[$i - 1]) . $arrWhere[$i];
        }
        $arrWhere = $this->getWhereConverter();
        $arrWhere[$target] = $where_tmp . " " . $option;
        return $arrWhere[$target];
    }

    /**
     * View をインラインビューに変換する.
     *
     * @access private
     * @param string $sql SQL 文
     * @return string インラインビューに変換した SQL 文
     */
    function sfChangeView($sql){

        $arrViewTmp = $this->viewToSubQuery();

            // viewのwhereを変換
        foreach($arrViewTmp as $key => $val){
            $arrViewTmp[$key] = strtr($arrViewTmp[$key], $this->getWhereConverter());
        }

            // viewを変換
        $changesql = strtr($sql, $arrViewTmp);

        return $changesql;
    }

    /**
     * ILIKE句 を LIKE句へ変換する.
     *
     * @access private
     * @param string $sql SQL文
     * @return string 変換後の SQL 文
     */
    function sfChangeILIKE($sql){
        $changesql = eregi_replace("(ILIKE )", "LIKE ", $sql);
        return $changesql;
    }

    /**
     * RANDOM() を RAND() に変換する.
     *
     * @access private
     * @param string $sql SQL文
     * @return string 変換後の SQL 文
     */
    function sfChangeRANDOM($sql){
        $changesql = eregi_replace("( RANDOM)", " RAND", $sql);
        return $changesql;
    }

    /**
     * TRUNC() を TRUNCATE() に変換する.
     *
     * @access private
     * @param string $sql SQL文
     * @return string 変換後の SQL 文
     */
    function sfChangeTrunc($sql){
        $changesql = eregi_replace("( TRUNC)", " TRUNCATE", $sql);
        return $changesql;
    }

    /**
     * WHERE 句置換用の配列を返す.
     *
     * @access private
     * @return array WHERE 句置換用の配列
     */
    function getWhereConverter() {
        return array(
            "&&crscls_where&&" => "",
            "&&crsprdcls_where&&" =>"",
            "&&noncls_where&&" => "",
            "&&allcls_where&&" => "",
            "&&allclsdtl_where&&" => "",
            "&&prdcls_where&&" => "",
            "&&catcnt_where&&" => ""
        );
    }

    /**
     * View をサブクエリに変換するための配列を返す.
     *
     * @access private
     * @return array View をサブクエリに変換するための配列
     */
    function viewToSubQuery() {

        static $sql = array();

        if (empty($sql)) {


            $sql['vw_products_allclass_detail'] = <<< __EOS__
                (
                    SELECT
                        dtb_products.product_id,
                        dtb_products.name,
                        dtb_products.maker_id,
                        dtb_products.rank,
                        dtb_products.status,
                        dtb_products.comment1,
                        dtb_products.comment2,
                        dtb_products.comment3,
                        dtb_products.comment4,
                        dtb_products.comment5,
                        dtb_products.comment6,
                        dtb_products.note,
                        dtb_products.file1,
                        dtb_products.file2,
                        dtb_products.file3,
                        dtb_products.file4,
                        dtb_products.file5,
                        dtb_products.file6,
                        dtb_products.main_list_comment,
                        dtb_products.main_list_image,
                        dtb_products.main_comment,
                        dtb_products.main_image,
                        dtb_products.main_large_image,
                        dtb_products.sub_title1,
                        dtb_products.sub_comment1,
                        dtb_products.sub_image1,
                        dtb_products.sub_large_image1,
                        dtb_products.sub_title2,
                        dtb_products.sub_comment2,
                        dtb_products.sub_image2,
                        dtb_products.sub_large_image2,
                        dtb_products.sub_title3,
                        dtb_products.sub_comment3,
                        dtb_products.sub_image3,
                        dtb_products.sub_large_image3,
                        dtb_products.sub_title4,
                        dtb_products.sub_comment4,
                        dtb_products.sub_image4,
                        dtb_products.sub_large_image4,
                        dtb_products.sub_title5,
                        dtb_products.sub_comment5,
                        dtb_products.sub_image5,
                        dtb_products.sub_large_image5,
                        dtb_products.sub_title6,
                        dtb_products.sub_comment6,
                        dtb_products.sub_image6,
                        dtb_products.sub_large_image6,
                        dtb_products.del_flg,
                        dtb_products.creator_id,
                        dtb_products.create_date,
                        dtb_products.update_date,
                        dtb_products.deliv_date_id,
                        dtb_products.down,
                        dtb_products.down_filename,
                        dtb_products.down_realfilename,
                        T4.product_code_min,
                        T4.product_code_max,
                        T4.price01_min,
                        T4.price01_max,
                        T4.price02_min,
                        T4.price02_max,
                        T4.stock_min,
                        T4.stock_max,
                        T4.stock_unlimited_min,
                        T4.stock_unlimited_max,
                        T4.class_count
                    FROM
                        dtb_products
                    JOIN
                            (
                                SELECT
                                    product_id,
                                    MIN(product_code) AS product_code_min,
                                    MAX(product_code) AS product_code_max,
                                    MIN(price01) AS price01_min,
                                    MAX(price01) AS price01_max,
                                    MIN(price02) AS price02_min,
                                    MAX(price02) AS price02_max,
                                    MIN(stock) AS stock_min,
                                    MAX(stock) AS stock_max,
                                    MIN(stock_unlimited) AS stock_unlimited_min,
                                    MAX(stock_unlimited) AS stock_unlimited_max,
                                    COUNT(*) as class_count
                                FROM dtb_products_class
                                GROUP BY product_id
                            ) AS T4
                            ON dtb_products.product_id = T4.product_id
                )
__EOS__;

            $sql['vw_products_allclass'] = <<< __EOS__
                (
                    SELECT
                        alldtl.*,
                        dtb_category.rank AS category_rank,
                        T2.category_id,
                        T2.rank AS product_rank
                    FROM
                        {$sql['vw_products_allclass_detail']} AS alldtl
                        LEFT JOIN
                            dtb_product_categories AS T2
                            ON alldtl.product_id = T2.product_id
                        LEFT JOIN
                            dtb_category
                            ON T2.category_id = dtb_category.category_id
                )
__EOS__;

        }

        return $sql;

    }
}
?>
