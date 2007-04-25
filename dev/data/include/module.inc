<?php

// タグ出力用クラス
class LC_EbisPage {
	function LC_EbisPage() {
		$this->tpl_mainpage = MODULE_PATH . "ebis_tag_text.tpl";
	}
}

// エビスタグの発行
function sfPrintEbisTag($pid = "") {
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("sub_data", "dtb_module", "module_id = ?", array(EBIS_TAG_MID));
	$arrSubData = unserialize($arrRet[0]['sub_data']);
	$arrEbis = array();
	
	if($arrSubData['cid'] != "") {
		$arrEbis['cid'] = $arrSubData['cid'];
	} else {
		return;
	}
	
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
			$temp_id = ereg_replace(HTML_PATH,"",$pid);			
			$temp_id = ereg_replace("^[/]+","",$temp_id);
			$temp_id = ereg_replace(".tpl$","",$temp_id);
			$temp_id = ereg_replace("[\./]","_",$temp_id);
			$arrEbis['pid'] = $temp_id;
		}
	}	
	
	// 商品一覧ページは、特殊IDを発行
	if(ereg("/products/list.php\?category_id=[0-9]+$", $_SERVER["REQUEST_URI"])) {
		$filename = basename($_SERVER["REQUEST_URI"]);
		$arrEbis['pid'] = ereg_replace("list.php\?category_id=", "list-c", $filename);
	}
	
	// 商品詳細ページは、特殊IDを発行
	if(ereg("/products/detail.php\?product_id=[0-9]+$", $_SERVER["REQUEST_URI"])) {
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

// コンバージョンタグの発行
function sfPrintAffTag($conv_page, $option) {
	if(is_numeric($conv_page)) {
		// sub_dataよりタグ情報を読み込む
		$objQuery = new SC_Query();
		$arrRet = $objQuery->select("sub_data", "dtb_module", "module_id = ?", array(AFF_TAG_MID));
		$arrSubData = unserialize($arrRet[0]['sub_data']);
		$aff_tag = $arrSubData[$conv_page];
		
		$array = split("\|", $option);
		
		// 特定文字の置き換え
		foreach($array as $each) {
			list($key, $value) = split("=", $each);
			$aff_tag = ereg_replace("\[\[" . $key . "\]\]", $value, $aff_tag);
		}
		print($aff_tag);		
	}
}

// dtb_paymentに汎用項目が存在していなければ追加する
function sfAlterMemo(){
	$objQuery = new SC_Query();
	
	// 汎用項目の存在チェック
	if(!sfColumnExists("dtb_payment", "memo01")){
		
		// モジュールIDを追加
		$objQuery->query("alter table dtb_payment add module_id int4;");
		
		// モジュールパスを追加
		$objQuery->query("alter table dtb_payment add module_path text;");
		
		// 汎用項目を10個追加
		for($i=1; $i<=9; $i++){
			$objQuery->query("alter table dtb_payment add memo0".$i." text;");
		}
		$objQuery->query("alter table dtb_payment add memo10 text;");
	}
}

?>