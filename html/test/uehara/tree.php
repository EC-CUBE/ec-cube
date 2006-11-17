<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'tree.tpl';
	}
}

$objPage = new LC_Page();
$objView = new SC_UserView(HTML_PATH."test/ueahra/");
$objQuery = new SC_Query();


$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);

//-----------------------------------------------------------------------------------------------------------------------------------

?>