<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = '/css/layout/point/index.css';	// メインCSSパス
		/** 必ず指定する **/
		$this->tpl_mainpage = 'point/index.tpl';		// メインテンプレート
		$this->tpl_page_category = 'point';				
		$this->tpl_title = 'ポイント制度について';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();

$arrInfo =$objQuery->select("*","dtb_baseinfo");
$objPage->arrPoint = $arrInfo[0]['point_rate'];

if ($arrInfo[0]['welcome_point'] != 0){
	$kome = "※";
	$mes = "会員登録するだけでもれなく".$arrInfo[0]['welcome_point']."ポイント付与されます。";
	$objPage->mes = $mes;
	$objPage->kome = $kome;
}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
?>
