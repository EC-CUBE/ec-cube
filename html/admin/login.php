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

// ����Ƚ��
if(strlen($_POST{'login_id'}) > 0 && strlen($_POST{'password'}) > 0) {
	// ǧ�ڥѥ���ɤ�Ƚ��
	$ret = fnCheckPassword($conn);
}

if($ret){
	// ����
	header("Location: ".URL_HOME);
	exit;
} else {
	// ���顼�ڡ�����ɽ��
	sfDispError(LOGIN_ERROR);
	exit;
}

/* ǧ�ڥѥ���ɤ�Ƚ�� */
function fnCheckPassword($conn) {
	$sql = "SELECT member_id, password, authority, login_date, name FROM dtb_member WHERE login_id = ? AND del_flg <> 1 AND work = 1";
	$arrcol = array ($_POST['login_id']);
	// DB����Ź沽�ѥ���ɤ�������롣
	$data_list = $conn->getAll($sql ,$arrcol); 
	// �ѥ���ɤμ���
	$password = $data_list[0]['password'];
	// �桼�����ϥѥ���ɤ�Ƚ��
	$ret = sha1($_POST['password'] . ":" . AUTH_MAGIC);
	
	if ($ret == $password) {
   		// ���å������Ͽ
		fnSetLoginSession($data_list[0]['member_id'], $data_list[0]['authority'], $data_list[0]['login_date'], $data_list[0]['name']);
		// ��������������Ͽ
		fnSetLoginDate();
		return true;
	} 
	
	// �ѥ����
	gfPrintLog($_POST['login_id'] . " password incorrect.");
	return false;
}

/* ǧ�ڥ��å�������Ͽ */
function fnSetLoginSession($member_id,$authority,$login_date, $login_name = '') {
	global $osess;
	// ǧ�ںѤߤ�����
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
	// ���˵�Ͽ����
	gfPrintLog("login : user=".$_SESSION{'login_id'}." auth=".$_SESSION{'authority'}." lastlogin=". $_SESSION{'last_login'} ." sid=".$sid);
}

/* �����������ι��� */
function fnSetLoginDate() {
	global $osess;
	$oquery = new SC_Query();
	$sqlval['login_date'] = date("Y-m-d H:i:s");
	$member_id = $osess->GetSession('member_id');
	$where = "member_id = " . $member_id;
	$ret = $oquery->update("dtb_member", $sqlval, $where);
}
?>