<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("./require.php");

class LC_Page {
	var $tpl_state;
	var $tpl_city;
	var $tpl_town;
	var $tpl_onload;
	var $tpl_message;
	function CPage() {
		$this->tpl_message = "住所を検索しています。";
	}
}

$conn = new SC_DBconn(ZIP_DSN);
$objPage = new LC_Page();
$objView = new SC_SiteView();

// 入力エラーチェック
$arrErr = fnErrorCheck();

// 入力エラーの場合は終了
if(count($arrErr) > 0) {
	$objPage->tpl_start = "window.close();";
}

// 郵便番号検索文作成
$zipcode = $_GET['zip1'].$_GET['zip2'];
$zipcode = mb_convert_kana($zipcode ,"n");
$sqlse = "SELECT state, city, town FROM mtb_zip WHERE zipcode = ?";

$data_list = $conn->getAll($sqlse, array($zipcode));

// インデックスと値を反転させる。
$arrREV_PREF = array_flip($arrPref);

$objPage->tpl_state = $arrREV_PREF[$data_list[0]['state']];
$objPage->tpl_city = $data_list[0]['city'];
$town =  $data_list[0]['town'];
/*
	総務省からダウンロードしたデータをそのままインポートすると
	以下のような文字列が入っているので	対策する。
	・（１~１９丁目）
	・以下に掲載がない場合
*/
$town = ereg_replace("（.*）$","",$town);
$town = ereg_replace("以下に掲載がない場合","",$town);
$objPage->tpl_town = $town;

// 郵便番号が発見された場合
if(count($data_list) > 0) {
	$func = "fnPutAddress('" . $_GET['input1'] . "','" . $_GET['input2']. "');";
	$objPage->tpl_onload = "$func";
	$objPage->tpl_start = "window.close();";
} else {
	$objPage->tpl_message = "該当する住所が見つかりませんでした。";
}

/* ページの表示　*/
$objView->assignobj($objPage);
$objView->display("input_zip.tpl");

/* 入力エラーのチェック */
function fnErrorCheck() {
	// エラーメッセージ配列の初期化
	$objErr = new SC_CheckError();
	
	// 郵便番号
	$objErr->doFunc( array("郵便番号1",'zip1',ZIP01_LEN ) ,array( "NUM_COUNT_CHECK" ) );
	$objErr->doFunc( array("郵便番号2",'zip2',ZIP02_LEN ) ,array( "NUM_COUNT_CHECK" ) );
	
	return $objErr->arrErr;
}

?>