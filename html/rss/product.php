<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

//共通部品の読み込み
require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = "rss/product.tpl";
		$this->encode = "UTF-8";
		$this->description = "新着情報";
	}
}

$objQuery = new SC_Query();
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteInfo = new SC_SiteInfo();

//新着情報を取得
$arrProduct = lfGetProductsDetail($objQuery, 1);

//キャッシュしない(念のため)
header("Paragrama: no-cache");

//XMLテキスト(これがないと正常にRSSとして認識してくれないツールがあるため)
header("Content-type: application/xml");

//商品情報をセット
$objPage->arrProduct = $arrProduct;

//店名をセット
$objPage->site_title = $arrProduct[0]['shop_name'];

//代表Emailアドレスをセット
$objPage->email = $arrProduct[0]['email'];

//セットしたデータをテンプレートファイルに出力
$objView->assignobj($objPage);

//画面表示
$objView->display($objPage->tpl_mainpage, true);

//---------------------------------------------------------------------------------------------------------------------
/**************************************************************************************************************
 * 関数名:lfGetProducts
 * 説明　:商品情報を取得する
 * 引数１:$objQuery		DB操作クラス
 * 引数２:$product_id	商品ID
 * 戻り値:$arrProduct	取得結果を配列で返す
 **************************************************************************************************************/
function lfGetProductsDetail($objQuery, $product_id){
	$sql = "";
	$sql .= "SELECT 
				prod.product_id
				,prod.name
				,prod.category_id
				,prod.point_rate
				,prod.comment3
				,prod.main_list_comment
				,prod.main_list_image
				,prod.main_comment
				,prod.main_image
				,prod.main_large_image
				,cls.price01
				,cls.price02
				,(SELECT name FROM dtb_classcategory AS clscat WHERE clscat.classcategory_id = cls.classcategory_id1) AS classcategory_name1
				,(SELECT name FROM dtb_classcategory AS clscat WHERE clscat.classcategory_id = cls.classcategory_id2) AS classcategory_name2
				,(SELECT category_name FROM dtb_category AS cat WHERE cat.category_id = prod.category_id) AS category_name
			FROM dtb_products AS prod, dtb_products_class AS cls
			WHERE prod.product_id = cls.product_id AND prod.del_flg = 0 AND prod.status = 1 AND prod.product_id = ?
			ORDER BY prod.product_id, cls.classcategory_id1, cls.classcategory_id2
			";
	$arrProduct = $objQuery->getall($sql, array($product_id));
	return $arrProduct;
}

?>