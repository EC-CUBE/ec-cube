<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_css = URL_DIR.'css/layout/order/index.css';		// �ᥤ��CSS�ѥ�
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = 'order/index.tpl';			// �ᥤ��ƥ�ץ졼��
		$this->tpl_page_category = 'order';	
		$this->tpl_title = '���꾦����˴ؤ���ˡΧ';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$arrRet = $objQuery->getall("SELECT * FROM dtb_baseinfo",array());
$objPage->arrRet = $arrRet[0];
$objPage->arrPref = $arrPref;

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
?>
