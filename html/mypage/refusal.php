<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = USER_PATH . 'templates/mypage/refusal.tpl';
		$this->tpl_title = "MY�ڡ���/����³��(���ϥڡ���)";
		$this->tpl_navi = USER_PATH . 'templates/mypage/navi.tpl';
		$this->tpl_mainno = 'mypage';
		$this->tpl_mypageno = 'refusal';
		//session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCustomer = new SC_Customer();
$objQuery = new SC_Query();

//������Ƚ��
if (!$objCustomer->isLoginSuccess()){
	sfDispSiteError(CUSTOMER_ERROR);
}else {
	//�ޥ��ڡ����ȥå׸ܵҾ���ɽ����
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
	$objPage->CustomerPoint = $objCustomer->getvalue('point');
}


// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

switch ($_POST['mode']){
	case 'confirm':
	$objPage->tpl_mainpage = USER_PATH . 'templates/mypage/refusal_confirm.tpl';
	$objPage->tpl_title = "MY�ڡ���/����³��(��ǧ�ڡ���)";

	break;
	
	case 'complete':
	//������
	$objQuery->exec("UPDATE dtb_customer SET del_flg=1, update_date=now() WHERE customer_id=?", array($objCustomer->getValue('customer_id')));

	$where = "email ILIKE ?";
	if (DB_TYPE == "mysql")	$where = sfChangeILIKE($where);
	
	$objQuery->delete("dtb_customer_mail", $where, array($objCustomer->getValue('email')));
	$objCustomer->EndSession();
	//��λ�ڡ�����
	header("Location: ./refusal_complete.php");
	exit;
}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>