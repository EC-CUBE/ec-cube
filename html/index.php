<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("./require.php");

class LC_Page {
	function LC_Page() {
		/** ɬ���ѹ����� **/
		$this->tpl_css = '/css/layout/index.css';						// �ᥤ��CSS�ѥ�
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = HTML_PATH . "user_data/templates/top.tpl";		// �ᥤ��ƥ�ץ졼��
	}
}

$objPage = new LC_Page();
$conn = new SC_DBConn();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "index.php");

sfprintr("oooooo");




$objView = new SC_SiteView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------

?>