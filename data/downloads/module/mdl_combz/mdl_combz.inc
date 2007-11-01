<?php
/**
 * 
 * @copyright	2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @link		http://www.lockon.co.jp/
 *
 */
require_once(DATA_PATH . "module/Request.php");
require_once(DATA_PATH . "lib/slib.php");

define('COMBZ_ENCODE', 'Shift-JIS');

/**** ▼定数宣言 *********************************************************************************************/

define("MDL_COMBZ_ID", 13);
define("MODULE_NAME", "コンビーズ連動モジュール");
define("MODULE_ID", MDL_COMBZ_ID);

/**************************************************************************************************************
 * 関数名	：sfSetModuleDB
 * 処理内容	：必要なデータを取得する。
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfSetModuleDB($module_id, $objFormParam){
    global $objQuery;
    $arrRet = $objFormParam->getHashArray();
    foreach($arrRet as $key => $val) {
    	$arrVal[$key] = $val;
    }    
	$sqlval['sub_data'] = serialize($arrVal);	
    $objQuery->update("dtb_module", $sqlval, "module_id = ?", array($module_id));
}

/**************************************************************************************************************
 * 関数名	：sfGetModuleDB
 * 処理内容	：必要なデータを取得する。
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
// DBからデータを取得する
function sfGetModuleDB($module_id){
   	$objQuery = new SC_Query();
    $arrVal = array($module_id);
    $arrRet = array();
    $sql = "SELECT 
                sub_data
            FROM dtb_module WHERE module_id = ? " . $where;
    $arrRet = $objQuery->getall($sql, $arrVal);
    return unserialize($arrRet[0]['sub_data']);
}
/**************************************************************************************************************
 * 関数名	：sfCombzReserve
 * 処理内容	：
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfCombzReserve($where, $arrval) {
	ini_set("mbstring.http_input", COMBZ_ENCODE);
	ini_set("mbstring.http_output", COMBZ_ENCODE);
	ini_set("default_charset", COMBZ_ENCODE);
	ini_set("mbstring.internal_encoding", COMBZ_ENCODE);
	
	//データを取得
 	$arrForm = sfGetModuleDB(MODULE_ID);
	//ページ管理クラス
	class LC_ReservePage {
		//コンストラクタ
		function LC_ReservePage() {
			//メインテンプレートの指定
			$this->tpl_mainpage = MODULE_PATH . 'mdl_combz/combz_reserve.tpl';
		}
	}
	
	$objPage = new LC_ReservePage();
	$objView = new SC_AdminView();
	$objQuery = new SC_Query();
	
	$objPage->arrForm = $arrForm;
	$objView->assignobj($objPage);						//変数をテンプレートにアサインする
	$fetch = $objView->fetch($objPage->tpl_mainpage);	//テンプレートの出力
	$fetch = mb_convert_encoding($fetch, COMBZ_ENCODE);
	print($fetch);
}

/**************************************************************************************************************
 * 関数名	：sfCombzRegist
 * 処理内容	：
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfCombzRegist($where, $arrval) {
	ini_set("mbstring.http_input", COMBZ_ENCODE);
	ini_set("mbstring.http_output", COMBZ_ENCODE);
	ini_set("default_charset", COMBZ_ENCODE);
	ini_set("mbstring.internal_encoding", COMBZ_ENCODE);
		
	//データを取得
 	$arrForm = sfGetModuleDB(MODULE_ID);
	
	//ページ管理クラス
	class LC_ReservePage {
		//コンストラクタ
		function LC_ReservePage() {
			//メインテンプレートの指定
			$this->tpl_mainpage = MODULE_PATH . 'mdl_combz/combz_regist.tpl';
		}
	}
	
	$objPage = new LC_ReservePage();
	$objView = new SC_AdminView();
	
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("email, email_mobile", "dtb_customer", $where, $arrval);
	$arrForm['set_data'] = "";
	for($i = 0; $i < count($arrRet); $i++) {
		if($arrRet[$i]['email'] != "") {
			$arrForm['set_data'].= 	$arrRet[$i]['email'] . "\n";
		}
		if($arrRet[$i]['email_mobile'] != "" && $arrRet[$i]['email'] != $arrRet[$i]['email_mobile']) {
			$arrForm['set_data'].= 	$arrRet[$i]['email_mobile'] . "\n";	
		}
	}
		
	$objPage->arrForm = $arrForm;
	$objView->assignobj($objPage);						//変数をテンプレートにアサインする
	$fetch = $objView->fetch($objPage->tpl_mainpage);	//テンプレートの出力
	$fetch = mb_convert_encoding($fetch, COMBZ_ENCODE);
	print($fetch);
	
}

/**************************************************************************************************************
 * 関数名	：sfCombzPost
 * 処理内容	：
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfCombzPost($combz_type, $where, $arrval) {
	switch($combz_type) {
	case 'reserve':
		sfCombzReserve($where, $arrval);
		break;
	case 'regist':
		sfCombzRegist($where, $arrval);
		break;
	default:
		print("コンビーズ連携：不正な値です。");
		break;	
	}
	exit;
}