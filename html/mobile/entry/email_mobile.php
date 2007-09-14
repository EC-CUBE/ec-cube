<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 * ��Х��륵����/���ӥ᡼����Ͽ
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'entry/email_mobile.tpl';
		$this->tpl_title = '���ӥ᡼����Ͽ';
		/*
		 session_start����no-cache�إå������������뤳�Ȥ�
		 �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
		 private-no-expire:���饤����ȤΥ���å������Ĥ��롣
		*/
		session_cache_limiter('private-no-expire');		
	}
}

$objPage = new LC_Page;
$objView = new SC_MobileView;
$objCustomer = new SC_Customer;
$objFormParam = new SC_FormParam;

if (isset($_SESSION['mobile']['kara_mail_from'])) {
	$_SERVER['REQUEST_METHOD'] = 'POST';
	$_POST['email_mobile'] = $_SESSION['mobile']['kara_mail_from'];
}

lfInitParam($objFormParam);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$objFormParam->setParam($_POST);
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($objFormParam, $objCustomer);

	if (empty($objPage->arrErr)) {
		lfRegister($objFormParam, $objCustomer);
		$objPage->tpl_mainpage = 'entry/email_mobile_complete.tpl';
		$objPage->tpl_title = '���ӥ᡼����Ͽ��λ';
	}
}

// ���᡼���ѤΥȡ������������롣
if (MOBILE_USE_KARA_MAIL) {
	$token = gfPrepareKaraMail('entry/email_mobile.php');
	if ($token !== false) {
		$objPage->tpl_kara_mail_to = MOBILE_KARA_MAIL_ADDRESS_USER . MOBILE_KARA_MAIL_ADDRESS_DELIMITER . 'entry_' . $token . '@' . MOBILE_KARA_MAIL_ADDRESS_DOMAIN;
	}
}

$objPage->tpl_name = $objCustomer->getValue('name01');
$objPage->arrForm = $objFormParam->getFormParamList();

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

function lfInitParam(&$objFormParam) {
	$objFormParam->addParam('�᡼�륢�ɥ쥹', 'email_mobile', MTEXT_LEN, 'a',
		array('NO_SPTAB', 'EXIST_CHECK', 'MAX_LENGTH_CHECK', 'CHANGE_LOWER', 'EMAIL_CHAR_CHECK', 'MOBILE_EMAIL_CHECK'));
}

function lfCheckError(&$objFormParam, &$objCustomer) {
	$arrRet = $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();

	if (count($objErr->arrErr) > 0) {
		return $objErr->arrErr;
	}

	$email_mobile = $objFormParam->getValue('email_mobile');
	$customer_id = $objCustomer->getValue('customer_id');
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select('email, email_mobile, update_date, del_flg', 'dtb_customer', '(email ILIKE ? OR email_mobile ILIKE ?) AND customer_id <> ? ORDER BY del_flg', array($email_mobile, $email_mobile, $customer_id));

	if (count($arrRet) > 0) {
		if ($arrRet[0]['del_flg'] != '1') {
			// ����Ǥ�����
			$objErr->arrErr['email_mobile'] .= '�� ���Ǥ���Ͽ����Ƥ���᡼�륢�ɥ쥹�Ǥ���<br>';
		} else {
			// ��񤷤�����Ǥ�����
			$leave_time = sfDBDatetoTime($arrRet[0]['update_date']);
			$now_time = time();
			$pass_time = $now_time - $leave_time;
			// ��񤫤鲿����-�вᤷ�Ƥ��뤫Ƚ�ꤹ�롣
			$limit_time = ENTRY_LIMIT_HOUR * 3600;
			if ($pass_time < $limit_time) {
				$objErr->arrErr['email_mobile'] .= '�� ��񤫤������֤δ֤ϡ�Ʊ���᡼�륢�ɥ쥹����Ѥ��뤳�ȤϤǤ��ޤ���<br>';
			}
		}
	}

	return $objErr->arrErr;
}

function lfRegister(&$objFormParam, &$objCustomer)
{
	$customer_id = $objCustomer->getValue('customer_id');
	$email_mobile = $objFormParam->getValue('email_mobile');

	$objQuery = new SC_Query();
	$objQuery->update('dtb_customer', array('email_mobile' => $email_mobile), 'customer_id = ?', array($customer_id));

	$objCustomer->setValue('email_mobile', $email_mobile);
}
?>
