<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = USER_PATH . 'templates/mypage/refusal_complete.tpl';
		$this->tpl_title = "MY�ڡ���/����³��(��λ�ڡ���)";
		$this->tpl_navi = USER_PATH . 'templates/mypage/navi.tpl';
		$this->tpl_mypageno = 'refusal';
		$this->point_disp = false;
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

$objCustomer = new SC_Customer();
//�ޥ��ڡ����ȥå׸ܵҾ���ɽ����
$objPage->CustomerName1 = $objCustomer->getvalue('name01');
$objPage->CustomerName2 = $objCustomer->getvalue('name02');
$objPage->CustomerPoint = $objCustomer->getvalue('point');

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>