<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * 履歴から注文を取得・登録し、カートに遷移する。
 */
require_once("../require.php");

$objCustomer = new SC_Customer();
$objCartSess = new SC_CartSession();

//受注詳細データの取得
$arrDisp = lfGetOrderDetail($_POST['order_id']);

//ログインしていない、またはDBに情報が無い場合
if (!$objCustomer->isLoginSuccess() or count($arrDisp) == 0){
	sfDispSiteError(CUSTOMER_ERROR, "", false, "", true);
}

for($num = 0; $num < count($arrDisp); $num++) {
	$product_id = $arrDisp[$num]['product_id'];
	$cate_id1 = $arrDisp[$num]['classcategory_id1'];
	$cate_id2 = $arrDisp[$num]['classcategory_id2'];
	$quantity = $arrDisp[$num]['quantity'];

	$objCartSess->addProduct(array($product_id, $cate_id1, $cate_id2), $quantity);
}

header("Location: " . gfAddSessionId(MOBILE_URL_CART_TOP));


//-----------------------------------------------------------------------------------------------------------------------------------
// 受注詳細データの取得
function lfGetOrderDetail($order_id) {
	$objQuery = new SC_Query();
	$col = "product_id, classcategory_id1, classcategory_id2, quantity";
	$where = "order_id = ?";
	$objQuery->setorder("classcategory_id1, classcategory_id2");
	$arrRet = $objQuery->select($col, "dtb_order_detail", $where, array($order_id));
	return $arrRet;
}

?>
