<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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
        list($db_type) = split(":", $dsn);
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
        return $sql;
    }

    /**
     * テーブルの存在チェックを行う SQL 文を返す.
     *
     * @return string テーブルの存在チェックを行う SQL 文
     */
    function getTableExistsSql() {
        return "SHOW TABLE STATUS LIKE ?";
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
    function findTableNames($expression) {
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
     * SQL の中の View の存在をチェックする.
     *
     * @access private
     * @param string $sql SQL 文
     * @return bool Viewが存在しない場合 false
     */
    function sfInArray($sql){
        $arrView = $this->viewToSubQuery();

        foreach($arrView as $key => $val){
            if (strcasecmp($sql, $val) == 0){
                $changesql = eregi_replace("($key)", "$val", $sql);
                $this->sfInArray($changesql);
            }
        }
        return false;
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
        $changesql = eregi_replace("(ILIKE )", "LIKE BINARY ", $sql);
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
        return array(
            "vw_cross_class" => '
                (SELECT T1.class_id AS class_id1, T2.class_id AS class_id2, T1.classcategory_id AS classcategory_id1, T2.classcategory_id AS classcategory_id2, T1.name AS name1, T2.name AS name2, T1.rank AS rank1, T2.rank AS rank2
                FROM dtb_classcategory AS T1, dtb_classcategory AS T2 ) ',

            "vw_cross_products_class" =>'
                (SELECT T1.class_id1, T1.class_id2, T1.classcategory_id1, T1.classcategory_id2, T2.product_id,
                T1.name1, T1.name2, T2.product_code, T2.stock, T2.price01, T2.price02, T1.rank1, T1.rank2
                FROM (SELECT T1.class_id AS class_id1, T2.class_id AS class_id2, T1.classcategory_id AS classcategory_id1, T2.classcategory_id AS classcategory_id2, T1.name AS name1, T2.name AS name2, T1.rank AS rank1, T2.rank AS rank2
                FROM dtb_classcategory AS T1, dtb_classcategory AS T2 ) AS T1 LEFT JOIN dtb_products_class AS T2
                ON T1.classcategory_id1 = T2.classcategory_id1 AND T1.classcategory_id2 = T2.classcategory_id2) ',

            "vw_products_nonclass" => '
                (SELECT
                    T1.product_id,
                    T1.name,
                    T1.deliv_fee,
                    T1.sale_limit,
                    T1.sale_unlimited,
                    T1.category_id,
                    T1.rank,
                    T1.status,
                    T1.product_flag,
                    T1.point_rate,
                    T1.comment1,
                    T1.comment2,
                    T1.comment3,
                    T1.comment4,
                    T1.comment5,
                    T1.comment6,
                    T1.file1,
                    T1.file2,
                    T1.file3,
                    T1.file4,
                    T1.file5,
                    T1.file6,
                    T1.main_list_comment,
                    T1.main_list_image,
                    T1.main_comment,
                    T1.main_image,
                    T1.main_large_image,
                    T1.sub_title1,
                    T1.sub_comment1,
                    T1.sub_image1,
                    T1.sub_large_image1,
                    T1.sub_title2,
                    T1.sub_comment2,
                    T1.sub_image2,
                    T1.sub_large_image2,
                    T1.sub_title3,
                    T1.sub_comment3,
                    T1.sub_image3,
                    T1.sub_large_image3,
                    T1.sub_title4,
                    T1.sub_comment4,
                    T1.sub_image4,
                    T1.sub_large_image4,
                    T1.sub_title5,
                    T1.sub_comment5,
                    T1.sub_image5,
                    T1.sub_large_image5,
                    T1.sub_title6,
                    T1.sub_comment6,
                    T1.sub_image6,
                    T1.sub_large_image6,
                    T1.del_flg,
                    T1.creator_id,
                    T1.create_date,
                    T1.update_date,
                    T1.deliv_date_id,
                    T2.product_id_sub,
                    T2.product_code,
                    T2.price01,
                    T2.price02,
                    T2.stock,
                    T2.stock_unlimited,
                    T2.classcategory_id1,
                    T2.classcategory_id2
                FROM (SELECT * FROM dtb_products &&noncls_where&&) AS T1 LEFT JOIN
                (SELECT
                product_id AS product_id_sub,
                product_code,
                price01,
                price02,
                stock,
                stock_unlimited,
                classcategory_id1,
                classcategory_id2
                FROM dtb_products_class WHERE classcategory_id1 = 0 AND classcategory_id2 = 0)
                AS T2
                ON T1.product_id = T2.product_id_sub) ',

            "vw_products_allclass" => '
                (SELECT
                product_id,
                product_code_min,
                product_code_max,
                price01_min,
                price01_max,
                price02_min,
                price02_max,
                stock_min,
                stock_max,
                stock_unlimited_min,
                stock_unlimited_max,
                del_flg,
                status,
                name,
                comment1,
                comment2,
                comment3,
                rank,
                main_list_comment,
                main_image,
                main_list_image,
                product_flag,
                deliv_date_id,
                sale_limit,
                point_rate,
                sale_unlimited,
                create_date,
                deliv_fee
                ,(SELECT rank AS category_rank FROM dtb_category AS T4 WHERE T1.category_id = T4.category_id) as category_rank
                ,(SELECT category_id AS sub_category_id FROM dtb_category T4 WHERE T1.category_id = T4.category_id) as category_id
            FROM
                dtb_products AS T1 RIGHT JOIN (SELECT product_id AS product_id_sub, MIN(product_code) AS product_code_min, MAX(product_code) AS product_code_max, MIN(price01) AS price01_min, MAX(price01) AS price01_max, MIN(price02) AS price02_min, MAX(price02) AS price02_max, MIN(stock) AS stock_min, MAX(stock) AS stock_max, MIN(stock_unlimited) AS stock_unlimited_min, MAX(stock_unlimited) AS stock_unlimited_max FROM dtb_products_class GROUP BY product_id) AS T2 ON T1.product_id = T2.product_id_sub
            ) ',

            "vw_products_allclass_detail" => '
                (SELECT product_id,price01_min,price01_max,price02_min,price02_max,stock_min,stock_max,stock_unlimited_min,stock_unlimited_max,
                del_flg,status,name,comment1,comment2,comment3,deliv_fee,main_comment,main_image,main_large_image,
                sub_title1,sub_comment1,sub_image1,sub_large_image1,
                sub_title2,sub_comment2,sub_image2,sub_large_image2,
                sub_title3,sub_comment3,sub_image3,sub_large_image3,
                sub_title4,sub_comment4,sub_image4,sub_large_image4,
                sub_title5,sub_comment5,sub_image5,sub_large_image5,
                product_flag,deliv_date_id,sale_limit,point_rate,sale_unlimited,file1,file2,category_id
                FROM ( SELECT * FROM (dtb_products AS T1 RIGHT JOIN
                (SELECT
                product_id AS product_id_sub,
                MIN(price01) AS price01_min,
                MAX(price01) AS price01_max,
                MIN(price02) AS price02_min,
                MAX(price02) AS price02_max,
                MIN(stock) AS stock_min,
                MAX(stock) AS stock_max,
                MIN(stock_unlimited) AS stock_unlimited_min,
                MAX(stock_unlimited) AS stock_unlimited_max
                FROM dtb_products_class GROUP BY product_id) AS T2
                ON T1.product_id = T2.product_id_sub ) ) AS T3 LEFT JOIN (SELECT rank AS category_rank, category_id AS sub_category_id FROM dtb_category) AS T4
                ON T3.category_id = T4.sub_category_id) ',

            "vw_product_class" => '
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
                ON product_id_sub = T6.product_id) ',

            "vw_category_count" => '
                (SELECT T1.category_id, T1.category_name, T1.parent_category_id, T1.level, T1.rank, T2.product_count
                FROM dtb_category AS T1 LEFT JOIN dtb_category_total_count AS T2
                ON T1.category_id = T2.category_id) '
        );
    }
}
?>
