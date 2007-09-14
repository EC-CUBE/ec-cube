<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

//---- �ڡ���ɽ���ѥ��饹
class LC_Page {
	var $arrSession;
	var $tpl_mode;
	var $list_data;

	var $arrErr;
	var $arrYear;
	var $arrMonth;
	var $arrDay;
	var $arrPref;
	var $arrJob;
	var $arrSex;
	var $arrReminder;
	var $count;
	
	var $tpl_strnavi;
				
	function LC_Page() {
		$this->tpl_mainpage = 'customer/edit.tpl';
		$this->tpl_mainno = 'customer';
		$this->tpl_subnavi = 'customer/subnavi.tpl';
		$this->tpl_subno = 'index';
		$this->tpl_pager = DATA_PATH . 'Smarty/templates/admin/pager.tpl';
		$this->tpl_subtitle = '�ܵҥޥ���';

		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrJob;
		$this->arrJob = $arrJob;
		global $arrSex;		
		$this->arrSex = $arrSex;
		global $arrReminder;
		$this->arrReminder = $arrReminder;
	}
}
$objQuery = new SC_Query();
$objConn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objDate = new SC_Date(1901);
$objPage->arrYear = $objDate->getYear();	//�����եץ����������
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

//---- ��Ͽ�ѥ��������
$arrRegistColumn = array(
							 array(  "column" => "name01",		"convert" => "aKV" ),
							 array(  "column" => "name02",		"convert" => "aKV" ),
							 array(  "column" => "kana01",		"convert" => "CKV" ),
							 array(  "column" => "kana02",		"convert" => "CKV" ),
							 array(  "column" => "zip01",		"convert" => "n" ),
							 array(  "column" => "zip02",		"convert" => "n" ),
							 array(  "column" => "pref",		"convert" => "n" ),
							 array(  "column" => "addr01",		"convert" => "aKV" ),
							 array(  "column" => "addr02",		"convert" => "aKV" ),
							 array(  "column" => "email",		"convert" => "a" ),
							 array(  "column" => "email_mobile",	"convert" => "a" ),
							 array(  "column" => "tel01",		"convert" => "n" ),
							 array(  "column" => "tel02",		"convert" => "n" ),
							 array(  "column" => "tel03",		"convert" => "n" ),
							 array(  "column" => "fax01",		"convert" => "n" ),
							 array(  "column" => "fax02",		"convert" => "n" ),
							 array(  "column" => "fax03",		"convert" => "n" ),
							 array(  "column" => "sex",			"convert" => "n" ),
							 array(  "column" => "job",			"convert" => "n" ),
							 array(  "column" => "birth",		"convert" => "n" ),
							 array(  "column" => "password",	"convert" => "a" ),
							 array(  "column" => "reminder",	"convert" => "n" ),
							 array(  "column" => "reminder_answer", "convert" => "aKV" ),
							 array(  "column" => "mailmaga_flg", "convert" => "n" ),						 
							 array(  "column" => "note",		"convert" => "aKV" ),
							 array(  "column" => "point",		"convert" => "n" ),
							 array(  "column" => "status",		"convert" => "n" )
						 );

//---- ��Ͽ�����ѥ��������
$arrRejectRegistColumn = array("year", "month", "day");

// ���������ݻ�
if ($_POST['mode'] == "edit_search") {
	$arrSearch = $_POST;
}else{
	$arrSearch = $_POST['search_data'];
}
if(is_array($arrSearch)){
	foreach($arrSearch as $key => $val){
		$arrSearchData[$key] = $val;
	}
}

$objPage->arrSearchData= $arrSearchData;

//----���ܵ��Խ��������
if (($_POST["mode"] == "edit" || $_POST["mode"] == "edit_search") && is_numeric($_POST["edit_customer_id"])) {

	//--���ܵҥǡ�������
	$sql = "SELECT * FROM dtb_customer WHERE del_flg = 0 AND customer_id = ?";
	$result = $objConn->getAll($sql, array($_POST["edit_customer_id"]));
	$objPage->list_data = $result[0];
	
	$birth = split(" ", $objPage->list_data["birth"]);
	$birth = split("-",$birth[0]);
	
	$objPage->list_data["year"] = $birth[0];
	$objPage->list_data["month"] = $birth[1];
	$objPage->list_data["day"] = $birth[2];
	
	$objPage->list_data["password"] = DEFAULT_PASSWORD;
	//DB��Ͽ�Υ᡼�륢�ɥ쥹���Ϥ�
	$objPage->tpl_edit_email = $result[0]['email'];
	//�����������μ���
	$objPage->arrPurchaseHistory = lfPurchaseHistory($_POST['edit_customer_id']);
	// ��ʧ����ˡ�μ���
	$objPage->arrPayment = sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
}

//----���ܵҾ����Խ�
if ( $_POST["mode"] != "edit" && $_POST["mode"] != "edit_search" && is_numeric($_POST["customer_id"])) {

	//-- POST�ǡ����ΰ����Ѥ�
	$objPage->arrForm = $_POST;
	$objPage->arrForm['email'] = strtolower($objPage->arrForm['email']);		// email�Ϥ��٤ƾ�ʸ���ǽ���

	//-- ���ϥǡ������Ѵ�
	$objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);
	//-- ���ϥ����å�
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);

	//-- ���ϥ��顼ȯ�� or �꥿�����
	if ($objPage->arrErr || $_POST["mode"] == "return") {
		foreach($objPage->arrForm as $key => $val) {
			$objPage->list_data[ $key ] = $val;
		}
		//�����������μ���
		$objPage->arrPurchaseHistory = lfPurchaseHistory($_POST['customer_id']);
		// ��ʧ����ˡ�μ���
		$objPage->arrPayment = sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
		
	} else {
		//-- ��ǧ
		if ($_POST["mode"] == "confirm") {
			$objPage->tpl_mainpage = 'customer/edit_confirm.tpl';
			$passlen = strlen($objPage->arrForm['password']);
			$objPage->passlen = lfPassLen($passlen);
			
		}
		//--���Խ�
		if($_POST["mode"] == "complete") {
			$objPage->tpl_mainpage = 'customer/edit_complete.tpl';
			
			// ���ߤβ��������������
			$arrCusSts = $objQuery->getOne("SELECT status FROM dtb_customer WHERE customer_id = ?", array($_POST["customer_id"]));

			// ��������ѹ�����Ƥ�����ˤϥ�������åȏ��⹹�����롣
			if ($arrCusSts != $_POST['status']){
				$secret = sfGetUniqRandomId("r");
				$objPage->arrForm['secret_key'] = $secret;
				array_push($arrRegistColumn, array('column' => 'secret_key', 'convert' => 'n'));
			}
			//-- �Խ���Ͽ
			sfEditCustomerData($objPage->arrForm, $arrRegistColumn);
		}
	}
}

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);



//-------------- function

// �Խ���Ͽ
function lfRegisDatat($array, $arrRegistColumn) {
	global $objConn;
	global $objQuery;
	foreach ($arrRegistColumn as $data) {
		if($array[$data["column"]] != "") {
			$arrRegist[$data["column"]] = $array[$data["column"]];
		} else {
			$arrRegist[$data["column"]] = NULL;
		}
	}
	if (strlen($array["year"]) > 0) {
		$arrRegist["birth"] = $array["year"] ."/". $array["month"] ."/". $array["day"] ." 00:00:00";
	}

	//-- �ѥ���ɤι�����������ϰŹ沽���ʹ������ʤ�����UPDATEʸ�������ʤ���
	if ($array["password"] != DEFAULT_PASSWORD) {
		$arrRegist["password"] = sha1($array["password"] . ":" . AUTH_MAGIC);
	} else {
		unset($arrRegist['password']);
	}

	$arrRegist["update_date"] = "Now()";

	//-- �Խ���Ͽ�¹�
	$objConn->query("BEGIN");
	$objQuery->Insert("dtb_customer", $arrRegist, "customer_id = '" .addslashes($array["customer_id"]). "'");

	$objConn->query("COMMIT");
}


//----������ʸ������Ѵ�
function lfConvertParam($array, $arrRegistColumn) {
	/*
	 *	ʸ������Ѵ�
	 *	K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
	 *	C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
	 *	V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ�	
	 *	n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
	 *  a :  ���ѱѿ�����Ⱦ�ѱѿ������Ѵ�����
	 */
	// �����̾�ȥ���С��Ⱦ���
	foreach ($arrRegistColumn as $data) {
		$arrConvList[ $data["column"] ] = $data["convert"];
	}
	// ʸ���Ѵ�
	foreach ($arrConvList as $key => $val) {
		// POST����Ƥ����ͤΤ��Ѵ����롣
		if(strlen(($array[$key])) > 0) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}

//---- ���ϥ��顼�����å�
function lfErrorCheck($array) {

	global $objConn;
	$objErr = new SC_CheckError($array);

	$objErr->doFunc(array("�������", 'status'), array("EXIST_CHECK"));
	$objErr->doFunc(array("��̾��������", 'name01', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��̾����̾��", 'name02', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�եꥬ�ʡ�����", 'kana01', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("�եꥬ�ʡ�̾��", 'kana02', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("͹���ֹ�1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("͹���ֹ�2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("͹���ֹ�", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("��ƻ�ܸ�", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("�������1��", "addr01", MTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������2��", "addr02", MTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹', "email", MTEXT_LEN) ,array("EXIST_CHECK", "NO_SPTAB", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	
	//�������Ƚ�� ����������⤷���ϲ���Ͽ��ϡ��ᥢ�ɰ�դ�����ˤʤäƤ�Τ�Ʊ���ᥢ�ɤ���Ͽ�Բ�
	if (strlen($array["email"]) > 0) {
		$sql = "SELECT customer_id FROM dtb_customer WHERE email ILIKE ? escape '#' AND (status = 1 OR status = 2) AND del_flg = 0 AND customer_id <> ?";
		$checkMail = ereg_replace( "_", "#_", $array["email"]);
		$result = $objConn->getAll($sql, array($checkMail, $array["customer_id"]));
		if (count($result) > 0) {
			$objErr->arrErr["email"] .= "�� ���Ǥ���Ͽ����Ƥ���᡼�륢�ɥ쥹�Ǥ���";
		} 
	}
	
	$objErr->doFunc(array('�᡼�륢�ɥ쥹(��Х���)', "email_mobile", MTEXT_LEN) ,array("EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	//�������Ƚ�� ����������⤷���ϲ���Ͽ��ϡ��ᥢ�ɰ�դ�����ˤʤäƤ�Τ�Ʊ���ᥢ�ɤ���Ͽ�Բ�
	if (strlen($array["email_mobile"]) > 0) {
		$sql = "SELECT customer_id FROM dtb_customer WHERE email_mobile ILIKE ? escape '#' AND (status = 1 OR status = 2) AND del_flg = 0 AND customer_id <> ?";
		$checkMail = ereg_replace( "_", "#_", $array["email_mobile"]);
		$result = $objConn->getAll($sql, array($checkMail, $array["customer_id"]));
		if (count($result) > 0) {
			$objErr->arrErr["email_mobile"] .= "�� ���Ǥ���Ͽ����Ƥ���᡼�륢�ɥ쥹(��Х���)�Ǥ���";
		} 
	}
	
	
	$objErr->doFunc(array("�������ֹ�1", 'tel01'), array("EXIST_CHECK"));
	$objErr->doFunc(array("�������ֹ�2", 'tel02'), array("EXIST_CHECK"));
	$objErr->doFunc(array("�������ֹ�3", 'tel03'), array("EXIST_CHECK"));
	$objErr->doFunc(array("�������ֹ�", "tel01", "tel02", "tel03", TEL_LEN) ,array("TEL_CHECK"));
	$objErr->doFunc(array("FAX�ֹ�", "fax01", "fax02", "fax03", TEL_LEN) ,array("TEL_CHECK"));
	$objErr->doFunc(array("������", "sex") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("������", "job") ,array("NUM_CHECK"));
	if ($array["password"] != DEFAULT_PASSWORD) {
		$objErr->doFunc(array("�ѥ����", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "ALNUM_CHECK", "NUM_RANGE_CHECK"));
	}
	$objErr->doFunc(array("�ѥ���ɤ�˺�줿�Ȥ��Υҥ�� ����", "reminder") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("�ѥ���ɤ�˺�줿�Ȥ��Υҥ�� ����", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�᡼��ޥ�����", "mailmaga_flg") ,array("SELECT_CHECK", "NUM_CHECK"));
	$objErr->doFunc(array("��ǯ����", "year", "month", "day"), array("CHECK_DATE"));
	$objErr->doFunc(array("SHOP�ѥ��", 'note', LTEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("����ݥ����", "point", TEL_LEN) ,array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	return $objErr->arrErr;
	
}

//�����������μ���
function lfPurchaseHistory($customer_id){
		global $objQuery;
		global $objPage;
		
		$objPage->tpl_pageno = $_POST['search_pageno'];
		$objPage->edit_customer_id = $customer_id;

		// �ڡ�������ν���
		$page_max = SEARCH_PMAX;
		//��������η������
		$objPage->tpl_linemax = $objQuery->count("dtb_order","customer_id=? AND del_flg = 0 ", array($customer_id));
		$linemax = $objPage->tpl_linemax;
		
		// �ڡ�������μ���
		$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnNaviSearchPage2", NAVI_PMAX);
		$objPage->arrPagenavi = $objNavi->arrPagenavi;
		$objPage->arrPagenavi['mode'] = 'edit';
		$startno = $objNavi->start_row;
		
		// �����ϰϤλ���(���Ϲ��ֹ桢�Կ��Υ��å�)
		$objQuery->setlimitoffset($page_max, $startno);
		// ɽ�����
		$order = "order_id DESC";
		$objQuery->setorder($order);
		//�����������μ���
		$arrPurchaseHistory = $objQuery->select("*", "dtb_order", "customer_id=? AND del_flg = 0 ", array($customer_id));
		
		return $arrPurchaseHistory;
}

//��ǧ�ڡ����ѥѥ����ɽ����

function lfPassLen($passlen){
	$ret = "";
	for ($i=0;$i<$passlen;true){
		$ret.="*";
		$i++;
	}
	return $ret;
}


?>