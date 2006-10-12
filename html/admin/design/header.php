<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

class LC_Page {
	var $arrForm;
	var $arrHidden;

	function LC_Page() {
		$this->tpl_mainpage = 'design/header.tpl';
		$this->tpl_subnavi 	= 'design/subnavi.tpl';
		$this->header_row = 13;
		$this->footer_row = 13;
		$this->tpl_subno = "header";
		$this->tpl_mainno = "design";
		$this->tpl_subtitle = '�إå������եå����Խ�';
		$this->tpl_onload = 'comment_start(); comment_end();';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

$division = $_POST['division'];
$pre_DIR = USER_INC_PATH . 'preview/';

// �ǡ�����������
if ($division != ''){
	// �ץ�ӥ塼�ѥƥ�ץ졼�Ȥ˽񤭹���	
	$fp = fopen($pre_DIR.$division.'.tpl',"w");
	fwrite($fp, $_POST[$division]);
	fclose($fp);

	// ��Ͽ���ϥץ�ӥ塼�ѥƥ�ץ졼�Ȥ򥳥ԡ�����
	if ($_POST['mode'] == 'confirm'){
		copy($pre_DIR.$division.".tpl", USER_INC_PATH . $division . ".tpl");
		// ��λ��å������ʥץ�ӥ塼����ɽ�����ʤ���
		$objPage->tpl_onload="alert('��Ͽ����λ���ޤ�����');";
		
		// �ƥ����ȥ��ꥢ�����򸵤��᤹(����������Τ���)
		$_POST['header_row'] = "";
		$_POST['footer_row'] = "";
	}else if ($_POST['mode'] == 'preview'){
		if ($division == "header") $objPage->header_prev = "on";
		if ($division == "footer") $objPage->footer_prev = "on";
	}

	// �إå����ե�������ɤ߹���(�ץ�ӥ塼�ǡ���)
	$header_data = file_get_contents($pre_DIR . "header.tpl");
	
	// �եå����ե�������ɤ߹���(�ץ�ӥ塼�ǡ���)
	$footer_data = file_get_contents($pre_DIR . "footer.tpl");
}else{
	// post�ǥǡ������Ϥ���ʤ���п����ɤ߹��ߤ�Ƚ�Ǥ򤷡��ץ�ӥ塼�ѥǡ����������Υǡ����Ǿ�񤭤���
	if (!is_dir($pre_DIR)) {
		mkdir($pre_DIR);
	}
	copy(USER_INC_PATH . "header.tpl", $pre_DIR . "header.tpl");
	copy(USER_INC_PATH . "footer.tpl", $pre_DIR . "footer.tpl");
	
	// �إå����ե�������ɤ߹���
	$header_data = file_get_contents(USER_INC_PATH . "header.tpl");
	// �եå����ե�������ɤ߹���
	$footer_data = file_get_contents(USER_INC_PATH . "footer.tpl");

}

// �ƥ����ȥ��ꥢ��ɽ��
$objPage->header_data = $header_data;
$objPage->footer_data = $footer_data;

if ($_POST['header_row'] != ''){
	$objPage->header_row = $_POST['header_row'];
}

if ($_POST['footer_row'] != ''){
	$objPage->footer_row = $_POST['footer_row'];
}

// �֥饦��������
$objPage->browser_type = $_POST['browser_type'];

// ���̤�ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
