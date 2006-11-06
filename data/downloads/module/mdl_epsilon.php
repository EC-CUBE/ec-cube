<?php
/**
 * 
 * @copyright	2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id$
 * @link		http://www.lockon.co.jp/
 *
 */
 
require_once("../../require.php");

define("EPSILON_ID", 4);

$arrPayment = array(
	1 => 'クレジット',
	2 => 'コンビニ'
);

$arrCredit = array(
	1 => 'VISA, MASTER',
	2 => 'JCB, AMEX'
);

$arrConvenience = array(
	11 => 'セブンイレブン'
	,21 => 'ファミリーマート'
	,31 => 'LAWSON'
	,32 => 'セイコーマート'
	,33 => 'ミニストップ'
	,34 => 'デイリーヤマザキ'
);

//ページ管理クラス
class LC_Page {
	//コンストラクタ
	function LC_Page() {
		//メインテンプレートの指定
		$this->tpl_mainpage = MODULE_PATH . 'mdl_epsilon.tpl';
		$this->tpl_subtitle = 'イプシロン決済モジュール';
		global $arrPayment;
		$this->arrPayment = $arrPayment;
		global $arrCredit;
		$this->arrCredit = $arrCredit;
		global $arrConvenience;
		$this->arrConvenience = $arrConvenience;
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

$objQuery = new SC_Query();

switch($_POST['mode']) {
case 'edit':
	// 入力エラー判定
	$objPage->arrErr = lfCheckError();
	
	if(count($objPage->arrErr) == 0) {
		
		// 汎用項目を追加
		sfAlterMemo();
		
		// 利用コンビニにチェックが入っている場合には、ハイフン区切りに編集する
		$convCnt = count($_POST["convenience"]);
		if($convCnt > 0){
			$convenience = $_POST["convenience"][0];
			for($i = 1 ; $i < $convCnt ; $i++){
				$convenience .= "-" . $_POST["convenience"][$i];
			}
		}
		
		// DEL/INSで登録する。
		$delsql = "DELETE FROM dtb_payment WHERE memo01 = ?";
		$objQuery->query($delsql, array(EPSILON_ID));
		
		foreach($_POST["payment"] as $key => $val){
			// クレジットにチェックが入っていればクレジットを登録する
			if($val == 1){
				$arrData = array(			
					"payment_method" => "クレジット(イプシロン)"
					,"rule" => "0"
					,"deliv_id" =>0
					,"rank" => "select max(rank) from dtb_payment"
					,"fix" => 3
					,"creator_id" => $objSess->member_id
					,"create_date" => "now()"
					,"update_date" => "now()"
					,"upper_rule" => 500000
					,"memo01" => EPSILON_ID
					,"memo02" => $val
					,"memo03" => $_POST["code"]
					,"memo04" => "10000-0000-00000"
				);
			}

			// コンビニにチェックが入っていればコンビニを登録する
			if($val == 2){
				$arrData = array(			
					"payment_method" => "コンビニ(イプシロン)"
					,"rule" => "0"
					,"deliv_id" =>0
					,"fix" => 3
					,"creator_id" => $objSess->member_id
					,"create_date" => "now()"
					,"update_date" => "now()"
					,"upper_rule" => 500000
					,"memo01" => EPSILON_ID
					,"memo02" => $val
					,"memo03" => $_POST["code"]
					,"memo04" => "00100-0000-00000"
					,"memo05" => $convenience
				);
			}
			
			$objQuery->insert("dtb_payment", $arrData);
			
		}
		
	
		// javascript実行
		//$objPage->tpl_onload = "window.close();";
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
	$objFormParam->addParam("契約コード", "code", INT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("利用決済", "payment", "", "", array("EXIST_CHECK"));
	$objFormParam->addParam("利用クレジット", "credit");	
	$objFormParam->addParam("利用コンビニ", "convenience");	
	return $objFormParam;
}

// エラーチェックを行う
function lfCheckError(){
	global $objFormParam;
	
	$arrErr = $objFormParam->checkError();
	
	// 利用クレジット、利用コンビニのエラーチェック
	$arrChkPay = $_POST["payment"];
	foreach($arrChkPay as $key => $val){
		// 利用クレジット
		if($val == 1 and count($_POST["credit"]) <= 0){
			$arrErr["credit"] = "利用クレジットが選択されていません。<br />";
		}
		
		// 利用コンビニ
		if($val == 2 and count($_POST["convenience"]) <= 0){
			$arrErr["convenience"] = "利用コンビニが選択されていません。<br />";
		}	}

	return $arrErr;
}

?>