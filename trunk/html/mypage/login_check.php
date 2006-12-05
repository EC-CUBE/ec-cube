<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

$objCustomer = new SC_Customer();
// ���å����������饹
$objCookie = new SC_Cookie(COOKIE_EXPIRE);
// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();
// POST�ͤμ���
$objFormParam->setParam($_POST);

switch($_POST['mode']) {
case 'login':
	$objFormParam->toLower('mypage_login_email');
	$arrErr = $objFormParam->checkError();
	$arrForm =  $objFormParam->getHashArray();
	
	// ���å�����¸Ƚ��
	if ($arrForm['mypage_login_memory'] == "1" && $arrForm['mypage_login_email'] != "") {
		$objCookie->setCookie('login_email', $_POST['mypage_login_email']);
	} else {
		$objCookie->setCookie('login_email', '');
	}
	if ($count == 0){
		if($objCustomer->getCustomerDataFromEmailPass($arrForm['mypage_login_pass'], $arrForm['mypage_login_email'])) {
			header("Location: ./index.php");
			exit;
		} else {
			$objQuery = new SC_Query;
			$where = "email = ? AND status = 1 AND del_flg = 0";
			$ret = $objQuery->count("dtb_customer", $where, array($arrForm['mypage_login_email']));
			
			if($ret > 0) {
				sfDispSiteError(TEMP_LOGIN_ERROR);
			} else {
				sfDispSiteError(SITE_LOGIN_ERROR);
			}
		}
	}
	
	break;

}

//-----------------------------------------------------------------------------------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("��������", "mypage_login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�᡼�륢�ɥ쥹", "mypage_login_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ѥ����", "mypage_login_pass", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
}
?>