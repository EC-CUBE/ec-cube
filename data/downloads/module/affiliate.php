<?php
/**
 * 
 * @copyright	2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id$
 * @link		http://www.lockon.co.jp/
 *
 */

require_once("../../require.php");

$arrConversionPage = array(
	1 => '商品購入完了画面',
	2 => '会員登録完了画面'
);

//ページ管理クラス
class LC_Page {
	//コンストラクタ
	function LC_Page() {
		//メインテンプレートの指定
		$this->tpl_mainpage = MODULE_PATH . 'affiliate.tpl';
		$this->tpl_subtitle = 'アフィリエイトタグ埋め込み';
		global $arrConversionPage;
		$this->arrConversionPage = $arrConversionPage;
	}
}
$objPage = new LC_Page();
$objView = new SC_AdminView();

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
$objFormParam = lfInitParam($objFormParam);
// POST値の取得
$objFormParam->setParam($_POST);

switch($_POST['mode']) {
case 'edit':
	// 入力エラー判定
	$objPage->arrErr = $objFormParam->checkError();
	if(count($objPage->arrErr) == 0) {
		$arrRet = $objFormParam->getHashArray();
		/*
		$sqlval['sub_data'] = serialize($arrRet);
		$objQuery = new SC_Query();
		$objQuery->update("dtb_module", $sqlval, "module_id = ?", array(EBIS_TAG_MID));
		// javascript実行
		//$objPage->tpl_onload = "window.close();";
		*/
	}
	break;
case 'select':
	sfPrintR($_POST);
	break;
default:
	$arrRet = $objQuery->select("sub_data", "dtb_module", "module_id = ?", array(EBIS_TAG_MID));
	$arrSubData = unserialize($arrRet[0]['sub_data']);
	$objFormParam->setParam($arrSubData);
	break;
}

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);					//変数をテンプレートにアサインする
$objView->display($objPage->tpl_mainpage);		//テンプレートの出力
//-------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam($objFormParam) {
	$objFormParam->addParam("コンバージョンページ", "conv_page", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("アフィリエイトタグ", "aff_tag", MTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));	
	return $objFormParam;
}
?>