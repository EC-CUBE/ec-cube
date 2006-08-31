<?php

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
$tpl_DIR = ROOT_DIR . INCLUDE_DIR;
$pre_DIR = ROOT_DIR . INCLUDE_DIR. 'preview/';

// データ更新処理
if ($division != ''){
	// プレビュー用テンプレートに書き込み	
	$fp = fopen($pre_DIR.$division.'.tpl',"w");
	fwrite($fp, $_POST[$division]);
	fclose($fp);

	// 登録時はプレビュー用テンプレートをコピーする
	if ($_POST['mode'] == 'confirm'){
		copy($pre_DIR.$division.".tpl", $tpl_DIR.$division.".tpl");
	}

	// ヘッダーファイルの読み込み(プレビューデータ)
	$header_data = file_get_contents($pre_DIR . "header.tpl");
	
	// フッターファイルの読み込み(プレビューデータ)
	$footer_data = file_get_contents($pre_DIR . "footer.tpl");
	
	$objPage->tpl_onload="alert('編集が完了しました。');";
	
}else{
	// postでデータが渡されなければ新規読み込みと判断をし、プレビュー用データを正規のデータで上書きする
	if (!is_dir($pre_DIR)) {
		mkdir($pre_DIR);
	}
	copy($tpl_DIR . "header.tpl", $pre_DIR . "header.tpl");
	copy($tpl_DIR . "footer.tpl", $pre_DIR . "footer.tpl");
	
	// ヘッダーファイルの読み込み
	$header_data = file_get_contents($tpl_DIR . "header.tpl");
	// フッターファイルの読み込み
	$footer_data = file_get_contents($tpl_DIR . "footer.tpl");

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

// 画面の表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
