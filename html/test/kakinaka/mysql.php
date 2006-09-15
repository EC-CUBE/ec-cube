<?php
require_once './DB.php'; // PEAR の DB クラスを読み込む

print("start<br>");
/*
$dsn = "mysql://eccube_db_user:password@210.188.212.163/eccube_db";
print($dsn."<br>");

if(($db = DB::connect($dsn)) == 0){
  print "おおっと！データベースに接続できません。";
}
$result = $db->query("select * from dtb_baseinfo");
while($row = $result->fetchRow()){
    print_r($row);
}

*/

$sql = '
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
        ,(SELECT rank AS category_rank FROM dtb_category AS T4 WHERE T1.category_id = T4.category_id)
        ,(SELECT category_id AS sub_category_id FROM dtb_category T4 WHERE T1.category_id = T4.category_id)
    FROM
        dtb_products AS T1 RIGHT JOIN (SELECT product_id AS product_id_sub, MIN(product_code) AS product_code_min, MAX(product_code) AS product_code_max, MIN(price01) AS price01_min, MAX(price01) AS price01_max, MIN(price02) AS price02_min, MAX(price02) AS price02_max, MIN(stock) AS stock_min, MAX(stock) AS stock_max, MIN(stock_unlimited) AS stock_unlimited_min, MAX(stock_unlimited) AS stock_unlimited_max FROM dtb_products_class GROUP BY product_id) AS T2 ON T1.product_id = T2.product_id_sub
    ) vw_products_allclass ';
	
print($sql);

print("end");

?> 