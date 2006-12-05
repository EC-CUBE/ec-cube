<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_mainpage = 'basis/index.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_subno = 'index';
		$this->tpl_mainno = 'basis';
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrTAXRULE;
		$this->arrTAXRULE = $arrTAXRULE;
		$this->tpl_subtitle = 'SHOP�ޥ���';
	}
}


$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

$cnt = $objQuery->count("dtb_baseinfo");

if ($cnt > 0) {
	$objPage->tpl_mode = "update";
} else {
	$objPage->tpl_mode = "insert";
}

if($_POST['mode'] != "") {
	// POST�ǡ����ΰ����Ѥ�
	$objPage->arrForm = $_POST;
	
	// ���ϥǡ������Ѵ�
	$objPage->arrForm = lfConvertParam($objPage->arrForm);
	// ���ϥǡ����Υ��顼�����å�
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);
	
	if(count($objPage->arrErr) == 0) {
		switch($_POST['mode']) {
		case 'update':
			lfUpdateData($objPage->arrForm);	// ��¸�Խ�
			break;
		case 'insert':
			lfInsertData($objPage->arrForm);	// ��������
			break;
		default:
			break;
		}
		// ��ɽ��
		sfReload();
	}
} else {
	$arrCol = lfGetCol();
	$col	= sfGetCommaList($arrCol);
	$arrRet = $objQuery->select($col, "dtb_baseinfo");
	$objPage->arrForm = $arrRet[0];
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//--------------------------------------------------------------------------------------------------------------------------------------
// ���ܾ����ѤΥ�������Ф���
function lfGetCol() {
	$arrCol = array(
		"company_name",
		"company_kana",
		"shop_name",
		"shop_kana",
		"zip01",
		"zip02",
		"pref",
		"addr01",
		"addr02",
		"tel01",
		"tel02",
		"tel03",
		"fax01",
		"fax02",
		"fax03",
		"business_hour",
		"email01",
		"email02",
		"email03",
		"email04",
		"tax",
		"tax_rule",
		"free_rule",
		"good_traded",
		"message"
		
	);
	return $arrCol;
}

function lfUpdateData($array) {
	$objQuery = new SC_Query();
	$arrCol = lfGetCol();
	foreach($arrCol as $val) {
		$sqlval[$val] = $array[$val];
	}
	$sqlval['update_date'] = 'Now()';
	// UPDATE�μ¹�
	$ret = $objQuery->update("dtb_baseinfo", $sqlval);
}

function lfInsertData($array) {
	$objQuery = new SC_Query();
	$arrCol = lfGetCol();
	foreach($arrCol as $val) {
		$sqlval[$val] = $array[$val];
	}	
	$sqlval['update_date'] = 'Now()';
	// INSERT�μ¹�
	$ret = $objQuery->insert("dtb_baseinfo", $sqlval);
}

/* ����ʸ������Ѵ� */
function lfConvertParam($array) {
	/*
	 *	ʸ������Ѵ�
	 *	K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
	 *	C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
	 *	V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ�	
	 *	n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
	 *  a :  ���ѱѿ�����Ⱦ�ѱѿ������Ѵ�����
	 */
	// ��ʪ���ܾ���
	
	// ���ݥåȾ���
	$arrConvList['company_name'] = "KVa";
	$arrConvList['company_kana'] = "KVC";
	$arrConvList['shop_name'] = "KVa";
	$arrConvList['shop_kana'] = "KVC";
	$arrConvList['addr01'] = "KVa";
	$arrConvList['addr02'] = "KVa";
	$arrConvList['zip01'] = "n";
	$arrConvList['zip02'] = "n";
	$arrConvList['tel01'] = "n";
	$arrConvList['tel02'] = "n";
	$arrConvList['tel03'] = "n";
	$arrConvList['fax01'] = "n";
	$arrConvList['fax02'] = "n";
	$arrConvList['fax03'] = "n";
	$arrConvList['email01'] = "a";
	$arrConvList['email02'] = "a";
	$arrConvList['email03'] = "a";
	$arrConvList['email04'] = "a";
	$arrConvList['tax'] = "n";
	$arrConvList['free_rule'] = "n";
	$arrConvList['business_hour'] = "KVa";
	$arrConvList['good_traded'] = "";
	$arrConvList['message'] = "";
	
	// ʸ���Ѵ�
	foreach ($arrConvList as $key => $val) {
		// POST����Ƥ����ͤΤ��Ѵ����롣
		if(isset($array[$key])) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}

// ���ϥ��顼�����å�
function lfErrorCheck($array) {
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("���̾", "company_name", STEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("���̾(����)", "company_kana", STEXT_LEN), array("KANA_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("Ź̾", "shop_name", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("Ź̾(����)", "shop_kana", STEXT_LEN), array("KANA_CHECK","MAX_LENGTH_CHECK"));
	// ͹���ֹ�����å�
	$objErr->doFunc(array("͹���ֹ�1","zip01",ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK","NUM_COUNT_CHECK"));
	$objErr->doFunc(array("͹���ֹ�2","zip02",ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK","NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("͹���ֹ�", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	// ��������å�
	$objErr->doFunc(array("��ƻ�ܸ�", "pref"), array("EXIST_CHECK"));
	$objErr->doFunc(array("����1", "addr01", STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("����2", "addr02", STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	// �᡼������å�
	$objErr->doFunc(array('������ʸ���ե᡼�륢�ɥ쥹', "email01", STEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�䤤��碌���ե᡼�륢�ɥ쥹', "email02", STEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼���������᡼�륢�ɥ쥹', "email03", STEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�������顼���ե᡼�륢�ɥ쥹', "email04", STEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK","MAX_LENGTH_CHECK"));
	// �����ֹ�����å�
	$objErr->doFunc(array("TEL", "tel01", "tel02", "tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
	$objErr->doFunc(array("FAX", "fax01", "fax02", "fax03", TEL_ITEM_LEN), array("TEL_CHECK"));
	// ����¾
	$objErr->doFunc(array("������Ψ", "tax", PERCENTAGE_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("����̵�����", "free_rule", PRICE_LEN), array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("Ź�ޱĶȻ���", "business_hour", STEXT_LEN), array("MAX_LENGTH_CHECK"));

	$objErr->doFunc(array("�谷����", "good_traded", LLTEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��å�����", "message", LLTEXT_LEN), array("MAX_LENGTH_CHECK"));

	return $objErr->arrErr;
}

?>