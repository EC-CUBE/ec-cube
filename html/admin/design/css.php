<?php

require_once("../../require.php");

class LC_Page {
	var $arrForm;
	var $arrHidden;

	function LC_Page() {
		$this->tpl_mainpage = 'design/css.tpl';
		$this->tpl_subnavi 	= 'design/subnavi.tpl';
		$this->area_row = 30;
		$this->tpl_subno = "css";
		$this->tpl_mainno = "design";
		$this->tpl_subtitle = 'CSS編集';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

$css_path = ROOT_DIR . USER_DIR . "css/contents.css";

// データ更新処理
if ($_POST['mode'] == 'confirm'){
	// プレビュー用テンプレートに書き込み	
	$fp = fopen($css_path,"w");
	fwrite($fp, $_POST['css']);
	fclose($fp);
	
	$objPage->tpl_onload="alert('編集が完了しました。');";
}

// CSSファイルの読み込み
if(file_exists($css_path)){
	$css_data = file_get_contents($css_path);
}

// テキストエリアに表示
$objPage->css_data = $css_data;

// 画面の表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
