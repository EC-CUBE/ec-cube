<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
    
    var $mailTemp;
    var $arrMAILTEMPLATE;
	function LC_Page() {
		$this->tpl_mainpage = 'order/mail.tpl';
		$this->tpl_subnavi = 'order/subnavi.tpl';
		$this->tpl_mainno = 'order';		
		$this->tpl_subno = 'index';
		$this->tpl_subtitle = '�������';
		
		
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

// �����ѥ�᡼���ΰ����Ѥ�
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key)) {
		$objPage->arrSearchHidden[$key] = $val;	
	}
}

$objPage->tpl_order_id = $_POST['order_id'];
// DB������������ɤ߹���
lfGetOrderData($_POST['order_id']);

// --�ƥ�ץ졼�ȡ��ץ�������˥塼�κ���
$conn = new SC_DbConn();
$sql = "SELECT * FROM dtb_mailtemplate WHERE del_flg=0 ORDER BY template_id ASC";

$Temp = $conn->getAll($sql);//$Temp�˼��������ǡ�������Ū�˳�Ǽ
//�ƥ�ץ졼�ȥե�����˽��Ϥ��뤿����󼡸��������������
for($i = 0;$i < count($Temp);$i++){
    $arrTemplate[0][$i] = $Temp[$i]['template_id'];
    $arrTemplate[1][$i] = $Temp[$i]['template_name'];
}

//�ƥ�ץ졼�ȥե�����إǡ���������
$objPage->mailTemp = $arrTemplate;
$objPage->arrMAILTEMPLATE = $arrTemplate[1];

switch($_POST['mode']) {
case 'pre_edit':
	break;
case 'return':
	// POST�ͤμ���
	$objFormParam->setParam($_POST);
	break;
case 'send':
	// POST�ͤμ���
	$objFormParam->setParam($_POST);
	// �����ͤ��Ѵ�
	$objFormParam->convParam();
	$objPage->arrErr = $objFormParam->checkerror();
	// �᡼�������
	if (count($objPage->arrErr) == 0) {
		// ��ʸ���ե᡼��
		sfSendOrderMail($_POST['order_id'], $_POST['template_id'], $_POST['subject'], $_POST['body']);
	}
	header("Location: " . URL_SEARCH_ORDER);
	exit;
	break;	
case 'confirm':
	// POST�ͤμ���
	$objFormParam->setParam($_POST);
	// �����ͤ��Ѵ�
	$objFormParam->convParam();
	// �����ͤΰ����Ѥ�
	$objPage->arrHidden = $objFormParam->getHashArray();
	$objPage->arrErr = $objFormParam->checkerror();
	// �᡼�������
	if (count($objPage->arrErr) == 0) {
		// ��ʸ���ե᡼��(�����ʤ�)
		$objSendMail = sfSendOrderMail($_POST['order_id'], $_POST['template_id'], $_POST['subject'], $_POST['body'], false);
		// ��ǧ�ڡ�����ɽ��
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
	// POST�ͤμ���
    
	$objFormParam->setValue('template_id', $_POST['template_id']);
    
    //�ƥ�ץ졼�ȥե���������򤵤줿�ƥ�ץ졼��̾��ƥ�ץ졼��ID�ȴ�Ϣ�դ���
    $_POST['template_id'] = $arrTemplate[0][$_POST['template_id']];
    if(sfIsInt($_POST['template_id'])) {
        $objQuery = new SC_Query();
		$where = "template_id = ?";
		$arrRet = $objQuery->select("subject, body", "dtb_mailtemplate", $where, array($_POST['template_id']));
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
/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("�ƥ�ץ졼��", "template_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�᡼�륿���ȥ�", "subject", STEXT_LEN, "KVa",  array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
	$objFormParam->addParam("�إå���", "body", LTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
	//$objFormParam->addParam("�եå���", "footer", LTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
}

function lfGetOrderData($order_id) {
	global $objFormParam;
	global $objPage;
	if(sfIsInt($order_id)) {
		// DB������������ɤ߹���
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
