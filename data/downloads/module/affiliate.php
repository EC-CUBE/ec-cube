<?php
/**
 * 
 * @copyright	2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: affiliate.php 8813 2006-12-04 05:24:35Z kakinaka $
 * @link		http://www.lockon.co.jp/
 *
 */

 
 
require_once("./require.php");

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

// 認証確認
$objSess = new SC_Session();
sfIsSuccess($objSess);

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
		$arrRet = $objQuery->select("sub_data", "dtb_module", "module_id = ?", array(AFF_TAG_MID));
		$arrSubData = unserialize($arrRet[0]['sub_data']);
		$arrRet = $objFormParam->getHashArray();		
		$arrSubData[$arrRet['conv_page']] = $arrRet['aff_tag'];
		$sqlval['sub_data'] = serialize($arrSubData);
		$objQuery = new SC_Query();
		$objQuery->update("dtb_module", $sqlval, "module_id = ?", array(AFF_TAG_MID));
		// javascript実行
		$objPage->tpl_onload = "window.close();";
	}
	break;
// コンバージョンページの選択
case 'select':
	if(is_numeric($_POST['conv_page'])) {
		// sub_dataよりタグ情報を読み込む
		$conv_page = $_POST['conv_page'];
		$arrRet = $objQuery->select("sub_data", "dtb_module", "module_id = ?", array(AFF_TAG_MID));
		$arrSubData = unserialize($arrRet[0]['sub_data']);
		$aff_tag = $arrSubData[$conv_page];
		$objFormParam->setValue('conv_page', $conv_page);
		$objFormParam->setValue('aff_tag', $aff_tag);		
	}
	break;
default:
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