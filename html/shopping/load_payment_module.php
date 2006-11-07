<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once(DATA_PATH . "module/Request.php");

//$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
//lfInitParam();
// POST�ͤμ���
$objFormParam->setParam($_POST);

// ������������������Ƚ��
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);


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
		print("�⥸�塼��μ����˼��Ԥ��ޤ�����:".$path);
	}
}

sfprintr($_SESSION);

?>