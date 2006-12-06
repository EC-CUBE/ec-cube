<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

class LC_Page {

	function LC_Page() {
		$this->tpl_mainpage = 'design/upload.tpl';
		$this->tpl_subnavi = 'design/subnavi.tpl';
		$this->tpl_subno = 'template';
		$this->tpl_subno_template = 'upload';
		$this->tpl_mainno = "design";
		$this->tpl_subtitle = 'アップロード';
		$this->template_name = 'アップロード';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// 画面の表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
