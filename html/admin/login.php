<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("./require.php");

$conn = new SC_DBConn();

$osess = new SC_Session();
$ret = false;

// 入力判定
if(strlen($_POST{'login_id'}) > 0 && strlen($_POST{'password'}) > 0) {
	// 認証パスワードの判定
	$ret = fnCheckPassword($conn);
}

if($ret){
	// 成功
	header("Location: ".URL_HOME);
	exit;
} else {
	// エラーページの表示
	sfDispError(LOGIN_ERROR);
	exit;
}

/* 認証パスワードの判定 */
function fnCheckPassword($conn) {
	$sql = "SELECT member_id, password, authority, login_date, name FROM dtb_member WHERE login_id = ? AND del_flg <> 1 AND work = 1";
	$arrcol = array ($_POST['login_id']);
	// DBから暗号化パスワードを取得する。
	$data_list = $conn->getAll($sql ,$arrcol); 
	// パスワードの取得
	$password = $data_list[0]['password'];
	// ユーザ入力パスワードの判定
	$ret = sha1($_POST['password'] . ":" . AUTH_MAGIC);
	
	if ($ret == $password) {
   		// セッション登録
		fnSetLoginSession($data_list[0]['member_id'], $data_list[0]['authority'], $data_list[0]['login_date'], $data_list[0]['name']);
		// ログイン日時の登録
		fnSetLoginDate();
		return true;
	} 
	
	// パスワード
	gfPrintLog($_POST['login_id'] . " password incorrect.");
	return false;
}

/* 認証セッションの登録 */
function fnSetLoginSession($member_id,$authority,$login_date, $login_name = '') {
	global $osess;
	// 認証済みの設定
	$osess->SetSession('cert', CERT_STRING);
	$osess->SetSession('login_id', $_POST{'login_id'});
	$osess->SetSession('authority', $authority);
	$osess->SetSession('member_id', $member_id);
	$osess->SetSession('login_name', $login_name);
    $osess->SetSession('uniqid', $osess->getUniqId());
	
	if(strlen($login_date) > 0) {
		$osess->SetSession('last_login', $login_date);
	} else {
		$osess->SetSession('last_login', date("Y-m-d H:i:s"));
	}
	$sid = $osess->GetSID();
	// ログに記録する
	gfPrintLog("login : user=".$_SESSION{'login_id'}." auth=".$_SESSION{'authority'}." lastlogin=". $_SESSION{'last_login'} ." sid=".$sid);
}

/* ログイン日時の更新 */
function fnSetLoginDate() {
	global $osess;
	$oquery = new SC_Query();
	$sqlval['login_date'] = date("Y-m-d H:i:s");
	$member_id = $osess->GetSession('member_id');
	$where = "member_id = " . $member_id;
	$ret = $oquery->update("dtb_member", $sqlval, $where);
}
?>