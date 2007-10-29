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
		
	$arrForm = array(
		'pubid' => 'u7gu',
		'pw' => 'iejkngt67',
		'magid' => 'j7zb',
	);
	
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
		
	$arrForm = array(
		'pubid' => 'u7gu',
		'pw' => 'iejkngt67',
		'magid' => 'j7zb',
	);
	
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
	
	for($i = 0; $i < count($arrRet); $i++) {
		if($arrRet[$i]['email'] != "") {
			$arrForm['set_data'] = 	$arrRet[$i]['email'] . "\n";
		}
		if($arrRet[$i]['email_mobile'] != "") {
			$arrForm['set_data'] = 	$arrRet[$i]['email_mobile'] . "\n";	
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