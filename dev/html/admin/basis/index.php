<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_mainpage = 'basis/index.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_subno = 'index';
		$this->tpl_mainno = 'basis';
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrTAXRULE;
		$this->arrTAXRULE = $arrTAXRULE;
		$this->tpl_subtitle = 'SHOPマスタ';
	}
}


$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// 認証可否の判定
sfIsSuccess($objSess);

$cnt = $objQuery->count("dtb_baseinfo");

if ($cnt > 0) {
	$objPage->tpl_mode = "update";
} else {
	$objPage->tpl_mode = "insert";
}

if($_POST['mode'] != "") {
	// POSTデータの引き継ぎ
	$objPage->arrForm = $_POST;
	
	// 入力データの変換
	$objPage->arrForm = lfConvertParam($objPage->arrForm);
	// 入力データのエラーチェック
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);
	
	if(count($objPage->arrErr) == 0) {
		switch($_POST['mode']) {
		case 'update':
			lfUpdateData($objPage->arrForm);	// 既存編集
			break;
		case 'insert':
			lfInsertData($objPage->arrForm);	// 新規作成
			break;
		default:
			break;
		}
		// 再表示
		sfReload();
	}
} else {
	$arrCol = lfGetCol();
	$col	= sfGetCommaList($arrCol);
	$arrRet = $objQuery->select($col, "dtb_baseinfo");
	$objPage->arrForm = $arrRet[0];
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//--------------------------------------------------------------------------------------------------------------------------------------
// 基本情報用のカラムを取り出す。
function lfGetCol() {
	$arrCol = array(
		"company_name",
		"company_kana",
		"shop_name",
		"shop_kana",
		"zip01",
		"zip02",
		"pref",
		"addr01",
		"addr02",
		"tel01",
		"tel02",
		"tel03",
		"fax01",
		"fax02",
		"fax03",
		"business_hour",
		"email01",
		"email02",
		"email03",
		"email04",
		"tax",
		"tax_rule",
		"free_rule",
		"good_traded",
		"message"
		
	);
	return $arrCol;
}

function lfUpdateData($array) {
	$objQuery = new SC_Query();
	$arrCol = lfGetCol();
	foreach($arrCol as $val) {
		$sqlval[$val] = $array[$val];
	}
	$sqlval['update_date'] = 'Now()';
	// UPDATEの実行
	$ret = $objQuery->update("dtb_baseinfo", $sqlval);
}

function lfInsertData($array) {
	$objQuery = new SC_Query();
	$arrCol = lfGetCol();
	foreach($arrCol as $val) {
		$sqlval[$val] = $array[$val];
	}	
	$sqlval['update_date'] = 'Now()';
	// INSERTの実行
	$ret = $objQuery->insert("dtb_baseinfo", $sqlval);
}

/* 取得文字列の変換 */
function lfConvertParam($array) {
	/*
	 *	文字列の変換
	 *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
	 *	C :  「全角ひら仮名」を「全角かた仮名」に変換
	 *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します	
	 *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
	 *  a :  全角英数字を半角英数字に変換する
	 */
	// 人物基本情報
	
	// スポット商品
	$arrConvList['company_name'] = "KVa";
	$arrConvList['company_kana'] = "KVC";
	$arrConvList['shop_name'] = "KVa";
	$arrConvList['shop_kana'] = "KVC";
	$arrConvList['addr01'] = "KVa";
	$arrConvList['addr02'] = "KVa";
	$arrConvList['zip01'] = "n";
	$arrConvList['zip02'] = "n";
	$arrConvList['tel01'] = "n";
	$arrConvList['tel02'] = "n";
	$arrConvList['tel03'] = "n";
	$arrConvList['fax01'] = "n";
	$arrConvList['fax02'] = "n";
	$arrConvList['fax03'] = "n";
	$arrConvList['email01'] = "a";
	$arrConvList['email02'] = "a";
	$arrConvList['email03'] = "a";
	$arrConvList['email04'] = "a";
	$arrConvList['tax'] = "n";
	$arrConvList['free_rule'] = "n";
	$arrConvList['business_hour'] = "KVa";
	$arrConvList['good_traded'] = "";
	$arrConvList['message'] = "";
	
	// 文字変換
	foreach ($arrConvList as $key => $val) {
		// POSTされてきた値のみ変換する。
		if(isset($array[$key])) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}

// 入力エラーチェック
function lfErrorCheck($array) {
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("会社名", "company_name", STEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("会社名(カナ)", "company_kana", STEXT_LEN), array("KANA_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("店名", "shop_name", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("店名(カナ)", "shop_kana", STEXT_LEN), array("KANA_CHECK","MAX_LENGTH_CHECK"));
	// 郵便番号チェック
	$objErr->doFunc(array("郵便番号1","zip01",ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK","NUM_COUNT_CHECK"));
	$objErr->doFunc(array("郵便番号2","zip02",ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK","NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	// 住所チェック
	$objErr->doFunc(array("都道府県", "pref"), array("EXIST_CHECK"));
	$objErr->doFunc(array("住所1", "addr01", STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("住所2", "addr02", STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	// メールチェック
	$objErr->doFunc(array('商品注文受付メールアドレス', "email01", STEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('問い合わせ受付メールアドレス', "email02", STEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('メール送信元メールアドレス', "email03", STEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('送信エラー受付メールアドレス', "email04", STEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK","MAX_LENGTH_CHECK"));
	// 電話番号チェック
	$objErr->doFunc(array("TEL", "tel01", "tel02", "tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
	$objErr->doFunc(array("FAX", "fax01", "fax02", "fax03", TEL_ITEM_LEN), array("TEL_CHECK"));
	// その他
	$objErr->doFunc(array("消費税率", "tax", PERCENTAGE_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("送料無料条件", "free_rule", PRICE_LEN), array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("店舗営業時間", "business_hour", STEXT_LEN), array("MAX_LENGTH_CHECK"));

	$objErr->doFunc(array("取扱商品", "good_traded", LLTEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("メッセージ", "message", LLTEXT_LEN), array("MAX_LENGTH_CHECK"));

	return $objErr->arrErr;
}

?>