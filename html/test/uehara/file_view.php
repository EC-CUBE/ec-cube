<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../../require.php");

// ľ��ɽ�����ʤ��ե���������
$arrViewFile = array(
 'html',
 'htm',
 'tpl',
 'php',
);

$arrResult = split('\.', $_GET['file']);
$ext = $arrResult[count($arrResult)-1];

sfprintr($ext);

// �ե���������ɽ��
header("Content-type: text/plain\n\n");
print(sfReadFile(USER_PATH.$_GET['file']));

?>