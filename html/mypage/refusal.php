<?php

require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = ROOT_DIR . USER_DIR . 'templates/mypage/refusal.tpl';
		$this->tpl_title = "MY�ڡ���/����³��(���ϥڡ���)";
		$this->tpl_navi = './navi.tpl';
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
	$objPage->tpl_mainpage = ROOT_DIR . USER_DIR . 'templates/mypage/refusal_confirm.tpl';
	$objPage->tpl_title = "MY�ڡ���/����³��(��ǧ�ڡ���)";

	break;
	
	case 'complete':
	//������
	$objQuery->exec("UPDATE dtb_customer SET delete=1 WHERE customer_id=?", array($objCustomer->getValue('customer_id')));
	$objQuery->delete("dtb_customer_mail", "email ILIKE ?", array($objCustomer->getValue('email')));
	$objCustomer->EndSession();
	//��λ�ڡ�����
	header("Location: ./refusal_complete.php");
	exit;
}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>