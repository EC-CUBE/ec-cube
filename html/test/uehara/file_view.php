<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../../require.php");

// �������Ȥ���ɽ������ե���������(ľ�ܼ¹Ԥ��ʤ��ե�����)
$arrViewFile = array(
					 'html',
					 'htm',
					 'tpl',
					 'php',
					 'css',
					 'js',
);

// ��ĥ�Ҽ���
$arrResult = split('\.', $_GET['file']);
$ext = $arrResult[count($arrResult)-1];

// �ե���������ɽ��
if(in_array($ext, $arrViewFile)) {
	// �ե�������ɤ߹����ɽ��
	header("Content-type: text/plain\n\n");
	print(sfReadFile(USER_PATH.$_GET['file']));
} else {
	header("Location: ".USER_URL.$_GET['file']);
}
?>