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

// ���ߤγ��ؤ����
if($_POST['mode'] != "") {
	$now_dir = $_POST['now_file'];
}

switch($_POST['mode']) {

case 'view':
	// ���顼�����å�
	if(!is_array(lfErrorCheck())) {
	
		// ���򤵤줿�ե����뤬�ǥ��쥯�ȥ�ʤ��ư
		if(is_dir($_POST['select_file'])) {
			$now_dir = $_POST['select_file'];
		} else {
			// javascript������ɽ��(�ƥ�ץ졼��¦���Ϥ�)
			$file_url = ereg_replace(USER_PATH, USER_URL, $_POST['select_file']);
			$objPage->tpl_javascript = "win02('". $file_url ."', 'user_data', '600', '400');";
		}
	}
	break;

case 'download':

	// ���顼�����å�
	if(!is_array(lfErrorCheck())) {
		if(is_dir($_POST['select_file'])) {
			// �ǥ��쥯�ȥ�ξ���javascript���顼
			$objPage->tpl_javascript = "alert('�����ǥ��쥯�ȥ���������ɤ��뤳�ȤϽ���ޤ���');";
		} else {
			// �ե�����ξ��ϥ�������ɤ�����
			header('Content-Disposition: attachment; filename="'. basename($_POST['select_file']) .'"');
		}
	}
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
$objPage->tpl_now_file = $now_dir;

sfprintr($now_dir);

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
					// ������/�������
					$dir = ereg_replace("\/$", "", $dir);
					$path = $dir."/".$file;
					$arrFileList[$cnt]['file_name'] = $file;
					$arrFileList[$cnt]['file_path'] = $path;
					$arrFileList[$cnt]['file_size'] = getDirSize($path);
					$arrFileList[$cnt]['file_time'] = date("Y/m/d", filemtime($path)); 
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
 * ����1 ���ե������Ǽ����
 */
function getDirSize($dir) {
	if(file_exists($dir)) {
		// �ǥ��쥯�ȥ�ξ�粼�إե���������̤����
		if (is_dir($dir)) {
		    $handle = opendir($dir); 
		    while ($file = readdir($handle)) {
				$path = $dir."/".$file;
		        if ($file != '..' && $file != '.' && !is_dir($path)) { 
		            $bytes += filesize($path); 
		        } else if (is_dir($path) && $file != '..' && $file != '.') { 
		            $bytes += getDirSize($path); 
		        } 
		    } 
		} else {
			// �ե�����ξ��
			$bytes = filesize($dir);
		}
	} else {
		// �ǥ��쥯�ȥ꤬¸�ߤ��ʤ�����0byte���֤�
		$bytes = 0;
	}
	
	if($bytes == "") $bytes = 0;
	
    return $bytes; 
} 

/* 
 * �ؿ�̾��lfErrorCheck()
 * �����������顼�����å�
 */
function lfErrorCheck() {

	if($_POST['select_file'] == '') {
		$arrErr['select_file'] = "�����ե����뤬���򤵤�Ƥ��ޤ���";
	}
	return $arrErr;
}
?>