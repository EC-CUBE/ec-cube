<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../../require.php");

			Header("Content-disposition: attachment; filename=".basename($_POST['select_file']));
			Header("Content-type: application/octet-stream; name=".basename($_POST['select_file']));
			Header("Cache-Control: ");
			Header("Pragma: ");

// �ե���������ɽ��
print("<pre>\n");
print(lfReadFile(USER_PATH.$_GET['file']));
print("</pre>\n");

/* 
 * �ؿ�̾��lfReadFile()
 * ����1 ���ե�����ѥ�
 * ���������ե������ɹ�
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