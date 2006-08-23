<?php
require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = 'mypage/change_complete.tpl';
		$this->tpl_css = '/css/layout/mypage/change_complete.css';
		$this->tpl_title = 'MY�ڡ���/�����Ͽ�����ѹ�(��λ�ڡ���)';
		$this->tpl_navi = 'mypage/navi.tpl';
		$this->tpl_mypageno = 'change';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCustomer = new SC_Customer();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

//������Ƚ��
if (!$objCustomer->isLoginSuccess()){
	sfDispSiteError(CUSTOMER_ERROR);
}else {
	//�ޥ��ڡ����ȥå׸ܵҾ���ɽ����
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
	$objPage->CustomerPoint = $objCustomer->getvalue('point');
}


$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>
