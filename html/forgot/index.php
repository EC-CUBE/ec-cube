<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	
	var $errmsg;
	var $arrReminder;
	var $temp_password;
		
	function LC_Page() {
		$this->tpl_mainpage = 'forgot/index.tpl';
		$this->tpl_mainno = '';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSess = new SC_Session();
$CONF = sf_getBasisData();					// 店舗基本情報
// クッキー管理クラス
$objCookie = new SC_Cookie(COOKIE_EXPIRE);

if ( $_POST['mode'] == 'mail_check' ){
	//メアド入力時
	$_POST['email'] = strtolower($_POST['email']);
	$sql = "SELECT * FROM dtb_customer WHERE email ILIKE ? AND status = 2 AND del_flg = 0";
	$result = $conn->getAll($sql, array($_POST['email']) );
	
	if ( $result[0]['reminder'] ){		// 本会員登録済みの場合
		// 入力emailが存在する		
		$_SESSION['forgot']['email'] = $_POST['email'];
		$_SESSION['forgot']['reminder'] = $result[0]['reminder'];
		// ヒミツの答え入力画面
		$objPage->Reminder = $arrReminder[$_SESSION['forgot']['reminder']];
		$objPage->tpl_mainpage = 'forgot/secret.tpl';
	} else {
		$sql = "SELECT customer_id FROM dtb_customer WHERE email ILIKE ? AND status = 1 AND del_flg = 0";	//仮登録中の確認
		$result = $conn->getAll($sql, array($_POST['email']) );
		if ($result) {
			$objPage->errmsg = "ご入力のemailアドレスは現在仮登録中です。<br>登録の際にお送りしたメールのURLにアクセスし、<br>本会員登録をお願いします。";
		} else {		//　登録していない場合
			$objPage->errmsg = "ご入力のemailアドレスは登録されていません";
		}
	}
	
} elseif( $_POST['mode'] == 'secret_check' ){
	//ヒミツの答え入力時
	
	if ( $_SESSION['forgot']['email'] ) {
		// ヒミツの答えの回答が正しいかチェック
		
		$sql = "SELECT * FROM dtb_customer WHERE email ILIKE ? AND del_flg = 0";
		$result = $conn->getAll($sql, array($_SESSION['forgot']['email']) );
		$data = $result[0];
		
		if ( $data['reminder_answer'] === $_POST['input_reminder'] ){
			// ヒミツの答えが正しい
						
			// 新しいパスワードを設定する
			$objPage->temp_password = gfMakePassword(8);
						
			if(FORGOT_MAIL == 1) {
				// メールで変更通知をする
				lfSendMail($CONF, $_SESSION['forgot']['email'], $data['name01'], $objPage->temp_password);
			}
			
			// DBを書き換える
			$sql = "UPDATE dtb_customer SET password = ?, update_date = now() WHERE customer_id = ?";
			$conn->query( $sql, array( sha1($objPage->temp_password . ":" . AUTH_MAGIC) ,$data['customer_id']) );
			
			// 完了画面の表示
			$objPage->tpl_mainpage = 'forgot/complete.tpl';
			
			// セッション変数の解放
			$_SESSION['forgot'] = array();
			unset($_SESSION['forgot']);
			
		} else {
			// ヒミツの答えが正しくない
			
			$objPage->Reminder = $arrReminder[$_SESSION['forgot']['reminder']];
			$objPage->errmsg = "パスワードを忘れたときの質問に対する回答が正しくありません";
			$objPage->tpl_mainpage = 'forgot/secret.tpl';

		}
	
		
	} else {
		// アクセス元が不正または、セッション保持期間が切れている
		$objPage->errmsg = "emailアドレスを再度登録してください。<br />前回の入力から時間が経っていますと、本メッセージが表示される可能性があります。";
	}
}

// デフォルト入力
if($_POST['email'] != "") {
	// POST値を入力
	$objPage->tpl_login_email = $_POST['email'];
} else {
	// クッキー値を入力
	$objPage->tpl_login_email = $objCookie->getCookie('login_email');
}

//----　ページ表示
$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);

// ---------------------------------------------------------------------------------------------------------------


function lfSendMail($CONF, $email, $customer_name, $temp_password){
	//　パスワード変更お知らせメール送信
	
	$objPage = new LC_Page();
	$objPage->customer_name = $customer_name;
	$objPage->temp_password = $temp_password;
	$objMailText = new SC_SiteView();
	$objMailText->assignobj($objPage);
	
	$toCustomerMail = $objMailText->fetch("mail_templates/forgot_mail.tpl");
	$objMail = new GC_SendMail();
	
	$objMail->setItem(
						  ''								//　宛先
						, "パスワードが変更されました" ."【" .$CONF["shop_name"]. "】"		//　サブジェクト
						, $toCustomerMail					//　本文
						, $CONF["email03"]					//　配送元アドレス
						, $CONF["shop_name"]				//　配送元　名前
						, $CONF["email03"]					//　reply_to
						, $CONF["email04"]					//　return_path
						, $CONF["email04"]					//  Errors_to

														);
	$objMail->setTo($email, $customer_name ." 様");
	$objMail->sendMail();	
	
}


?>

