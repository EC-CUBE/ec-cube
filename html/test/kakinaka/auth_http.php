<?php  
// �ǡ����١����������ǡ����μ����� MD5 �ѥ���ɤ���Ѥ�����
require_once("../../require.php");
$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/pear/Auth_HTTP.php");


$params = Array("../../.htpasswd","authType"=>"basic","cryptType"=>"MD5");

$objAuth = new Auth_HTTP("File",$params);

// realm�ΰ�̾
$objAuth->setRealm('Please Enter Your Password');

// ǧ�ڤ򥭥�󥻥��ǧ�ڥ��顼�����ݤ�ɽ��������å�����
$objAuth->setCancelText('<h2>Authorization Required</h2>');

// ǧ�ڥץ����γ���
$objAuth->start();

if($objAuth->getAuth()) {
    echo $objAuth->username . "��ǧ�ڤ�����!";
}

define("DEFAULT_DSN", "pgsql://kakinaka_db_user:password@kakinaka.ec-cube.net/kakinaka_db");


// �ǡ����١�����³���ץ���������
$AuthOptions = array(
//'dsn'=>"pgsql://test:test@localhost/testdb",
'dsn'=>DEFAULT_DSN,
'table'=>"dtb_member",                            // �ơ��֥�̾ 
'usernamecol'=>"login_id",			// �桼��̾�Υ����
'passwordcol'=>"password",			// �ѥ���ɤΥ����
//'cryptType'=>"md5",				// �ǡ����١�����ǤΥѥ���ɤΰŹ沽����
'cryptType'=>"none",				// �ǡ����١�����ǤΥѥ���ɤΰŹ沽����
'dbFields'=>"*",				// ¾�Υ����μ������ǽ�ˤ���
);

$a = new Auth_HTTP("DB", $AuthOptions);


sfprintr($a);

//$a->setRealm('yourrealm');			// �ΰ� (realm) ̾
//$a->setCancelText('<h2>Error 401</h2>');        // ǧ�ڤ����Ԥ����ݤ�ɽ��������å�����
$a->start();					// ǧ�ڥץ����γ���

/*

if($a->getAuth())				// ǧ�ڤ��٤��桼�����ɤ����γ�ǧ 
{
	echo "Hello $a->username welcome to my secret page <BR>";
	echo "Your details on file are: <BR>";
	echo $a->getAuthData('userid');		// �ǡ����١�������¾�Υǡ�����������Ƥ��롣
	echo $a->getAuthData('telephone');      // ������Ǥϡ��桼��ID (userid)�������ֹ� (telephone)
	echo $a->getAuthData('email');		// ����ӥ᡼�륢�ɥ쥹 (email) �������
};
*/

?>