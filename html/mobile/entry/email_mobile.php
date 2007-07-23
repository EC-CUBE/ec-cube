<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 * モバイルサイト/携帯メール登録
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'entry/email_mobile.tpl';
		$this->tpl_title = '携帯メール登録';
		/*
		 session_start時のno-cacheヘッダーを抑制することで
		 「戻る」ボタン使用時の有効期限切れ表示を抑制する。
		 private-no-expire:クライアントのキャッシュを許可する。
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
		$objPage->tpl_title = '携帯メール登録完了';
	}
}

// 空メール用のトークンを作成する。
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
	$objFormParam->addParam('メールアドレス', 'email_mobile', MTEXT_LEN, 'a',
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
			// 会員である場合
			$objErr->arrErr['email_mobile'] .= '※ すでに登録されているメールアドレスです。<br>';
		} else {
			// 退会した会員である場合
			$leave_time = sfDBDatetoTime($arrRet[0]['update_date']);
			$now_time = time();
			$pass_time = $now_time - $leave_time;
			// 退会から何時間-経過しているか判定する。
			$limit_time = ENTRY_LIMIT_HOUR * 3600;
			if ($pass_time < $limit_time) {
				$objErr->arrErr['email_mobile'] .= '※ 退会から一定期間の間は、同じメールアドレスを使用することはできません。<br>';
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
