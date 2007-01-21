<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $tpl_category;	// 分類(HOME:1,人物登録:2,人物検索:3,システム:4,ログアウト:5)
	var $list_data;		// テーブルデータ取得用
	var $arrAUTHORITY;
	var $tpl_onload;
	var $tpl_disppage;	// 表示中のページ番号
	var $tpl_strnavi;
	function LC_Page() {
		$this->tpl_mainpage = 'system/index.tpl';
		$this->tpl_subnavi = 'system/subnavi.tpl';
		$this->tpl_mainno = 'system';
		$this->tpl_subno = 'index';
		$this->tpl_onload = 'fnGetRadioChecked();';
		$this->tpl_subtitle = 'メンバー管理';
		global $arrAUTHORITY;
		$this->arrAUTHORITY = $arrAUTHORITY;
	}
}

// セッションクラス
$objSess = new SC_Session();
// 認証可否の判定
sfIsSuccess($objSess);

$conn = new SC_DbConn();

// テンプレート変数の保持クラス
$objPage = new LC_Page();
// SQL作成用オブジェクト生成
$objSql = new SC_SelectSql();
$objSql->setSelect("SELECT member_id,name,department,login_id,authority,rank,work FROM dtb_member");
$objSql->setOrder("rank DESC");
$objSql->setWhere("del_flg <> 1 AND member_id <> ". ADMIN_ID);

//簡易クエリ実行オブジェクト
$oquery = new SC_Query();
// 行数の取得
$linemax = $oquery->count("dtb_member", "del_flg <> 1 AND member_id <>".ADMIN_ID);

// 稼動中の件数を取得
$workmax = $oquery->count("dtb_member", "work = 1 AND del_flg <> 1 AND member_id <>".ADMIN_ID);
$objPage->workmax= $workmax;

// ページ送りの処理
$objNavi = new SC_PageNavi($_GET['pageno'], $linemax, MEMBER_PMAX, "fnMemberPage", NAVI_PMAX);
$objPage->tpl_strnavi = $objNavi->strnavi;
$objPage->tpl_disppage = $objNavi->now_page;
$objPage->tpl_pagemax = $objNavi->max_page;
$startno = $objNavi->start_row;

// 取得範囲の指定(開始行番号、行数のセット)
$objSql->setLimitOffset(MEMBER_PMAX, $startno);
$objPage->list_data = $conn->getAll($objSql->getSql());

// ページの表示
$objView = new SC_AdminView();
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

?>