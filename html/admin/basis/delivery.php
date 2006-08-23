<?php

require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_mainpage = 'basis/delivery.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_subno = 'delivery';
		$this->tpl_mainno = 'basis';
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrTAXRULE;
		$this->arrTAXRULE = $arrTAXRULE;
		$this->tpl_subtitle = '配送業者設定';

	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// 認証可否の判定
sfIsSuccess($objSess);

switch($_POST['mode']) {
case 'delete':
	// ランク付きレコードの削除
	sfDeleteRankRecord("dtb_deliv", "deliv_id", $_POST['deliv_id']);
	// 再表示
	sfReload();
	break;
case 'up':
	sfRankUp("dtb_deliv", "deliv_id", $_POST['deliv_id']);
	// 再表示
	sfReload();
	break;
case 'down':
	sfRankDown("dtb_deliv", "deliv_id", $_POST['deliv_id']);
	// 再表示
	sfReload();
	break;
default:
	break;
}

// 配送業者一覧の取得
$col = "deliv_id, name, service_name";
$where = "delete = 0";
$table = "dtb_deliv";
$objQuery->setorder("rank DESC");
$objPage->arrDelivList = $objQuery->select($col, $table, $where);

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//--------------------------------------------------------------------------------------------------------------------------------------
