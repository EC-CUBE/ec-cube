<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * ���ޥ���Ͽ
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = 'magazine/regist.tpl';
		$this->tpl_title .= '���ޥ���Ͽ��λ';
	}
}

$objPage = new LC_Page();
$objQuery = new SC_Query();

// secret_key�μ���
$key = $_GET['id'];

if (empty($key) or !lfExistKey($key))  {
	sfDispSiteError(PAGE_ERROR, "", false, "", true);
} else {
	lfChangeData($key);
}

$objView = new SC_MobileView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

// ���ޥ�����Ͽ��λ������
function lfChangeData($key) {
	global $objQuery;

	$arrUpdate['mail_flag'] = 2;
	$arrUpdate['secret_key'] = NULL;
	$result = $objQuery->update("dtb_customer_mail", $arrUpdate, "secret_key = '" .addslashes($key). "'");
}

// ������¸�ߤ��뤫�ɤ���
function lfExistKey($key) {
	global $objQuery;

	$sql = "SELECT count(*) FROM dtb_customer_mail WHERE secret_key = ?";
	$result = $objQuery->getOne($sql, array($key));

	if ($result == 1) {
		return true;
	} else {
		return false;
	}
}


?>
