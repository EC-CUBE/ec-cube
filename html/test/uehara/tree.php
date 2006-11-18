<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");


$top_dir = USER_PATH;

$objView = new SC_UserView("./templates/");
$objQuery = new SC_Query();

switch($_POST['mode']) {

case 'view':
case 'download':	
case 'delete':
	$now_dir = $_POST['view_dir'];
	
case 'view':
	break;

case 'download':
	break;
	
case 'delete':
	break;
	
default :
	$now_dir = $top_dir;
	break;
}
// ���ߤΥǥ��쥯�ȥ��۲��Υե�������������
$arrFileList = getFileList($now_dir);

sfprintr($now_dir);
sfprintr($arrFileList);

//$objView->assignobj($objPage);
$objView->display("tree.tpl");

//-----------------------------------------------------------------------------------------------------------------------------------

/* 
 * �ؿ�̾��getFileList()
 * ������������ѥ��۲��Υǥ��쥯�ȥ����
 * ����1 ���ĥ꡼���Ǽ����
 * ����2 ����������ǥ��쥯�ȥ�ѥ�
 */
function getFileList($dir) {
	$arrFileList = array();
	if (is_dir($dir)) { 
	    if ($dh = opendir($dir)) { 
	        while (($file = readdir($dh)) !== false) { 
				// ./ �� ../������ǥ��쥯�ȥ�Τߤ����
				//if(filetype($dir . $file) == 'dir' && $file != "." && $file != "..") {
				if($file != "." && $file != "..") {
					$arrFileList[] = $dir.$file;
				}
	        } 
	        closedir($dh); 
	    }
	} 
	
	return $arrFileList;
}

?>