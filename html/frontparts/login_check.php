<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

// ������URL��POST���줿���ϥ��顼ɽ��
if (isset($_POST['url']) && lfIsValidURL() !== true) {
    gfPrintLog('invalid access :login_check.php $POST["url"]=' . $_POST['url']);
    sfDispSiteError(PAGE_ERROR);
}

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
	$objFormParam->toLower('login_email');
	$arrErr = $objFormParam->checkError();
	$arrForm =  $objFormParam->getHashArray();
	// ���å�����¸Ƚ��
	if ($arrForm['login_memory'] == "1" && $arrForm['login_email'] != "") {
		$objCookie->setCookie('login_email', $_POST['login_email']);
	} else {
		$objCookie->setCookie('login_email', '');
	}
	
	if(count($arrErr) == 0) {
		if($objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'])) {
			header("Location: " . $_POST['url']);
			exit;
		} else {
			$objQuery = new SC_Query;
			$where = "email ILIKE ? AND status = 1 AND del_flg = 0";
			$ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email']));
			
			if($ret > 0) {
				sfDispSiteError(TEMP_LOGIN_ERROR);
			} else {
				sfDispSiteError(SITE_LOGIN_ERROR);
			}
		}
	} else {
		// ���ϥ��顼�ξ�硢���Υ��ɥ쥹���᤹��
		header("Location: " . $_POST['url']);
		exit;
	}
	break;
case 'logout':
	// ���������β���
	$objCustomer->EndSession();
	$mypage_url_search = strpos('.'.$_POST['url'], "mypage");
	//�ޥ��ڡ�����������ϥ�������̤ذܹ�
	if ($mypage_url_search == 2){
	header("Location: /mypage/login.php");
	}else{
	header("Location: " . $_POST['url']);	
	}
	exit;
	break;
}

//-----------------------------------------------------------------------------------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("��������", "login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�᡼�륢�ɥ쥹", "login_email", STEXT_LEN, "a", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ѥ����", "login_pass", STEXT_LEN, "", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
}

/* POST�����URL�Υ����å�*/
function lfIsValidURL() {
    $site_url  = sfIsHTTPS() ? SSL_URL : SITE_URL;
    $check_url = trim($_POST['url']);
    
    // �ɥᥤ������å�
    $pattern = "|^$site_url|";
    if (!preg_match($pattern, $check_url)) {
        return false;
    }

    // ���ԥ�����(CR��LF)��NULL�Х��ȥ����å�
    $pattern = '/\r|\n|\0|%0D|%0A|%00/';
    if (preg_match_all($pattern, $check_url, $matches)) {
        return false;
    }
    
    return true;
}

?>