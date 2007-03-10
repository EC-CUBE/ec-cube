<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");
require_once(DATA_PATH . "include/file_manager.inc");

class LC_Page {

	function LC_Page() {
		$this->tpl_mainpage = 'contents/campaign_design.tpl';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = "campaign";
		$this->tpl_mainno = 'contents';
		$this->header_row = 13;
		$this->contents_row = 13;
		$this->footer_row = 13;		
		$this->tpl_subtitle = 'キャンペーンデザイン編集';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// キャンペーンデータを引き継ぎ
if($_POST['mode'] != "") {
	$arrForm = $_POST;
} else {
	$arrForm = $_GET;
}

// 正しく値が取得できない場合はキャンペーンTOPへ
if($arrForm['campaign_id'] == "" || $arrForm['status'] == "") {
	header("location: ".URL_CAMPAIGN_TOP);
}

switch($arrForm['status']) {
	case 'active':
		$status = CAMPAIGN_TEMPLATE_ACTIVE;
		$objPage->tpl_campaign_title = "キャンペーン中デザイン編集";
		break;
	case 'end':
		$status = CAMPAIGN_TEMPLATE_END;
		$objPage->tpl_campaign_title = "キャンペーン終了デザイン編集";
		break;
	default:
		break;
}

// ディレクトリ名を取得名		
$directory_name = $objQuery->get("dtb_campaign", "directory_name", "campaign_id = ?", array($arrForm['campaign_id']));
// キャンペーンテンプレート格納ディレクトリ
$campaign_dir = CAMPAIGN_TEMPLATE_PATH . $directory_name . "/" .$status;

switch($_POST['mode']) {
case 'regist':
	// ファイルを更新
	sfWriteFile($arrForm['header'], $campaign_dir."header.tpl", "w");
	sfWriteFile($arrForm['contents'], $campaign_dir."contents.tpl", "w");
	sfWriteFile($arrForm['footer'], $campaign_dir."footer.tpl", "w");
	// サイトフレーム作成
	$site_frame  = $arrForm['header']."\n";
	$site_frame .= '<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>'."\n";
	$site_frame .= '<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>'."\n";
	$site_frame .= '<!--{include file=$tpl_mainpage}-->'."\n";
	$site_frame .= $arrForm['footer']."\n";
	sfWriteFile($site_frame, $campaign_dir."site_frame.tpl", "w");
	
	// 完了メッセージ（プレビュー時は表示しない）
	$objPage->tpl_onload="alert('登録が完了しました。');";
	break;
case 'preview':
	// プレビューを書き出し別窓で開く
	sfWriteFile($arrForm['header'] . $arrForm['contents'] . $arrForm['footer'], $campaign_dir."preview.tpl", "w");
	$objPage->tpl_onload = "win02('./campaign_preview.php?status=". $arrForm['status'] ."&campaign_id=". $arrForm['campaign_id'] ."', 'preview', '600', '400');";
	$objPage->header_data = $arrForm['header'];	
	$objPage->contents_data = $arrForm['contents'];	
	$objPage->footer_data = $arrForm['footer'];	
	break;
case 'return':
	// 登録ページへ戻る
	header("location: ".URL_CAMPAIGN_TOP);
	break;
default:	
	break;
}

if ($arrForm['header_row'] != ''){
	$objPage->header_row = $arrForm['header_row'];
}
if ($arrForm['contents_row'] != ''){
	$objPage->contents_row = $arrForm['contents_row'];
}
if ($arrForm['footer_row'] != ''){
	$objPage->footer_row = $arrForm['footer_row'];
}

if($_POST['mode'] != 'preview') {
	// ヘッダーファイルの読み込み
	$objPage->header_data = file_get_contents($campaign_dir . "header.tpl");	
	// コンテンツファイルの読み込み
	$objPage->contents_data = file_get_contents($campaign_dir . "contents.tpl");	
	// フッターファイルの読み込み
	$objPage->footer_data = file_get_contents($campaign_dir . "footer.tpl");
}

// フォームの値を格納
$objPage->arrForm = $arrForm;

// 画面の表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
