<?php
/**
 * ���ޥ����
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = 'magazine/cancel.tpl';
		$this->tpl_title .= '���ޥ������λ';
	}
}

$objPage = new LC_Page();
$objQuery = new SC_Query();

// secret_key�μ���
$key = $_GET['id'];

if (empty($key) or !lfExistKey($key))  {
	sfDispSiteError(PAGE_ERROR);
} else {
	lfChangeData($key);
}

$objView = new SC_SiteView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

// ���ޥ��β����λ������
function lfChangeData($key) {
	global $objQuery;

	$arrUpdate['mail_flag'] = 3;
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
