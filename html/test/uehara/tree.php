<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

class LC_Page{
	function LC_Page() {
	}
}

$top_dir = USER_PATH;

$objPage = new LC_Page();
$objView = new SC_UserView("./templates");
$objQuery = new SC_Query();

switch($_POST['mode']) {

case 'view':
case 'download':	
case 'delete':
	// ���ɽ���ʳ��ϸ���������Υǥ��쥯�ȥ�����
	$now_dir = $_POST['select_file'];
	
case 'view':
	break;

case 'download':
	break;
	
case 'delete':
	break;
	
default :
	// ���ɽ���ϥ롼�ȥǥ��쥯�ȥ�(user_data/upload/)��ɽ��
	$now_dir = $top_dir;
	break;
}
// ���ߤΥǥ��쥯�ȥ��۲��Υե�������������
$objPage->arrFileList = lfGetFileList($now_dir);

sfprintr($objPage->arrFileList);

sfprintr($now_dir);
sfprintr($arrFileList);

$objView->assignobj($objPage);
$objView->display("tree.tpl");

//-----------------------------------------------------------------------------------------------------------------------------------

/* 
 * �ؿ�̾��lfGetFileList()
 * ������������ѥ��۲��Υǥ��쥯�ȥ����
 * ����1 ���ĥ꡼���Ǽ����
 * ����2 ����������ǥ��쥯�ȥ�ѥ�
 */
function lfGetFileList($dir) {
	$arrFileList = array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) { 
			$cnt = 0;
			while (($file = readdir($dh)) !== false) { 
				// ./ �� ../������ե�����Τߤ����
				if($file != "." && $file != "..") {
sfprintr($file);	// ������/�������
					$dir = ereg_replace("\/$", "", $dir);
					$arrFileList[$cnt]['file_name'] = $file;
					$arrFileList[$cnt]['file_path'] = $dir."/".$file;
					$arrFileList[$cnt]['file_size'] = getDirSize($dir."/".$file);
					$arrFileList[$cnt]['file_time'] = date("Y/m/d", filemtime($dir."/".$file)); 
					$cnt++;
				}
	        }
	        closedir($dh); 
	    }
	} 
	
	return $arrFileList;
}

/* 
 * �ؿ�̾��getDirSize()
 * �����������ꤷ���ǥ��쥯�ȥ�ΥХ��ȿ������
 * ����1 ���ǥ��쥯�ȥ�ѥ���Ǽ����
 */
function getDirSize($dir) { 
    $handle = opendir($dir); 
    while ($file = readdir($handle)) { 
        if ($file != '..' && $file != '.' && !is_dir($dir.'/'.$file)) { 
            $bytes += filesize($dir.'/'.$file); 
        } else if (is_dir($dir.'/'.$file) && $file != '..' && $file != '.') { 
            $bytes += getDirSize($dir.'/'.$file); 
        } 
    } 
    return $bytes; 
} 

/* 
 * �ؿ�̾��lfErrorCheck()
 * �����������顼�����å�
 */
function lfErrorCheck($array) {

	
	return $arrErr;
}
?>