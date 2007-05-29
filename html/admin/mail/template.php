<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $list_data;
	var $arrMagazineType;
	
	function LC_Page() {
		$this->tpl_mainpage = 'mail/template.tpl';
		$this->tpl_mainno = 'mail';
		$this->tpl_subnavi = 'mail/subnavi.tpl';
		$this->tpl_subno = "template";
		$this->tpl_subtitle = 'テンプレート設定';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// 認証可否の判定
sfIsSuccess($objSess);

if ( $_GET['mode'] == "delete" && sfCheckNumLength($_GET['id'])===true ){

	// メール担当の画像があれば削除しておく
	$sql = "SELECT charge_image FROM dtb_mailmaga_template WHERE template_id = ?";
	$result = $conn->getOne($sql, array($_GET["id"]));
	if (strlen($result) > 0) {
		@unlink(IMAGE_SAVE_DIR. $result);
	}
	
	// 登録削除
	$sql = "UPDATE dtb_mailmaga_template SET del_flg = 1 WHERE template_id = ?";
	$conn->query($sql, array($_GET['id']));
	sfReload();
}


$sql = "SELECT *, (substring(create_date, 1, 19)) as disp_date FROM dtb_mailmaga_template WHERE del_flg = 0 ORDER BY create_date DESC";
$objPage->list_data = $list_data = $conn->getAll($sql);
$objPage->arrMagazineType = $arrMagazineTypeAll;


$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
?>