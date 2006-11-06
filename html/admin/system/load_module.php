<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once(DATA_PATH . "module/Request.php");

// 認証確認
$objSess = new SC_Session();
sfIsSuccess($objSess);

if(is_numeric($_GET['module_id'])) {
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("main_php", "dtb_module", "module_id = ?", array($_GET['module_id']));
	$path = MODULE_PATH . $arrRet[0]['main_php'];
	if(file_exists($path)) {
		
		if($_GET['mode'] == "module_del"){
			// モジュール側に削除情報を送信する
			$req = new HTTP_Request($path);
			$req->setMethod(HTTP_REQUEST_METHOD_POST);
			$req->addPostData("mode", "module_del");
			$req->sendRequest();
			$req->clearPostData();
			sfprintr("tet");
		}else{
			require_once($path);
		}
		exit;
	} else {
		print("モジュールの取得に失敗しました。:".$path);
	}	
}

?>