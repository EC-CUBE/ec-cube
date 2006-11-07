<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once(DATA_PATH . "module/Request.php");

$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();

// アクセスの正当性の判定
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

$payment_id = $_SESSION["payment_id"];

// 決済情報を取得する
if(sfColumnExists("dtb_payment", "memo01")){
	$sql = "SELECT memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10 FROM dtb_payment WHERE payment_id = ?";
	$arrPayment = $objQuery->getall($sql, array($payment_id));
}

if(is_numeric($module_id)) {
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("main_php", "dtb_module", "module_id = ?", array($module_id));
	$path = MODULE_PATH . $arrRet[0]['main_php'];
	if(file_exists($path)) {
		require_once($path);
		exit;
	} else {
		print("モジュールの取得に失敗しました。:".$path);
	}
}

?>