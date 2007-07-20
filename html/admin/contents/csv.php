<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");
require_once(DATA_PATH . "include/csv_output.inc");

class LC_Page {
	var $arrForm;
	var $arrHidden;

	function LC_Page() {
		$this->tpl_mainpage = 'contents/csv.tpl';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = 'csv';
		$this->tpl_subno_csv = $this->arrSubnavi[1];
		$this->tpl_mainno = "contents";
		$this->tpl_subtitle = 'CSV出力設定';
	}
}
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

$objPage->arrSubnavi = $arrSubnavi;
$objPage->arrSubnaviName = $arrSubnaviName;

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

$arrOutput = array();
$arrChoice = array();

$get_tpl_subno_csv = $_GET['tpl_subno_csv'];
// GETで値が送られている場合にはその値を元に画面表示を切り替える
if ($get_tpl_subno_csv != ""){
	// 送られてきた値が配列に登録されていなければTOPを表示
	if (in_array($get_tpl_subno_csv,$objPage->arrSubnavi)){
		$subno_csv = $get_tpl_subno_csv;
	}else{
		$subno_csv = $objPage->arrSubnavi[1];
	}
} else {
	// GETで値がなければPOSTの値を使用する
	if ($_POST['tpl_subno_csv'] != ""){
		$subno_csv = $_POST['tpl_subno_csv'];
	}else{
		$subno_csv = $objPage->arrSubnavi[1];
	}
}

// subnoの番号を取得
$subno_id = array_keys($objPage->arrSubnavi,$subno_csv);
$subno_id = $subno_id[0];
// データの登録
if ($_POST["mode"] == "confirm") {
	
	// エラーチェック
	$objPage->arrErr = lfCheckError($_POST['output_list']);
	
	if (count($objPage->arrErr) <= 0){
		// データの更新
		lfUpdCsvOutput($subno_id, $_POST['output_list']);
		
		// 画面のリロード
		sfReload("tpl_subno_csv=$subno_csv");
	}
}

// 出力項目の取得
$arrOutput = sfSwapArray(sfgetCsvOutput($subno_csv, "WHERE csv_id = ? AND status = 1", array($subno_id)));
$arrOutput = sfarrCombine($arrOutput['col'], $arrOutput['disp_name']);

// 非出力項目の取得
$arrChoice = sfSwapArray(sfgetCsvOutput($subno_csv, "WHERE csv_id = ? AND status = 2", array($subno_id)));
$arrChoice = sfarrCombine($arrChoice['col'], $arrChoice['disp_name']);

$objPage->arrOutput=$arrOutput;
$objPage->arrChoice=$arrChoice;


$objPage->SubnaviName = $objPage->arrSubnaviName[$subno_id];
$objPage->tpl_subno_csv = $subno_csv;

// 画面の表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**************************************************************************************************************
 * 関数名	：lfUpdCsvOutput
 * 処理内容	：CSV出力項目を更新する
 * 引数		：なし
 **************************************************************************************************************/
function lfUpdCsvOutput($csv_id, $arrData = array()){
	$objQuery = new SC_Query();

	// ひとまず、全部使用しないで更新する
	$upd_sql = "UPDATE dtb_csv SET status = 2, rank = NULL, update_date = now() WHERE csv_id = ?";
	$objQuery->query($upd_sql, array($csv_id));

	// 使用するものだけ、再更新する。
	if (is_array($arrData)) {
		foreach($arrData as $key => $val){
			$upd_sql = "UPDATE dtb_csv SET status = 1, rank = ? WHERE csv_id = ? AND col = ? ";
			$objQuery->query($upd_sql, array($key+1, $csv_id,$val));
		}
	}
}

/**************************************************************************************************************
 * 関数名	：lfUpdCsvOutput
 * 処理内容	：CSV出力項目を更新する
 * 引数		：なし
 * 戻値		：なし
 **************************************************************************************************************/
function lfCheckError($data){
	$objErr = new SC_CheckError();
	$objErr->doFunc( array("出力項目", "output_list"), array("EXIST_CHECK") );
	
	return $objErr->arrErr;

}

