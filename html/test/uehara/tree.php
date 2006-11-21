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
} else {
	// ���ɽ���ϥ롼�ȥǥ��쥯�ȥ�(user_data/upload/)��ɽ��
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
			$now_dir = $_POST['select_file'];
		} else {
			// javascript������ɽ��(�ƥ�ץ졼��¦���Ϥ�)
			$file_url = ereg_replace(USER_PATH, "", $_POST['select_file']);
			$objPage->tpl_javascript = "win02('./file_view.php?file=". $file_url ."', 'user_data', '600', '400');";
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
			$arrErr['download'] = "�� �ǥ��쥯�ȥ���������ɤ��뤳�ȤϽ���ޤ���";
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
	$create_dir = ereg_replace("/$", "", $now_dir);
	// �ե��������
	if(!sfCreateFile($create_dir."/".$_POST['create_file'], 0755)) {
		// �������顼
		$arrErr['create'] = "�� ".$_POST['create_file']."�κ����˼��Ԥ��ޤ�����";
	}
	break;
// �ե����륢�åץ���
case 'upload':
	// ������¸����
	$ret = $objUpFile->makeTempFile('upload_file', false);
	if($ret != "") $arrErr['upload_file'] = $ret;
	break;
// ���ɽ��
default :
	break;
}


// ���ߤΥǥ��쥯�ȥ��۲��Υե�������������
$objPage->arrFileList = sfGetFileList($now_dir);
$objPage->tpl_now_file = $now_dir;
$objPage->arrErr = $arrErr;
$objPage->arrParam = $_POST;

$objView->assignobj($objPage);
$objView->display("tree.tpl");

//-----------------------------------------------------------------------------------------------------------------------------------

/* 
 * �ؿ�̾��lfErrorCheck()
 * �����������顼�����å�
 */
function lfErrorCheck() {

	if($_POST['select_file'] == '') {
		$arrErr['select_file'] = "�� �ե����뤬���򤵤�Ƥ��ޤ���";
	}
	return $arrErr;
}

/* 
 * �ؿ�̾��lfInitFile()
 * ���������ե��������ν����
 */
function lfInitFile() {
	global $objUpFile;
	$objUpFile->addFile("�ե�����", 'upload_file', array(), FILE_SIZE, true, 0, 0, false);
}
?>