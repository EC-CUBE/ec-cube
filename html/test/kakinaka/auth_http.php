<?php  
// �ǡ����١����������ǡ����μ����� MD5 �ѥ���ɤ���Ѥ�����
require_once("../../require.php");
$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/pear/Auth_HTTP.php");

//define("DSN", "pgsql://kakinaka_db_user:password@kakinaka.ec-cube.net/kakinaka_db");
define("DSN", "mysql://eccube_db_user:password@210.18.212.165:3308/eccube_db");

// �ǡ����١�����³���ץ���������
$arrDbConn = array(
	'dsn'=>DSN,
	'table'=>"dtb_member",              // �ơ��֥�̾ 
	'usernamecol'=>"login_id",			// �桼��̾�Υ����
	'passwordcol'=>"password",			// �ѥ���ɤΥ����
	'cryptType'=>"none",					// �ѥ���ɤΰŹ沽����(�Ź沽�ʤ��ΤȤ���none)
	'db_fields'=>"*",					// ����¾�Υ��������������ˤϥ�������ꤹ��
);

$objAuthHttp = new Auth_HTTP("DB", $arrDbConn);		// ���֥�����������

$objAuthHttp->setRealm('user realm');				// �ΰ� (realm) ̾
$objAuthHttp->setCancelText('��³���顼'); 		   	// ǧ�ڤ����Ԥ����ݤ�ɽ��������å�����
$objAuthHttp->start();								// ǧ�ڥץ����γ���

// ǧ�ڥ����å�(������TRUE�����ԡ�FALSE)
if($objAuthHttp->getAuth())				
{
	echo "ǧ������";
	echo $objAuthHttp->getAuthData('name');		// ����ӥ᡼�륢�ɥ쥹 (email) �������
}

?>
