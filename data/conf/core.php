<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

///////////////////////////////////////////////////////////
/*
	�����Ȥ��Ȥ�ɬ���ѹ���������
*/
///////////////////////////////////////////////////////////

// DATA�ǥ��쥯�ȥ�ѥ�(HTML�ǥ��쥯�ȥ�ѥ���������Хѥ�)
define("DATA_PATH", HTML_PATH . "../data");

// �桼���ե�������¸��
define("USER_PATH", HTML_PATH . "user_data/");

// �桼�����󥯥롼�ɥե�������¸��
define("USER_INC_PATH", USER_PATH, "include/");

// �֥�å��ե�������¸��
define("BLOC_PATH", HTML_PATH . "user_data/include/bloc/");

// �桼�����������̤Υǥե����PHP�ե�����
define("USER_DEF_PHP", HTML_PATH . "__default.php");

define("USER_DIR", "html/user_data/");
define("INCLUDE_DIR", USER_DIR."include/");
define("BLOC_DIR", "html/user_data/include/bloc/");

// ����¾���̤Υǥե���ȥڡ����쥤������
define("DEF_LAYOUT", "products/list.php");

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

?>