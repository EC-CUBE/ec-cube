<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_mainpage = 'basis/payment.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_mainno = 'basis';
		$this->tpl_subno = 'payment';
		$this->tpl_subtitle = '支払方法設定';
	}
}
$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// 認証可否の判定
sfIsSuccess($objSess);

switch($_POST['mode']) {
	case 'delete':
	// ランク付きレコードの削除
	sfDeleteRankRecord("dtb_payment", "payment_id", $_POST['payment_id']);
	// 再表示
	sfReload();
	break;
case 'up':
	sfRankUp("dtb_payment", "payment_id", $_POST['payment_id']);
	// 再表示
	sfReload();
	break;
case 'down':
	sfRankDown("dtb_payment", "payment_id", $_POST['payment_id']);
	// 再表示
	sfReload();
	break;
}

$objPage->arrDelivList = sfGetIDValueList("dtb_deliv", "deliv_id", "service_name");
$objPage->arrPaymentListFree = lfGetPaymentList(2);

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
// 配送業者一覧の取得
function lfGetPaymentList($fix = 1) {
	$objQuery = new SC_Query();
	// 配送業者一覧の取得
	$col = "payment_id, payment_method, charge, rule, upper_rule, note, deliv_id, fix, charge_flg";
	$where = "del_flg = 0";
//	$where .= " AND fix = ?";
	$table = "dtb_payment";
	$objQuery->setorder("rank DESC");
	$arrRet = $objQuery->select($col, $table, $where);
	return $arrRet;
}

?>