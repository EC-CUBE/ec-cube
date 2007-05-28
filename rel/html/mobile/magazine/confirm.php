<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * メルマガ確認
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** 必ず変更する **/
		$this->tpl_mainpage = 'magazine/confirm.tpl';
		$this->tpl_title .= 'メルマガ確認';
	}
}

$objPage = new LC_Page();
$objConn = new SC_DbConn();
$objPage->arrForm = $_POST;

// 登録
if (isset($_REQUEST['btnRegist'])) {
	$objPage->arrErr = lfMailErrorCheck($objPage->arrForm, "regist");

	// エラーがなければ
	if (count($objPage->arrErr) == 0) {
		// 確認
		$objPage->arrForm['kind'] = 'メルマガ登録';
		$objPage->arrForm['type'] = 'regist';
		$objPage->arrForm['mail'] = $objPage->arrForm['regist'];
	} else {
		$objPage->tpl_mainpage = 'magazine/index.tpl';
		$objPage->tpl_title = 'メルマガ登録・解除';
	}
// 解除
} elseif (isset($_REQUEST['btnCancel'])) {
	$objPage->arrErr = lfMailErrorCheck($objPage->arrForm, "cancel");

	// エラーがなければ
	if (count($objPage->arrErr) == 0) {
		// 確認
		$objPage->arrForm['kind'] = 'メルマガ解除';
		$objPage->arrForm['type'] = 'cancel';
		$objPage->arrForm['mail'] = $objPage->arrForm['cancel'];
	} else {
		$objPage->tpl_mainpage = 'magazine/index.tpl';
		$objPage->tpl_title = 'メルマガ登録・解除';
	}
// 完了
} elseif ($_REQUEST['mode'] == 'regist' or $_REQUEST['mode'] == 'cancel') {

	//　登録
	if ($_REQUEST['mode'] == 'regist') {
		$uniqId = lfRegistData($_POST["email"]);
		$subject = sfMakesubject('メルマガ登録のご確認');
	//　解除
	} elseif ($_REQUEST['mode'] == 'cancel') {
		$uniqId = lfGetSecretKey($_POST["email"]);
		$subject = sfMakesubject('メルマガ解除のご確認');
	}
	$CONF = sf_getBasisData();
	$objPage->CONF = $CONF;
	$objPage->tpl_url = gfAddSessionId(MOBILE_SSL_URL . "magazine/" . $_REQUEST['mode'] . ".php?id=" . $uniqId);
	
	$objMailText = new SC_MobileView();
	$objMailText->assignobj($objPage);
	$toCustomerMail = $objMailText->fetch("mail_templates/mailmagazine_" . $_REQUEST['mode'] . ".tpl");
	$objMail = new GC_SendMail();
	$objMail->setItem(
						''									//　宛先
						, $subject							//　サブジェクト
						, $toCustomerMail					//　本文
						, $CONF["email03"]					//　配送元アドレス
						, $CONF["shop_name"]				//　配送元　名前
						, $CONF["email03"]					//　reply_to
						, $CONF["email04"]					//　return_path
						, $CONF["email04"]					//  Errors_to
						, $CONF["email01"]					//  Bcc
														);
	// 宛先の設定
	$objMail->setTo($_POST["email"], $_POST["email"]);
	$objMail->sendMail();

	// 完了ページに移動させる。
	header("Location:" . gfAddSessionId("./complete.php"));
	exit;
} else {
	sfDispSiteError(CUSTOMER_ERROR, "", false, "", true);
}

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView = new SC_MobileView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------


//---- 入力エラーチェック
function lfMailErrorCheck($array, $dataName) {
	$objErr = new SC_CheckError($array);
	$objErr->doFunc(
				array('メールアドレス', $dataName, MTEXT_LEN) ,
				array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", 
					"SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK", "MOBILE_EMAIL_CHECK"));

	// 入力エラーがなければ
	if (count($objErr->arrErr) == 0) {
		// メルマガの登録有無
		$flg = lfIsRegistData($array[$dataName]);

		// 登録の時
		if ($dataName == 'regist' and $flg == true) {
			$objErr->arrErr[$dataName] = "既に登録されています。<br>";
		// 解除の時
		} elseif ($dataName == 'cancel' and $flg == false) {
			$objErr->arrErr[$dataName] = "メルマガ登録がされていません。<br>";
		}
	}

	return $objErr->arrErr;
}


//---- メルマガ登録
function lfRegistData ($email) {
	global $objConn;

	$count = 1;
	while ($count != 0) {
		$uniqid = sfGetUniqRandomId("t");
		$count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer_mail WHERE secret_key = ?", array($uniqid));
	}
	
	$arrRegist["email"] = $email;			// メールアドレス
	$arrRegist["mail_flag"] = 5;			// 登録状態
	$arrRegist["secret_key"] = $uniqid;		// ID発行
	$arrRegist["create_date"] = "now()"; 	// 作成日
	$arrRegist["update_date"] = "now()"; 	// 更新日

	//-- 仮登録実行
	$objConn->query("BEGIN");

	$objQuery = new SC_Query();

	//--　既にメルマガ登録しているかの判定
	$sql = "SELECT count(*) FROM dtb_customer_mail WHERE email = ?";
	$mailResult = $objConn->getOne($sql, array($arrRegist["email"]));

	if ($mailResult == 1) {		
		$objQuery->update("dtb_customer_mail", $arrRegist, "email = '" .addslashes($arrRegist["email"]). "'");			
	} else {
		$objQuery->insert("dtb_customer_mail", $arrRegist);		
	}
	$objConn->query("COMMIT");

	return $uniqid;
}

// 登録されているキーの取得
function lfGetSecretKey ($email) {
	global $objConn;

	$sql = "SELECT secret_key FROM dtb_customer_mail WHERE email = ?";
	$uniqid = $objConn->getOne($sql, array($email));

	if ($uniqid == '') {
		$count = 1;
		while ($count != 0) {
			$uniqid = sfGetUniqRandomId("t");
			$count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer_mail WHERE secret_key = ?", array($uniqid));
		}

		$objQuery = new SC_Query();
		$objQuery->update("dtb_customer_mail", array('secret_key' => $uniqid), "email = '" .addslashes($email). "'");
	}

	return $uniqid;
}

// 既に登録されているかどうか
function lfIsRegistData ($email) {
	global $objConn;

	$sql = "SELECT email, mail_flag FROM dtb_customer_mail WHERE email = ?";
	$mailResult = $objConn->getRow($sql, array($email));

	// NULLも購読とみなす
	if (count($mailResult) == 0 or ($mailResult[1] != null and $mailResult[1] != 2 )) {
		return false;
	} else {
		return true;
	}
}


?>
