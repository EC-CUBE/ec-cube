<?php
/**
 * 
 * @copyright	2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: mdl_epsilon.php 1.3 2007-12-13 11:50:00Z satou $
 * @link		http://www.lockon.co.jp/
 *
 */

require_once(MODULE_PATH . "mdl_paygent/mdl_paygent.inc");

$arrPayment = array(
	1 => 'クレジット',
	2 => 'コンビニ',
	3 => 'ATM決済',
	4 => '銀行ネット'
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
		$this->tpl_mainpage = MODULE_PATH . 'mdl_paygent/mdl_paygent.tpl';
		$this->tpl_subtitle = 'ペイジェント決済モジュール';
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
		lfUpdPaymentDB(MDL_PAYGENT_ID);
		
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
    $arrSiteInfo = sf_getBasisData();
    // デフォルト値
    $arrDefault  = array(
        'conveni_limit_date' => 15,
        'atm_limit_date'     => 30,
        'payment_detail' => $arrSiteInfo['shop_kana'],
        'claim_kanji'    => $arrSiteInfo['shop_kana'],
        'claim_kana'     => $arrSiteInfo['shop_kana'],
        'asp_payment_term' => 7,
    );
	$objFormParam->addParam("マーチャントID", "merchant_id", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("接続ID", "connect_id", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("接続パスワード", "connect_password", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("支払期限日", "conveni_limit_date", 2, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrDefault['conveni_limit_date']);
	$objFormParam->addParam("支払期限日", "atm_limit_date", 2, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrDefault['atm_limit_date']);
	$objFormParam->addParam("表示店舗名(カナ)", "payment_detail", 12, "KVa", array("MAX_LENGTH_CHECK", "KANA_CHECK"), $arrDefault['payment_detail']);
	$objFormParam->addParam("支払期限日", "asp_payment_term", 2, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrDefault['asp_payment_term']);
	$objFormParam->addParam("表示店舗名(漢字)", "claim_kanji", 12, "KVa", array("MAX_LENGTH_CHECK"), $arrDefault['claim_kanji']);
	$objFormParam->addParam("表示店舗名(カナ)", "claim_kana", 12, "KVa", array("MAX_LENGTH_CHECK", "KANA_CHECK"), $arrDefault['claim_kana']);
	$objFormParam->addParam("利用決済", "payment", "", "", array("EXIST_CHECK"));
	$objFormParam->addParam("決済ページ用コピーライト(半角英数)", "copy_right", 64, "KVa", array("MAX_LENGTH_CHECK"));
	$objFormParam->addParam("決済ページ用説明文(全角)", "free_memo", 128, "KVa", array("MAX_LENGTH_CHECK"));	
	return $objFormParam;
}
	
// エラーチェックを行う
function lfCheckError(){
	global $objFormParam;
	$arrErr = $objFormParam->checkError();
		
	if($_POST['conveni_limit_date'] != "" && !($_POST['conveni_limit_date'] >= 1 &&  $_POST['conveni_limit_date'] <= 60)) {
			$arrErr['conveni_limit_date'] = "※ 支払期限日は、1〜60日までの間で設定してください。<br>";
	}
	if($_POST['atm_limit_date'] != "" && !($_POST['atm_limit_date'] >= 0 &&  $_POST['atm_limit_date'] <= 60)) {
			$arrErr['atm_limit_date'] = "※ 支払期限日は、0〜60日までの間で設定してください。<br>";
	}
    if(isset($_POST['payment_detail']) && $_POST['payment_detail'] == '') {
            $arrErr['payment_detail'] = "※ 表示店舗名(カナ)を入力してください。<br>";
    }
    if(isset($_POST['claim_kanji']) && $_POST['claim_kanji'] == '') {
            $arrErr['claim_kanji'] = "※ 表示店舗名（漢字）を入力してください。<br>";
    }
    if(isset($_POST['claim_kana']) && $_POST['claim_kana'] == '') {
            $arrErr['claim_kana'] = "※ 表示店舗名（カナ）を入力してください。<br>";
    }
	
    
    
    /** 共通電文 **/	
	// マーチャントID
	$arrParam['merchant_id'] = $objFormParam->getValue('merchant_id');
	// 接続ID
	$arrParam['connect_id'] = $objFormParam->getValue('connect_id');
	// 接続パスワード
	$arrParam['connect_password'] = $objFormParam->getValue('connect_password');

	// 接続テストを実行する。
	if(!sfPaygentTest($arrParam)) {
		$arrErr['err'] = "※ 接続試験に失敗しました。";
	}	
	
	return $arrErr;
}

// 登録データを読み込む
function lfLoadData(){
	global $objFormParam;
	
	//データを取得
	$arrRet = sfGetPaymentDB(MDL_PAYGENT_ID, "AND del_flg = '0'");
	$objFormParam->setParam($arrRet[0]);
	
	
	// 画面表示用にデータを変換
	$arrDisp = array();
	
	foreach($arrRet as $key => $val){
		// 利用決済を表示用に変換
		$arrDisp["payment"][$key] = $val["payment"];
		
		switch($val['payment']) {
		// クレジット
		case '1':
			break;
		// コンビニ
		case '2':
			$arrParam = unserialize($val['other_param']);
			$arrDisp['conveni_limit_date'] = $arrParam['payment_limit_date'];
			break;
		// ATM決済
		case '3':
			$arrParam = unserialize($val['other_param']);
			$arrDisp['payment_detail'] = $arrParam['payment_detail'];
			$arrDisp['atm_limit_date'] = $arrParam['payment_limit_date'];
			break;
		// ネットバンク
		case '4':
			$arrParam = unserialize($val['other_param']);
			$arrDisp['claim_kana'] = $arrParam['claim_kana'];
			$arrDisp['claim_kanji'] = $arrParam['claim_kanji'];
			$arrDisp['asp_payment_term'] = $arrParam['asp_payment_term'];
			$arrDisp['copy_right'] = $arrParam['copy_right'];
			$arrDisp['free_memo'] = $arrParam['free_memo'];
			break;					
		}
	}	
	
	$objFormParam->setParam($arrDisp);
}

// データの更新処理
function lfUpdPaymentDB($module_id){
	global $objQuery;
	global $objSess;
		
	// 関連する支払い方法のdel_flgを削除にしておく
	$del_sql = "UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ? ";
	$arrDel = array($module_id);
	$objQuery->query($del_sql, $arrDel);
	
	// データ登録
	foreach($_POST["payment"] as $key => $val){
		// ランクの最大値を取得する
		$max_rank = $objQuery->getone("SELECT max(rank) FROM dtb_payment");

		// 支払方法データを取得			
		$arrPaymentData = sfGetPaymentDB(MDL_PAYGENT_ID, "AND memo03 = ?", array($val));
		
		// クレジットにチェックが入っていればクレジットを登録する
		if($val == 1){
			$arrData = array(			
				"payment_method" => "PAYGENTクレジット"
				,"fix" => 3
				,"creator_id" => $objSess->member_id
				,"create_date" => "now()"
				,"update_date" => "now()"
				,"upper_rule" => 500000
				,"module_id" => $module_id
				,"module_path" => MODULE_PATH . "mdl_paygent/paygent_credit.php"
				,"memo01" => $_POST["merchant_id"]
				,"memo02" => $_POST["connect_id"]
				,"memo03" => $val
				,"memo04" => $_POST["connect_password"]
				,"memo05" => ""
				,"del_flg" => "0"
				,"charge_flg" => "2"
				,"upper_rule_max" => CHARGE_MAX
				
			);
		}
		
		// コンビニにチェックが入っていればコンビニを登録する
		if($val == 2){
			$arrParam = array();
			$arrParam['payment_limit_date'] = $_POST['conveni_limit_date'];
			
			$arrData = array(
				"payment_method" => "PAYGENTコンビニ"
				,"fix" => 3
				,"creator_id" => $objSess->member_id
				,"create_date" => "now()"
				,"update_date" => "now()"
				,"upper_rule" => $upper_rule
				,"module_id" => $module_id
				,"module_path" => MODULE_PATH . "mdl_paygent/paygent_conveni.php"
				,"memo01" => $_POST["merchant_id"]
				,"memo02" => $_POST["connect_id"]
				,"memo03" => $val
				,"memo04" => $_POST["connect_password"]
				,"memo05" => serialize($arrParam)
				,"del_flg" => "0"
				,"charge_flg" => "1"
				,"upper_rule_max" => $upper_rule_max
			);
		}
		
		// ATM決済にチェックが入っていればATM決済を登録する
		if($val == 3){
			$arrParam = array();
			$arrParam['payment_detail'] = $_POST['payment_detail'];
			$arrParam['payment_limit_date'] = $_POST['atm_limit_date'];
			
			$arrData = array(
				"payment_method" => "PAYGENTATM決済"
				,"fix" => 3
				,"creator_id" => $objSess->member_id
				,"create_date" => "now()"
				,"update_date" => "now()"
				,"upper_rule" => $upper_rule
				,"module_id" => $module_id
				,"module_path" => MODULE_PATH . "mdl_paygent/paygent_atm.php"
				,"memo01" => $_POST["merchant_id"]
				,"memo02" => $_POST["connect_id"]
				,"memo03" => $val
				,"memo04" => $_POST["connect_password"]
				,"memo05" => serialize($arrParam)
				,"del_flg" => "0"
				,"charge_flg" => "1"
				,"upper_rule_max" => $upper_rule_max
			);
		}
		
		// 銀行NETにチェックが入っていればATM決済を登録する
		if($val == 4){
			$arrParam = array();
			$arrParam['claim_kana'] = $_POST['claim_kana'];
			$arrParam['claim_kanji'] = $_POST['claim_kanji'];
			$arrParam['asp_payment_term'] = $_POST['asp_payment_term'];
			$arrParam['copy_right'] = $_POST['copy_right'];
			$arrParam['free_memo'] = $_POST['free_memo'];
			$arrData = array(
				"payment_method" => "PAYGENT銀行ネット"
				,"fix" => 3
				,"creator_id" => $objSess->member_id
				,"create_date" => "now()"
				,"update_date" => "now()"
				,"upper_rule" => $upper_rule
				,"module_id" => $module_id
				,"module_path" => MODULE_PATH . "mdl_paygent/paygent_bank.php"
				,"memo01" => $_POST["merchant_id"]
				,"memo02" => $_POST["connect_id"]
				,"memo03" => $val
				,"memo04" => $_POST["connect_password"]
				,"memo05" => serialize($arrParam)
				,"del_flg" => "0"
				,"charge_flg" => "1"
				,"upper_rule_max" => $upper_rule_max
			);
		}
		
		
		// データが存在していればUPDATE、無ければINSERT
		if(count($arrPaymentData) > 0){
			$objQuery->update("dtb_payment", $arrData, " module_id = '" . $module_id . "' AND memo03 = '" . $val ."'");
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