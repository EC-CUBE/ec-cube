<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("./require.php");
require_once(DATA_PATH . "module/Request.php");

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess(new SC_Session());

if($_GET['module_id'] != ""){
	$module_id = $_GET['module_id'];
}elseif($_POST['module_id'] != ""){
	$module_id = $_POST['module_id'];
}

gfprintlog("mode -------------------------------> ".$_POST["mode"]);

if(is_numeric($module_id)) {
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("main_php", "dtb_module", "module_id = ?", array($module_id));
	$path = MODULE_PATH . $arrRet[0]['main_php'];
	if(file_exists($path)) {
		require_once($path);
		exit;
	} else {
		print("�⥸�塼��μ����˼��Ԥ��ޤ�����:".$path);
	}
}

?>