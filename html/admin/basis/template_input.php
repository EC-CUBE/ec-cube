<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	
	var $arrSession;
	var $site_info;
	var $objDate;
	var $arrForm;
	var $mode;
	var $arrMagazineType;
	var $title;
	
	function LC_Page() {
		$this->tpl_mainpage = 'basis/mail.tpl';
		$this->tpl_mainno = 'basis';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_subno = 'mail';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// 認証可否の判定
sfIsSuccess($objSess);

$objPage->arrSendType = array("","パソコン","携帯");
$objPage->mode = "regist";

// idが指定されているときは「編集」表示
if ( $_REQUEST['template_id'] ){
	$objPage->title = "編集";
} else {
	$objPage->title = "新規登録";
}

// モードによる処理分岐
if ( $_POST['mode'] == 'regist' ) {
	
	// 新規登録
	$objPage->arrForm = lfConvData( $_POST );
    $objPage->arrErr = lfErrorCheck($objPage->arrForm);
	
	if ( ! $objPage->arrErr ){
		// エラーが無いときは登録・編集
		lfRegistData( $objPage->arrForm, $_POST['template_id']);	
	}
} 

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);


function lfRegistData( $arrVal, $id = null ){
	
	$query = new SC_Query();
	
    $sqlval['template_name'] = $arrVal['template_name'];
	$sqlval['subject'] = $arrVal['subject'];
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$sqlval['body'] = $arrVal['body'];
	$sqlval['update_date'] = "now()";

	if ( $id ){
		$query->update("dtb_mailtemplate", $sqlval, "template_id=".$id );
	} else {
		$sqlval['create_date'] = "now()";
		$query->insert("dtb_mailtemplate", $sqlval);
	}
}

function lfConvData( $data ){
	
	 // 文字列の変換（mb_convert_kanaの変換オプション）							
	$arrFlag = array(
					  "template_name" => "KV"
                     ,"subject" => "KV"
					 ,"body" => "KV"
					);
		
	if ( is_array($data) ){
		foreach ($arrFlag as $key=>$line) {
			$data[$key] = mb_convert_kana($data[$key], $line);
		}
	}
	return $data;
}

// 入力エラーチェック
function lfErrorCheck($array) {
	$objErr = new SC_CheckError($array);
	$objErr->doFunc(array("メール形式", "send_type"), array("EXIST_CHECK"));
    $objErr->doFunc(array("テンプレート", "template_name"), array("EXIST_CHECK"));
	$objErr->doFunc(array("Subject", "subject"), array("EXIST_CHECK"));
	$objErr->doFunc(array("本文", 'body'), array("EXIST_CHECK"));

	return $objErr->arrErr;
}



?>