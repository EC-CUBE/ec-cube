<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

///////////////////////////////////////////////////////////
/*
	�����Ȥ��Ȥ�ɬ���ѹ���������
*/
///////////////////////////////////////////////////////////

// DB���顼�᡼��������
define ("DB_ERROR_MAIL_TO", "error-ml@lockon.co.jp");

// DB���顼�᡼���̾
define ("DB_ERROR_MAIL_SUBJECT", "OS_TEST_ERROR");

if(defined('DB_TYPE') && defined('DB_USER') && defined('DB_PASSWORD') && defined('DB_SERVER') && defined('DB_PORT') && defined('DB_NAME')) {
	// ��������DB
	define ("DEFAULT_DSN", DB_TYPE . "://" . DB_USER . ":" . DB_PASSWORD . "@" . DB_SERVER . ":" .DB_PORT . "/" . DB_NAME);
}

// ͹���ֹ�����DB
define ("ZIP_DSN", DEFAULT_DSN);

define ("USER_URL", SITE_URL."user_data/"); // �桼���������ڡ����� 

// ǧ���� magic
define ("AUTH_MAGIC", "31eafcbd7a81d7b401a7fdc12bba047c02d1fae6");

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// WEB���ʬ������
define ("MULTI_WEB_SERVER_MODE", false);    // ���ʬ���⡼��(true:ON false:OFF)

/*
 *  host:IP���ɥ쥹�ǻ��ꤹ�롣
 *  user:FTP�桼��
 *  pass:FTP�ѥ����
 */
$arrWEB_SERVERS = array(
    array('host_name' => 'web1', 'host' => '127.0.0.1',   'user' => 'users', 'pass' => 'pass'),        // WEB������1
    array('host_name' => 'web2', 'host' => 'host2',   'user' => 'users', 'pass' => 'pass'),        // WEB������2
);
//////////////////////////////////////////////////////////////////////////////////?///////////////////////////////////////////


?>