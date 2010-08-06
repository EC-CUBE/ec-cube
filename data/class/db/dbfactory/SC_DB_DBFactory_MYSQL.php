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

    /**
     * DBのバージョンを取得する.
     *
     * @param string $dsn データソース名
     * @return string データベースのバージョン
     */
    function sfGetDBVersion($dsn = "") {
        $objQuery = new SC_Query($this->getDSN($dsn), true, true);
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
        $objQuery = new SC_Query();
        $arrRet = $objQuery->getAll("SHOW VARIABLES LIKE 'char%'");
        return $arrRet;
    }

    /**
     * テーブルの存在チェックを行う SQL 文を返す.
     *
     * @param string $table_name 存在チェックを行うテーブル名
     * @return string テーブルの存在チェックを行う SQL 文
     */
    function getTableExistsSql($table_name) {
        // XXX 何故かブレースホルダが使えない
        $objQuery = new SC_Query();
        return "SHOW TABLE STATUS LIKE " . $objQuery->quote($table_name);
    }

    /**
     * インデックスの検索結果を配列で返す.
     *
     * @param string $index_name インデックス名
     * @param string $table_name テーブル名
     * @return array インデックスの検索結果の配列
     */
    function getTableIndex($index_name, $table_name = "") {
        $objQuery = new SC_Query("", true, true);
        return $objQuery->getAll("SHOW INDEX FROM " . $table_name . " WHERE Key_name = ?",
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
        $objQuery->query("CREATE INDEX ? ON ? (?(?))", array($index_name, $table_name, $col_name, $length));
    }

    /**
     * テーブルのカラム一覧を取得する.
     *
     * @param string $table_name テーブル名
     * @return array テーブルのカラム一覧の配列
     */
    function sfGetColumnList($table_name) {
        $objQuery = new SC_Query();
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
        $objQuery = new SC_Query();
        $sql = "SHOW TABLES LIKE ?";
        $arrColList = $objQuery->getAll($sql, array("%" . $expression . "%"));
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

            $sql['vw_cross_class'] = <<< __EOS__
                (
                    SELECT
                        T1.class_id AS class_id1,
                        T2.class_id AS class_id2,
                        T1.classcategory_id AS classcategory_id1,
                        T2.classcategory_id AS classcategory_id2,
                        T1.name AS name1,
                        T2.name AS name2,
                        T1.rank AS rank1,
                        T2.rank AS rank2
                    FROM
                        dtb_classcategory AS T1,
                        dtb_classcategory AS T2
                )
__EOS__;

            $sql['vw_cross_products_class'] = <<< __EOS__
                (
                    SELECT
                        T1.class_id1,
                        T1.class_id2,
                        T1.classcategory_id1,
                        T1.classcategory_id2,
                        T2.product_id,
                        T1.name1,
                        T1.name2,
                        T2.product_code,
                        T2.stock,
                        T2.price01,
                        T2.price02,
                        T1.rank1,
                        T1.rank2
                    FROM
                        {$sql['vw_cross_class']} AS T1
                        LEFT JOIN dtb_products_class AS T2
                            ON T1.classcategory_id1 = T2.classcategory_id1
                            AND T1.classcategory_id2 = T2.classcategory_id2
                )
__EOS__;

            $sql['vw_products_nonclass'] = <<< __EOS__
                (
                    SELECT *
                    FROM
                        dtb_products AS T1
                        LEFT JOIN
                        (
                            SELECT
                                product_id AS product_id_sub,
                                product_code,
                                price01,
                                price02,
                                stock,
                                stock_unlimited,
                                classcategory_id1,
                                classcategory_id2
                            FROM dtb_products_class
                            WHERE
                                classcategory_id1 = 0
                                AND classcategory_id2 = 0
                        ) AS T2
                        ON T1.product_id = T2.product_id_sub
                )
__EOS__;

            $sql['vw_products_allclass_detail'] = <<< __EOS__
                (
                    SELECT
                        dtb_products.product_id,
                        dtb_products.name,
                        dtb_products.deliv_fee,
                        dtb_products.sale_limit,
                        dtb_products.maker_id,
                        dtb_products.rank,
                        dtb_products.status,
                        dtb_products.product_flag,
                        dtb_products.point_rate,
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
                        LEFT JOIN
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

            $sql['vw_product_class'] = <<< __EOS__
                (SELECT * FROM
                (SELECT T3.product_class_id, T3.product_id AS product_id_sub, classcategory_id1, classcategory_id2,
                T3.rank AS rank1, T4.rank AS rank2, T3.class_id AS class_id1, T4.class_id AS class_id2,
                stock, price01, price02, stock_unlimited, product_code
                FROM ( SELECT
                        T1.product_class_id,
                        T1.product_id,
                        classcategory_id1,
                        classcategory_id2,
                        T2.rank,
                        T2.class_id,
                        stock,
                        price01,
                        price02,
                        stock_unlimited,
                        product_code
                 FROM (dtb_products_class AS T1 LEFT JOIN dtb_classcategory AS T2
                ON T1.classcategory_id1 = T2.classcategory_id))
                AS T3 LEFT JOIN dtb_classcategory AS T4
                ON T3.classcategory_id2 = T4.classcategory_id) AS T5 LEFT JOIN dtb_products AS T6
                ON product_id_sub = T6.product_id)
__EOS__;

            $sql['vw_category_count'] = <<< __EOS__
                (SELECT T1.category_id, T1.category_name, T1.parent_category_id, T1.level, T1.rank, T2.product_count
                FROM dtb_category AS T1 LEFT JOIN dtb_category_total_count AS T2
                ON T1.category_id = T2.category_id)
__EOS__;

            $sql['vw_download_class'] = <<< __EOS__
                (SELECT p.product_id AS product_id, p.down_realfilename AS down_realfilename , p.down_filename AS down_filename, od.order_id AS order_id, o.customer_id AS customer_id, o.commit_date AS commit_date, o.status AS status FROM
                    dtb_products p, dtb_order_detail od, dtb_order o
                WHERE p.product_id = od.product_id AND od.order_id = o.order_id)
__EOS__;
        }

        return $sql;

    }
}
?>
