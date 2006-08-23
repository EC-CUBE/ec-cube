<?php

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
		$this->tpl_mainpage = 'mail/template_input.tpl';
		$this->tpl_mainno = 'mail';
		$this->tpl_subnavi = 'mail/subnavi.tpl';
		$this->tpl_subno = "template";
		$this->tpl_subtitle = 'テンプレート設定';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// 認証可否の判定
sfIsSuccess($objSess);

$objPage->arrMagazineType = $arrMagazineType;
$objPage->mode = "regist";

// idが指定されているときは「編集」表示
if ( $_REQUEST['template_id'] ){
	$objPage->title = "編集";
} else {
	$objPage->title = "新規登録";
}

// モードによる処理分岐
if ( $_GET['mode'] == 'edit' && sfCheckNumLength($_GET['template_id'])===true ){
	
	// 編集
	$sql = "SELECT * FROM dtb_mailmaga_template WHERE template_id = ? AND delete = 0";
	$result = $conn->getAll($sql, array($_GET['template_id']));
	$objPage->arrForm = $result[0];
	
		
} elseif ( $_POST['mode'] == 'regist' ) {
	
	// 新規登録
	$objPage->arrForm = lfConvData( $_POST );
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);
	
	if ( ! $objPage->arrErr ){
		// エラーが無いときは登録・編集
		lfRegistData( $objPage->arrForm, $_POST['template_id']);	
		sfReload("mode=complete");	// 自分を再読込して、完了画面へ遷移
	}
	
} elseif ( $_GET['mode'] == 'complete' ) {		
	
	// 完了画面表示
	$objPage->tpl_mainpage = 'mail/template_complete.tpl';
	
} 






$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);


function lfRegistData( $arrVal, $id = null ){
	
	$query = new SC_Query();
	
	$sqlval['subject'] = $arrVal['subject'];
	$sqlval['mail_method'] = $arrVal['mail_method'];
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$sqlval['body'] = $arrVal['body'];
	
	if ( $id ){
		$query->update("dtb_mailmaga_template", $sqlval, "template_id=".$id );
	} else {
		$query->insert("dtb_mailmaga_template", $sqlval);
	}
}



function lfConvData( $data ){
	
	 // 文字列の変換（mb_convert_kanaの変換オプション）							
	$arrFlag = array(
					  "subject" => "aKV"
					 ,"body" => "aKV"
					);
		
	if ( is_array($data) ){
		foreach ($arrFlag as $key=>$line) {
			$data[$key] = mb_convert_kana($data[$key], $line);
		}
	}

	return $data;
}

// 入力エラーチェック
function lfErrorCheck() {
	$objErr = new SC_CheckError();
	
	$objErr->doFunc(array("メール形式", "mail_method"), array("EXIST_CHECK", "ALNUM_CHECK"));
	$objErr->doFunc(array("Subject", "subject", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("本文", 'body', LLTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));

	return $objErr->arrErr;
}



?>