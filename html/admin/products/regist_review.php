<?php
/*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	function LC_Page() {
		$this->tpl_mainpage = 'home.tpl';
		$this->tpl_mainpage = 'products/regist_review.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';		
		$this->tpl_subno = 3;
		$this->tpl_subtitle = 'レビュー管理';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// 認証可否の判定
sfIsSuccess($objSess);

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

?>