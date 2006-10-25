<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

// 認証確認
$objSess = new SC_Session();
sfIsSuccess($objSess);

if(is_numeric($_GET['module_id'])) {
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("*", "dtb_module", "module_id = ?", array($_GET['module_id']));
	$path = MODULE_PATH . $arrRet[0]['main_php'];
	if(file_exists($path)) {
		require_once($path);
		exit;
	} else {
		print("モジュールの取得に失敗しました。");
	}	
}

?>