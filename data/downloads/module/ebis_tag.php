<?php
/**
 * 
 * @copyright	2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id$
 * @link		http://www.lockon.co.jp/
 *
 */

require_once("../../require.php");

//ページ管理クラス
class LC_Page {
	//コンストラクタ
	function LC_Page() {
		//メインテンプレートの指定
		$this->tpl_mainpage = MODULE_PATH . 'ebis_tag.tpl';
		$this->tpl_subtitle = 'EBiSタグ埋め機能';
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
		$sqlval['sub_data'] = serialize($arrRet);
		$objQuery = SC_Query();
		$objQuery->update("dtb_module", $sqlval, "module_id = ?", array(EBIS_TAG_MID));
	}
	break;
default:
	break;
}

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);		//変数をテンプレートにアサインする
$objView->display($objPage->tpl_mainpage);		//テンプレートの出力
//-------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam($objFormParam) {
	$objFormParam->addParam("ユーザID", "user", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("パスワード", "pass", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("タグ識別ID", "cid", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	return $objFormParam;
}
?>