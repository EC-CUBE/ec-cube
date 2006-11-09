<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");
require_once(DATA_PATH . "module/Request.php");
require_once(MODULE_PATH . "mdl_epsilon/mdl_epsilon.inc");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = MODULE_PATH . "mdl_epsilon/convenience.tpl";
		/*
		 session_start時のno-cacheヘッダーを抑制することで
		 「戻る」ボタン使用時の有効期限切れ表示を抑制する。
		 private-no-expire:クライアントのキャッシュを許可する。
		*/
		session_cache_limiter('private-no-expire');		
	}
}

global $arrConvenience;
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
// POST値の取得
$objFormParam->setParam($_POST);

// ユーザユニークIDの取得と購入状態の正当性をチェック
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

// カート集計処理
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);

// 一時受注テーブルの読込
$arrData = sfGetOrderTemp($uniqid);

// カート集計を元に最終計算
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

// 代表商品情報
$arrMainProduct = $objPage->arrProductsClass[0];

// 支払い情報を取得
$arrPayment = $objQuery->getall("SELECT module_id, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10 FROM dtb_payment WHERE payment_id = ? ", array($arrData["payment_id"]));

// データ送信先CGI
$order_url = $arrPayment[0]["memo02"];

// trans_codeに値があり且つ、正常終了のときはオーダー確認を行う。
if($_GET["trans_code"] != ""){
	
	// 正常な推移であることを記録しておく
	$objSiteSess->setRegistFlag();
	
	// GETデータを保存
	//$arrVal["credit_result"] = $_GET["result"];
	$arrVal["memo01"] = PAYMENT_CONVENIENCE_ID;
	$arrVal["memo03"] = $arrPayment[0]["module_id"];
	
	// トランザクションコード
	$arrMemo["trans_code"] = array("name"=>"Epsilonトランザクションコード", "value" => $_GET["trans_code"]);
	$arrVal["memo02"] = serialize($arrMemo);

	// 受注一時テーブルに更新
	sfRegistTempOrder($uniqid, $arrVal);

	// 完了画面へ
	header("Location: " .  URL_SHOP_COMPLETE);
}

switch($_POST["mode"]){
	//戻る
	case 'return':
		// 正常に登録されたことを記録しておく
		$objSiteSess->setRegistFlag();
		// 確認ページへ移動
		header("Location: " . URL_SHOP_CONFIRM);
		exit;
		break;

	case "send":
		$arrErr = array();
		$arrErr = $objFormParam->checkError();
		$objPage->arrErr = $arrErr;
	
		if(count($arrErr) <= 0){
			// 送信データ生成
			$arrSendData = array(
				'contract_code' => $arrPayment[0]["memo01"],						// 契約コード
				'user_id' => $arrData["customer_id"],								// ユーザID
				'user_name' => $arrData["order_name01"].$arrData["order_name02"],	// ユーザ名
				'user_mail_add' => $arrData["order_email"],							// メールアドレス
				'order_number' => $arrData["order_id"],								// オーダー番号
				'item_code' => $arrMainProduct["product_code"],						// 商品コード(代表)
				'item_name' => $arrMainProduct["name"],								// 商品名(代表)
				'item_price' => $arrData["payment_total"],							// 商品価格(税込み総額)
				'st_code' => $arrPayment[0]["memo04"],								// 決済区分
				'mission_code' => '1',												// 課金区分(固定)
				'process_code' => '1',												// 処理区分(固定)
				'xml' => '1',														// 応答形式(固定)
				
				'conveni_code' => $_POST["convenience"],							// コンビニコード
				'user_tel' => $_POST["order_tel01"].$_POST["order_tel02"].$_POST["order_tel03"],	// 電話番号
				'user_name_kana' => $_POST["order_kana01"].$_POST["order_kana02"],					// 氏名(カナ)
				'haraikomi_mail' => 1,												// 払込メール(送信しない)
				
				'memo1' => ECCUBE_PAYMENT,											// 予備01
				'memo2' => ''														// 予備02
			);
			
			// データ送信
			$arrXML = sfPostPaymentData($order_url, $arrSendData);
			
			// エラーがあるかチェックする
			$err_code = sfGetXMLValue($arrXML,'RESULT','ERR_CODE');
			
			if($err_code != "") {
				$err_detail = sfGetXMLValue($arrXML,'RESULT','ERR_DETAIL');
				sfDispSiteError(FREE_ERROR_MSG, "", false, "購入処理中に以下のエラーが発生しました。<br /><br /><br />・" . $err_detail);
			} else {
				// 正常な推移であることを記録しておく
				$objSiteSess->setRegistFlag();
				
				$conveni_code = sfGetXMLValue($arrXML,'RESULT','CONVENI_CODE');	// コンビニコード
				$receipt_no   = sfGetXMLValue($arrXML,'RESULT','RECEIPT_NO');	// 払込票番号
				$payment_url = sfGetXMLValue($arrXML,'RESULT','HARAIKOMI_URL');	// 払込票URL(PC)
				$company_code = sfGetXMLValue($arrXML,'RESULT','KIGYOU_CODE');	// 企業コード
				$order_no = sfGetXMLValue($arrXML,'RESULT','ORDER_NUMBER');		// 受付番号
				$tel = $_POST["order_tel01"]."-".$_POST["order_tel02"]."-".$_POST["order_tel03"];	// 電話番号
				$payment_limit = sfGetXMLValue($arrXML,'RESULT','CONVENI_LIMIT');	// 支払期日
				
				//コンビニの種類
				switch($conveni_code) {
				//セブンイレブン
				case '11':
					$arrRet['cv_type'] = $arrConvenience[$conveni_code];			//コンビニの種類
					$arrRet['cv_payment_url'] = $payment_url;	//払込票URL(PC)
					$arrRet['cv_receipt_no'] = $receipt_no;		//払込票番号
					$arrRet['cv_message'] = "上記のページをプリントアウトされるか払込票番号をメモして、お支払い期限までに、最寄りのセブンイレブンにて代金をお支払いください。";
					break;
				//ファミリーマート
				case '21':
					$arrRet['cv_type'] = $arrConvenience[$conveni_code];			//コンビニの種類
					$arrRet['cv_company_code'] = $company_code;	//企業コード
					$arrRet['cv_order_no'] = $receipt_no;		//受付番号
					$arrRet['cv_message'] = "ファミリーマート店頭にございます Famiポート／ファミネットにて以下の「企業コード」と「注文番号」を入力し、申込券を印字後、お支払い期限までに代金をお支払い下さい。";
					break;
				//ローソン
				case '31':
					$arrRet['cv_type'] = $arrConvenience[$conveni_code];			//コンビニの種類
					$arrRet['cv_receipt_no'] = $receipt_no;		//払込票番号
					$arrRet['cv_tel'] = $tel;					//電話番号
					$arrRet['cv_message'] = "<お支払い方法>
1. ローソンの店内に設置してあるLoppiのトップ画面の中から、
  「インターネット受付」をお選びください。

2. 次画面のジャンルの中から「インターネット受付」をお選びください。

3. 画面に従って「お支払い受付番号」と、ご注文いただいた際の
  「電話番号」をご入力下さい。→Loppiより「申込券」が発券されます。 
    ※申込券の有効時間は30分間です。お早めにレジへお持ち下さい。

4. 申込券に現金またはクレジットカードを添えてレジにて代金を
   お支払い下さい。

5. 代金と引換に「領収書」をお渡しいたします。領収書は大切に保管
   してください。代金払込の証書となります。";
					break;
				//セイコーマート
				case '32':
					$arrRet['cv_type'] = $arrConvenience[$conveni_code];			//コンビニの種類
					$arrRet['cv_receipt_no'] = $receipt_no;		//払込票番号
					$arrRet['cv_tel'] = $tel;					//電話番号
					$arrRet['cv_message'] = "<お支払い方法>
1.　セイコーマートの店内に設置してあるセイコーマートクラブステーション
   （情報端末）のトップ画面の中から、「インターネット受付」をお選び下さい。

2.  画面に従って「お支払い受付番号」と、お申し込み時の「電話番号」を
　　ご入力いただくとセイコーマートクラブステーションより「決済サービス
　　払込取扱票・払込票兼受領証・領収書（計3枚）」が発券されます。

3.  発券された「決済サービス払込取扱票・払込票兼受領証・領収書（計3枚）」
　　をお持ちの上、レジにて代金をお支払い下さい。 
";
					break;
				//ミニストップ
				case '33':
					$arrRet['cv_type'] = $arrConvenience[$conveni_code];			//コンビニの種類
					$arrRet['cv_payment_url'] = $payment_url;	//払込票URL
					$arrRet['cv_message'] = "お支払い期限までにミニストップにて代金をお支払い下さい。
お支払いの際には「払込取扱票」が必要となりますので、以下URLで表示
されるページを印刷してレジまでお持ち下さい。";
					break;
				//デイリーヤマザキ
				case '34':
					$arrRet['cv_type'] = $arrConvenience[$conveni_code];			//コンビニの種類
					$arrRet['cv_payment_url'] = $payment_url;	//払込票URL
					$arrRet['cv_message'] = "お支払い期限までにデイリーヤマザキ／ヤマザキデイリーストア
にて代金をお支払い下さい。
お支払いの際には「払込取扱票」が必要となりますので、以下URLで表示
されるページを印刷してレジまでお持ち下さい。";
					break;
				}
				
				//支払期限
				$arrRet['cv_payment_limit'] = $payment_limit;
				//コンビニ決済情報を格納
				$sqlval['conveni_data'] = serialize($arrRet);
				$sqlval['memo02'] = serialize($arrRet);
	
				// 受注一時テーブルに更新
				sfRegistTempOrder($uniqid, $sqlval);
					
				header("Location: " . URL_SHOP_COMPLETE);
			}
		}
		break;
		
	default:
		$objFormParam->setParam($arrData);
		break;
}

// 利用可能コンビニ
$objFormParam->setValue("convenience", $arrPayment[0]["memo05"]);
$objFormParam->splitParamCheckBoxes("convenience");
$arrUseConv = $objFormParam->getValue("convenience");
foreach($arrUseConv as $key => $val){
	$arrConv[$val] = $arrConvenience[$val];
}
$objPage->arrConv = $arrConv;

$objPage->arrForm =$objFormParam->getHashArray();

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
//パラメータの初期化
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("コンビニの種類", "convenience", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("お名前(セイ)", "order_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("お名前(メイ)", "order_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("お電話番号1", "order_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("お電話番号2", "order_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("お電話番号3", "order_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
}
	

?>