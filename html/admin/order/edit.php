<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'order/edit.tpl';
		$this->tpl_subnavi = 'order/subnavi.tpl';
		$this->tpl_mainno = 'order';		
		$this->tpl_subno = 'index';
		$this->tpl_subtitle = '�������';
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrORDERSTATUS;
		$this->arrORDERSTATUS = $arrORDERSTATUS;
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objSiteInfo = new SC_SiteInfo();
$arrInfo = $objSiteInfo->data;

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

// ɽ���⡼��Ƚ��
if(sfIsInt($_GET['order_id'])) {
	$objPage->disp_mode = true;
	$order_id = $_GET['order_id'];
} else {
	$order_id = $_POST['order_id'];
}
$objPage->tpl_order_id = $order_id;

// DB������������ɤ߹���
lfGetOrderData($order_id);

switch($_POST['mode']) {
case 'pre_edit':
case 'order_id':
	break;
case 'edit':
	// POST����Ǿ��
	$objFormParam->setParam($_POST);
	
	// �����ͤ��Ѵ�
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($arrRet);
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfCheek($arrInfo);
		if(count($objPage->arrErr) == 0) {
			lfRegistData($_POST['order_id']);
			// DB�������������ɹ�
			lfGetOrderData($order_id);
			$objPage->tpl_onload = "window.alert('����������Խ����ޤ�����');";
		}
	}
	break;
// �Ʒ׻�
case 'cheek':
	// POST����Ǿ��
	$objFormParam->setParam($_POST);
	// �����ͤ��Ѵ�
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($arrRet);
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfCheek($arrInfo);
	}
	break;
default:
	break;
}

// ��ʧ����ˡ�μ���
$objPage->arrPayment = sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
// �������֤μ���
$arrRet = sfGetDelivTime($objFormParam->getValue('payment_id'));
$objPage->arrDelivTime = sfArrKeyValue($arrRet, 'time_id', 'deliv_time');

$objPage->arrForm = $objFormParam->getFormParamList();

$objPage->arrInfo = $arrInfo;

$objView->assignobj($objPage);
// ɽ���⡼��Ƚ��
if(!$objPage->disp_mode) {
	$objView->display(MAIN_FRAME);
} else {
	$objView->display('order/disp.tpl');
}
//-----------------------------------------------------------------------------------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	// ���������
	$objFormParam->addParam("��̾��1", "deliv_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("��̾��2", "deliv_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�եꥬ��1", "deliv_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�եꥬ��2", "deliv_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("͹���ֹ�1", "deliv_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("͹���ֹ�2", "deliv_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("��ƻ�ܸ�", "deliv_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("����1", "deliv_addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("����2", "deliv_addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�����ֹ�1", "deliv_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�����ֹ�2", "deliv_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�����ֹ�3", "deliv_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	// �����ʾ���
	$objFormParam->addParam("�Ͱ���", "discount", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("����", "deliv_fee", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("�����", "charge", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("���ѥݥ����", "use_point", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("����ʧ����ˡ", "payment_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��������ID", "deliv_time_id", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�б�����", "status", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��ã��", "deliv_date", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
	$objFormParam->addParam("����ʧ��ˡ̾��", "payment_method");
	$objFormParam->addParam("��������", "deliv_time");
	
	// ����ܺپ���
	$objFormParam->addParam("ñ��", "price", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("�Ŀ�", "quantity", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("����ID", "product_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("�ݥ������ͿΨ", "point_rate");
	$objFormParam->addParam("���ʥ�����", "product_code");
	$objFormParam->addParam("����̾", "product_name");
	$objFormParam->addParam("����1", "classcategory_id1");
	$objFormParam->addParam("����2", "classcategory_id2");
	$objFormParam->addParam("����̾1", "classcategory_name1");
	$objFormParam->addParam("����̾2", "classcategory_name2");
	$objFormParam->addParam("���", "note", MTEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
	// DB�ɹ���
	$objFormParam->addParam("����", "subtotal");
	$objFormParam->addParam("���", "total");
	$objFormParam->addParam("��ʧ�����", "payment_total");
	$objFormParam->addParam("�û��ݥ����", "add_point");
	$objFormParam->addParam("���������ݥ����", "birth_point");
	$objFormParam->addParam("�����ǹ��", "tax");
	$objFormParam->addParam("�ǽ��ݻ��ݥ����", "total_point");
	$objFormParam->addParam("�ܵ�ID", "customer_id");
	$objFormParam->addParam("���ߤΥݥ����", "point");
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
		// ����ܺ٥ǡ����μ���
		$arrRet = lfGetOrderDetail($order_id);
		$arrRet = sfSwapArray($arrRet);
		$objPage->arrDisp = array_merge($objPage->arrDisp, $arrRet);
		$objFormParam->setParam($arrRet);
		
		// ����¾��ʧ�������ɽ��
		if($objPage->arrDisp["memo02"] != "") $objPage->arrDisp["payment_info"] = unserialize($objPage->arrDisp["memo02"]);
		if($objPage->arrDisp["memo01"] == PAYMENT_CREDIT_ID){
			$objPage->arrDisp["payment_type"] = "���쥸�åȷ��";
		}elseif($objPage->arrDisp["memo01"] == PAYMENT_CONVENIENCE_ID){
			$objPage->arrDisp["payment_type"] = "����ӥ˷��";
		}else{
			$objPage->arrDisp["payment_type"] = "����ʧ��";
		}
	}
}

// ����ܺ٥ǡ����μ���
function lfGetOrderDetail($order_id) {
	$objQuery = new SC_Query();
	$col = "product_id, classcategory_id1, classcategory_id2, product_code, product_name, classcategory_name1, classcategory_name2, price, quantity, point_rate";
	$where = "order_id = ?";
	$objQuery->setorder("classcategory_id1, classcategory_id2");
	$arrRet = $objQuery->select($col, "dtb_order_detail", $where, array($order_id));
	return $arrRet;
}

/* �������ƤΥ����å� */
function lfCheckError() {
	global $objFormParam;
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	return $objErr->arrErr;
}

/* �׻����� */
function lfCheek($arrInfo) {
	global $objFormParam;
		
	$arrVal = $objFormParam->getHashArray();
			
	// ���ʤμ����
	$max = count($arrVal['quantity']);
	$subtotal = 0;
	$totalpoint = 0;
	$totaltax = 0;
	for($i = 0; $i < $max; $i++) {
		// ���פη׻�
		$subtotal += sfPreTax($arrVal['price'][$i], $arrInfo['tax'], $arrInfo['tax_rule']) * $arrVal['quantity'][$i];
		// ���פη׻�
		$totaltax += sfTax($arrVal['price'][$i], $arrInfo['tax'], $arrInfo['tax_rule']) * $arrVal['quantity'][$i];
		// �û��ݥ���Ȥη׻�
		$totalpoint += sfPrePoint($arrVal['price'][$i], $arrVal['point_rate'][$i]) * $arrVal['quantity'][$i];
	}
	
	// ������
	$arrVal['tax'] = $totaltax;	
	// ����
	$arrVal['subtotal'] = $subtotal;
	// ���
	$arrVal['total'] = $subtotal - $arrVal['discount'] + $arrVal['deliv_fee'] + $arrVal['charge'];
	// ����ʧ�����
	$arrVal['payment_total'] = $arrVal['total'] - ($arrVal['use_point'] * POINT_VALUE);
	
	// �û��ݥ����
	$arrVal['add_point'] = sfGetAddPoint($totalpoint, $arrVal['use_point'], $arrInfo);
		
	list($arrVal['point'], $arrVal['total_point']) = sfGetCustomerPoint($_POST['order_id'], $arrVal['use_point'], $arrVal['add_point']);
		
	if($arrVal['total'] < 0) {
		$arrErr['total'] = '��׳ۤ��ޥ��ʥ�ɽ���ˤʤ�ʤ��褦��Ĵ�����Ʋ�������<br />';
	}
	
	if($arrVal['payment_total'] < 0) {
		$arrErr['payment_total'] = '����ʧ����׳ۤ��ޥ��ʥ�ɽ���ˤʤ�ʤ��褦��Ĵ�����Ʋ�������<br />';
	}

	if($arrVal['total_point'] < 0) {
		$arrErr['total_point'] = '�ǽ��ݻ��ݥ���Ȥ��ޥ��ʥ�ɽ���ˤʤ�ʤ��褦��Ĵ�����Ʋ�������<br />';
	}

	$objFormParam->setParam($arrVal);
	return $arrErr;
}

/* DB��Ͽ���� */
function lfRegistData($order_id) {
	global $objFormParam;
	$objQuery = new SC_Query();
	
	$objQuery->begin();

	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	
	foreach($arrRet as $key => $val) {
		// �������Ͽ���ʤ�
		if(!is_array($val)) {
			$sqlval[$key] = $val;
		}
	}
	
	unset($sqlval['total_point']);
	unset($sqlval['point']);
			
	$where = "order_id = ?";
	
	// �����ơ�������Ƚ��
	if ($sqlval['status'] == ODERSTATUS_COMMIT) {
		// ����ơ��֥��ȯ���Ѥ����򹹿�����
		$sqlval['commit_date'] = "Now()";
	}
    
    $sqlval['update_date'] = "Now()";
	
	// ����ơ��֥�ι���
	$objQuery->update("dtb_order", $sqlval, $where, array($order_id));

	$sql = "";
	$sql .= " UPDATE";
	$sql .= "     dtb_order";
	$sql .= " SET";
	$sql .= "     payment_method = (SELECT payment_method FROM dtb_payment WHERE payment_id = ?)";
	$sql .= "     ,deliv_time = (SELECT deliv_time FROM dtb_delivtime WHERE time_id = ? AND deliv_id = (SELECT deliv_id FROM dtb_payment WHERE payment_id = ? ))";
	$sql .= " WHERE order_id = ?";
	
	if ($arrRet['deliv_time_id'] == "") {
		$deliv_time_id = 0;
	}else{
		$deliv_time_id = $arrRet['deliv_time_id'];
	}
	$arrUpdData = array($arrRet['payment_id'], $deliv_time_id, $arrRet['payment_id'], $order_id);
	$objQuery->query($sql, $arrUpdData);

	// ����ܺ٥ǡ����ι���
	$arrDetail = $objFormParam->getSwapArray(array("product_id", "product_code", "product_name", "price", "quantity", "point_rate", "classcategory_id1", "classcategory_id2", "classcategory_name1", "classcategory_name2"));
	$objQuery->delete("dtb_order_detail", $where, array($order_id));
	
	$max = count($arrDetail);
	for($i = 0; $i < $max; $i++) {
		$sqlval = array();
		$sqlval['order_id'] = $order_id;
		$sqlval['product_id']  = $arrDetail[$i]['product_id'];
		$sqlval['product_code']  = $arrDetail[$i]['product_code'];
		$sqlval['product_name']  = $arrDetail[$i]['product_name'];
		$sqlval['price']  = $arrDetail[$i]['price'];
		$sqlval['quantity']  = $arrDetail[$i]['quantity'];
		$sqlval['point_rate']  = $arrDetail[$i]['point_rate'];
		$sqlval['classcategory_id1'] = $arrDetail[$i]['classcategory_id1'];
		$sqlval['classcategory_id2'] = $arrDetail[$i]['classcategory_id2'];
		$sqlval['classcategory_name1'] = $arrDetail[$i]['classcategory_name1'];
		$sqlval['classcategory_name2'] = $arrDetail[$i]['classcategory_name2'];		
		$objQuery->insert("dtb_order_detail", $sqlval);
	}
	$objQuery->commit();
}
?>