<?php

require_once './DB.php'; // PEAR の DB クラスを読み込む

// View変換用(MySQL対応)
$arrView = array(
	"vw_cross_class" => '
		(SELECT T1.class_id AS class_id1, T2.class_id AS class_id2, T1.classcategory_id AS classcategory_id1, T2.classcategory_id AS classcategory_id2, T1.name AS name1, T2.name AS name2, T1.rank AS rank1, T2.rank AS rank2
		FROM dtb_classcategory AS T1, dtb_classcategory AS T2 ) vw_cross_class ',
		
	"vw_cross_products_class" =>'
		(SELECT T1.class_id1, T1.class_id2, T1.classcategory_id1, T1.classcategory_id2, T2.product_id,
		T1.name1, T1.name2, T2.product_code, T2.stock, T2.price01, T2.price02, T1.rank1, T1.rank2
		FROM vw_cross_class AS T1 LEFT JOIN dtb_products_class AS T2 
		ON T1.classcategory_id1 = T2.classcategory_id1 AND T1.classcategory_id2 = T2.classcategory_id2) vw_cross_products_class ',
		
	"vw_products_nonclass" => '
		(SELECT * FROM dtb_products AS T1 LEFT JOIN 
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
		ON T1.product_id = T2.product_id_sub) vw_products_nonclass ',
	
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
        ,(SELECT rank AS category_rank FROM dtb_category AS T4 WHERE T1.category_id = T4.category_id)
        ,(SELECT category_id AS sub_category_id FROM dtb_category T4 WHERE T1.category_id = T4.category_id)
    FROM
        dtb_products AS T1 RIGHT JOIN (SELECT product_id AS product_id_sub, MIN(product_code) AS product_code_min, MAX(product_code) AS product_code_max, MIN(price01) AS price01_min, MAX(price01) AS price01_max, MIN(price02) AS price02_min, MAX(price02) AS price02_max, MIN(stock) AS stock_min, MAX(stock) AS stock_max, MIN(stock_unlimited) AS stock_unlimited_min, MAX(stock_unlimited) AS stock_unlimited_max FROM dtb_products_class GROUP BY product_id) AS T2 ON T1.product_id = T2.product_id_sub
    ) chg_vw_products_allclass ',

	"vw_products_allclass_detail" => '
		(SELECT product_id,price01_min,price01_max,price02_min,price02_max,stock_min,stock_max,stock_unlimited_min,stock_unlimited_max,
		del_flg,status,name,comment1,comment2,comment3,deliv_fee,main_comment,main_image,main_large_image,
		sub_title1,sub_comment1,sub_image1,sub_large_image1,
		sub_title2,sub_comment2,sub_image2,sub_large_image2,
		sub_title3,sub_comment3,sub_image3,sub_large_image3,
		sub_title4,sub_comment4,sub_image4,sub_large_image4,
		sub_title5,sub_comment5,sub_image5,sub_large_image5,
		product_flag,deliv_date_id,sale_limit,point_rate,sale_unlimited,file1,file2,category_id
		FROM (dtb_products AS T1 RIGHT JOIN 
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
		ON T1.product_id = T2.product_id_sub) AS T3 LEFT JOIN (SELECT rank AS category_rank, category_id AS sub_category_id FROM dtb_category) AS T4
		ON T3.category_id = T4.sub_category_id) vw_products_allclass_detail ',

	"vw_product_class" => '
		(SELECT * FROM 
		(SELECT T3.product_class_id, T3.product_id AS product_id_sub, classcategory_id1, classcategory_id2, 
		T3.rank AS rank1, T4.rank AS rank2, T3.class_id AS class_id1, T4.class_id AS class_id2,
		stock, price01, price02, stock_unlimited, product_code
		FROM (dtb_products_class AS T1 LEFT JOIN dtb_classcategory AS T2
		ON T1.classcategory_id1 = T2.classcategory_id)
		AS T3 LEFT JOIN dtb_classcategory AS T4
		ON T3.classcategory_id2 = T4.classcategory_id) AS T5 LEFT JOIN dtb_products AS T6
		ON product_id_sub = T6.product_id) vw_product_class ',

	"vw_category_count" => '
		(SELECT T1.category_id, T1.category_name, T1.parent_category_id, T1.level, T1.rank, T2.product_count
		FROM dtb_category AS T1 LEFT JOIN dtb_category_total_count AS T2
		ON T1.category_id = T2.category_id) vw_category_count '
);

print("start<br>");

$dsn = "mysql://eccube_db_user:password@210.188.212.163:3307/eccube_db";
print($dsn."<br>");

if(($db = DB::connect($dsn)) == 0){
  print "おおっと！データベースに接続できません。";
}
$result = $db->query("SELECT last_insert_id(id+1)");
while($row = $result->fetchRow()){
    print_r($row);
}

//print(preg_replace("/[\r\n\t]/"," ",$sql));
// print(eregi_replace("(ILIKE )", "LIKE BINARY ", $sql));

print(sfInArray($sql));

print("end");

// 配列の中にデータが存在しているかチェックを行う(大文字小文字の区別なし)
function sfInArray($sql){
	global $arrView;

	foreach($arrView as $key => $val){
		if (stristr($sql, $key) != ""){
			$changesql = eregi_replace("($key)", "$val", $sql);
			print("------------------------------------------------------------<BR>".$changesql."<BR>------------------------------------------------------------<BR>");
			sfInArray($changesql);
		}
	}

	return $sql;
}
?> 