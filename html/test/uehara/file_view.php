<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
 
// �ե���������ɽ��
print("<pre>\n");
lfReadFile($_GET['file']);
print("</pre>\n");

/* 
 * �ؿ�̾��lfReadFile()
 * ����1 ���ե�����ѥ�
 * ���������ե������ɹ�
 */
function lfReadFile($file) {
	$fp = fopen($file, "r");
	$read_file = fpassthru($fp); 
	fclose($fp); 
}
?>