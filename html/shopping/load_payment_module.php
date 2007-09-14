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

// ���Υڡ�������������Ͽ��³�����Ԥ�줿��Ͽ�����뤫Ƚ��
sfIsPrePage($objSiteSess);

// ������������������Ƚ��
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

$payment_id = $_SESSION["payment_id"];

// ��ʧ��ID��̵�����ˤϥ��顼
if($payment_id == ""){
	sfDispSiteError(PAGE_ERROR, "", true);
}

// ��Ѿ�����������
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
		sfDispSiteError(FREE_ERROR_MSG, "", true, "�⥸�塼��ե�����μ����˼��Ԥ��ޤ�����<br />���μ�³����̵���Ȥʤ�ޤ�����");
	}
}

?>