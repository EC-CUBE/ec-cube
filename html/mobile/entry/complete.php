<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css = '/css/layout/entry/complete.css';	// �ᥤ��CSS�ѥ�
		$this->tpl_mainpage = 'entry/complete.tpl';			// �ᥤ��ƥ�ץ졼��
		$this->tpl_title .= '�����Ͽ(��λ�ڡ���)';			//���ڡ��������ȥ�
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();


// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//----------------------------------------------------------------------------------------------------------------------
?>