<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css = '/css/layout/contact/index.css';	// �ᥤ��CSS�ѥ�
		$this->tpl_mainpage = 'contact/index.tpl';
		$this->tpl_title = '���䤤��碌(���ϥڡ���)';
		$this->tpl_page_category = 'contact';
		global $arrPref;
		$this->arrPref = $arrPref;
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_MobileView();
$CONF = sf_getBasisData();			// Ź�޴��ܾ���

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->assignarray($CONF);
$objView->display(SITE_FRAME);

//------------------------------------------------------------------------------------------------------------------------------------------
?>