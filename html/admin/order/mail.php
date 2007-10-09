<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

// 認証可否の判定
sfIsSuccess(new SC_Session());

// order_idの検証
if (lfIsValidOrderID() !== true) {
    sfDispError('');
}

class LC_Page {
    var $arrMAILTEMPLATE;

    function LC_Page() {
		$this->tpl_mainpage = 'order/mail.tpl';
		$this->tpl_subnavi = 'order/subnavi.tpl';
		$this->tpl_mainno = 'order';
		$this->tpl_subno = 'index';
		$this->tpl_subtitle = '受注管理';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objFormParam = new SC_FormParam();

// パラメータ情報の初期化
lfInitParam();

// 検索パラメータの引き継ぎ
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key)) {
		$objPage->arrSearchHidden[$key] = $val;
	}
}

$objPage->tpl_order_id = $_POST['order_id'];

// DBから受注情報を読み込む
lfGetOrderData($_POST['order_id']);

//テンプレートファイルへデータを代入
$objPage->arrMAILTEMPLATE = lfCreateTemplateList();

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
switch($mode) {
// 受注検索からの遷移
case 'pre_edit':
	break;
// 確認画面から戻る.
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
		sfSendOrderMail($_POST['order_id'], $_POST['template_id'], $_POST['subject'], $_POST['body']);
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
		$objSendMail = sfSendOrderMail($_POST['order_id'], $_POST['template_id'], $_POST['subject'], $_POST['body'], false);
		// 確認ページの表示
		$objPage->tpl_subject = $objSendMail->subject;
		$objPage->tpl_body = mb_convert_encoding( $objSendMail->body, "EUC-JP", "auto" );
		$objPage->tpl_to = $objSendMail->tpl_to;
		$objPage->tpl_mainpage = 'order/mail_confirm.tpl';

		$objView->assignobj($objPage);
		$objView->display(MAIN_FRAME);

		exit;
	}
	break;
case 'change':
	$objFormParam->setValue('template_id', $_POST['template_id']);

    if(sfIsInt($_POST['template_id'])) {
        $objQuery = new SC_Query();
		$where = "template_id = ?";
		$arrRet = $objQuery->select("subject, body", "dtb_mailtemplate", $where, array($_POST['template_id']));
        $objFormParam->setParam($arrRet[0]);
	}
	break;
}

if(sfIsInt($_POST['order_id'])) {
	$objPage->arrMailHistory = lfGetMailHistory($_POST['order_id']);
}

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignObj($objPage);
$objView->display(MAIN_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("テンプレート", "template_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("メールタイトル", "subject", STEXT_LEN, "KVa",  array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
	$objFormParam->addParam("本文", "body", LTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
}

function lfGetOrderData($order_id) {
	global $objFormParam;
	global $objPage;
	if(sfIsInt($order_id)) {
		// DBから受注情報を読み込む
		$objQuery = new SC_Query();
		$where = "order_id = ?";
		$arrRet = $objQuery->select("*", "dtb_order", $where, array($order_id));
		$objFormParam->setParam($arrRet[0]);
		list($point, $total_point) = sfGetCustomerPoint($order_id, $arrRet[0]['use_point'], $arrRet[0]['add_point']);
		$objFormParam->setValue('total_point', $total_point);
		$objFormParam->setValue('point', $point);
		$objPage->arrDisp = $arrRet[0];
	}
}

/**
 * POSTされるorder_idを検証する.
 *
 * @param void
 * @return boolean
 */
function lfIsValidOrderID() {
    if (isset($_POST['order_id']) && sfIsint($_POST['order_id'])) {
        return true;
    }
    return false;
}

/**
 * テンプレートプルダウンメニューの配列を作成する.
 * array(
 *   array(template_id => template_name),
 *   array(template_id => template_name),
 *   ...
 * )
 *
 * @param void
 * @return array
 */
function lfCreateTemplateList() {
    $objQuery = new SC_Query;
    $objQuery->setOrder('template_id ASC');
    $arrTemp = $objQuery->select('template_id, template_name', 'dtb_mailtemplate', 'del_flg = 0');

    $arrRet = array();
    foreach($arrTemp as $val) {
        $arrRet[$val['template_id']] = $val['template_name'];
    }

    return $arrRet;
}

/**
 * メール配信履歴を取得する.
 *
 * @param integer $order_id
 * @return array
 */
function lfGetMailHistory($order_id) {
    $cols  = "send_date, subject, template_id, send_id";
    $where = "order_id = ?";
    $objQuery = new SC_Query();
    $objQuery->setorder("send_date DESC");

    $arrRet = $objQuery->select($cols, "dtb_mail_history", $where, array($order_id));
    return $arrRet;
}
