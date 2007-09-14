<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

session_start();

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = USER_PATH . 'templates/mypage/delivery_addr.tpl';
		$this->tpl_title = "���������Ϥ�����ɲÎ��ѹ�";
		global $arrPref;
		$this->arrPref = $arrPref;
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView(false);
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();
$objConn = new SC_DBConn();
$ParentPage = MYPAGE_DELIVADDR_URL;

// GET�ǥڡ�������ꤵ��Ƥ�����ˤϻ���ڡ������᤹
if (isset($_GET['page'])) {
	$ParentPage = $_GET['page'];
}
$objPage->ParentPage = $ParentPage;

//������Ƚ��
if (!$objCustomer->isLoginSuccess()){
	sfDispSiteError(CUSTOMER_ERROR);
}

if ($_POST['mode'] == ""){
	$_SESSION['other_deliv_id'] = $_GET['other_deliv_id'];
}

if ($_GET['other_deliv_id'] != ""){
	//������������Ƚ��
	$flag = $objQuery->count("dtb_other_deliv", "customer_id=? AND other_deliv_id=?", array($objCustomer->getValue("customer_id"), $_SESSION['other_deliv_id']));
	if (!$objCustomer->isLoginSuccess() || $flag == 0){
		sfDispSiteError(CUSTOMER_ERROR);
	}
}

//�̤Τ��Ϥ���ģ���Ͽ�ѥ��������
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
							 array(  "column" => "tel01",		"convert" => "n" ),
							 array(  "column" => "tel02",		"convert" => "n" ),
							 array(  "column" => "tel03",		"convert" => "n" ),
						);

switch ($_POST['mode']){
	case 'edit':
		$_POST = lfConvertParam($_POST,$arrRegistColumn);
		$objPage->arrErr =lfErrorCheck($_POST);
		if ($objPage->arrErr){
			foreach ($_POST as $key => $val){
				$objPage->$key = $val;
			}
		}else{
			//�̤Τ��Ϥ�����Ͽ���μ���
			$deliv_count = $objQuery->count("dtb_other_deliv", "customer_id=?", array($objCustomer->getValue('customer_id')));
			if ($deliv_count < DELIV_ADDR_MAX or isset($_POST['other_deliv_id'])){
				lfRegistData($_POST,$arrRegistColumn);
			}
			$objPage->tpl_onload = "fnUpdateParent('".$_POST['ParentPage']."'); window.close();";
		}
		break;
}

if ($_GET['other_deliv_id'] != ""){
	//�̤Τ��Ϥ���������
	$arrOtherDeliv = $objQuery->select("*", "dtb_other_deliv", "other_deliv_id=? ", array($_SESSION['other_deliv_id']));
	$objPage->arrOtherDeliv = $arrOtherDeliv[0];
}

$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);

//-------------------------------------------------------------------------------------------------------------

/* ���顼�����å� */
function lfErrorCheck() {
	$objErr = new SC_CheckError();
	
	$objErr->doFunc(array("��̾��������", 'name01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��̾����̾��", 'name02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�եꥬ�ʡ�����", 'kana01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("�եꥬ�ʡ�̾��", 'kana02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("͹���ֹ�1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("͹���ֹ�2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("͹���ֹ�", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("��ƻ�ܸ�", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("�������1��", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������2��", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������ֹ�1", 'tel01'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("�������ֹ�2", 'tel02'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("�������ֹ�3", 'tel03'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("�������ֹ�", "tel01", "tel02", "tel03", TEL_LEN) ,array("TEL_CHECK"));
	return $objErr->arrErr;
	
}

/* ��Ͽ�¹� */
function lfRegistData($array, $arrRegistColumn) {
	global $objConn;
	global $objCustomer;
	
	foreach ($arrRegistColumn as $data) {
		if (strlen($array[ $data["column"] ]) > 0) {
			$arrRegist[ $data["column"] ] = $array[ $data["column"] ];
		}
	}
	
	$arrRegist['customer_id'] = $objCustomer->getvalue('customer_id');
	
	//-- �Խ���Ͽ�¹�
	$objConn->query("BEGIN");
	if ($array['other_deliv_id'] != ""){
	$objConn->autoExecute("dtb_other_deliv", $arrRegist, "other_deliv_id='" .addslashes($array["other_deliv_id"]). "'");
	}else{
	$objConn->autoExecute("dtb_other_deliv", $arrRegist);
	}
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
?>