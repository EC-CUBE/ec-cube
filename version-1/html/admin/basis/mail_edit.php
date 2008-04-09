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
		$this->tpl_mainpage = "basis/mail_edit.tpl";
		$this->tpl_subnavi = "basis/subnavi.tpl";
		$this->tpl_mainno = "basis";
		$this->tpl_subno = "mail";
		$this->tpl_subtitle = "メール設定";
	}
}

$objQuery = new SC_Query();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

//認証可否の判定
sfIsSuccess($objSess);

$objPage->arrMailTEMPLATE = $arrMAILTEMPLATE;
$objPage->arrSendType = $arrMailType;

// 編集/新規
if ($_POST['mode'] == "edit") {
	if (sfCheckNumLength($_POST['template_id']) === true) {
		$result = $objQuery->select("*", "dtb_mailtemplate", "template_id = ?", array($_POST['template_id']));
		if ($result) {
			$objPage->arrForm = $result[0];
		} else {
			$objPage->arrForm['template_id'] = $_POST['template_id'];
		}
	} else {
		$objPage->arrForm['template_id'] = 0;
	}

// 登録
} elseif ($_POST['mode'] == "regist" && sfCheckNumLength($_POST['template_id']) === true) {
	// POSTデータの引き継ぎ
	$objPage->arrForm = lfConvertParam($_POST);
	$objPage->arrErr = fnErrorCheck($objPage->arrForm);
	// エラー
	if ($objPage->arrErr) {
		$objPage->tpl_msg = "エラーが発生しました";
	// 正常
	} else {
		lfRegist($objQuery, $objPage->arrForm, $_POST['template_id']);
		sfReload("mode=complete");
	}

// 完了
} elseif ($_GET['mode'] == 'complete') {
	$objPage->tpl_mainpage = "basis/mail_complete.tpl";
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

function lfRegist($objQuery, $arrVal, $id) {
	$sqlval['template_name'] = $arrVal['template_name'];
	$sqlval['subject'] = $arrVal['subject'];
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$sqlval['body'] = $arrVal['body'];
	$sqlval['send_type'] = $arrVal['send_type'];
	$sqlval['update_date'] = "now()";
	
	$result = $objQuery->count("dtb_mailtemplate", "template_id=?", array($id));
	if ($result > 0) {
		$objQuery->update("dtb_mailtemplate", $sqlval, "template_id=?", array($id));
	} else {
		$sqlval['create_date'] = "now()";
		$objQuery->insert("dtb_mailtemplate", $sqlval);
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
	$objErr->doFunc(array("メールの内容",'body',LTEXT_LEN,"BIG"), array("MAX_LENGTH_CHECK","EXIST_CHECK"));
	
	return $objErr->arrErr;
}

?>