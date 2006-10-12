<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
//�ǡ����١������龦�ʸ�����¹Ԥ��롣��EC���å�ư���Ѥγ�ȯ��
require_once("../require.php");

class LC_Page{
	function LC_Page() {
		$this->tpl_mainpage = USER_PATH . 'templates/mypage/change.tpl';
		$this->tpl_title = 'MY�ڡ���/�����Ͽ�����ѹ�(���ϥڡ���)';
		$this->tpl_navi = USER_PATH . 'templates/mypage/navi.tpl';
		$this->tpl_mainno = 'mypage';
		$this->tpl_mypageno = 'change';
		global $arrReminder;
		global $arrPref;
		global $arrJob;
		global $arrMAILMAGATYPE;
		global $arrSex;
		$this->arrReminder = $arrReminder;
		$this->arrPref = $arrPref;
		$this->arrJob = $arrJob;
		$this->arrMAILMAGATYPE = $arrMAILMAGATYPE;
		$this->arrSex = $arrSex;
		session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();				
$objView = new SC_SiteView();			
$objQuery = new SC_Query();             
$objCustomer = new SC_Customer();
$objFormParam = new SC_FormParam();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

//���եץ����������
$objDate = new SC_Date(1901);
$objPage->arrYear = $objDate->getYear();	
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

// ����������å�
if (!$objCustomer->isLoginSuccess()){
	sfDispSiteError(CUSTOMER_ERROR); 
}else {
	//�ޥ��ڡ����ȥå׸ܵҾ���ɽ����
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
	$objPage->CustomerPoint = $objCustomer->getvalue('point');
}

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
							 array(  "column" => "tel01",		"convert" => "n" ),
							 array(  "column" => "tel02",		"convert" => "n" ),
							 array(  "column" => "tel03",		"convert" => "n" ),
							 array(  "column" => "fax01",		"convert" => "n" ),
							 array(  "column" => "fax02",		"convert" => "n" ),
							 array(  "column" => "fax03",		"convert" => "n" ),
							 array(  "column" => "sex",			"convert" => "n" ),
							 array(  "column" => "job",			"convert" => "n" ),
							 array(  "column" => "birth",		"convert" => "n" ),
							 array(  "column" => "password",	"convert" => "an" ),
							 array(  "column" => "reminder",	"convert" => "n" ),
							 array(  "column" => "reminder_answer", "convert" => "aKV" ),
						);


switch ($_POST['mode']){
	
case 'confirm':
	//-- ���ϥǡ������Ѵ�
	$objPage->arrForm = $_POST;
	$objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);
	$objPage->arrForm['email'] = strtolower($objPage->arrForm['email']);		// email�Ϥ��٤ƾ�ʸ���ǽ���

	/* ���������ѹ��ϲ�ǽ�ˤ���
	//�����������ѹ��Υ����å�
	$arrCustomer = lfGetCustomerData();
	if ($arrCustomer['birth'] != "" && ($objPage->arrForm['year'] != $arrCustomer['year'] || $objPage->arrForm['month'] != $arrCustomer['month'] || $objPage->arrForm['day'] != $arrCustomer['day'])){
		sfDispSiteError(CUSTOMER_ERROR);
	}else{
	*/
		//���顼�����å�
		$objPage->arrErr = lfErrorCheck($objPage->arrForm);
		$email_flag = true;
		//�᡼�륢�ɥ쥹���ѹ����Ƥ����硢�᡼�륢�ɥ쥹�ν�ʣ�����å�
		if ($objPage->arrForm['email'] != $objCustomer->getValue('email')){
			$email_cnt = $objQuery->count("dtb_customer","del_flg=0 AND email=?", array($objPage->arrForm['email']));
			if ($email_cnt > 0){
				$email_flag = false;
			}
		}
		//���顼�ʤ��Ǥ��ĥ᡼�륢�ɥ쥹����ʣ���Ƥ��ʤ����
		if ($objPage->arrErr == "" && $email_flag == true){
			//��ǧ�ڡ�����
			$objPage->tpl_mainpage = USER_PATH . 'templates/mypage/change_confirm.tpl';
			$objPage->tpl_title = 'MY�ڡ���/�����Ͽ�����ѹ�(��ǧ�ڡ���)';
			$passlen = strlen($objPage->arrForm['password']);
			$objPage->passlen = lfPassLen($passlen);
		} else {
			lfFormReturn($objPage->arrForm,$objPage);
			if ($email_flag == false){
				$objPage->arrErr['email'].="���˻��Ѥ���Ƥ���᡼�륢�ɥ쥹�Ǥ���";
			}
		}
	//}
	break;
	
case 'return':
	$objPage->arrForm = $_POST;
	lfFormReturn($objPage->arrForm,$objPage);
	break;
	
case 'complete':

	//-- ���ϥǡ������Ѵ�
	$arrForm = lfConvertParam($_POST, $arrRegistColumn);
	$arrForm['email'] = strtolower($arrForm['email']);		// email�Ϥ��٤ƾ�ʸ���ǽ���
	
	/* ���������ѹ��ϲ�ǽ�ˤ���
	//�����������ѹ��Υ����å�
	$arrCustomer = lfGetCustomerData();
	if ($arrCustomer['birth'] != "" && ($arrForm['year'] !=  $arrCustomer['year'] || $arrForm['month'] != $arrCustomer['month'] || $arrForm['day'] != $arrCustomer['day'])){
		sfDispSiteError(CUSTOMER_ERROR);
	} else {*/
	
		//���顼�����å�
		$objPage->arrErr = lfErrorCheck($objPage->arrForm);
		$email_flag = true;
		if($objPage->arrForm['email'] != $objCustomer->getValue('email')) {
			//�᡼�륢�ɥ쥹�ν�ʣ�����å�
			$email_cnt = $objQuery->count("dtb_customer","del_flg=0 AND email=?", array($objPage->arrForm['email']));
			if ($email_cnt > 0){
				$email_flag = false;
			}
		}
		//���顼�ʤ��Ǥ��ĥ᡼�륢�ɥ쥹����ʣ���Ƥ��ʤ����
		if($objPage->arrErr == "" && $email_flag) {
			$arrForm['customer_id'] = $objCustomer->getValue('customer_id');
			//-- �Խ���Ͽ
			sfEditCustomerData($arrForm, $arrRegistColumn);
			//���å��������ǿ��ξ��֤˹�������
			$objCustomer->updateSession();
			//��λ�ڡ�����
			header("Location: ./change_complete.php");
			exit;
		} else {
			sfDispSiteError(CUSTOMER_ERROR);
		}
	//}
	break;
	
default:
	//�ܵҾ������
	$objPage->arrForm = lfGetCustomerData();
	$objPage->arrForm['password'] = DEFAULT_PASSWORD;
	$objPage->arrForm['password02'] = DEFAULT_PASSWORD;
	break;
}

//�������ǡ�����Ͽ��̵ͭ
$arrCustomer = lfGetCustomerData();
if ($arrCustomer['birth'] != ""){	
	$objPage->birth_check = true;
}

$objView->assignobj($objPage);				//$objpage������ƤΥƥ�ץ졼���ѿ���smarty�˳�Ǽ
$objView->display(SITE_FRAME);				//�ѥ��ȥƥ�ץ졼���ѿ��θƤӽФ����¹�

//-------------------------------------------------------------------------------------------------------------------------

/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("��̾��(��)", "name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("��̾��(̾)", "name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�եꥬ��(����)", "kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�եꥬ��(�ᥤ)", "kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("͹���ֹ�1", "zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("͹���ֹ�2", "zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("��ƻ�ܸ�", "pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("������1", "addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("������2", "addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�������ֹ�1", "tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�������ֹ�2", "tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�������ֹ�3", "tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
}
											
//���顼�����å�

function lfErrorCheck($array) {
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("��̾��������", 'name01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��̾����̾��", 'name02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�եꥬ�ʡʥ�����", 'kana01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("�եꥬ�ʡʥᥤ��", 'kana02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("͹���ֹ�1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("͹���ֹ�2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("͹���ֹ�", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("��ƻ�ܸ�", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("������1", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("������2", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹', "email", MTEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "NO_SPTAB" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹(��ǧ)', "email02", MTEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK","NO_SPTAB" , "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹', '�᡼�륢�ɥ쥹(��ǧ)', "email", "email02") ,array("EQUAL_CHECK"));
	$objErr->doFunc(array("�������ֹ�1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK"));
	$objErr->doFunc(array("�������ֹ�2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK"));
	$objErr->doFunc(array("�������ֹ�3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK"));
	$objErr->doFunc(array("�������ֹ�", "tel01", "tel02", "tel03", TEL_LEN) ,array("TEL_CHECK"));
	$objErr->doFunc(array("FAX�ֹ�", "fax01", "fax02", "fax03", TEL_LEN) ,array("TEL_CHECK"));
	$objErr->doFunc(array("������", "sex") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("������", "job") ,array("NUM_CHECK"));
	$objErr->doFunc(array("��ǯ����", "year", "month", "day"), array("CHECK_DATE"));
	$objErr->doFunc(array("�ѥ����", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "ALNUM_CHECK", "NUM_RANGE_CHECK"));
	$objErr->doFunc(array("�ѥ����(��ǧ)", 'password02', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "ALNUM_CHECK", "NUM_RANGE_CHECK"));
	$objErr->doFunc(array("�ѥ����", '�ѥ����(��ǧ)', 'password', 'password02'), array("EQUAL_CHECK"));
	$objErr->doFunc(array("�ѥ���ɤ�˺�줿�Ȥ��μ���", "reminder") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("�ѥ���ɤ�˺�줿�Ȥ�������", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�᡼��ޥ�����", "mail_flag") ,array("SELECT_CHECK", "NUM_CHECK"));
	return $objErr->arrErr;
	
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

//�ܵҾ���μ���
function lfGetCustomerData(){
	global $objQuery;
	global $objCustomer;
	//�ܵҾ������
	$ret = $objQuery->select("*","dtb_customer","customer_id=?", array($objCustomer->getValue('customer_id')));
	$arrForm = $ret[0];

	//���ޥ��ե饰����
	$arrForm['mail_flag'] = $objQuery->get("dtb_customer_mail","mail_flag","email=?", array($objCustomer->getValue('email')));
	
	//��������ǯ��������
	if (isset($arrForm['birth'])){
		$birth = split(" ", $arrForm["birth"]);
		list($year, $month, $day) = split("-",$birth[0]);
		
		$arrForm['year'] = $year;
		$arrForm['month'] = $month;
		$arrForm['day'] = $day;
		
	}
	return $arrForm;
}
	
// �Խ���Ͽ
function lfRegistData($array, $arrRegistColumn) {
	global $objQuery;
	global $objCustomer;
	
	foreach ($arrRegistColumn as $data) {
		if ($data["column"] != "password") {
			if($array[ $data['column'] ] == "") {
				$arrRegist[ $data['column'] ] = NULL;
			} else {
				$arrRegist[ $data["column"] ] = $array[ $data["column"] ];
			}
		}
	}
	if (strlen($array["year"]) > 0 && strlen($array["month"]) > 0 && strlen($array["day"]) > 0) {
		$arrRegist["birth"] = $array["year"] ."/". $array["month"] ."/". $array["day"] ." 00:00:00";
	} else {
		$arrRegist["birth"] = NULL;
	}

	//-- �ѥ���ɤι�����������ϰŹ沽���ʹ������ʤ�����UPDATEʸ�������ʤ���
	if ($array["password"] != DEFAULT_PASSWORD) $arrRegist["password"] = sha1($array["password"] . ":" . AUTH_MAGIC);
	$arrRegist["update_date"] = "NOW()";
	
	//-- �Խ���Ͽ�¹�
	$objQuery->begin();
	$objQuery->update("dtb_customer", $arrRegist, "customer_id = ? ", array($objCustomer->getValue('customer_id')));
	$objQuery->commit();
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

//���顼�������˥ե���������Ͼ�����֤�
function lfFormReturn($array,$objPage){
	foreach($array as $key => $val){
		switch ($key){
			case 'password':
			case 'password02':
			$objPage->$key = $val;
			break;
			default:
			$array[ $key ] = $val;
			break;
		}
	}
}

?>