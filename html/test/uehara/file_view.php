<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../../require.php");

// �ե���������ɽ��
print("<pre>\n");
print(lfReadFile(USER_PATH.$_GET['file']));
print("</pre>\n");

/* 
 * �ؿ�̾��lfReadFile()
 * ����1 ���ե�����ѥ�
 * ���������ե������ɹ�
 */
 /*
function lfReadFile($file) {
	$fp = fopen($file, "r");
	$read_file = fpassthru($fp); 
	fclose($fp); 
}
*/
function lfReadFile($filename) { 
    $str = ""; 
    // �Х��ʥ�⡼�ɤǥ����ץ� 
    $fp = @fopen($filename, "rb" ); 
    //�ե��������Ƥ������ѿ����ɤ߹��� 
    if($fp) { 
        $str = @fread($fp, filesize($filename)+1); 
    } 
    @fclose($fp); 
    // ���ԥ����ɤ�����<br />������ 
    $str = nl2br($str); 
    return $str; 
} 
?>