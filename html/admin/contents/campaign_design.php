<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");
require_once(DATA_PATH . "include/file_manager.inc");

class LC_Page {

	function LC_Page() {
		$this->tpl_mainpage = 'contents/campaign_design.tpl';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = "campaign";
		$this->tpl_mainno = 'contents';
		$this->header_row = 13;
		$this->contents_row = 13;
		$this->footer_row = 13;		
		$this->tpl_subtitle = '�����ڡ���ǥ������Խ�';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

// �����ڡ���ǡ���������Ѥ�
if($_POST['mode'] != "") {
	$arrForm = $_POST;
} else {
	$arrForm = $_GET;
}

// �������ͤ������Ǥ��ʤ����ϥ����ڡ���TOP��
if($arrForm['campaign_id'] == "" || $arrForm['status'] == "") {
	header("location: ".URL_CAMPAIGN_TOP);
}

switch($arrForm['status']) {
	case 'active':
		$status = CAMPAIGN_TEMPLATE_ACTIVE;
		$objPage->tpl_campaign_title = "�����ڡ�����ǥ������Խ�";
		break;
	case 'end':
		$status = CAMPAIGN_TEMPLATE_END;
		$objPage->tpl_campaign_title = "�����ڡ���λ�ǥ������Խ�";
		break;
	default:
		break;
}

// �ǥ��쥯�ȥ�̾�����̾		
$directory_name = $objQuery->get("dtb_campaign", "directory_name", "campaign_id = ?", array($arrForm['campaign_id']));
// �����ڡ���ƥ�ץ졼�ȳ�Ǽ�ǥ��쥯�ȥ�
$campaign_dir = CAMPAIGN_TEMPLATE_PATH . $directory_name . "/" .$status;

switch($_POST['mode']) {
case 'regist':
	// �ե�����򹹿�
	sfWriteFile($arrForm['header'], $campaign_dir."header.tpl", "w");
	sfWriteFile($arrForm['contents'], $campaign_dir."contents.tpl", "w");
	sfWriteFile($arrForm['footer'], $campaign_dir."footer.tpl", "w");
	// �����ȥե졼�����
	$site_frame  = $arrForm['header']."\n";
	$site_frame .= '<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>'."\n";
	$site_frame .= '<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>'."\n";
	$site_frame .= '<!--{include file=$tpl_mainpage}-->'."\n";
	$site_frame .= $arrForm['footer']."\n";
	sfWriteFile($site_frame, $campaign_dir."site_frame.tpl", "w");
	
	// ��λ��å������ʥץ�ӥ塼����ɽ�����ʤ���
	$objPage->tpl_onload="alert('��Ͽ����λ���ޤ�����');";
	break;
case 'preview':
	// �ץ�ӥ塼��񤭽Ф�����ǳ���
	// �ץ�ӥ塼��񤭽Ф�����ǳ���
	$preview  = $arrForm['header']."\n";
	$preview .= '<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>'."\n";
	$preview .= '<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>'."\n";
	$preview .= $arrForm['contents'] . "\n";
	$preview .= $arrForm['footer']."\n";
	sfWriteFile($preview, $campaign_dir."preview.tpl", "w");
	
	$objPage->tpl_onload = "win02('./campaign_preview.php?status=". $arrForm['status'] ."&campaign_id=". $arrForm['campaign_id'] ."', 'preview', '600', '400');";
	$objPage->header_data = $arrForm['header'];	
	$objPage->contents_data = $arrForm['contents'];	
	$objPage->footer_data = $arrForm['footer'];	
	break;
case 'return':
	// ��Ͽ�ڡ��������
	header("location: ".URL_CAMPAIGN_TOP);
	break;
default:	
	break;
}

if ($arrForm['header_row'] != ''){
	$objPage->header_row = $arrForm['header_row'];
}
if ($arrForm['contents_row'] != ''){
	$objPage->contents_row = $arrForm['contents_row'];
}
if ($arrForm['footer_row'] != ''){
	$objPage->footer_row = $arrForm['footer_row'];
}

if($_POST['mode'] != 'preview') {
	// �إå����ե�������ɤ߹���
	$objPage->header_data = file_get_contents($campaign_dir . "header.tpl");	
	// ����ƥ�ĥե�������ɤ߹���
	$objPage->contents_data = file_get_contents($campaign_dir . "contents.tpl");	
	// �եå����ե�������ɤ߹���
	$objPage->footer_data = file_get_contents($campaign_dir . "footer.tpl");
}

// �ե�������ͤ��Ǽ
$objPage->arrForm = $arrForm;

// ���̤�ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
