<?php
/**
 * 
 * @copyright	2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id$
 * @link		http://www.lockon.co.jp/
 *
 */
 
require_once("../../require.php");
require_once(MODULE_PATH . "mdl_epsilon/mdl_epsilon.inc");

define("MDL_EPSILON_ID", 4);

$arrPayment = array(
	1 => 'クレジット',
	2 => 'コンビニ'
);

$arrCredit = array(
	1 => 'VISA, MASTER',
	2 => 'JCB, AMEX'
);

//ページ管理クラス
class LC_Page {
	//コンストラクタ
	function LC_Page() {
		//メインテンプレートの指定
		$this->tpl_mainpage = MODULE_PATH . 'mdl_epsilon/mdl_epsilon.tpl';
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

// 汎用項目を追加(必須！！)
sfAlterMemo();

switch($_POST['mode']) {
case 'edit':
	// 入力エラー判定
	$objPage->arrErr = lfCheckError();
	
	if(count($objPage->arrErr) == 0) {
		// 利用コンビニにチェックが入っている場合には、ハイフン区切りに編集する
		$convCnt = count($_POST["convenience"]);
		if($convCnt > 0){
			$convenience = $_POST["convenience"][0];
			for($i = 1 ; $i < $convCnt ; $i++){
				$convenience .= "-" . $_POST["convenience"][$i];
			}
		}
		
		// del_flgを削除にしておく
		$objQuery->query("UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ?", array(MDL_EPSILON_ID));
		
		foreach($_POST["payment"] as $key => $val){
			// ランクの最大値を取得する
			$max_rank = $objQuery->getone("SELECT max(rank) FROM dtb_payment");
			
			// クレジットにチェックが入っていればクレジットを登録する
			if($val == 1){
				(in_array(1, $_POST["credit"])) ? $visa = "1" : $visa = "0";
				(in_array(2, $_POST["credit"])) ? $jcb = "1" : $jcb = "0";
				
				$arrData = array(			
					"payment_method" => "Epsilonクレジット"
					,"rank" => $max_rank + 1
					,"fix" => 3
					,"creator_id" => $objSess->member_id
					,"create_date" => "now()"
					,"update_date" => "now()"
					,"upper_rule" => 500000
					,"module_id" => MDL_EPSILON_ID
					,"module_path" => MODULE_PATH . "mdl_epsilon/card.php"
					,"memo01" => $_POST["code"]
					,"memo02" => $_POST["url"]
					,"memo03" => $val
					,"memo04" => $visa . $jcb . "000-0000-00000"
					,"del_flg" => "0"
				);
			}

			// コンビニにチェックが入っていればコンビニを登録する
			if($val == 2){
				$arrData = array(			
					"payment_method" => "Epsilonコンビニ"
					,"rank" => $max_rank + 1
					,"fix" => 3
					,"creator_id" => $objSess->member_id
					,"create_date" => "now()"
					,"update_date" => "now()"
					,"upper_rule" => 500000
					,"module_id" => MDL_EPSILON_ID
					,"module_path" => MODULE_PATH . "mdl_epsilon/convenience.php"
					,"memo01" => $_POST["code"]
					,"memo02" => $_POST["url"]
					,"memo03" => $val
					,"memo04" => "00100-0000-00000"
					,"memo05" => $convenience
					,"del_flg" => "0"
				);
			}
			
			$arrPaymentData = lfGetPaymentDB("AND memo03 = ?", array($val));
			if(count($arrPaymentData) > 0){
				$objQuery->update("dtb_payment", $arrData, " module_id = " . MDL_EPSILON_ID);
			}else{
				$objQuery->insert("dtb_payment", $arrData);
			}
		}
	
		// javascript実行
		$objPage->tpl_onload = 'alert("登録完了しました。\n基本情報＞支払方法設定より詳細設定をしてください。"); window.close();';
	}
	break;
case 'module_del':
	// 汎用項目の存在チェック
	if(sfColumnExists("dtb_payment", "memo01")){
		// データの削除フラグをたてる
		$objQuery->query("UPDATE dtb_payment SET del_flg = 2 WHERE module_id = ?", array(MDL_EPSILON_ID));
	}
	break;
default:
	// データのロード
	lfLoadData();	
	break;
}

$objPage->arrForm = $objFormParam->getFormParamList();

$objView->assignobj($objPage);					//変数をテンプレートにアサインする
$objView->display($objPage->tpl_mainpage);		//テンプレートの出力
//-------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam($objFormParam) {
	$objFormParam->addParam("契約コード", "code", INT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("接続先URL", "url", URL_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "URL_CHECK"));
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

// 登録データを読み込む
function lfLoadData(){
	global $objFormParam;
	
	//データを取得
	$arrRet = lfGetPaymentDB();
	
	// 値をセット
	$objFormParam->setParam($arrRet[0]);

	// 画面表示用にデータを変換
	$arrDisp = array();
	foreach($arrRet as $key => $val){
		// 利用決済を表示用に変換
		$arrDisp["payment"][$key] = $val["payment"];
		
		// クレジットの決済区分を取得
		if($val["payment"] == 1) $credit = $val["payment_code"];
		
		// コンビニ
		if($val["payment"] == 2) $arrDisp["convenience"] = $val["convenience"];
	}
	$objFormParam->setParam($arrDisp);
	$objFormParam->splitParamCheckBoxes("convenience");
	
	// クレジット
	if(substr($credit, 0, 1)) $arrCredit["credit"][] = 1;
	if(substr($credit, 1, 1)) $arrCredit["credit"][] = 2;
	$objFormParam->setParam($arrCredit);
}

// DBからデータを取得する
function lfGetPaymentDB($where = "", $arrWhereVal = array()){
	global $objQuery;
	
	$arrVal = array(MDL_EPSILON_ID);
	$arrVal = array_merge($arrVal, $arrWhereVal);
	
	$arrRet = array();
	$sql = "SELECT 
				module_id, 
				memo01 as code, 
				memo02 as url, 
				memo03 as payment,
				memo04 as payment_code, 
				memo05 as convenience
			FROM dtb_payment WHERE module_id = ? " . $where;
	$arrRet = $objQuery->getall($sql, $arrVal);
		
	return $arrRet;
}

?>