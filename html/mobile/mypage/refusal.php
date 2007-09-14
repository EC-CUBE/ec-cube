<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * ������
 */
require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = 'mypage/refusal.tpl';
		$this->tpl_title = "MY�ڡ���/����³��(���ϥڡ���)";
		//session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$objView = new SC_MobileView();
$objCustomer = new SC_Customer();
$objQuery = new SC_Query();

//������Ƚ��
if (!$objCustomer->isLoginSuccess()){
	sfDispSiteError(CUSTOMER_ERROR, "", false, "", true);
}else {
	//�ޥ��ڡ����ȥå׸ܵҾ���ɽ����
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
	$objPage->CustomerPoint = $objCustomer->getvalue('point');
}


// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

if (isset($_POST['no'])) {
	header("Location: " . gfAddSessionId("index.php"));
	exit;
} elseif (isset($_POST['complete'])){
	//������
	$objQuery->exec("UPDATE dtb_customer SET del_flg=1, update_date=now() WHERE customer_id=?", array($objCustomer->getValue('customer_id')));

	$where = "email ILIKE ?";
	if (DB_TYPE == "mysql")	$where = sfChangeILIKE($where);
	$objCustomer->EndSession();
	//��λ�ڡ�����
	header("Location: " . gfAddSessionId("refusal_complete.php"));
	exit;
}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>
