<?php
/**
 * 
 * @copyright	2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: mdl_epsilon.inc 7162 2006-11-18 09:53:33Z kakinaka $
 * @link		http://www.lockon.co.jp/
 *
 */
$paygent_credit_php_dir = realpath(dirname( __FILE__));
// include_pathにmdl_paygentのパスを含める
ini_set('include_path', $paygent_credit_php_dir . PATH_SEPARATOR . ini_get('include_path'));

$paygent_module_dir = "jp/co/ks/merchanttool/connectmodule/";

// モジュールファイルの存在チェック
if(!file_exists($paygent_credit_php_dir . "/" . $paygent_module_dir)) {
	sfErrorHeader(">> ペイジェント提供モジュールを設置してください。");
} else {
	// paygentモジュールの読込
	include_once($paygent_module_dir . "entity/ResponseDataFactory.php");
	include_once($paygent_module_dir . "system/PaygentB2BModule.php");
	include_once($paygent_module_dir . "exception/PaygentB2BModuleConnectException.php");
	include_once($paygent_module_dir . "exception/PaygentB2BModuleException.php");
}

// ペイジェント決済のモジュールID（dtb_moduleと合わせる）
define("MDL_PAYGENT_ID", 9);

// ペイジェントの各払込の対応番号
/*
	電文種別を表す区分
	010：ATM決済申込
	020：ｶｰﾄﾞ決済ｵｰｿﾘ
	021：ｶｰﾄﾞ決済ｵｰｿﾘｷｬﾝｾﾙ
	022：ｶｰﾄﾞ決済売上
	023：ｶｰﾄﾞ決済売上ｷｬﾝｾﾙ
	024：ｶｰﾄﾞ決済3Dｵｰｿﾘ
	030：ｺﾝﾋﾞﾆ決済(番号方式)申込
	040：ｺﾝﾋﾞﾆ決済(払込票方式)申込
	050：銀行ﾈｯﾄ決済申込
	060：銀行ネット決済ASP
	090：決済情報照会
	091：差分照会
*/
define("PAYGENT_ATM", '010');
define("PAYGENT_CREDIT", '020');
define("PAYGENT_AUTH_CANCEL", '021');
define("PAYGENT_CARD_COMMIT", '022');
define("PAYGENT_CARD_COMMIT_CANCEL", '023');
define("PAYGENT_CARD_3D", '024');
define("PAYGENT_CONVENI", '030');
define("PAYGENT_BANK", '060');
// バッチで使用する。
define("PAYGENT_REF", '091');

// 無限ループを避ける
define("PAYGENT_REF_LOOP", 1000);

$arrDispKind = array(
	PAYGENT_AUTH_CANCEL => 'オーソリキャンセル',
	PAYGENT_CARD_COMMIT => '売上',
	PAYGENT_CARD_COMMIT_CANCEL => '売上キャンセル'
);

// 受注時の初期ステータス
$arrInitStatus = array(
	PAYGENT_CREDIT => ORDER_NEW,		// クレジットは新規受付
	PAYGENT_ATM => ORDER_PAY_WAIT,		// ATM決済は入金待ち
	PAYGENT_CONVENI => ORDER_PAY_WAIT,	// コンビニは入金待ち
	PAYGENT_BANK => ORDER_PAY_WAIT		// 銀行は入金待ち
);

define ("CHARGE_MAX", 500000);
define ("SEVEN_CHARGE_MAX", 300000);

// 電文バージョン
define ("TELEGRAM_VERSION", '1.0');

// コンビニコード
define ("CODE_SEVENELEVEN", "00C001");	// セブンイレブン
define ("CODE_LOWSON", "00C002");		// ローソン
define ("CODE_MINISTOP", "00C004");		// ミニストップ
define ("CODE_FAMILYMART", "00C005");	// ファミリーマート
define ("CODE_SUNKUS", "00C006");		// サンクス
define ("CODE_CIRCLEK", "00C007");		// サークルK
define ("CODE_YAMAZAKI", "00C014");		// デイリーヤマザキ
define ("CODE_SEICOMART", "00C016");	// セイコーマート

//コンビニの種類
$arrConvenience = array(
	CODE_SEICOMART => 'セイコーマート',
	CODE_LOWSON => 'ローソン',
	CODE_MINISTOP => 'ミニストップ',
	CODE_FAMILYMART => 'ファミリーマート',
	CODE_SUNKUS => 'サンクス',
	CODE_CIRCLEK => 'サークルK',
	CODE_YAMAZAKI => 'デイリーヤマザキ',
	CODE_SEVENELEVEN => 'セブンイレブン'
);
	
// ネットバンクの種類
$arrNetBank = array(
	'D005' => 'e-payment',
	'D008' => 'ネット振込EDI',
	'D009' => 'One\'s ダイレクト',
	'D033' => 'ジャパンネット銀行',
	'D036' => 'イーバンク'	
);
	
// クレジット分割回数
$arrPaymentClass = array(
	'10' => '一括払い',
	'61-2' => '分割2回払い',
	'61-3' => '分割3回払い',
	'61-6' => '分割6回払い',
	'61-10' => '分割10回払い',
	'61-15' => '分割15回払い',
	'61-20' => '分割20回払い',
	'80' => 'リボ払い'
);

// 文字入力制限（byte）
define ("PAYGENT_BANK_STEXT_LEN", "12");	// ATM，銀行ネットの利用者名（漢字，カナ）
define ("PAYGENT_CONVENI_STEXT_LEN", "14");	// コンビニの利用者名（カナ）
define ("PAYGENT_CONVENI_MTEXT_LEN", "20");	// コンビニの利用者名（漢字）

//====================================================================================

/**************************************************************************************************************
 * 関数名	：sfGetPaygentShare
 * 処理内容	：ペイジェント情報送信の共通処理
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfGetPaygentShare($telegram_kind, $order_id, $arrParam, $payment_id = "") {
	
	/** 共通電文 **/	
	// マーチャントID
	$arrSend['merchant_id'] = $arrParam['merchant_id'];
	// 接続ID
	$arrSend['connect_id'] = $arrParam['connect_id'];
	// 接続パスワード
	$arrSend['connect_password'] = $arrParam['connect_password'];
	// 電文種別ID
	$arrSend['telegram_kind'] = $telegram_kind;
	// 電文バージョン
	$arrSend['telegram_version'] = TELEGRAM_VERSION;
	// マーチャント取引ID
	$arrSend['trading_id'] = $order_id;
	// 決済ID
	if (strlen($payment_id) > 0) $arrSend['payment_id'] = $payment_id;
	// EC-CUBEからの電文であることを示す。
	$arrSend['partner'] = 'lockon';
	
	return $arrSend;
}

/**************************************************************************************************************
 * 関数名	：sfSendPaygentCredit
 * 処理内容	：クレジット情報の送信
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfSendPaygentCredit($arrData, $arrInput, $uniqid) {
	// 接続モジュールのインスタンス取得 (コンストラクタ)と初期化
	$p = new PaygentB2BModule();
	$p->init();
	
	// クレジット用パラメータの取得
	$arrPaymentDB = sfGetPaymentDB(MDL_PAYGENT_ID, "AND memo03 = 1");
	$arrOtherParam = unserialize($arrPaymentDB[0]['other_param']);
	
	// 共通データの取得
	$arrSend = sfGetPaygentShare(PAYGENT_CREDIT, $arrData['order_id'], $arrPaymentDB[0]);
	
	/** 個別電文 **/
	// 決済金額
	$arrSend['payment_amount'] = $arrData['payment_total'];
	// カード番号
	$arrSend['card_number'] = $arrInput['card_no01'].$arrInput['card_no02'].$arrInput['card_no03'].$arrInput['card_no04'];
	// カード有効期限(MMYY)
	$arrSend['card_valid_term'] = $arrInput['card_month'].$arrInput['card_year'];
	// 支払い区分、分割回数の取得
	list($payment_class, $split_count) = split("-", $arrInput['payment_class']);
	// 支払い区分
	/*
	 * 10:1回
	 * 23:ボーナス1回
	 * 61:分割
	 * 80:リボルビング
	 */
	$arrSend['payment_class'] = $payment_class;
	// 分割回数
	$arrSend['split_count'] = $split_count;
	/** 3Dセキュア関連 **/
	if ($arrOtherParam['credit_3d'] != 1 || GC_MobileUserAgent::isMobile()) {
		// 3Dセキュア不要区分
		$arrSend['3dsecure_ryaku'] = '1';
	} else {
		// HttpAccept
		$arrSend['http_accept'] = $_SERVER['HTTP_ACCEPT'];
		// HttpUserAgent
		$arrSend['http_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		// 3Dセキュア戻りURL
		$arrSend['term_url'] = SSL_URL. "shopping/load_payment_module.php?mode=credit_3d&uniqid=". $uniqid;
	}
	
	// 電文の送付
	foreach($arrSend as $key => $val) {
		$p->reqPut($key, $val);
	}
	// 電文の送信
	$p->post();
	// 応答を処理
	$arrRet = sfPaygentResponse(PAYGENT_CREDIT, $p, $uniqid, $arrInput);
	
	return $arrRet;
}

/**************************************************************************************************************
 * 関数名	：sfSendPaygentCredit3d
 * 処理内容	：3Dセキュア情報の送信
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfSendPaygetnCredit3d($arrData, $arrInput, $uniqid) {
	// 接続モジュールのインスタンス取得 (コンストラクタ)と初期化
	$p = new PaygentB2BModule();
	$p->init();
	
	// クレジット用パラメータの取得
	$arrPaymentDB = sfGetPaymentDB(MDL_PAYGENT_ID, "AND memo03 = 1");
	$arrOtherParam = unserialize($arrPaymentDB[0]['other_param']);
	
	// 共通データの取得
	$arrSend = sfGetPaygentShare(PAYGENT_CARD_3D, $arrData['order_id'], $arrPaymentDB[0], $arrData['memo06']);
	
	/** 個別電文 **/
	// ACS応答
	$arrSend['PaRes'] = $arrInput['PaRes'];
	// マーチャントデータ
	$arrSend['MD'] = $arrInput['MD'];
	
	// 電文の送付
	foreach($arrSend as $key => $val) {
		$p->reqPut($key, $val);
	}
	// 電文の送信
	$p->post();
	// 応答を処理
	$arrRet = sfPaygentResponse(PAYGENT_CREDIT, $p, $uniqid, $arrInput);
	
	return $arrRet;
}

/**************************************************************************************************************
 * 関数名	：sfSendPaygentConveni
 * 処理内容	：コンビニ情報の送信
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfSendPaygentConveni($arrData, $arrInput, $uniqid) {
// 接続モジュールのインスタンス取得 (コンストラクタ)と初期化
	$p = new PaygentB2BModule();
	$p->init();
	
	// コンビニ用パラメータの取得
	$arrPaymentDB = sfGetPaymentDB(MDL_PAYGENT_ID, "AND memo03 = 2");
	$arrOtherParam = unserialize($arrPaymentDB[0]['other_param']);
	
	// 共通データの取得
	$arrSend = sfGetPaygentShare(PAYGENT_CONVENI, $arrData['order_id'], $arrPaymentDB[0]);
	
	/** 個別電文 **/
	// 決済金額
	$arrSend['payment_amount'] = $arrData['payment_total'];
	// 利用者姓
	$arrSend['customer_family_name'] = $arrInput['customer_family_name'];
	// 利用者名
	$arrSend['customer_name'] = $arrInput['customer_name'];	
	// 利用者姓半角カナ
	$arrSend['customer_family_name_kana'] = mb_convert_kana($arrInput['customer_family_name_kana'],'k');
	$arrSend['customer_family_name_kana'] = preg_replace("/ﾞ|ﾟ/", "", $arrSend['customer_family_name_kana']);
	// 利用者名半角カナ
	$arrSend['customer_name_kana'] = mb_convert_kana($arrInput['customer_name_kana'],'k');
	$arrSend['customer_name_kana'] = preg_replace("/ﾞ|ﾟ/", "", $arrSend['customer_name_kana']);
	// 利用者電話番号
	$arrSend['customer_tel'] = $arrInput['customer_tel'];
	// 支払期限日
	$arrSend['payment_limit_date'] = $arrOtherParam['payment_limit_date'];
	// コンビニ企業コード
	$arrSend['cvs_company_id'] = $arrInput['cvs_company_id'];
	// 支払種別
	$arrSend['sales_type'] = '1';
	
	// 電文の送付
	foreach($arrSend as $key => $val) {
		// Shift-JISにエンコードする必要あり
		$enc_val = mb_convert_encoding($val, "Shift-JIS", CHAR_CODE);
		$p->reqPut($key, $enc_val);
	}
	// 電文の送信
	$p->post();
	// 応答を処理
	$arrRet = sfPaygentResponse(PAYGENT_CONVENI, $p, $uniqid, $arrInput);
		
	return $arrRet;
}

/**************************************************************************************************************
 * 関数名	：sfSendPaygentATM
 * 処理内容	：ATM決済情報の送信
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfSendPaygentATM($arrData, $arrInput, $uniqid) {
// 接続モジュールのインスタンス取得 (コンストラクタ)と初期化
	$p = new PaygentB2BModule();
	$p->init();
	
	// ATM決済用パラメータの取得
	$arrPaymentDB = sfGetPaymentDB(MDL_PAYGENT_ID, "AND memo03 = 3");
	$arrOtherParam = unserialize($arrPaymentDB[0]['other_param']);
	
	// 共通データの取得
	$arrSend = sfGetPaygentShare(PAYGENT_ATM, $arrData['order_id'], $arrPaymentDB[0]);
	
	/** 個別電文 **/
	// 決済金額
	$arrSend['payment_amount'] = $arrData['payment_total'];
	// 利用者姓
	$arrSend['customer_family_name'] = $arrInput['customer_family_name'];
	// 利用者名
	$arrSend['customer_name'] = $arrInput['customer_name'];	
	// 利用者姓半角カナ
	$arrSend['customer_family_name_kana'] = mb_convert_kana($arrInput['customer_family_name_kana'],'k');
	$arrSend['customer_family_name_kana'] = preg_replace("/ｰ/", "-", $arrSend['customer_family_name_kana']);
	$arrSend['customer_family_name_kana'] = preg_replace("/ﾞ|ﾟ/", "", $arrSend['customer_family_name_kana']);
	// 利用者名半角カナ
	$arrSend['customer_name_kana'] = mb_convert_kana($arrInput['customer_name_kana'],'k');
	$arrSend['customer_name_kana'] = preg_replace("/ｰ/", "-", $arrSend['customer_name_kana']);
	$arrSend['customer_name_kana'] = preg_replace("/ﾞ|ﾟ/", "", $arrSend['customer_name_kana']);
	// 決済内容
	$arrSend['payment_detail'] = $arrOtherParam['payment_detail'];
	// 決済内容半角カナ
	$arrSend['payment_detail_kana'] = mb_convert_kana($arrOtherParam['payment_detail'],'k');
	$arrSend['payment_detail_kana'] = preg_replace("/ｰ/", "-", $arrSend['payment_detail_kana']);
	// 支払期限日
	$arrSend['payment_limit_date'] = $arrOtherParam['payment_limit_date'];
	
	// 電文の送付
	foreach($arrSend as $key => $val) {
		// Shift-JISにエンコードする必要あり
		$enc_val = mb_convert_encoding($val, "Shift-JIS", CHAR_CODE);		
		$p->reqPut($key, $enc_val);
	}
	// 電文の送信
	$p->post();
	// 応答を処理
	$arrRet = sfPaygentResponse(PAYGENT_ATM, $p, $uniqid, $arrInput);
	
	return $arrRet;
}

/**************************************************************************************************************
 * 関数名	：sfSendPaygentBANK
 * 処理内容	：銀行NET決済情報の送信
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfSendPaygentBANK($arrData, $arrInput, $uniqid) {
// 接続モジュールのインスタンス取得 (コンストラクタ)と初期化
	$p = new PaygentB2BModule();
	$p->init();
	
	// 銀行NET用パラメータの取得
	$arrPaymentDB = sfGetPaymentDB(MDL_PAYGENT_ID, "AND memo03 = 4");
	$arrOtherParam = unserialize($arrPaymentDB[0]['other_param']);
	
	// 共通データの取得
	$arrSend = sfGetPaygentShare(PAYGENT_BANK, $arrData['order_id'], $arrPaymentDB[0]);
	
	/** 個別電文 **/
	//$arrSend['bank_code'] = $arrInput['bank_code'];	
	// 決済金額
	$arrSend['amount'] = $arrData['payment_total'];
	// 請求内容カナ
	$arrSend['claim_kana'] = mb_convert_kana($arrOtherParam['claim_kana'],'k');
	$arrSend['claim_kana'] = preg_replace("/ｰ/", "-", $arrSend['claim_kana']);
	// 請求内容漢字
	$arrSend['claim_kanji'] = $arrOtherParam['claim_kanji'];	
	// 利用者姓
	$arrSend['customer_family_name'] = $arrInput['customer_family_name'];
	// 利用者名
	$arrSend['customer_name'] = $arrInput['customer_name'];	
	// 利用者姓半角カナ
	$arrSend['customer_family_name_kana'] = mb_convert_kana($arrInput['customer_family_name_kana'],'k');
	$arrSend['customer_family_name_kana'] = preg_replace("/ｰ/", "-", $arrSend['customer_family_name_kana']);
	$arrSend['customer_family_name_kana'] = preg_replace("/ﾞ|ﾟ/", "", $arrSend['customer_family_name_kana']);
	// 利用者名半角カナ
	$arrSend['customer_name_kana'] = mb_convert_kana($arrInput['customer_name_kana'],'k');
	$arrSend['customer_name_kana'] = preg_replace("/ｰ/", "-", $arrSend['customer_name_kana']);
	$arrSend['customer_name_kana'] = preg_replace("/ﾞ|ﾟ/", "", $arrSend['customer_name_kana']);
	// PC-Mobile区分
	/*
	 * 0:PC
	 * 1:docomo	
	 * 2:au
	 * 3:softbank
	 */
	$arrSend['pc_mobile_type'] = '0';
	// 店舗名
	$arrSend['merchant_name'] = $arrOtherParam['claim_kanji'];
	// 完了後の戻りＵＲＬ 
	$arrSend['return_url'] = SITE_URL. "index.php";
	// 戻りボタンＵＲＬ 
	$arrSend['stop_return_url'] = SSL_URL. "shopping/load_payment_module.php";
	// コピーライト	
	$arrSend['copy_right'] = $arrOtherParam['copy_right'];
	// 自由メモ欄
	$arrSend['free_memo'] = $arrOtherParam['free_memo'];
	// 支払期間(0DDhhmm)
	$arrSend['asp_payment_term'] = sprintf("0%02d0000", $arrOtherParam['asp_payment_term']);
	
	// 電文の送付
	foreach($arrSend as $key => $val) {
		// Shift-JISにエンコードする必要あり
		$enc_val = mb_convert_encoding($val, "Shift-JIS", CHAR_CODE);
		$p->reqPut($key, $enc_val);
	}
	// 電文の送信
	$p->post();
	// 応答を処理
	$arrRet = sfPaygentResponse(PAYGENT_BANK, $p, $uniqid, $arrInput);
	
	return $arrRet;
}

/**************************************************************************************************************
 * 関数名	：sfPaygentResponse
 * 処理内容	：応答を処理する
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfPaygentResponse($telegram_kind, $objPaygent, $uniqid, $arrInput) {
	global $arrConvenience;
	global $arrInitStatus;
	
	// 処理結果取得（共通）
	$resultStatus = $objPaygent->getResultStatus(); # 処理結果 0=正常終了, 1=異常終了
	$responseCode = $objPaygent->getResponseCode(); # 異常終了時、レスポンスコードが取得できる
	$responseDetail = $objPaygent->getResponseDetail(); # 異常終了時、レスポンス詳細が取得できる

	// Shift-JISで応答があるので、エンコードする。
	$responseDetail = mb_convert_encoding($responseDetail, CHAR_CODE, "Shift-JIS");	
	
	$arrResOther['result'] = $resultStatus;
	$arrResOther['code'] = $responseCode;
	$arrResOther['detail'] = $responseDetail;
	
	// 取得した値をログに保存する。
	foreach($arrResOther as $key => $val) {
		gfPrintLog($key."->".$val);
	}
		
	// レスポンスの取得
	while($objPaygent->hasResNext()) {
		# データが存在する限り、取得
		$arrRes[] = $objPaygent->resNext(); # 要求結果取得
	}
	
	$arrRes[0]['result'] = $resultStatus; 
	
	// 取得した値をログに保存する。
	foreach($arrRes[0] as $key => $val) {
		gfPrintLog($key."->".$val);
	}
	
	// 決済毎に異なる処理
	switch($telegram_kind) {
	// クレジット決済の場合
	case PAYGENT_CREDIT:
		// 初期ステータスを設定する。
		$arrVal["status"] = $arrInitStatus[PAYGENT_CREDIT];
		// 空の配列を格納しておく
		$arrVal["memo02"] = serialize(array());		
		break;
	// コンビニ決済の場合
	case PAYGENT_CONVENI:
		// タイトルを設定する
		$arrMemo['title'] = sfSetConvMSG("コンビニお支払", true);
		// 決済ベンダ受付番号（コンビニによって名称が異なる）
		switch ($arrInput['cvs_company_id']) {
		// セブンイレブン
		case CODE_SEVENELEVEN:
			$receipt_num_name = "払込票番号";
			break;
		// ローソン，ファミリーマート
		case CODE_LOWSON:
		case CODE_FAMILYMART:
			$receipt_num_name = "お客様番号";
			break;
		// ミニストップ，サンクス，サークルK，デイリーヤマザキ
		case CODE_MINISTOP:
		case CODE_SUNKUS:
		case CODE_CIRCLEK:
		case CODE_YAMAZAKI:
			$receipt_num_name = "ケータイ／オンライン決済番号";
			break;
		// セイコーマート
		case CODE_SEICOMART:
			$receipt_num_name = "お客様の受付番号";
			break;
		default:
			break;
		}
		$arrMemo['receipt_number'] = sfSetConvMSG($receipt_num_name, $arrRes[0]['receipt_number']);
		// 払込票URL(PC) ： セブンイレブンの場合のみ払込票URLが有効なので表示する。
		if($arrInput['cvs_company_id'] == CODE_SEVENELEVEN && !GC_MobileUserAgent::isMobile()) {
			$arrMemo['receipt_print_url'] = sfSetConvMSG("払込票URL", $arrRes[0]['receipt_print_url']);
		}
		// 確認番号 ： ローソン，ファミリーマートのみ確認番号が必要なので表示する。
		if ($arrInput['cvs_company_id'] == CODE_LOWSON || $arrInput['cvs_company_id'] == CODE_FAMILYMART) {
			$arrMemo['confirm_number'] = sfSetConvMSG("確認番号", "400008");
		}
		// 対象コンビニ
		$arrCVS = split("-", $arrRes[0]['usable_cvs_company_id']);
		foreach($arrCVS as $val) {
			if($cvsLine != "") {
				$cvsLine.= ",".$arrConvenience[$val];
			} else {
				$cvsLine = $arrConvenience[$val];
			}
		}
		$arrMemo['usable_cvs_company_id'] = sfSetConvMSG("お支払可能なコンビニ", $cvsLine);
		// 支払期日
		$arrMemo['payment_limit_date'] = sfSetConvMSG("お支払期日", date("Y年m月d日", strtotime($arrRes[0]['payment_limit_date'])));
		// ヘルプ画面
		if (GC_MobileUserAgent::isMobile()) {
			$arrMemo['help_url'] = sfSetConvMSG("お支払方法の説明", "http://www.paygent.co.jp/mb/pay_help/conv.html");
		} else {
			$arrMemo['help_url'] = sfSetConvMSG("お支払方法の説明", "http://www.paygent.co.jp/service/pay_cvs.html");
		}
		// 受注テーブルに保存
		$arrVal["memo02"] = serialize($arrMemo);
		// 初期ステータスを設定する。
		$arrVal["status"] = $arrInitStatus[PAYGENT_CONVENI];		
		break;
	// ATM決済の場合
	case PAYGENT_ATM:
		// タイトルを設定する
		$arrMemo['title'] = sfSetConvMSG("ATMお支払", true);	
		$arrMemo['pay_center_number'] = sfSetConvMSG("収納機関番号", $arrRes[0]['pay_center_number']);
		$arrMemo['customer_number'] = sfSetConvMSG("お客様番号", $arrRes[0]['customer_number']);
		$arrMemo['conf_number'] = sfSetConvMSG("確認番号", $arrRes[0]['conf_number']);
		// 支払期日
		$arrMemo['payment_limit_date'] = sfSetConvMSG("お支払期日", date("Y年m月d日", strtotime($arrRes[0]['payment_limit_date'])));
		// ヘルプ画面
		if (GC_MobileUserAgent::isMobile()) {
			$arrMemo['help_url'] = sfSetConvMSG("お支払方法の説明", "http://www.paygent.co.jp/mb/pay_help/atm.html");
		} else {
			$arrMemo['help_url'] = sfSetConvMSG("お支払方法の説明", "http://www.paygent.co.jp/service/pay_atm.html");
		}
		// 受注テーブルに保存
		$arrVal["memo02"] = serialize($arrMemo);
		// 初期ステータスを設定する。
		$arrVal["status"] = $arrInitStatus[PAYGENT_ATM];		
		break;
	// 銀行ネットの場合
	case PAYGENT_BANK:
		// 初期ステータスを設定する。
		$arrVal["status"] = $arrInitStatus[PAYGENT_BANK];
		// 空の配列を格納しておく
		$arrVal["memo02"] = serialize(array());
		break;
	default:
		break;
	}
	
	// 受注テーブルに記録する
	$arrVal["memo01"] = MDL_PAYGENT_ID;		// 処理結果
		
	// memo02は、支払情報を格納する。
	$arrVal["memo03"] = $resultStatus;		// 処理結果
	$arrVal["memo04"] = $responseCode;		// レスポンスコード
	$arrVal["memo05"] = $responseDetail;	// エラーメッセージ
	$arrVal["memo06"] = $arrRes[0]['payment_id'];		// 承認番号
	$arrVal["memo07"] = "";					// ステータス取得で使用
	$arrVal["memo08"] = $telegram_kind;
	$arrVal["memo09"] = "";					// カード決済連携で使用
	$arrVal["memo10"] = "";					// 再取得用のnotice_idを保存しておく
	
	// 受注一時テーブルに更新
	sfRegistTempOrder($uniqid, $arrVal);
	
	// 承認番号とメッセージを返す
	return $arrRes[0];
}

/**************************************************************************************************************
 * 関数名	：sfSetConvMSG
 * 処理内容	：コンビニ情報表示用
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfSetConvMSG($name, $value){
	return array("name" => $name, "value" => $value);
}

/**************************************************************************************************************
 * 関数名	：sfPaygentDisp
 * 処理内容	：共通の表示処理
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfPaygentDisp($objPage, $payment_id) {
	$objQuery = new SC_Query();
	// 支払い方法の説明画像を取得する。
	$arrRet = $objQuery->select("payment_method, payment_image", "dtb_payment", "payment_id = ?", array($payment_id));
	$objPage->tpl_payment_method = $arrRet[0]['payment_method'];
	$objPage->tpl_payment_image = $arrRet[0]['payment_image'];
	return $objPage;
}

/**************************************************************************************************************
 * 関数名	：sfGetPaymentDB
 * 処理内容	：必要なデータを取得する。
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfGetPaymentDB($module_id, $where = "", $arrWhereVal = array()){
	$objQuery = new SC_Query();
		
	$arrVal = array($module_id);
	$arrVal = array_merge($arrVal, $arrWhereVal);
	
	$arrRet = array();
	$sql = "SELECT 
				module_id, 
				memo01 as merchant_id, 
				memo02 as connect_id, 
				memo03 as payment,
				memo04 as connect_password, 
				memo05 as other_param
			FROM dtb_payment WHERE module_id = ? " . $where;
	$arrRet = $objQuery->getall($sql, $arrVal);

	return $arrRet;
}

/**************************************************************************************************************
 * 関数名	：sfPaygentBatch
 * 処理内容	：入金ステータス確認バッチ
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfPaygentBatch() {
	$objQuery = new SC_Query();
	
	// 接続モジュールのインスタンス取得 (コンストラクタ)と初期化
	$objPaygent = new PaygentB2BModule();
	$objPaygent->init();
	
	//　設定パラメータの取得
	$arrPaymentDB = sfGetPaymentDB(MDL_PAYGENT_ID);
	
	$where = "memo01 = ? AND status = ?";
	$arrOrder = $objQuery->select("order_id", "dtb_order", $where, array(MDL_PAYGENT_ID, ORDER_PAY_WAIT));
	
	// 共通データの取得
	$arrSend = sfGetPaygentShare(PAYGENT_REF, 0, $arrPaymentDB[0]);
	
	if(count($arrOrder) > 0) {
		sfUpdatePaymentStatus($objPaygent, $arrSend);
	}	
}

/**************************************************************************************************************
 * 関数名	：sfPaygentTest
 * 処理内容	：接続テスト
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfPaygentTest($arrParam) {
	$objQuery = new SC_Query();
	
	// 接続モジュールのインスタンス取得 (コンストラクタ)と初期化
	$objPaygent = new PaygentB2BModule();
	$objPaygent->init();
		
	// 共通データの取得
	$arrSend = sfGetPaygentShare(PAYGENT_REF, '0', $arrParam);

	// 電文の送付
	foreach($arrSend as $key => $val) {
		$objPaygent->reqPut($key, $val);
	}
	// 電文の送信
	$objPaygent->post();
	
	// 処理結果取得（共通）
	$resultStatus = $objPaygent->getResultStatus(); # 処理結果 0=正常終了, 1=異常終了
			
	if($resultStatus == 0) {
		return true;
	} else {
		return false;
	}
}

/**************************************************************************************************************
 * 関数名	：sfUpdatePaymentStatus
 * 処理内容	：入金ステータス差分照会バッチ
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfUpdatePaymentStatus($objPaygent, $arrSend) {
	$objQuery = new SC_Query();
	
	if (DB_TYPE == "pgsql") $col = "cast(memo10 AS int4)";
	elseif (DB_TYPE == "mysql") $col = "cast(memo10 AS SIGNED)";
	$notice_id = $objQuery->max("dtb_order", $col, "memo01 = ?", array(MDL_PAYGENT_ID));
	
	$objQuery->begin();
	
	// ステータスの取得
	$i = 0;
	while($i < 1000) {
		$arrRet = sfGetPaymentStatus($objPaygent, $arrSend);
		if ($arrRet['result'] == 0) {
			// 取得成功
			if($arrRet['success_code'] == '0') {
				// 未取得決済通知IDの保持
				if($arrRet['payment_notice_id'] > $notice_id + 1) {
					for($notice_id++; $notice_id < $arrRet['payment_notice_id']; $notice_id++) {
						$arrErrId[] = $notice_id;
						gfPrintLog("決済通知ID=". $notice_id. "が欠番です。");
					}
				}
				$notice_id = $arrRet['payment_notice_id'];
				sfUpdatePaygentOrder($objQuery, $arrRet);
			// 取得完了
			} elseif($arrRet['success_code'] == '1') {
				gfPrintLog("差分の取得が終了しました。");
				break;
			// 取得失敗
			} elseif($arrRet['payment_notice_id'] == "") {
				$notice_id++;
				$arrErrId[] = $notice_id;
				gfPrintLog("決済通知ID=". $notice_id. "が欠番です。");
			}
		} else {
			gfPrintLog("差分照会が異常終了しました。");
			break;
		}
		$i++;
	}
	
	// 取得失敗ステータスの再取得
	for ($i = 0; $i < count($arrErrId); $i++) {
		$arrRet = sfGetPaymentStatus($objPaygent, $arrSend, $arrErrId[$i]);
		if ($arrRet['result'] == 0) {
			// 再取得成功
			if($arrRet['success_code'] == '0') {
				sfUpdatePaygentOrder($objQuery, $arrRet);
			// 再取得失敗
			} elseif($arrRet['payment_notice_id'] == "") {
				gfPrintLog("決済通知ID=". $arrErrId[$i]. "の再取得に失敗しました。バッチを強制終了します。");
				$err_flg = false;
				break;
			}
			// 再取得完了
			if($i == count($arrErrId) - 1) {
				gfPrintLog("差分の再取得が終了しました。");
			}
		} else {
			gfPrintLog("差分照会（リトライ）が異常終了しました。");
			$err_flg = false;
			break;
		}
	}
	
	// コミット成否判定
	if ($err_flg === false) {
		$objQuery->rollback();
	} else {
		$objQuery->commit();
	}
}

/**************************************************************************************************************
 * 関数名	：sfGetPaymentStatus
 * 処理内容	：入金ステータス差分照会（差分照会では変更のあったものだけ取得するためorder_idは不要）
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfGetPaymentStatus($objPaygent, $arrSend, $notice_id = "") {
	// 既に取得した差分を取得
	if($notice_id !== "") {
		$arrSend['payment_notice_id'] = $notice_id;
	}
	
	// 電文の送付
	foreach($arrSend as $key => $val) {
		$objPaygent->reqPut($key, $val);
	}
	// 電文の送信
	$objPaygent->post();
	
	// レスポンスの取得
	while($objPaygent->hasResNext()) {
		# データが存在する限り、取得
		$arrRes[] = $objPaygent->resNext(); # 要求結果取得
	}
	
	$arrRes[0]['result'] = $objPaygent->getResultStatus(); # 処理結果 0=正常終了, 1=異常終了
	
	foreach($arrRes[0] as $key => $val) {
		// Shift-JISで応答があるので、エンコードする。
		$arrRes[0][$key] = mb_convert_encoding($val, CHAR_CODE, "Shift-JIS");
		gfPrintLog($key."->".$arrRes[0][$key]);
	}
	
	return $arrRes[0];
}

/**************************************************************************************************************
 * 関数名	：sfUpdatePaygentOrder($objQuery, $arrRet)
 * 処理内容	：支払ステータス更新
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfUpdatePaygentOrder($objQuery, $arrRet) {
	/*
	 * 10:申込済
	 * 12:支払期限切れ
	 * 15:申込中断
	 * 40:消込済
	 * 43:速報検知済
	 * 61:速報取消済
	 * etc
	 */
	if($arrRet['trading_id'] != "") {
		// 入金済みに変更
		if($arrRet['payment_status'] == '40' || $arrRet['payment_status'] == '43') {
			$arrVal['status'] = ORDER_PRE_END;
		// キャンセルに変更
		} elseif($arrRet['payment_status'] == '12') {
			$arrVal['status'] = ORDER_CANCEL;
		}
		
		// 取得ステータスを記憶する。
		$arrVal['memo07'] = $arrRet['payment_status'];
		// 再度照会する場合に使用する。
		$arrVal['memo10'] = $arrRet['payment_notice_id'];
		$objQuery->update("dtb_order", $arrVal, "order_id = ?", array($arrRet['trading_id']));
	}
}

/**************************************************************************************************************
 * 関数名	：sfPaygentOrder($paygent_type)
 * 処理内容	：受注連携
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfPaygentOrder($paygent_type, $order_id) {
	global $arrDispKind;
	
	// 接続モジュールのインスタンス取得 (コンストラクタ)と初期化
	$objPaygent = new PaygentB2BModule();
	$objPaygent->init();
	
	// 設定パラメータの取得
	$arrPaymentDB = sfGetPaymentDB(MDL_PAYGENT_ID);
	
	// 処理分岐
	switch($paygent_type) {
	case 'auth_cancel':
		$kind = PAYGENT_AUTH_CANCEL;
		break;
	case 'card_commit':
		$kind = PAYGENT_CARD_COMMIT;
		break;
	case 'card_commit_cancel':
		$kind = PAYGENT_CARD_COMMIT_CANCEL;
		break;
	}
	
	// 決済IDの取得
	$objQuery = new SC_Query();
	$payment_id = $objQuery->get("dtb_order", "memo06", "order_id = ?", array($order_id));
	
	$arrSend = sfGetPaygentShare($kind, $order_id, $arrPaymentDB[0], $payment_id);
	
	// 電文の送付
	foreach($arrSend as $key => $val) {
		$objPaygent->reqPut($key, $val);
	}
	// 電文の送信
	$objPaygent->post();

	// レスポンスの取得
	while($objPaygent->hasResNext()) {
		# データが存在する限り、取得
		$arrRes[] = $objPaygent->resNext(); # 要求結果取得
	}

	$resultStatus = $objPaygent->getResultStatus(); # 処理結果 0=正常終了, 1=異常終了
	gfPrintLog('result'."->".$resultStatus);
	
	foreach($arrRes[0] as $key => $val) {
		// Shift-JISで応答があるので、エンコードする。
		$arrRes[0][$key] = mb_convert_encoding($val, CHAR_CODE, "Shift-JIS");
		gfPrintLog($key."->".$arrRes[0][$key]);
	}
	
	// 正常終了
	if($resultStatus == '0') {
		$objQuery = new SC_Query();
		$arrVal['memo09'] = $kind;
		$objQuery->update("dtb_order", $arrVal, "order_id = ?", array($order_id));
		$arrReturn['return'] = true;	
	} else {
		$arrReturn['return'] = false;
	}
	$arrReturn['kind'] = $kind;
	return $arrReturn;
}

/**************************************************************************************************************
 * 関数名	：sfPaygentOrderPage($objPage)
 * 処理内容	：表示用パラーメータを追加する。
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfPaygentOrderPage($objPage) {
	global $arrDispKind;
	$objPage->arrDispKind = $arrDispKind;
	return $objPage;
}
?>