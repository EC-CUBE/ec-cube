<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

class LC_Page {
	var $arrForm;
	var $arrHidden;

	function LC_Page() {
		$this->tpl_mainpage = 'design/header.tpl';
		$this->tpl_subnavi 	= 'design/subnavi.tpl';
		$this->header_row = 13;
		$this->footer_row = 13;
		$this->tpl_subno = "header";
		$this->tpl_mainno = "design";
		$this->tpl_subtitle = 'ヘッダー･フッター編集';
		$this->tpl_onload = 'comment_start(); comment_end();';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

$division = $_POST['division'];
$pre_DIR = USER_INC_PATH . 'preview/';

// データ更新処理
if ($division != ''){
	// プレビュー用テンプレートに書き込み	
	$fp = fopen($pre_DIR.$division.'.tpl',"w");
	fwrite($fp, $_POST[$division]);
	fclose($fp);

	// 登録時はプレビュー用テンプレートをコピーする
	if ($_POST['mode'] == 'confirm'){
		copy($pre_DIR.$division.".tpl", USER_INC_PATH . $division . ".tpl");
		// 完了メッセージ（プレビュー時は表示しない）
		$objPage->tpl_onload="alert('登録が完了しました。');";
		
		// テキストエリアの幅を元に戻す(処理の統一のため)
		$_POST['header_row'] = "";
		$_POST['footer_row'] = "";
	}else if ($_POST['mode'] == 'preview'){
		if ($division == "header") $objPage->header_prev = "on";
		if ($division == "footer") $objPage->footer_prev = "on";
	}

	// ヘッダーファイルの読み込み(プレビューデータ)
	$header_data = file_get_contents($pre_DIR . "header.tpl");
	
	// フッターファイルの読み込み(プレビューデータ)
	$footer_data = file_get_contents($pre_DIR . "footer.tpl");
}else{
	// postでデータが渡されなければ新規読み込みと判断をし、プレビュー用データを正規のデータで上書きする
	if (!is_dir($pre_DIR)) {
		mkdir($pre_DIR);
	}
	copy(USER_INC_PATH . "header.tpl", $pre_DIR . "header.tpl");
	copy(USER_INC_PATH . "footer.tpl", $pre_DIR . "footer.tpl");
	
	// ヘッダーファイルの読み込み
	$header_data = file_get_contents(USER_INC_PATH . "header.tpl");
	// フッターファイルの読み込み
	$footer_data = file_get_contents(USER_INC_PATH . "footer.tpl");

}

// テキストエリアに表示
$objPage->header_data = $header_data;
$objPage->footer_data = $footer_data;

if ($_POST['header_row'] != ''){
	$objPage->header_row = $_POST['header_row'];
}

if ($_POST['footer_row'] != ''){
	$objPage->footer_row = $_POST['footer_row'];
}

// ブラウザタイプ
$objPage->browser_type = $_POST['browser_type'];

// 画面の表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
