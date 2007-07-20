<?php

require_once("../../require.php");

//---- ページ表示用クラス
class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'utf.tpl';
	}
}

$objView = new SC_UserView("./templates/");
$objPage = new LC_Page();
$objQuery = new SC_Query();


$ret = $objQuery->select("SELECT * FROM dtb_products");

sfprintr($ret);

$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);

?>
