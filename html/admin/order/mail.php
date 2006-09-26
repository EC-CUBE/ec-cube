<?php
/*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'order/mail.tpl';
		$this->tpl_subnavi = 'order/subnavi.tpl';
		$this->tpl_mainno = 'order';		
		$this->tpl_subno = 'index';
		$this->tpl_subtitle = '受注管理';
		global $arrMAILTEMPLATE;
		$this->arrMAILTEMPLATE = $arrMAILTEMPLATE;
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
sfIsSuccess($objSess);

// 検索パラメータの引き継ぎ
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key)) {
		$objPage->arrSearchHidden[$key] = $val;	
	}
}

$objPage->tpl_order_id = $_POST['order_id'];

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();

switch($_POST['mode']) {
case 'pre_edit':
	break;
case 'return':
	// POST値の取得
	$objFormParam->setParam($_POST);
	break;
case 'send':
	// POST値の取得
	$objFormParam->setParam($_POST);
	// 入力値の変換
	$objFormParam->convParam();
	$objPage->arrErr = $objFormParam->checkerror();
	// メールの送信
	if (count($objPage->arrErr) == 0) {
		// 注文受付メール
		sfSendOrderMail($_POST['order_id'], $_POST['template_id'], $_POST['subject'], $_POST['header'], $_POST['footer']);
	}
	header("Location: " . URL_SEARCH_ORDER);
	exit;
	break;	
case 'confirm':
	// POST値の取得
	$objFormParam->setParam($_POST);
	// 入力値の変換
	$objFormParam->convParam();
	// 入力値の引き継ぎ
	$objPage->arrHidden = $objFormParam->getHashArray();
	$objPage->arrErr = $objFormParam->checkerror();
	// メールの送信
	if (count($objPage->arrErr) == 0) {
		// 注文受付メール(送信なし)
		$objSendMail = sfSendOrderMail($_POST['order_id'], $_POST['template_id'], $_POST['subject'], $_POST['header'], $_POST['footer'], false);
		// 確認ページの表示
		$objPage->tpl_subject = $objSendMail->subject;
		$objPage->tpl_body = $objSendMail->body;
		$objPage->tpl_to = $objSendMail->tpl_to;
		$objPage->tpl_mainpage = 'order/mail_confirm.tpl';
		
		$objView->assignobj($objPage);
		$objView->display(MAIN_FRAME);
		
		exit;	
	}
	break;
case 'change':
	// POST値の取得
	$objFormParam->setValue('template_id', $_POST['template_id']);
	if(sfIsInt($_POST['template_id'])) {
		$objQuery = new SC_Query();
		$where = "template_id = ?";
		$arrRet = $objQuery->select("subject, header, footer", "dtb_mailtemplate", $where, array($_POST['template_id']));
		$objFormParam->setParam($arrRet[0]);
	}
	break;
}

$objQuery = new SC_Query();
$col = "send_date, subject, template_id, send_id";
$where = "order_id = ?";
$objQuery->setorder("send_date DESC");

if(sfIsInt($_POST['order_id'])) {
	$objPage->arrMailHistory = $objQuery->select($col, "dtb_mail_history", $where, array($_POST['order_id']));
}

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("テンプレート", "template_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("メールタイトル", "subject", STEXT_LEN, "KVa",  array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
	$objFormParam->addParam("ヘッダー", "header", LTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
	$objFormParam->addParam("フッター", "footer", LTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
}
