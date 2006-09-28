<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/*　この商品を買った人はこんな商品も買っています。集計ファイル  */

$BATCH_DIR = realpath(dirname( __FILE__));
require_once($BATCH_DIR  . "/../../data/lib/slib.php");
require_once($BATCH_DIR  . "/../../data/lib/glib.php");
require_once($BATCH_DIR  . "/../../data/class/SC_Query.php");
require_once($BATCH_DIR  . "/../../data/class/SC_DbConn.php");
		
$objQuery = new SC_Query();

$objQuery->begin();
$objQuery->delete("dtb_bat_relate_products");
$arrCID = $objQuery->select("customer_id", "dtb_order", "del_flg = 0");
foreach($arrCID as $cdata) {
	$where = "order_id IN (SELECT order_id FROM dtb_order WHERE customer_id = ? )";
	//顧客が購入した商品ＩＤを取得する
	$arrPID = $objQuery->select("product_id", "dtb_order_detail", $where, array($cdata['customer_id']));
	//顧客が商品を複数購入していれば
	if(count($arrPID) > 1) {
		foreach($arrPID as $pdata1) {
			//この商品ID
			$sqlval['product_id'] = $pdata1['product_id'];
			foreach($arrPID as $pdata2) {
				if($pdata2['product_id'] != $pdata1['product_id']) {
					//こんな商品ID
					$sqlval['relate_product_id'] = $pdata2['product_id'];
					$sqlval['create_date'] = "now()";
					//データ挿入
					$objQuery->insert("dtb_bat_relate_products", $sqlval);
				}
			}
		}
	}
}
$objQuery->commit();

?>