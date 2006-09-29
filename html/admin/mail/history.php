<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	function LC_Page() {
		$this->tpl_mainpage = 'mail/history.tpl';
		$this->tpl_mainno = 'mail';
		$this->tpl_subnavi = 'mail/subnavi.tpl';
		$this->tpl_subno = "history";
		$this->tpl_subtitle = '配信履歴';
	}
}

//---- ページ初期設定
$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objDate = new SC_Date();

// 認証可否の判定
sfIsSuccess($objSess);

// 削除時
if ( sfCheckNumLength($_GET['send_id']) && ($_GET['mode']=='delete') ){
	
	$sql = "UPDATE dtb_send_history SET del_flg = 1 WHERE send_id = ?";
	$conn->query($sql, array($_GET['send_id']) );
	sfReload();

}	
	$col = "*";
	$from = "dtb_send_history";
	
	$where .= " del_flg = ?";
	$arrval[] = "0";
	
	$objQuery = new SC_Query();
	// 行数の取得
	$linemax = $objQuery->count($from, $where, $arrval);
	$objPage->tpl_linemax = $linemax;				// 何件が該当しました。表示用
	
	// ページ送りの取得
	$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, SEARCH_PMAX, "fnNaviSearchPage", NAVI_PMAX);
	$objPage->tpl_strnavi = $objNavi->strnavi;		// 表示文字列
	$startno = $objNavi->start_row;
	
	// 取得範囲の指定(開始行番号、行数のセット)
	$objQuery->setlimitoffset(SEARCH_PMAX, $startno);
	
	// 表示順序
	$order = "start_date DESC, send_id DESC";
	$objQuery->setorder($order);
	
	// 検索結果の取得
	$objPage->arrDataList = $objQuery->select($col, $from, $where, $arrval);
		$objQuery->getlastquery();

	
//----　ページ表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
