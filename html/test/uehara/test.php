<?php
require_once("../../require.php");
require_once(DATA_PATH. "module/Tar.php");

//���̥ե饰TRUE��gzip����򤪤��ʤ�
$tar = new Archive_Tar(USER_TEMPLATE_PATH."bbb/eccube-1.0.2beta.tar.gz", TRUE);
//���ꤵ�줿�ե������˲��ह��
$err = $tar->extractModify(USER_TEMPLATE_PATH."bbb/", "eccube-1.0.2beta");

	// ��ĥ�Ҥ��ڤ���
	$file_name = ereg_replace("\.tar$", "", "bbb/eccube-1.0.2beta.tar.gz");
	$file_name = ereg_replace("\.tar\.gz$", "", $file_name);

echo $file_name;	
?>