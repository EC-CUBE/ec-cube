<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_css = URL_DIR.'css/layout/fax/index.css';	// �ᥤ��CSS�ѥ�
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = 'fax/index.tpl';			// �ᥤ��ƥ�ץ졼��
		$this->tpl_page_category = 'fax';				
		$this->tpl_title = 'FAX��ʸ�ˤĤ���';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
?>
