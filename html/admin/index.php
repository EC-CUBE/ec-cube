<?php
require_once("./require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'login.tpl';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objView->assignobj($objPage);
$objView->display(LOGIN_FRAME);
?>
