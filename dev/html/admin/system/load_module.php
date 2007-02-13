<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once(DATA_PATH . "module/Request.php");

// 認証確認
$objSess = new SC_Session();
sfIsSuccess($objSess);

if($_GET['module_id'] != ""){
	$module_id = $_GET['module_id'];
}elseif($_POST['module_id'] != ""){
	$module_id = $_POST['module_id'];
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