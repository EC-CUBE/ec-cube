<?php
/*
 * Copyright(c) 2000-2008 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrErr;		// エラーメッセージ出力用
	var $tpl_recv;		// 入力情報POST先
	var $arrForm;		// フォーム出力用
	function LC_Page() {
		$this->tpl_recv =  'index.php';
		$this->SHORTTEXT_MAX = STEXT_LEN;
		$this->MIDDLETEXT_MAX = MTEXT_LEN;
		$this->LONGTEXT_MAX = LTEXT_LEN;
		
		$this->arrYear  = array(2007=>"2007",2008=>"2008",2009=>"2009",2010=>"2010",2011=>"2011",2012=>"2012");
		$this->arrMonth = array("01"=>"01","02"=>"02","03"=>"03","04"=>"04","05"=>"05","06"=>"06","07"=>"07","08"=>"08","09"=>"09","10"=>"10","11"=>"11","12"=>"12");
		$this->arrDay   = array("01"=>"01","02"=>"02","03"=>"03","04"=>"04","05"=>"05","06"=>"06","07"=>"07","08"=>"08","09"=>"09","10"=>"10","11"=>"11","12"=>"12","13"=>"13","14"=>"14","15"=>"15","16"=>"16","17"=>"17","18"=>"18","19"=>"19","20"=>"20","21"=>"21","22"=>"22","23"=>"23","24"=>"24","25"=>"25","26"=>"26","27"=>"27","28"=>"28","29"=>"29","30"=>"30","31"=>"31");
		$this->arrMode  = array("納品書");
		$this->arrDownload = array("ブラウザに開く","ファイルに保存");
	}
}

$conn = new SC_DbConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);


// 受注番号があったら、セットする
if(sfIsInt($_GET['order_id'])) {
	$objPage->tpl_order_id = $_GET['order_id'];
}

// タイトルをセット
$arrForm['chohyo_title'] = "お買上げ明細書(納品書)";

// 今日の日付をセット
$arrForm['year']  = date("Y");
$arrForm['month'] = date("m");
$arrForm['day']   = date("d");

// メッセージ
$arrForm['chohyo_msg1'] = 'このたびはお買上げいただきありがとうございます。';
$arrForm['chohyo_msg2'] = '下記の内容にて納品させていただきます。';
$arrForm['chohyo_msg3'] = 'ご確認いただきますよう、お願いいたします。';

$objPage->arrForm = $arrForm;

// 画面遷移の正当性チェック用にuniqidを埋め込む
$objPage->tpl_uniqid = $objSess->getUniqId();

// テンプレート用変数の割り当て
$objView->assignobj($objPage);
$objView->display('pdf.d/input.tpl');
?>