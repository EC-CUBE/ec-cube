<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = 'regist/complete.tpl';
		$this->tpl_css = '/css/layout/regist/complete.css';
		$this->tpl_title = '�����Ͽ(��λ�ڡ���)';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

// �����Ȥ������ɤ������ǧ���롣
$objCartSess = new SC_CartSession("", false);
$objPage->tpl_cart_empty = count($objCartSess->getCartList()) < 1;

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>
