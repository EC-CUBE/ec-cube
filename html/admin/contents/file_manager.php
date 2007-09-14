<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once(DATA_PATH . "include/file_manager.inc");

//---- ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

class LC_Page{
	function LC_Page() {
		$this->tpl_mainpage = 'contents/file_manager.tpl';
		$this->tpl_mainno = 'contents';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = "file";
		$this->tpl_subtitle = '�ե��������';		
	}
}

// �롼�ȥǥ��쥯�ȥ�
$top_dir = USER_PATH;

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

// ���ߤγ��ؤ����
if($_POST['mode'] != "") {
	$now_dir = $_POST['now_file'];
} else {
	// ���ɽ���ϥ롼�ȥǥ��쥯�ȥ�(user_data/)��ɽ��
	$now_dir = $top_dir;
}

// �ե�����������饹
$objUpFile = new SC_UploadFile($now_dir, $now_dir);
// �ե��������ν����
lfInitFile();

switch($_POST['mode']) {

// �ե�����ɽ��
case 'view':
	// ���顼�����å�
	$arrErr = lfErrorCheck();
	if(!is_array($arrErr)) {
	
		// ���򤵤줿�ե����뤬�ǥ��쥯�ȥ�ʤ��ư
		if(is_dir($_POST['select_file'])) {
			///$now_dir = $_POST['select_file'];
			// �ĥ꡼�����Ѥ�javascript��������
			$arrErr['select_file'] = "�� �ǥ��쥯�ȥ��ɽ�����뤳�ȤϽ���ޤ���<br/>";
			
		} else {
			// javascript������ɽ��(�ƥ�ץ졼��¦���Ϥ�)
			$file_url = ereg_replace(USER_PATH, "", $_POST['select_file']);
			$tpl_onload = "win02('./file_view.php?file=". $file_url ."', 'user_data', '600', '400');";
		}
	}
	break;
// �ե�������������
case 'download':

	// ���顼�����å�
	$arrErr = lfErrorCheck();
	if(!is_array($arrErr)) {
		if(is_dir($_POST['select_file'])) {
			// �ǥ��쥯�ȥ�ξ���javascript���顼
			$arrErr['select_file'] = "�� �ǥ��쥯�ȥ���������ɤ��뤳�ȤϽ���ޤ���<br/>";
		} else {
			// �ե�������������
			sfDownloadFile($_POST['select_file']);
			exit;			
		}
	}
	break;
// �ե�������
case 'delete':
	// ���顼�����å�
	$arrErr = lfErrorCheck();
	if(!is_array($arrErr)) {
		sfDeleteDir($_POST['select_file']);
	}
	break;
// �ե��������
case 'create':
	// ���顼�����å�
	$arrErr = lfCreateErrorCheck();
	if(!is_array($arrErr)) {
		$create_dir = ereg_replace("/$", "", $now_dir);
		// �ե��������
		if(!sfCreateFile($create_dir."/".$_POST['create_file'], 0755)) {
			// �������顼
			$arrErr['create_file'] = "�� ".$_POST['create_file']."�κ����˼��Ԥ��ޤ�����<br/>";
		} else {
			$tpl_onload .= "alert('�ե������������ޤ�����');";
		}
	}
	break;
// �ե����륢�åץ���
case 'upload':
	// ������¸����
	$ret = $objUpFile->makeTempFile('upload_file', false);
	if($ret != "") {
		$arrErr['upload_file'] = $ret;
	} else {
		$tpl_onload .= "alert('�ե�����򥢥åץ��ɤ��ޤ�����');";
	}
	break;
// �ե������ư
case 'move':
	$now_dir = $_POST['tree_select_file'];
	break;
// ���ɽ��
default :
	break;
}
// �ȥåץǥ��쥯�ȥ꤫Ĵ��
$is_top_dir = false;
// ������/��Ȥ�
$top_dir_check = ereg_replace("/$", "", $top_dir);
$now_dir_check = ereg_replace("/$", "", $now_dir);
if($top_dir_check == $now_dir_check) $is_top_dir = true;

// ���ߤγ��ؤ���ľ�γ��ؤ����
$parent_dir = lfGetParentDir($now_dir);

// ���ߤΥǥ��쥯�ȥ��۲��Υե�������������
$objPage->arrFileList = sfGetFileList($now_dir);
$objPage->tpl_is_top_dir = $is_top_dir;
$objPage->tpl_parent_dir = $parent_dir;
$objPage->tpl_now_dir = $now_dir;
$objPage->tpl_now_file = basename($now_dir);
$objPage->arrErr = $arrErr;
$objPage->arrParam = $_POST;

// �ĥ꡼��ɽ������ div����id, �ĥ꡼�����ѿ�̾, ���ߥǥ��쥯�ȥ�, ����ĥ꡼hidden̾, �ĥ꡼����hidden̾, mode hidden̾
$objPage->tpl_onload .= "fnTreeView('tree', arrTree, '$now_dir', 'tree_select_file', 'tree_status', 'move');$tpl_onload";
// �ĥ꡼��������� javascript
$arrTree = sfGetFileTree($top_dir, $_POST['tree_status']);
$objPage->tpl_javascript .= "arrTree = new Array();\n";
foreach($arrTree as $arrVal) {
	$objPage->tpl_javascript .= "arrTree[".$arrVal['count']."] = new Array(".$arrVal['count'].", '".$arrVal['type']."', '".$arrVal['path']."', ".$arrVal['rank'].",";
	if ($arrVal['open']) {
		$objPage->tpl_javascript .= "true);\n";
	} else {
		$objPage->tpl_javascript .= "false);\n";
	}
}

// ���̤�ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

/* 
 * �ؿ�̾��lfErrorCheck()
 * �����������顼�����å�
 */
function lfErrorCheck() {
	$objErr = new SC_CheckError($_POST);
	$objErr->doFunc(array("�ե�����", "select_file"), array("SELECT_CHECK"));
	
	return $objErr->arrErr;
}

/* 
 * �ؿ�̾��lfCreateErrorCheck()
 * ���������ե���������������顼�����å�
 */
function lfCreateErrorCheck() {
	$objErr = new SC_CheckError($_POST);
	$objErr->doFunc(array("�����ե�����̾", "create_file"), array("EXIST_CHECK", "FILE_NAME_CHECK_BY_NOUPLOAD"));
	
	return $objErr->arrErr;
}

/* 
 * �ؿ�̾��lfInitFile()
 * ���������ե��������ν����
 */
function lfInitFile() {
	global $objUpFile;
	$objUpFile->addFile("�ե�����", 'upload_file', array(), FILE_SIZE, true, 0, 0, false);
}

/* 
 * �ؿ�̾��lfGetParentDir()
 * ����1 ���ǥ��쥯�ȥ�
 * ���������ƥǥ��쥯�ȥ����
 */
function lfGetParentDir($dir) {
	$dir = ereg_replace("/$", "", $dir);
	$arrDir = split('/', $dir);
	array_pop($arrDir);
	foreach($arrDir as $val) {
		$parent_dir .= "$val/";
	}
	$parent_dir = ereg_replace("/$", "", $parent_dir);
	
	return $parent_dir;
}
?>