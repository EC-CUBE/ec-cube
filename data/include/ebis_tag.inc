<?php

/*	EBISタグ用パラメータ */
define("EBIS_CID", "aaaaaaa");

/* ページIDリスト */
$arrEbisPID = array(
);

class LC_EbisPage {
	function LC_EbisPage() {
		$this->tpl_mainpage = MODULE_PATH . "ebis_tag_text.tpl";
	}
}

// エビスタグの発行
function sfPrintEbisTag($pid = "") {
	// 「/」が重複しているものへの対応
	$php_self = ereg_replace("[/]+", "/", $_SERVER['PHP_SELF']);
	// PHPファイルの後ろに「/」がついてしまっているものへの対応
	$php_self = ereg_replace(".php[/]+$", ".php", $php_self);
	
	if(!is_array($pid) && $pid != "") {
		if(!ereg(".tpl$", $pid)) {
			// ページIDを上書きする
			$arrEbis['pid'] = $pid;
		} else {
			// テンプレートのパスが与えられている場合
			$temp_id = ereg_replace("^[/]+","",$pid);
			$temp_id = ereg_replace(".tpl$","",$temp_id);
			$temp_id = ereg_replace("[\./]","_",$temp_id);
			$arrEbis['pid'] = $temp_id;
		}
	}	
	
	// 商品一覧ページは、特殊IDを発行
	if(ereg("^/products/list-c[0-9]+.html", $_SERVER["REQUEST_URI"])) {
		$filename = basename($_SERVER["REQUEST_URI"]);
		$arrEbis['pid'] = ereg_replace(".html$", "", $filename);
	}
	
	// 商品一覧ページは、特殊IDを発行
	if(ereg("^/products/list.php\?category_id=[0-9]+", $_SERVER["REQUEST_URI"])) {
		$filename = basename($_SERVER["REQUEST_URI"]);
		$arrEbis['pid'] = ereg_replace("list.php\?category_id=", "list-c", $filename);
	}
	
	// 商品詳細ページは、特殊IDを発行
	if(ereg("^/products/detail-p[0-9]+.html", $_SERVER["REQUEST_URI"])) {
		$filename = basename($_SERVER["REQUEST_URI"]);
		$arrEbis['pid'] = ereg_replace(".html$", "", $filename);
	}
	
	// 商品詳細ページは、特殊IDを発行
	if(ereg("^/products/detail.php\?product_id=[0-9]+", $_SERVER["REQUEST_URI"])) {
		$filename = basename($_SERVER["REQUEST_URI"]);
		$arrEbis['pid'] = ereg_replace("detail.php\?product_id=", "detail-p", $filename);
	}
	
	// ID割り当てされていないページは、自動的に生成する。
	if($arrEbis['pid'] == "") {				
		$temp_id = ereg_replace("^[/]+","",$_SERVER['PHP_SELF']);
		$temp_id = ereg_replace(".php$","",$temp_id);
		$temp_id = ereg_replace("[\./]","_",$temp_id);
		$arrEbis['pid'] = $temp_id;
	}
			
	// ページIDが登録されている場合のみタグを出力する。
	if($arrEbis['pid'] != "") {
		$objSubPage = new LC_EbisPage();
		$objSubPage->arrEbis = $arrEbis;
		$objSubView = new SC_SiteView();
		$objSubView->assignobj($objSubPage);
		$objSubView->display($objSubPage->tpl_mainpage);
	}
}
?>