<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'entry/kiyaku.tpl';
		$this->tpl_title="ご利用規約";
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCustomer = new SC_Customer();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

// 規約内容の取得
$objQuery = new SC_Query();
$objQuery->setorder("rank DESC");
$arrRet = $objQuery->select("kiyaku_title, kiyaku_text", "dtb_kiyaku", "delete <> 1");

$max = count($arrRet);
$objPage->tpl_kiyaku_text = "";
for ($i = 0; $i < $max; $i++) {
	$objPage->tpl_kiyaku_text.=$arrRet[$i]['kiyaku_title'] . "\n\n"; 
	$objPage->tpl_kiyaku_text.=$arrRet[$i]['kiyaku_text'] . "\n\n"; 
}


$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//--------------------------------------------------------------------------------------------------------------------------
?>
