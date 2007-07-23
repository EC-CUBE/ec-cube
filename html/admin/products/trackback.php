<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once("./index_csv.php");

session_start();

class LC_Page {
	var $arrSession;
	function LC_Page() {
		global $arrPageMax;
		$this->arrPageMax = $arrPageMax;
		$this->tpl_mainpage = 'products/trackback.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';
		$this->tpl_subno = 'trackback';
		$this->tpl_pager = DATA_PATH . 'Smarty/templates/admin/pager.tpl';
		$this->tpl_subtitle = 'トラックバック管理';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objDate = new SC_Date();
$objQuery = new SC_Query();

// 状態の設定
$objPage->arrTrackBackStatus = $arrTrackBackStatus;

// 登録・更新検索開始年
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrStartYear = $objDate->getYear();
$objPage->arrStartMonth = $objDate->getMonth();
$objPage->arrStartDay = $objDate->getDay();
// 登録・更新検索終了年
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrEndYear = $objDate->getYear();
$objPage->arrEndMonth = $objDate->getMonth();
$objPage->arrEndDay = $objDate->getDay();

// 認証可否の判定
sfIsSuccess($objSess);

// トラックバック情報のカラムの取得(viewとの結合のため、テーブルをAと定義しておく)
$select = "A.trackback_id, A.product_id, A.blog_name, A.title, A.url, ";
$select .= "A.excerpt, A.status, A.create_date, A.update_date, B.name";
$from = "dtb_trackback AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id ";

// 検索ワードの引き継ぎ
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key)) {
		$objPage->arrHidden[$key] = $val;
	}
}

// トラックバックの削除
if ($_POST['mode'] == "delete") {
	$objQuery->exec("UPDATE dtb_trackback SET del_flg = 1, update_date = now() WHERE trackback_id = ?", array($_POST['trackback_id']));
}
	
if ($_POST['mode'] == 'search' || $_POST['mode'] == 'csv' || $_POST['mode'] == 'delete'){
	
	//削除されていない商品を検索
	$where="A.del_flg = 0 AND B.del_flg = 0";
	$objPage->arrForm = $_POST;

	//エラーチェック
	$objPage->arrErr = lfCheckError();

	if (!$objPage->arrErr) {
		foreach ($_POST as $key => $val) {

			$val = sfManualEscape($val);
			
			if ($val == "") {
				continue;
			}
			
			switch ($key) {

				case 'search_blog_name':
					$val = ereg_replace(" ", "%", $val);
					$val = ereg_replace("　", "%", $val);
					$where.= " AND A.blog_name ILIKE ? ";
					$arrval[] = "%$val%";
					break;
			
				case 'search_blog_title':
					$val = ereg_replace(" ", "%", $val);
					$val = ereg_replace("　", "%", $val);
					$where.= " AND A.title ILIKE ? ";
					$arrval[] = "%$val%";
					break;
			
				case 'search_blog_url':
					$val = ereg_replace(" ", "%", $val);
					$val = ereg_replace("　", "%", $val);
					$where.= " AND A.url ILIKE ? ";
					$arrval[] = "%$val%";
					break;
					
				case 'search_status':
					if (isset($_POST['search_status'])) {
						$where.= " AND A.status = ? ";
						$arrval[] = $val;
					}
					break;
							
				case 'search_name':
					$val = ereg_replace(" ", "%", $val);
					$val = ereg_replace("　", "%", $val);
					$where.= " AND B.name ILIKE ? ";
					$arrval[] = "%$val%";
					break;
					
				case 'search_product_code':
					$val = ereg_replace(" ", "%", $val);
					$val = ereg_replace("　", "%", $val);
					$where.= " AND B.product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code ILIKE ? )";
					$arrval[] = "%$val%";
					break;
					
				case 'search_startyear':
					if (isset($_POST['search_startyear']) && isset($_POST['search_startmonth']) && isset($_POST['search_startday'])) {
						$date = sfGetTimestamp($_POST['search_startyear'], $_POST['search_startmonth'], $_POST['search_startday']);
						$where.= " AND A.create_date >= ? ";
						$arrval[] = $date;
					}
					break;
	
				case 'search_endyear':
					if (isset($_POST['search_startyear']) && isset($_POST['search_startmonth']) && isset($_POST['search_startday'])) {
						$date = sfGetTimestamp($_POST['search_endyear'], $_POST['search_endmonth'], $_POST['search_endday']);
						
						$end_date = date("Y/m/d",strtotime("1 day" ,strtotime($date)));
						
						$where.= " AND A.create_date <= cast('$end_date' as date) ";
					}
					break;
			}
		
		}
			
	}
	
	$order = "A.create_date DESC";
	
	// ページ送りの処理
	if(is_numeric($_POST['search_page_max'])) {	
		$page_max = $_POST['search_page_max'];
	} else {
		$page_max = SEARCH_PMAX;
	}
	
	$linemax = $objQuery->count($from, $where, $arrval);
	$objPage->tpl_linemax = $linemax;
	
	// ページ送りの取得
	$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnNaviSearchPage", NAVI_PMAX);
	$objPage->arrPagenavi = $objNavi->arrPagenavi;
	$startno = $objNavi->start_row;

	$objPage->tpl_pageno = $_POST['search_pageno'];
	
	// 取得範囲の指定(開始行番号、行数のセット)
	$objQuery->setlimitoffset($page_max, $startno);

	// 表示順序
	$objQuery->setorder($order);
	
	//検索結果の取得
	$objPage->arrTrackback = $objQuery->select($select, $from, $where, $arrval);
	
	//CSVダウンロード
	if ($_POST['mode'] == 'csv'){
		// オプションの指定
		$option = "ORDER BY A.trackback_id";
		// CSV出力タイトル行の作成
		$head = sfGetCSVList($arrTRACKBACK_CVSTITLE);
		$data = lfGetTrackbackCSV($where, '', $arrval);
		// CSVを送信する。
		sfCSVDownload($head.$data);
		exit;
	}	
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-------------------------------------------------------------------------------------

// 入力エラーチェック
function lfCheckError() {
	$objErr = new SC_CheckError();
	switch ($_POST['mode']){
		case 'search':
		$objErr->doFunc(array("投稿者", "search_startyear", "search_startmonth", "search_startday"), array("CHECK_DATE"));
		$objErr->doFunc(array("開始日", "search_startyear", "search_startmonth", "search_startday"), array("CHECK_DATE"));
		$objErr->doFunc(array("終了日", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_DATE"));
		$objErr->doFunc(array("開始日", "終了日", "search_startyear", "search_startmonth", "search_startday", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_SET_TERM"));
		break;
		
		case 'complete':
		$objErr->doFunc(array("おすすめレベル", "recommend_level"), array("SELECT_CHECK"));
		$objErr->doFunc(array("タイトル", "title", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
		$objErr->doFunc(array("コメント", "comment", LTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
		break;
	}
	return $objErr->arrErr;
}

?>
