<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 */

$CONF_PHP_PATH = realpath( dirname( __FILE__) );
require_once($CONF_PHP_PATH ."/../install.php");
require_once($CONF_PHP_PATH ."/core.php" );

//--------------------------------------------------------------------------------------------------------
/** エラーレベル設定
/*
 *	'E_ERROR'             => 大な実行時エラー。これは、メモリ確保に関する問題のように復帰で きないエラーを示します。スクリプトの実行は中断されます。
 *	'E_WARNING'           => 実行時の警告 (致命的なエラーではない)。スクリプトの実行は中断さ れません
 *	'E_PARSE'             => コンパイル時のパースエラー。パースエラーはパーサでのみ生成されま す。
 *	'E_NOTICE'            => 実行時の警告。エラーを発しうる状況に遭遇したことを示す。 ただし通常のスクリプト実行の場合にもこの警告を発することがありうる。
 *	'E_CORE_ERROR'        => PHPの初期始動時点での致命的なエラー。E_ERRORに 似ているがPHPのコアによって発行される点が違う。
 *	'E_CORE_WARNING'      => （致命的ではない）警告。PHPの初期始動時に発生する。 E_WARNINGに似ているがPHPのコアによって発行される 点が違う。
 *	'E_COMPILE_ERROR'     => コンパイル時の致命的なエラー。E_ERRORに 似ているがZendスクリプティングエンジンによって発行される点が違う。
 *	'E_COMPILE_WARNING'   => コンパイル時の警告（致命的ではない）。E_WARNINGに 似ているがZendスクリプティングエンジンによって発行される点が違う。
 *	'E_USER_ERROR'        => ユーザーによって発行されるエラーメッセージ。E_ERROR に似ているがPHPコード上でtrigger_error()関数を 使用した場合に発行される点が違う。
 *	'E_USER_WARNING'      => ユーザーによって発行される警告メッセージ。E_WARNING に似ているがPHPコード上でtrigger_error()関数を 使用した場合に発行される点が違う。
 *	'E_USER_NOTICE'       => ユーザーによって発行される注意メッセージ。E_NOTICEに に似ているがPHPコード上でtrigger_error()関数を 使用した場合に発行される点が違う。
 *	'E_ALL'               => サポートされる全てのエラーと警告。PHP < 6 では E_STRICT レベルのエラーは除く。
 *	'E_STRICT'            => ※PHP5からサポート 実行時の注意。コードの相互運用性や互換性を維持するために PHP がコードの変更を提案する。
 *	'E_RECOVERABLE_ERROR' => ※PHP5からサポート キャッチできる致命的なエラー。危険なエラーが発生したが、 エンジンが不安定な状態になるほどではないことを表す。 ユーザ定義のハンドラでエラーがキャッチされなかった場合 (set_error_handler() も参照ください) は、 E_ERROR として異常終了する。
 */
error_reporting(E_ALL & ~E_NOTICE);
//error_reporting(E_ALL);

if (is_file($CONF_PHP_PATH . "/cache/mtb_constants.php")) {
    require_once($CONF_PHP_PATH . "/cache/mtb_constants.php");
} else {
    // TODO インストーラで設定する
}

/*--------- ▲View管理用 ---------*/

// ViewのWhere句置換用
$arrViewWhere = array(
    "&&crscls_where&&" => "",
    "&&crsprdcls_where&&" =>"",
    "&&noncls_where&&" => "",
    "&&allcls_where&&" => "",
    "&&allclsdtl_where&&" => "",
    "&&prdcls_where&&" => "",
    "&&catcnt_where&&" => ""
);

// View変換用(MySQL対応)
$arrView = array(
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

?>
