<?php
/*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once("./index_csv.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'order/index.tpl';
		$this->tpl_subnavi = 'order/subnavi.tpl';
		$this->tpl_mainno = 'order';		
		$this->tpl_subno = 'index';
		$this->tpl_pager = ROOT_DIR . 'data/Smarty/templates/admin/pager.tpl';
		$this->tpl_subtitle = '受注管理';
		global $arrORDERSTATUS;
		$this->arrORDERSTATUS = $arrORDERSTATUS;
		global $arrORDERSTATUS_COLOR;
		$this->arrORDERSTATUS_COLOR = $arrORDERSTATUS_COLOR;
		global $arrSex;
		$this->arrSex = $arrSex;
		global $arrPageMax;
		$this->arrPageMax = $arrPageMax;
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
$objFormParam->setParam($_POST);

$objFormParam->splitParamCheckBoxes('search_order_sex');
$objFormParam->splitParamCheckBoxes('search_payment_id');

// 検索ワードの引き継ぎ
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key)) {
		switch($key) {
			case 'search_order_sex':
			case 'search_payment_id':
				$objPage->arrHidden[$key] = sfMergeParamCheckBoxes($val);
				break;
			default:
				$objPage->arrHidden[$key] = $val;
				break;
		}		
	}
}

// ページ送り用
$objPage->arrHidden['search_pageno'] = $_POST['search_pageno'];

// 認証可否の判定
sfIsSuccess($objSess);

if($_POST['mode'] == 'delete') {
	if(sfIsInt($_POST['order_id'])) {
		$objQuery = new SC_Query();
		$where = "order_id = ?";
		$sqlval['delete'] = '1';
		$objQuery->update("dtb_order", $sqlval, $where, array($_POST['order_id']));
	}	
}

switch($_POST['mode']) {
case 'delete':
case 'csv':
case 'delete_all':
case 'search':
	// 入力値の変換
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($arrRet);
	$arrRet = $objFormParam->getHashArray();
	// 入力なし
	if (count($objPage->arrErr) == 0) {
		$where = "del_flg = 0";
		foreach ($arrRet as $key => $val) {
			if($val == "") {
				continue;
			}
			$val = sfManualEscape($val);
			
			switch ($key) {
				case 'search_order_name':
					$where .= " AND order_name01||order_name02 ILIKE ?";
					$nonsp_val = ereg_replace("[ 　]+","",$val);
					$arrval[] = "%$nonsp_val%";
					break;
				case 'search_order_kana':
					$where .= " AND order_kana01||order_kana02 ILIKE ?";
					$nonsp_val = ereg_replace("[ 　]+","",$val);
					$arrval[] = "%$nonsp_val%";
					break;
				case 'search_order_id1':
					$where .= " AND order_id >= ?";
					$arrval[] = $val;
					break;
				case 'search_order_id2':
					$where .= " AND order_id <= ?";
					$arrval[] = $val;
					break;
				case 'search_order_sex':
					$tmp_where = "";
					foreach($val as $element) {
						if($element != "") {
							if($tmp_where == "") {
								$tmp_where .= " AND (order_sex = ?";
							} else {
								$tmp_where .= " OR order_sex = ?";
							}
							$arrval[] = $element;
						}
					}
					
					if($tmp_where != "") {
						$tmp_where .= ")";
						$where .= " $tmp_where ";
					}					
					break;
				case 'search_order_tel':
					$where .= " AND (order_tel01||order_tel02||order_tel03) ILIKE ?";
					$nonmark_val = ereg_replace("[()-]+","",$val);
					$arrval[] = "$nonmark_val%";
					break;
				case 'search_order_email':
					$where .= " AND order_email ILIKE ?";
					$arrval[] = "%$val%";
					break;
				case 'search_payment_id':
					$tmp_where = "";
					foreach($val as $element) {
						if($element != "") {
							if($tmp_where == "") {
								$tmp_where .= " AND (payment_id = ?";
							} else {
								$tmp_where .= " OR payment_id = ?";
							}
							$arrval[] = $element;
						}
					}
					
					if($tmp_where != "") {
						$tmp_where .= ")";
						$where .= " $tmp_where ";
					}
					break;
				case 'search_total1':
					$where .= " AND total >= ?";
					$arrval[] = $val;
					break;
				case 'search_total2':
					$where .= " AND total <= ?";
					$arrval[] = $val;
					break;
				case 'search_startyear':
					$date = sfGetTimestamp($_POST['search_startyear'], $_POST['search_startmonth'], $_POST['search_startday']);
					$where.= " AND update_date >= ?";
					$arrval[] = $date;
					break;
				case 'search_endyear':
					$date = sfGetTimestamp($_POST['search_endyear'], $_POST['search_endmonth'], $_POST['search_endday'], true);
					$where.= " AND update_date <= ?";
					$arrval[] = $date;
					break;
				case 'search_sbirthyear':
					$date = sfGetTimestamp($_POST['search_sbirthyear'], $_POST['search_sbirthmonth'], $_POST['search_sbirthday']);
					$where.= " AND order_birth >= ?";
					$arrval[] = $date;
					break;
				case 'search_ebirthyear':
					$date = sfGetTimestamp($_POST['search_ebirthyear'], $_POST['search_ebirthmonth'], $_POST['search_ebirthday'], true);
					$where.= " AND order_birth <= ?";
					$arrval[] = $date;
					break;
				case 'search_order_status':
					$where.= " AND status = ?";
					$arrval[] = $val;
					break;
				default:
					break;
			}
		}
		
		$order = "update_date DESC";
		
		switch($_POST['mode']) {
		case 'csv':
			// オプションの指定
			$option = "ORDER BY $order";
			
			// CSV出力タイトル行の作成
			$arrCsvOutput = sfSwapArray(sfgetCsvOutput(3, " WHERE csv_id = 3 AND status = 1"));
			
			if (count($arrCsvOutput) <= 0) break;
			
			$arrCsvOutputCols = $arrCsvOutput['col'];
			$arrCsvOutputTitle = $arrCsvOutput['disp_name'];
			$head = sfGetCSVList($arrCsvOutputTitle);
			$data = lfGetCSV("dtb_order", $where, $option, $arrval, $arrCsvOutputCols);
			
			// CSVを送信する。
			sfCSVDownload($head.$data);
			exit;
			break;
		case 'delete_all':
			// 検索結果をすべて削除
			$sqlval['del_flg'] = 1;
			$objQuery = new SC_Query();
			$objQuery->update("dtb_order", $sqlval, $where, $arrval);
			break;
		default:
			// 読み込む列とテーブルの指定
			$col = "*";
			$from = "dtb_order";
			
			$objQuery = new SC_Query();
			// 行数の取得
			$linemax = $objQuery->count($from, $where, $arrval);
			$objPage->tpl_linemax = $linemax;				// 何件が該当しました。表示用
			
			// ページ送りの処理
			if(is_numeric($_POST['search_page_max'])) {	
				$page_max = $_POST['search_page_max'];
			} else {
				$page_max = SEARCH_PMAX;
			}
			
			// ページ送りの取得
			$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnNaviSearchPage", NAVI_PMAX);
			$startno = $objNavi->start_row;
			$objPage->arrPagenavi = $objNavi->arrPagenavi;		
			
			// 取得範囲の指定(開始行番号、行数のセット)
			$objQuery->setlimitoffset($page_max, $startno);
			// 表示順序
			$objQuery->setorder($order);
			// 検索結果の取得
			$objPage->arrResults = $objQuery->select($col, $from, $where, $arrval);
		}
	}
	break;
	
default:
	break;
}

$objDate = new SC_Date();
// 登録・更新日検索用
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrRegistYear = $objDate->getYear();
// 生年月日検索用
$objDate->setStartYear(BIRTH_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrBirthYear = $objDate->getYear();
// 月日の設定
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

// 入力値の取得
$objPage->arrForm = $objFormParam->getFormParamList();
// 支払い方法の取得
$arrRet = sfGetPayment();
$objPage->arrPayment = sfArrKeyValue($arrRet, 'payment_id', 'payment_method');

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("受注番号1", "search_order_id1", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("受注番号2", "search_order_id2", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("対応状況", "search_order_status", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("顧客名", "search_order_name", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
	$objFormParam->addParam("顧客名(カナ)", "search_order_kana", STEXT_LEN, "KVCa", array("KANA_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("性別", "search_order_sex", INT_LEN, "n", array("MAX_LENGTH_CHECK"));
	$objFormParam->addParam("年齢1", "search_age1", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("年齢2", "search_age2", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("メールアドレス", "search_order_email", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
	$objFormParam->addParam("TEL", "search_order_tel", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
	$objFormParam->addParam("支払い方法", "search_payment_id", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("購入金額1", "search_total1", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("購入金額2", "search_total2", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("表示件数", "search_page_max", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("開始日", "search_startyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("開始日", "search_startmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("開始日", "search_startday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("終了日", "search_endyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("終了日", "search_endmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("終了日", "search_endday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("開始日", "search_sbirthyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("開始日", "search_sbirthmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("開始日", "search_sbirthday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("終了日", "search_ebirthyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("終了日", "search_ebirthmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("終了日", "search_ebirthday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
}

/* 入力内容のチェック */
function lfCheckError() {
	global $objFormParam;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	// 特殊項目チェック
	$objErr->doFunc(array("受注番号1", "受注番号2", "search_order_id1", "search_order_id2"), array("GREATER_CHECK"));
	$objErr->doFunc(array("年齢1", "年齢2", "search_age1", "search_age2"), array("GREATER_CHECK"));
	$objErr->doFunc(array("購入金額1", "購入金額2", "search_total1", "search_total2"), array("GREATER_CHECK"));
	$objErr->doFunc(array("開始日", "search_startyear", "search_startmonth", "search_startday"), array("CHECK_DATE"));
	$objErr->doFunc(array("終了日", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_DATE"));
	$objErr->doFunc(array("開始日", "終了日", "search_startyear", "search_startmonth", "search_startday", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_SET_TERM"));
	
	$objErr->doFunc(array("開始日", "search_sbirthyear", "search_sbirthmonth", "search_sbirthday"), array("CHECK_DATE"));
	$objErr->doFunc(array("終了日", "search_ebirthyear", "search_ebirthmonth", "search_ebirthday"), array("CHECK_DATE"));
	$objErr->doFunc(array("開始日", "終了日", "search_sbirthyear", "search_sbirthmonth", "search_sbirthday", "search_ebirthyear", "search_ebirthmonth", "search_ebirthday"), array("CHECK_SET_TERM"));

	return $objErr->arrErr;
}


?>