<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 */

require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_css = '/css/layout/shopping/pay.css';
		$this->tpl_mainpage = 'nonmember/payment.tpl';
		$this->tpl_onload = 'fnCheckInputPoint();';
		$this->tpl_title = "����ʧ��ˡ�λ���";
		/*
		 session_start����no-cache�إå������������뤳�Ȥ�
		 �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
		 private-no-expire:���饤����ȤΥ���å������Ĥ��롣
		*/
		session_cache_limiter('private-no-expire');		
	}
}

$objPage = new LC_Page();
$objView = new SC_MobileView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objCustomer = new SC_Customer();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();
// POST�ͤμ���
$objFormParam->setParam($_POST);
// �桼����ˡ���ID�μ����ȹ������֤�������������å�
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);
// ��ˡ���ID������Ѥ�
$objPage->tpl_uniqid = $uniqid;

// �������������å�
if($objCustomer->isLoginSuccess()) {
	$objPage->tpl_login = '1';
	$objPage->tpl_user_point = $objCustomer->getValue('point');
}

// ��ۤμ��� (�������������ڤ줿���ˤϤ��δؿ���ˤƤ��ξ��ʤθĿ������ˤʤ�)
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
$objPage->arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

// ��������ξ��ʤ�����ڤ�����å�
$objCartSess->chkSoldOut($objCartSess->getCartList(), true);

// ���ܥ���ν���
if (!empty($_POST['return'])) {
	switch ($_POST['mode']) {
	case 'confirm':
		$_POST['mode'] = 'payment';
		break;
	default:
		// ����ʿ�ܤǤ��뤳�Ȥ�Ͽ���Ƥ���
		$objSiteSess->setRegistFlag();
		//header("Location: " . gfAddSessionId(MOBILE_URL_SHOP_TOP));
        header("Location: " . gfAddSessionId('../nonmember/deliv.php'));
		exit;
//        $_POST['mode'] = "deliv_date";
	}
}

switch($_POST['mode']) {
// ��ʧ����ˡ���� �� ��ã��������
case 'deliv_date':
	
    // �����ͤ��Ѵ�
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($objPage->arrData, $arrInfo, $objCartSess );
	if (!isset($objPage->arrErr['payment_id'])) {
		// ��ʧ����ˡ�����ϥ��顼�ʤ�
		$objPage->tpl_mainpage = 'nonmember/deliv_date.tpl';
		$objPage->tpl_title = "��ã��������";
		break;
	} else {
		// �桼����ˡ���ID�μ���
		$uniqid = $objSiteSess->getUniqId();
		// �������ơ��֥뤫��ξ�����Ǽ
		lfSetOrderTempData($uniqid);
	}
	break;
case 'confirm':
	// �����ͤ��Ѵ�
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($objPage->arrData, $arrInfo, $objCartSess );
	// ���ϥ��顼�ʤ�
	if(count($objPage->arrErr) == 0) {
		// DB�ؤΥǡ�����Ͽ
		lfRegistData($uniqid);
		// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
		$objSiteSess->setRegistFlag();
		// ��ǧ�ڡ����ذ�ư
		//header("Location: " . gfAddSessionId(MOBILE_URL_SHOP_CONFIRM));
        header("Location: " . gfAddSessionId('./confirm.php'));
		exit;
	}else{
		// �桼����ˡ���ID�μ���
		$uniqid = $objSiteSess->getUniqId();
		// �������ơ��֥뤫��ξ�����Ǽ
		lfSetOrderTempData($uniqid);
		if (!isset($objPage->arrErr['payment_id'])) {
			// ��ʧ����ˡ�����ϥ��顼�ʤ�
			$objPage->tpl_mainpage = 'nonmember/deliv_date.tpl';
			$objPage->tpl_title = "��ã��������";
		}
	}
	break;
// ���Υڡ��������
case 'return':
	// �����ξ��
	// ����ʿ�ܤǤ��뤳�Ȥ�Ͽ���Ƥ���
	$objSiteSess->setRegistFlag();
	header("Location: " . gfAddSessionId('../shopping/index.php'));
	exit;
	break;
// ��ʧ����ˡ���ѹ����줿���
case 'payment':
	// ������break�ϡ���̣������Τǳ����ʤ��ǲ�������
	break;
default:
	// �������ơ��֥뤫��ξ�����Ǽ
	lfSetOrderTempData($uniqid);
	break;
}

// Ź�޾���μ���
$arrInfo = $objSiteInfo->data;
// ������ۤμ�����
$total_pretax = $objCartSess->getAllProductsTotal($arrInfo);
// ��ʧ����ˡ�μ���
$objPage->arrPayment = lfGetPayment($total_pretax);
// �������֤μ���
$arrRet = sfGetDelivTime($objFormParam->getValue('payment_id'));
$objPage->arrDelivTime = sfArrKeyValue($arrRet, 'time_id', 'deliv_time');
$objPage->objCustomer = $objCustomer;
//�������������μ���
$objPage->arrDelivDate = lfGetDelivDate();

$objPage->arrForm = $objFormParam->getFormParamList();

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//--------------------------------------------------------------------------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("����ʧ����ˡ", "payment_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�ݥ����", "use_point", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK", "ZERO_START"));
	$objFormParam->addParam("��ã����", "deliv_time_id", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("������", "message", LTEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ݥ���Ȥ���Ѥ���", "point_check", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), '2');
	$objFormParam->addParam("��ã��", "deliv_date", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
}

function lfGetPayment($total_pretax) {
	$objQuery = new SC_Query();
	$objQuery->setorder("rank DESC");
	//�������Ƥ��ʤ���ʧ��ˡ�����
	$arrRet = $objQuery->select("payment_id, payment_method, rule, upper_rule, note, payment_image", "dtb_payment", "del_flg = 0 AND deliv_id IN (SELECT deliv_id FROM dtb_deliv WHERE del_flg = 0) ");
	//���Ѿ�狼���ʧ��ǽ��ˡ��Ƚ��
	foreach($arrRet as $data) {
		//���¤Ⱦ�¤����ꤵ��Ƥ���
		if($data['rule'] > 0 && $data['upper_rule'] > 0) {
			if($data['rule'] <= $total_pretax && $data['upper_rule'] >= $total_pretax) {
				$arrPayment[] = $data;
			}
		//���¤Τ����ꤵ��Ƥ���
		} elseif($data['rule'] > 0) {	
			if($data['rule'] <= $total_pretax) {
				$arrPayment[] = $data;
			}
		//��¤Τ����ꤵ��Ƥ���
		} elseif($data['upper_rule'] > 0) {
			if($data['upper_rule'] >= $total_pretax) {
				$arrPayment[] = $data;
			}
		//����ʤ�
		} else {
			$arrPayment[] = $data;
		}	
	}
	return $arrPayment;	
}

/* �������ƤΥ����å� */
function lfCheckError($arrData, $arrInfo, $objCartSess ) {
	global $objFormParam;
	global $objCustomer;
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	if($_POST['point_check'] == '1') {
		$objErr->doFunc(array("�ݥ���Ȥ���Ѥ���", "point_check"), array("EXIST_CHECK"));
		$objErr->doFunc(array("�ݥ����", "use_point"), array("EXIST_CHECK"));
		$max_point = $objCustomer->getValue('point');
		if($max_point == "") {
			$max_point = 0;
		}
		if($arrRet['use_point'] > $max_point) {
			$objErr->arrErr['use_point'] = "�� �����ѥݥ���Ȥ�����ݥ���Ȥ�Ķ���Ƥ��ޤ���<br>";
		}
		if(($arrRet['use_point'] * POINT_VALUE) > $arrData['subtotal']) {
			$objErr->arrErr['use_point'] = "�� �����ѥݥ���Ȥ���������ۤ�Ķ���Ƥ��ޤ���<br>";
		}
	}
	
   	//����ʧ��ˡ��������
    // ������ۤμ�����
    $total_pretax = $objCartSess->getAllProductsTotal($arrInfo);
    // ��ʧ����ˡ�μ���
    $arrPayment = lfGetPayment($total_pretax);
    $pay_flag = true;
    foreach ($arrPayment as $key => $payment) {
        if ($payment['payment_id'] == $arrRet['payment_id']) {
                $pay_flag = false;
                break;
            }
    }
    if ($pay_flag) {
        sfDispSiteError(CUSTOMER_ERROR);
    }
	
	return $objErr->arrErr;
}

/* ��ʧ����ˡʸ����μ��� */
function lfGetPaymentInfo($payment_id) {
	$objQuery = new SC_Query();
	$where = "payment_id = ?";
	$arrRet = $objQuery->select("payment_method, charge", "dtb_payment", $where, array($payment_id));
	return (array($arrRet[0]['payment_method'], $arrRet[0]['charge']));
}

/* ��������ʸ����μ��� */
function lfGetDelivTimeInfo($time_id) {
	$objQuery = new SC_Query();
	$where = "time_id = ?";
	$arrRet = $objQuery->select("deliv_id, deliv_time", "dtb_delivtime", $where, array($time_id));
	return (array($arrRet[0]['deliv_id'], $arrRet[0]['deliv_time']));
}

/* DB�إǡ�������Ͽ */
function lfRegistData($uniqid) {
	global $objFormParam;
	$arrRet = $objFormParam->getHashArray();
	$sqlval = $objFormParam->getDbArray();
	// ��Ͽ�ǡ����κ���
	$sqlval['order_temp_id'] = $uniqid;
	$sqlval['update_date'] = 'Now()';
	
	if($sqlval['payment_id'] != "") {
		list($sqlval['payment_method'], $sqlval['charge']) = lfGetPaymentInfo($sqlval['payment_id']);
	} else {
		$sqlval['payment_id'] = '0';
		$sqlval['payment_method'] = "";
	}
	
	if($sqlval['deliv_time_id'] != "") {
		list($sqlval['deliv_id'], $sqlval['deliv_time']) = lfGetDelivTimeInfo($sqlval['deliv_time_id']);
	} else {
		$sqlval['deliv_time_id'] = '0';
		$sqlval['deliv_id'] = '0';
		$sqlval['deliv_time'] = "";
	}
	
	// ���ѥݥ���Ȥ�����
	if($sqlval['point_check'] != '1') {
		$sqlval['use_point'] = 0;
	}
	
	sfRegistTempOrder($uniqid, $sqlval);
}

/* ��ã��������������� */
function lfGetDelivDate() {
	$objCartSess = new SC_CartSession();
	$objQuery = new SC_Query();
	// ����ID�μ���
	$max = $objCartSess->getMax();
	for($i = 1; $i <= $max; $i++) {
		if($_SESSION[$objCartSess->key][$i]['id'][0] != "") {
			$arrID['product_id'][$i] = $_SESSION[$objCartSess->key][$i]['id'][0];
		}
	}
	if(count($arrID['product_id']) > 0) {
		$id = implode(",", $arrID['product_id']);
		//���ʤ���ȯ���ܰ¤μ���
		$deliv_date = $objQuery->get("dtb_products", "MAX(deliv_date_id)", "product_id IN (".$id.")");
		//ȯ���ܰ�
		switch($deliv_date) {
		//¨��ȯ��
		case '1':
			$start_day = 1;
			break;
		//1-2����
		case '2':
			$start_day = 3;
			break;
		//3-4����
		case '3':
			$start_day = 5;
			break;
		//1���ְ���
		case '4':
			$start_day = 8;
			break;
		//2���ְ���
		case '5':
			$start_day = 15;
			break;
		//3���ְ���
		case '6':
			$start_day = 22;
			break;
		//1�������
		case '7':
			$start_day = 32;
			break;
		//2����ʹ�
		case '8':
			$start_day = 62;			
			break;
		//������(�������ٸ�)
		case '9':
			$start_day = "";
			break;
		default:
			//���Ϥ��������ꤵ��Ƥ��ʤ����
			$start_day = "";
			break;
		}
		//��ã��ǽ���Υ��������ͤ��顢��ã����������������
		$arrDelivDate = lfGetDateArray($start_day, DELIV_DATE_END_MAX);
	}
	return $arrDelivDate;
}

//��ã��ǽ���Υ��������ͤ��顢��ã����������������
function lfGetDateArray($start_day, $end_day) {
	global $arrWDAY;
	//��ã��ǽ���Υ��������ͤ����åȤ���Ƥ����
	if($start_day >= 1) {
		$now_time = time();
		$max_day = $start_day + $end_day;
		// ����
		for ($i = $start_day; $i < $max_day; $i++) {
			// ���ܻ��֤����������ɲä��Ƥ���
			$tmp_time = $now_time + ($i * 24 * 3600);
			list($y, $m, $d, $w) = split(" ", date("y m d w", $tmp_time));	
			$val = sprintf("%02d/%02d/%02d(%s)", $y, $m, $d, $arrWDAY[$w]);
			$arrDate[$val] = $val;
		}
	} else {
		$arrDate = false;
	}
	return $arrDate;
}

//�������ơ��֥뤫��ξ�����Ǽ����
function lfSetOrderTempData($uniqid) {
	global $objQuery;
	global $objFormParam;
	
	$objQuery = new SC_Query();
	$col = "payment_id, use_point, deliv_time_id, message, point_check, deliv_date";
	$from = "dtb_order_temp";
	$where = "order_temp_id = ?";
	$arrRet = $objQuery->select($col, $from, $where, array($uniqid));
	// DB�ͤμ���
	$objFormParam->setParam($arrRet[0]);
	return $objFormParam;
}


?>
