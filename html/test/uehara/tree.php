<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");


$dir = USER_PATH;

$objView = new SC_UserView("./templates/");
$objQuery = new SC_Query();

$arrTree = array();

getDir($arrTree, $dir);

sfprintr($arrTree);
//$objView->assignobj($objPage);
$objView->display("tree.tpl");

//-----------------------------------------------------------------------------------------------------------------------------------

/* 
 * �ؿ�̾��getDir()
 * ������������ѥ��۲��Υǥ��쥯�ȥ����
 * ����1 ���ĥ꡼���Ǽ����
 * ����2 ����������ǥ��쥯�ȥ�ѥ�
 */
function getDir(&$arrTree, $dir) {
	if (is_dir($dir)) { 
	    if ($dh = opendir($dir)) { 
	        while (($file = readdir($dh)) !== false) { 
				// ./ �� ../������ǥ��쥯�ȥ�Τߤ����
				if(filetype($dir . $file) == 'dir' && $file != "." && $file != "..") {
					$arrTree[] = dir.file;
				} 
	        } 
	        closedir($dh); 
	    }
	} 
}

?>