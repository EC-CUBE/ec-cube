<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_mainpage = 'basis/mail.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_mainno = 'basis';
		$this->tpl_subno = 'mail';
		$this->tpl_subtitle = 'メール設定';
	}
}

$conn = new SC_DBConn();
$objQuery = new SC_Query();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

//認証可否の判定
sfIsSuccess($objSess);

$objPage->arrMailTEMPLATE = $arrMAILTEMPLATE;

$objPage->arrSendType = array("パソコン","携帯");

if ( $_GET['mode'] == 'edit' && sfCheckNumLength($_GET['template_id'])===true ){
	
	if ( sfCheckNumLength( $_GET['template_id']) ){
		$sql = "SELECT * FROM dtb_mailtemplate WHERE template_id = ?";
		$result = $conn->getAll($sql, array($_GET['template_id']) );
        //print_r($result);
		if ( $result ){
			$objPage->arrForm = $result[0];
		} else {
			$objPage->arrForm['template_id'] = $_GET['template_id'];
		}
	}
	
} elseif ( $_POST['mode'] == 'regist' && sfCheckNumLength( $_POST['template_id']) ){
//    elseif ( $_GET['mode'] == 'regist' ){
	// POSTデータの引き継ぎ
	$objPage->arrForm = lfConvertParam($_POST);
	$objPage->arrErr = fnErrorCheck($objPage->arrForm);
	if ( $objPage->arrErr ){
		// エラーメッセージ
		$objPage->tpl_msg = "エラーが発生しました";
		
	} else {
		// 正常
		lfRegist($conn, $objPage->arrForm);
		
		// 完了メッセージ
		$objPage->tpl_onload = "window.alert('メール設定が完了しました。テンプレートを選択して内容をご確認ください。');";
		//unset($objPage->arrForm);
	}
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

function lfRegist( $conn, $data ){
	
	$data['creator_id'] = $_SESSION['member_id'];
	
	$sql = "SELECT * FROM dtb_mailtemplate WHERE template_id = ? AND del_flg = 0";
	$result = $conn->getAll($sql, array($_POST['template_id']) );
	if ( $result ){
		$sql_where = "template_id = ". addslashes($_POST['template_id']);
		$conn->query("UPDATE dtb_mailtemplate SET send_type = ?,template_id = ?, template_name = ?,subject = ?,header = ?, footer = ?,creator_id = ?, update_date = now() WHERE ".$sql_where, $data);
	}else{
		$conn->query("INSERT INTO dtb_mailtemplate (send_type,template_id,template_name,subject,header,footer,creator_id,update_date,create_date) values ( ?,?,?,?,?,?,?,now(),now() )", $data);
	}

}


function lfConvertParam($array) {
	
    $new_array["send_type"] = $array["send_type"];
	$new_array["template_id"] = $array["template_id"];
    $new_array["template_name"] = mb_convert_kana($array["template_name"],"KV");
	$new_array["subject"] = mb_convert_kana($array["subject"] ,"KV");
	$new_array["body"] = mb_convert_kana($array["body"] ,"KV");
	
	return $new_array;
}

/* 入力エラーのチェック */
function fnErrorCheck($array) {
	
	$objErr = new SC_CheckError($array);
	$objErr->doFunc(array("メールの種類",'send_type'), array("EXIST_CHECK"));
	$objErr->doFunc(array("テンプレート",'template_id'), array("EXIST_CHECK"));
    $objErr->doFunc(array("テンプレート",'template_name'), array("EXIST_CHECK"));
	$objErr->doFunc(array("メールタイトル",'subject',MTEXT_LEN,"BIG"), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("ヘッダー",'body',LTEXT_LEN,"BIG"), array("MAX_LENGTH_CHECK"));

	return $objErr->arrErr;
}

?>