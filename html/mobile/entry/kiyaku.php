<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'entry/kiyaku.tpl';
		$this->tpl_title="ご利用規約";
	}
}

$objPage = new LC_Page();
$objView = new SC_MobileView();
$objCustomer = new SC_Customer();

$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
$next = $offset;

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

// 規約内容の取得
$objQuery = new SC_Query();
$count = $objQuery->count("dtb_kiyaku", "del_flg <> 1");
$objQuery->setorder("rank DESC");
$objQuery->setlimitoffset(1, $offset);
$arrRet = $objQuery->select("kiyaku_title, kiyaku_text", "dtb_kiyaku", "del_flg <> 1");

if($count > $offset + 1){
	$next++;
} else {
	$next = -1;
}

$max = count($arrRet);
$objPage->tpl_kiyaku_text = "";
for ($i = 0; $i < $max; $i++) {
	$objPage->tpl_kiyaku_text.=$arrRet[$i]['kiyaku_title'] . "\n\n"; 
	$objPage->tpl_kiyaku_text.=$arrRet[$i]['kiyaku_text'] . "\n\n"; 
}

$objView->assign("offset", $next);
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//--------------------------------------------------------------------------------------------------------------------------
?>
