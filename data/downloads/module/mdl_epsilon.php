<?php
/**
 * 
 * @copyright	2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id$
 * @link		http://www.lockon.co.jp/
 *
 */
 
require_once("../../require.php");

define("MDL_EPSILON_ID", 4);

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

// 汎用項目を追加
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
		
		// DEL/INSで登録する。
		$delsql = "DELETE FROM dtb_payment WHERE memo01 = ?";
		$objQuery->query($delsql, array(MDL_EPSILON_ID));
		
		foreach($_POST["payment"] as $key => $val){
			
			// ランクの最大値を取得する
			$max_rank = $objQuery->getone("SELECT max(rank) FROM dtb_payment");
			
			// クレジットにチェックが入っていればクレジットを登録する
			if($val == 1){
				(in_array(1, $_POST["credit"])) ? $visa = "1" : $visa = "0";
				(in_array(2, $_POST["credit"])) ? $jcb = "1" : $jcb = "0";
				
				$arrData = array(			
					"payment_method" => "クレジット(イプシロン)"
					,"rule" => "0"
					,"deliv_id" =>0
					,"rank" => $max_rank + 1
					,"fix" => 3
					,"creator_id" => $objSess->member_id
					,"create_date" => "now()"
					,"update_date" => "now()"
					,"upper_rule" => 500000
					,"memo01" => MDL_EPSILON_ID
					,"memo02" => $_POST["code"]
					,"memo03" => $_POST["url"]
					,"memo04" => $val
					,"memo05" => $visa . $jcb . "000-0000-00000"
				);
			}

			// コンビニにチェックが入っていればコンビニを登録する
			if($val == 2){
				$arrData = array(			
					"payment_method" => "コンビニ(イプシロン)"
					,"rule" => "0"
					,"deliv_id" =>0
					,"rank" => $max_rank + 1
					,"fix" => 3
					,"creator_id" => $objSess->member_id
					,"create_date" => "now()"
					,"update_date" => "now()"
					,"upper_rule" => 500000
					,"memo01" => MDL_EPSILON_ID
					,"memo02" => $_POST["code"]
					,"memo03" => $_POST["url"]
					,"memo04" => $val
					,"memo05" => "00100-0000-00000"
					,"memo06" => $convenience
				);
			}
			
			$objQuery->insert("dtb_payment", $arrData);
			
		}
	
		// javascript実行
		$objPage->tpl_onload = 'alert("登録完了しました。\n基本情報＞支払方法設定より詳細設定をしてください。"); window.close();';
	}
	break;
case 'module_del':
	// 汎用項目の存在チェック
	if(!sfColumnExists("dtb_payment", "memo01")){
		// データの削除
		$objQuery->query("DELETE FROM dtb_payment WHERE memo01 = ?", array(MDL_EPSILON_ID));
	}
	break;
default:
	// データのロード
	lfLoadData();	
	break;
}

$objPage->arrForm = $objFormParam->getFormParamList();

$objView->assignobj($objPage);					//変数をテンプレートにアサインするaaaaa
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
	global $objQuery;
	global $objFormParam;
	
	$sql = "SELECT 
				memo01, 
				memo02 as code, 
				memo03 as url, 
				memo04 as payment,
				memo05 as payment_code, 
				memo06 as convenience
			FROM dtb_payment WHERE memo01 = ?";
	$arrRet = $objQuery->getall($sql, array(MDL_EPSILON_ID));
	
	$objFormParam->setParam($arrRet[0]);
	$objFormParam->splitParamCheckBoxes("convenience");

	// 画面表示用にデータを変換
	$arrDisp = array();
	foreach($arrRet as $key => $val){
		// 利用決済を表示用に変換
		$arrDisp["payment"][$key] = $val["payment"];
		
		// クレジットの決済区分を取得
		if($val["payment"] == 1) $credit = $val["payment_code"];
	}
	$objFormParam->setParam($arrDisp);
	
	// クレジット
	if(substr($credit, 0, 1)) $arrCredit["credit"][] = 1;
	if(substr($credit, 1, 1)) $arrCredit["credit"][] = 2;
	$objFormParam->setParam($arrCredit);
	
}

?>