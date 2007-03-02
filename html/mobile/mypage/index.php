<?php
/*
 * MyPage
 */

require_once("../require.php");

class LC_Page{
	function LC_Page() {
		$this->tpl_mainpage = 'mypage/index.tpl';
		$this->tpl_title = 'MY�ڡ���/�����������';
		session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();
// ���å����������饹
$objCookie = new SC_Cookie(COOKIE_EXPIRE);
// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();
// POST�ͤμ���
$objFormParam->setParam($_POST);

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

// ����ü��ID�����פ�������¸�ߤ��뤫�ɤ���������å����롣
$objPage->tpl_valid_phone_id = $objCustomer->checkMobilePhoneId();

// ���������
if($_POST['mode'] == 'login') {
	$objFormParam->toLower('login_email');
	$arrErr = $objFormParam->checkError();
	$arrForm =  $objFormParam->getHashArray();
	
	// ���å�����¸Ƚ��
	if ($arrForm['login_memory'] == "1" && $arrForm['login_email'] != "") {
		$objCookie->setCookie('login_email', $_POST['login_email']);
	} else {
		$objCookie->setCookie('login_email', '');
	}

	if (count($arrErr) == 0){
		if($objCustomer->getCustomerDataFromMobilePhoneIdPass($arrForm['login_pass']) ||
		   $objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'], true)) {
			// �����������������Ϸ���ü��ID����¸���롣
			$objCustomer->updateMobilePhoneId();

			// ���ӤΥ᡼�륢�ɥ쥹�򥳥ԡ����롣
			$objCustomer->updateEmailMobile();

			// ���ӤΥ᡼�륢�ɥ쥹����Ͽ����Ƥ��ʤ����
			if (!$objCustomer->hasValue('email_mobile')) {
				header('Location: ' . gfAddSessionId('../entry/email_mobile.php'));
				exit;
			}
		} else {
			$objQuery = new SC_Query;
			$where = "email = ? AND status = 1 AND del_flg = 0";
			$ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email']));
			
			if($ret > 0) {
				sfDispSiteError(TEMP_LOGIN_ERROR);
			} else {
				sfDispSiteError(SITE_LOGIN_ERROR);
			}
		}
	}
}


// ����������å�
if(!$objCustomer->isLoginSuccess()) {
	$objPage->tpl_mainpage = 'mypage/login.tpl';
	$objView->assignArray($objFormParam->getHashArray());
	$objView->assignArray(array("arrErr" => $arrErr));
}else {
	//�ޥ��ڡ����ȥå׸ܵҾ���ɽ����
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
}

$objView->assignobj($objPage);				//$objpage������ƤΥƥ�ץ졼���ѿ���smarty�˳�Ǽ
$objView->display(SITE_FRAME);				//�ѥ��ȥƥ�ץ졼���ѿ��θƤӽФ����¹�

//-------------------------------------------------------------------------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("��������", "login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�᡼�륢�ɥ쥹", "login_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ѥ����", "login_pass", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
}
?>
