<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'sales/index.tpl';
		$this->tpl_mainno = 'sales';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

// �����ѥ�᡼���ΰ����Ѥ�
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key)) {
		$objPage->arrSearchHidden[$key] = $val;	
	}
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
?>