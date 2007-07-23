<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objQuery = new SC_Query();

// 前のページで正しく登録手続きが行われた記録があるか判定
sfIsPrePage($objSiteSess);

// アクセスの正当性の判定
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

$payment_id = $_SESSION["payment_id"];

// 支払いIDが無い場合にはエラー
if($payment_id == ""){
	sfDispSiteError(PAGE_ERROR, "", true);
}

// 決済情報を取得する
if(sfColumnExists("dtb_payment", "memo01")){
	$sql = "SELECT module_path, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10 FROM dtb_payment WHERE payment_id = ?";
	$arrPayment = $objQuery->getall($sql, array($payment_id));
}

if(count($arrPayment) > 0) {
	$path = $arrPayment[0]['module_path'];
	if(file_exists($path)) {
		require_once($path);
		exit;
	} else {
		sfDispSiteError(FREE_ERROR_MSG, "", true, "モジュールファイルの取得に失敗しました。<br />この手続きは無効となりました。");
	}
}

?>