<?php  
// �ǡ����١����������ǡ����μ����� MD5 �ѥ���ɤ���Ѥ�����
require_once("../../require.php");
$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/pear/Auth_HTTP.php");

define("DSN", "pgsql://kakinaka_db_user:password@kakinaka.ec-cube.net/kakinaka_db");

// �ǡ����١�����³���ץ���������
$AuthOptions = array(
'dsn'=>DSN,
'table'=>"dtb_member",                            // �ơ��֥�̾ 
'usernamecol'=>"login_id",			// �桼��̾�Υ����
'passwordcol'=>"password",			// �ѥ���ɤΥ����
//'cryptType'=>"none",				// �ǡ����١�����ǤΥѥ���ɤΰŹ沽����
'cryptType'=>"none",				// �ǡ����١�����ǤΥѥ���ɤΰŹ沽����
'dbFields'=>"name",				// ¾�Υ����μ������ǽ�ˤ���
);

$a = new Auth_HTTP("DB", $AuthOptions);

$a->setRealm('user realm');			// �ΰ� (realm) ̾
$a->setCancelText('<h2>Error 401</h2>');        // ǧ�ڤ����Ԥ����ݤ�ɽ��������å�����
$a->start();					// ǧ�ڥץ����γ���

if($a->getAuth())				// ǧ�ڤ��٤��桼�����ɤ����γ�ǧ 
{
	echo "Hello " . $a->username . " welcome to my secret page <BR>";
	echo "Your details on file are: <BR>";
	echo $a->getAuthData('name');		// �ǡ����١�������¾�Υǡ�����������Ƥ��롣
	echo $a->getAuthData('telephone');      // ������Ǥϡ��桼��ID (userid)�������ֹ� (telephone)
	echo $a->getAuthData('name');		// ����ӥ᡼�륢�ɥ쥹 (email) �������
};

sfprintr($a);

?>
