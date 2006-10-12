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
		$this->tpl_mainpage = 'design/css.tpl';
		$this->tpl_subnavi 	= 'design/subnavi.tpl';
		$this->area_row = 30;
		$this->tpl_subno = "css";
		$this->tpl_mainno = "design";
		$this->tpl_subtitle = 'CSS�Խ�';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

$css_path = USER_PATH . "css/contents.css";

// �ǡ�����������
if ($_POST['mode'] == 'confirm'){
	// �ץ�ӥ塼�ѥƥ�ץ졼�Ȥ˽񤭹���	
	$fp = fopen($css_path,"w");
	fwrite($fp, $_POST['css']);
	fclose($fp);
	
	$objPage->tpl_onload="alert('��Ͽ����λ���ޤ�����');";
}

// CSS�ե�������ɤ߹���
if(file_exists($css_path)){
	$css_data = file_get_contents($css_path);
}

// �ƥ����ȥ��ꥢ��ɽ��
$objPage->css_data = $css_data;

// ���̤�ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
