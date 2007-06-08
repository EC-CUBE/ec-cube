<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * メルマガ解除
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** 必ず変更する **/
		$this->tpl_mainpage = 'magazine/cancel.tpl';
		$this->tpl_title .= 'メルマガ解除完了';
	}
}

$objPage = new LC_Page();
$objQuery = new SC_Query();

// secret_keyの取得
$key = $_GET['id'];

if (empty($key) or !lfExistKey($key))  {
	sfDispSiteError(PAGE_ERROR, "", false, "", true);
} else {
	lfChangeData($key);
}

$objView = new SC_MobileView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

// メルマガの解除を完了させる
function lfChangeData($key) {
	global $objQuery;

	$arrUpdate['mail_flag'] = 3;
	$arrUpdate['secret_key'] = NULL;
	$result = $objQuery->update("dtb_customer_mail", $arrUpdate, "secret_key = '" .addslashes($key). "'");
}

// キーが存在するかどうか
function lfExistKey($key) {
	global $objQuery;

	$sql = "SELECT count(*) FROM dtb_customer_mail WHERE secret_key = ?";
	$result = $objQuery->getOne($sql, array($key));

	if ($result == 1) {
		return true;
	} else {
		return false;
	}
}


?>
