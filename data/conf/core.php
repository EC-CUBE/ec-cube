<?php
/*
 * Copyright ��� 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

///////////////////////////////////////////////////////////
/*
	�����Ȥ��Ȥ�ɬ���ѹ���������
*/
///////////////////////////////////////////////////////////

// �ƥ�ץ졼�ȥե�������¸��
define("USER_DIR", "html/user_data/");

// �ƥ�ץ졼�ȥե�������¸��
define("INCLUDE_DIR", USER_DIR."include/");

// �֥�å��ե�������¸��
define("BLOC_DIR", "html/user_data/include/bloc/");

// �桼�����������̤Υǥե����PHP�ե�����
define("USER_DEF_PHP", ROOT_DIR."html/__default.php");

// ����¾���̤Υǥե���ȥڡ����쥤������
define("DEF_LAYOUT", "products/list.php");

// DB���顼�᡼��������
define ("DB_ERROR_MAIL_TO", "error-ml@lockon.co.jp");

// DB���顼�᡼���̾
define ("DB_ERROR_MAIL_SUBJECT", "OS_TEST_ERROR");

// ��������DB
define ("DEFAULT_DSN", DB_TYPE . "://" . DB_USER . ":" . DB_PASSWORD . "@" . DB_SERVER . DB_PORT . "/" . DB_NAME);

// ͹���ֹ�����DB
define ("ZIP_DSN", DEFAULT_DSN);

define ("USER_URL", SITE_URL."user_data/"); // �桼���������ڡ����� 

// ǧ���� magic
define ("AUTH_MAGIC", "31eafcbd7a81d7b401a7fdc12bba047c02d1fae6");

?>