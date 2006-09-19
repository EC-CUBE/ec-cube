<?php

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
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// 認証可否の判定
sfIsSuccess($objSess);

$objPage->arrMailTEMPLATE = $arrMAILTEMPLATE;

if ( $_POST['mode'] == 'id_set'){
	// テンプレートプルダウン変更時
	
	if ( sfCheckNumLength( $_POST['template_id']) ){
		$sql = "SELECT * FROM dtb_mailtemplate WHERE template_id = ?";
		$result = $conn->getAll($sql, array($_POST['template_id']) );
		if ( $result ){
			$objPage->arrForm = $result[0];
		} else {
			$objPage->arrForm['template_id'] = $_POST['template_id'];
		}
	}
	
} elseif ( $_POST['mode'] == 'regist' && sfCheckNumLength( $_POST['template_id']) ){

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
		unset($objPage->arrForm);
	}

}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

function lfRegist( $conn, $data ){
	
	$data['creator_id'] = $_SESSION['member_id'];
	sfprintr($conn);
	sfprintr($data);
	
	$sql = "SELECT * FROM dtb_mailtemplate WHERE template_id = ?";
	$result = $conn->getAll($sql, array($_POST['template_id']) );
	if ( $result ){
		$sql_where = "template_id = ". addslashes($_POST['template_id']);
		$conn->query("UPDATE dtb_mailtemplate SET template_id = ?, subject = ?,header = ?, footer = ?,creator_id = ?, update_date = now() WHERE ".$sql_where, $data);
	}else{
		$conn->query("INSERT INTO dtb_mailtemplate (template_id,subject,header,footer,creator_id,update_date,create_date) values ( ?,?,?,?,?,now(),now() )", $data);
	}

//	$conn->autoExecute("dtb_mailtemplate", $data, $sql_where);	
}


function lfConvertParam($array) {
	
	$new_array["template_id"] = $array["template_id"];
	$new_array["subject"] = mb_convert_kana($array["subject"] ,"KV");
	$new_array["header"] = mb_convert_kana($array["header"] ,"KV");
	$new_array["footer"] = mb_convert_kana($array["footer"] ,"KV");
	
	return $new_array;
}

/* 入力エラーのチェック */
function fnErrorCheck($array) {
	
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("テンプレート",'template_id'), array("EXIST_CHECK"));
	$objErr->doFunc(array("メールタイトル",'subject',MTEXT_LEN,"BIG"), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("ヘッダー",'header',LTEXT_LEN,"BIG"), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("フッター",'footer',LTEXT_LEN,"BIG"), array("MAX_LENGTH_CHECK"));

	return $objErr->arrErr;
}

?>