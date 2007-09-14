<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

//---- �ڡ���ɽ�����饹
class LC_Page {
	
	function LC_Page() {
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView(false);
$objQuery = new SC_Query();

// �������ͤ������Ǥ��ʤ����ϥ����ڡ���TOP��
if($_GET['campaign_id'] == "" || $_GET['status'] == "") {
	header("location: ".URL_CAMPAIGN_TOP);
}

// status��Ƚ��
switch($_GET['status']) {
	case 'active':
		$status = CAMPAIGN_TEMPLATE_ACTIVE;
		break;
	case 'end':
		$status = CAMPAIGN_TEMPLATE_END;
		break;
	default:
		$status = CAMPAIGN_TEMPLATE_ACTIVE;
		break;
}

// �ǥ��쥯�ȥ�̾�����̾		
$directory_name = $objQuery->get("dtb_campaign", "directory_name", "campaign_id = ?", array($_GET['campaign_id']));
$objPage->dir_name = $directory_name;

$template_dir = CAMPAIGN_TEMPLATE_PATH . $directory_name  . "/" . $status . "preview.tpl";

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display($template_dir);