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
		$this->tpl_mainpage = 'magazine/complete.tpl';		// �ᥤ��ƥ�ץ졼��
		$this->tpl_title .= '���ޥ���Ͽ(��λ�ڡ���)';			//���ڡ��������ȥ�
	}
}

$objPage = new LC_Page();
$objView = new SC_MobileView();

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//----------------------------------------------------------------------------------------------------------------------
?>
