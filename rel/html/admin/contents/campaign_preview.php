<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

//---- ページ表示クラス
class LC_Page {
	
	function LC_Page() {
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView(false);
$objQuery = new SC_Query();

// 正しく値が取得できない場合はキャンペーンTOPへ
if($_GET['campaign_id'] == "" || $_GET['status'] == "") {
	header("location: ".URL_CAMPAIGN_TOP);
}

// statusの判別
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

// ディレクトリ名を取得名		
$directory_name = $objQuery->get("dtb_campaign", "directory_name", "campaign_id = ?", array($_GET['campaign_id']));

$template_dir = CAMPAIGN_TEMPLATE_PATH . $directory_name  . "/" . $status . "preview.tpl";

//----　ページ表示
$objView->assignobj($objPage);
$objView->display($template_dir);