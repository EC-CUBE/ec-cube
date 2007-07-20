<?php
/**
 * 
 * @copyright	2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id$
 * @link		http://www.lockon.co.jp/
 *
 */
require_once(MODULE_PATH . "mdl_epsilon/mdl_epsilon.inc");

$arrPayment = array(
	1 => 'クレジット',
	2 => 'コンビニ'
);

$arrCredit = array(
	1 => 'VISA, MASTER, Diners',
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
$objQuery = new SC_Query();

// コンビニ入金チェック
lfEpsilonCheck();

// 認証確認
$objSess = new SC_Session();
sfIsSuccess($objSess);

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
$objFormParam = lfInitParam($objFormParam);
// POST値の取得
$objFormParam->setParam($_POST);

// 汎用項目を追加(必須！！)
sfAlterMemo();

switch($_POST['mode']) {
case 'edit':
	// 入力エラー判定
	$objPage->arrErr = lfCheckError();

	// エラーなしの場合にはデータを更新	
	if(count($objPage->arrErr) == 0) {
		// データ更新
		lfUpdPaymentDB();
		
		// javascript実行
		$objPage->tpl_onload = 'alert("登録完了しました。\n基本情報＞支払方法設定より詳細設定をしてください。"); window.close();';
	}
	break;
case 'module_del':
	// 汎用項目の存在チェック
	if(sfColumnExists("dtb_payment", "memo01")){
		// データの削除フラグをたてる
		$objQuery->query("UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ?", array(MDL_EPSILON_ID));
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
	foreach((array)$arrChkPay as $key => $val){
		// 利用クレジット
		if($val == 1 and count($_POST["credit"]) <= 0){
			$arrErr["credit"] = "利用クレジットが選択されていません。<br />";
		}
		// 利用コンビニ
		if($val == 2 and count($_POST["convenience"]) <= 0){
			$arrErr["convenience"] = "利用コンビニが選択されていません。<br />";
		}
	}

	// ssl対応判定
	if(!extension_loaded('openssl') and ereg( "^https://", $_POST["url"] )){
		$arrErr["url"] = "このサーバーはSSLに対応していません。<br>httpで接続してください。";
	}

	// 接続チェックを行う
	if(count($arrErr) == 0) $arrErr = lfChkConnect();

	return $arrErr;
}

// 接続チェックを行う
function lfChkConnect(){
	global $objQuery;
	global $objPage;
	
	$arrRet = array();
	
	// メールアドレス取得
	$email = $objQuery->getone("SELECT email03 FROM dtb_baseinfo");

	// 契約コード	
	(in_array(1, (array)$_POST["payment"])) ? $cre = "1" : $cre = "0";
	(in_array(2, (array)$_POST["payment"])) ? $con = "1" : $con = "0";
	$st_code = $cre . "0" . $con . "00-0000-00000";
	
	// 送信データ生成
	$arrSendData = array(
		'contract_code' => $_POST["code"],		// 契約コード
		'user_id' => "connect_test",			// ユーザID
		'user_name' => "接続テスト",			// ユーザ名
		'user_mail_add' => $email,				// メールアドレス
		'st_code' => $st_code,					// 決済区分
		'process_code' => '3',					// 処理区分(固定)
		'xml' => '1',							// 応答形式(固定)
	);
	
	// データ送信
	$arrXML = sfPostPaymentData($_POST["url"], $arrSendData, false);
	if($arrXML == "") {
		$arrRet["url"] = "接続できませんでした。<br>";
		return $arrRet;	
	}
	
	// エラーがあるかチェックする
	$err_code = sfGetXMLValue($arrXML,'RESULT','ERR_CODE');
	switch ($err_code) {
		case "":
			break;
		case "607":
			$arrRet["code"] = "契約コードが違います。<br>";
			return $arrRet;
		default :
			$arrRet["service"] = sfGetXMLValue($arrXML,'RESULT','ERR_DETAIL');
			return $arrRet;
	}

	// コンビニ指定があればコンビニ分ループし、チェックを行う
	if(count($_POST["convenience"]) > 0){
		foreach($_POST["convenience"] as $key => $val){
			// 送信データ生成
			$arrSendData['conveni_code'] = $val;			// コンビニコード
			$arrSendData['user_tel'] = "0300000000";		// ダミー電話番号
			$arrSendData['user_name_kana'] = "送信テスト";	// ダミー氏名(カナ)
			$arrSendData['haraikomi_mail'] = 0;				// 払込メール(送信しない)
			
			// データ送信
			$arrXML = sfPostPaymentData($_POST["url"], $arrSendData, false);
			if($arrXML == "") {
				$arrRet["url"] = "接続できませんでした。<br>";
				return $arrRet;	
			}
			
			// エラーがあるかチェックする
			$err_code = sfGetXMLValue($arrXML,'RESULT','ERR_CODE');
			if($err_code != ""){
				$arrRet["service"] = sfGetXMLValue($arrXML,'RESULT','ERR_DETAIL');
				return $arrRet;
			}
		}
	}
	
	return $arrRet;	
}

// 登録データを読み込む
function lfLoadData(){
	global $objFormParam;
	
	//データを取得
	$arrRet = lfGetPaymentDB(" AND del_flg = '0'");
	
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


// データの更新処理
function lfUpdPaymentDB(){
	global $objQuery;
	global $objSess;
	
	// 利用コンビニにチェックが入っている場合には、ハイフン区切りに編集する
	$convCnt = count($_POST["convenience"]);
	if($convCnt > 0){
		$convenience = $_POST["convenience"][0];
		for($i = 1 ; $i < $convCnt ; $i++){
			$convenience .= "-" . $_POST["convenience"][$i];
		}
	}
		
	// del_flgを削除にしておく
	$del_sql = "UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ? ";
	$arrDel = array(MDL_EPSILON_ID);
	$objQuery->query($del_sql, $arrDel);
	
	// データ登録
	foreach($_POST["payment"] as $key => $val){
		// ランクの最大値を取得する
		$max_rank = $objQuery->getone("SELECT max(rank) FROM dtb_payment");

		// 支払方法データを取得			
		$arrPaymentData = lfGetPaymentDB("AND memo03 = ?", array($val));
		
		// クレジットにチェックが入っていればクレジットを登録する
		if($val == 1){
			(in_array(1, $_POST["credit"])) ? $visa = "1" : $visa = "0";
			(in_array(2, $_POST["credit"])) ? $jcb = "1" : $jcb = "0";
			$arrData = array(			
				"payment_method" => "Epsilonクレジット"
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
				,"charge_flg" => "2"
				,"upper_rule_max" => CHARGE_MAX
				
			);
		}
		
		// コンビニにチェックが入っていればコンビニを登録する
		if($val == 2){
			
			// セブンイレブンのみ選択した場合には利用上限を30万にする。
			if(count($_POST["convenience"]) == 1 and $_POST["convenience"][0] == 11) {
				$upper_rule_max = SEVEN_CHARGE_MAX;
				($arrPaymentData["upper_rule"] > $upper_rule_max or $arrPaymentData["upper_rule"] == "") ? $upper_rule = $upper_rule_max : $upper_rule = $arrPaymentData["upper_rule"];
			}else{
				$upper_rule_max = CHARGE_MAX;
				$upper_rule = $upper_rule_max;
			}
			
			$arrData = array(
				"payment_method" => "Epsilonコンビニ"
				,"fix" => 3
				,"creator_id" => $objSess->member_id
				,"create_date" => "now()"
				,"update_date" => "now()"
				,"upper_rule" => $upper_rule
				,"module_id" => MDL_EPSILON_ID
				,"module_path" => MODULE_PATH . "mdl_epsilon/convenience.php"
				,"memo01" => $_POST["code"]
				,"memo02" => $_POST["url"]
				,"memo03" => $val
				,"memo04" => "00100-0000-00000"
				,"memo05" => $convenience
				,"del_flg" => "0"
				,"charge_flg" => "1"
				,"upper_rule_max" => $upper_rule_max
			);
		}

		// データが存在していればUPDATE、無ければINSERT
		if(count($arrPaymentData) > 0){
			$objQuery->update("dtb_payment", $arrData, " module_id = '" . MDL_EPSILON_ID . "' AND memo03 = '" . $val ."'");
		}else{
			$arrData["rank"] = $max_rank + 1;
			$objQuery->insert("dtb_payment", $arrData);
		}
	}
}

// コンビニ入金確認処理
function lfEpsilonCheck(){
	global $objQuery;
	
	// trans_code を指定されていて且つ、入金済みの場合
	if($_POST["trans_code"] != "" and $_POST["paid"] == 1 and $_POST["order_number"] != ""){
		// ステータスを入金済みに変更する
		$sql = "UPDATE dtb_order SET status = 6, update_date = now() WHERE order_id = ? AND memo04 = ? ";
		$objQuery->query($sql, array($_POST["order_number"], $_POST["trans_code"]));
		
		// POSTの内容を全てログ保存
		$log_path = DATA_PATH . "logs/epsilon.log";
		gfPrintLog("epsilon conveni start---------------------------------------------------------", $log_path);
		foreach($_POST as $key => $val){
			gfPrintLog( "\t" . $key . " => " . $val, $log_path);
		}
		gfPrintLog("epsilon conveni end-----------------------------------------------------------", $log_path);
		
		//応答結果を表示
		echo "1";
	}
}

?>