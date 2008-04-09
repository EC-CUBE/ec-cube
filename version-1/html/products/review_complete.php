<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'products/review_complete.tpl';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);					

?>
