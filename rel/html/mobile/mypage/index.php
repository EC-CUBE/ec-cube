<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * MyPage
 */

require_once("../require.php");

class LC_Page{
	function LC_Page() {
		$this->tpl_mainpage = 'mypage/index.tpl';
		$this->tpl_title = 'MYページ/購入履歴一覧';
		session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$objView = new SC_MobileView();
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();
// クッキー管理クラス
$objCookie = new SC_Cookie(COOKIE_EXPIRE);
// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
// POST値の取得
$objFormParam->setParam($_POST);

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

// 携帯端末IDが一致する会員が存在するかどうかをチェックする。
$objPage->tpl_valid_phone_id = $objCustomer->checkMobilePhoneId();

// ログイン処理
if($_POST['mode'] == 'login') {
	$objFormParam->toLower('login_email');
	$arrErr = $objFormParam->checkError();
	$arrForm =  $objFormParam->getHashArray();
	
	// クッキー保存判定
	if ($arrForm['login_memory'] == "1" && $arrForm['login_email'] != "") {
		$objCookie->setCookie('login_email', $_POST['login_email']);
	} else {
		$objCookie->setCookie('login_email', '');
	}

	if (count($arrErr) == 0){
		if($objCustomer->getCustomerDataFromMobilePhoneIdPass($arrForm['login_pass']) ||
		   $objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'], true)) {
			// ログインが成功した場合は携帯端末IDを保存する。
			$objCustomer->updateMobilePhoneId();

			// 携帯のメールアドレスをコピーする。
			$objCustomer->updateEmailMobile();

			// 携帯のメールアドレスが登録されていない場合
			if (!$objCustomer->hasValue('email_mobile')) {
				header('Location: ' . gfAddSessionId('../entry/email_mobile.php'));
				exit;
			}
		} else {
			$objQuery = new SC_Query;
			$where = "email = ? AND status = 1 AND del_flg = 0";
			$ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email']));

			if($ret > 0) {
				sfDispSiteError(TEMP_LOGIN_ERROR, "", false, "", true);
			} else {
				sfDispSiteError(SITE_LOGIN_ERROR, "", false, "", true);
			}
		}
	}
}


// ログインチェック
if(!$objCustomer->isLoginSuccess()) {
	$objPage->tpl_mainpage = 'mypage/login.tpl';
	$objView->assignArray($objFormParam->getHashArray());
	$objView->assignArray(array("arrErr" => $arrErr));
}else {
	//マイページトップ顧客情報表示用
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
}

$objView->assignobj($objPage);				//$objpage内の全てのテンプレート変数をsmartyに格納
$objView->display(SITE_FRAME);				//パスとテンプレート変数の呼び出し、実行

//-------------------------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("記憶する", "login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("メールアドレス", "login_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("パスワード", "login_pass", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
}
?>
