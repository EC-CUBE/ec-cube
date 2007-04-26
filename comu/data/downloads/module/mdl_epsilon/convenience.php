<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");
require_once(DATA_PATH . "module/Request.php");
require_once(MODULE_PATH . "mdl_epsilon/mdl_epsilon.inc");

class LC_Page {
	function LC_Page() {
		if (is_callable(GC_MobileUserAgent) && GC_MobileUserAgent::isMobile()) {
			$this->tpl_mainpage = MODULE_PATH . "mdl_epsilon/convenience_mobile.tpl";
		} else {
			$this->tpl_mainpage = MODULE_PATH . "mdl_epsilon/convenience.tpl";
		}
		$this->tpl_title = "コンビニ決済";
		/*
		 session_start時のno-cacheヘッダーを抑制することで
		 「戻る」ボタン使用時の有効期限切れ表示を抑制する。
		 private-no-expire:クライアントのキャッシュを許可する。
		*/
		session_cache_limiter('private-no-expire');		
	}
}

global $arrConvenience;
global $arrConveni_message;

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCampaignSess = new SC_CampaignSession();
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

switch($_POST["mode"]){
	//戻る
	case 'return':
		// 正常に登録されたことを記録しておく
		$objSiteSess->setRegistFlag();
		// 確認ページへ移動
		if (is_callable(GC_MobileUserAgent) && GC_MobileUserAgent::isMobile()) {
			header("Location: " . gfAddSessionId(URL_SHOP_CONFIRM));
		} else {
			header("Location: " . URL_SHOP_CONFIRM);
		}
		exit;
		break;

	case "send":
		$arrErr = array();
		$arrErr = $objFormParam->checkError();
		$objPage->arrErr = $arrErr;
		
		// 非会員のときは user_id に not_memberと送る
		($arrData["customer_id"] == 0) ? $user_id = "not_member" : $user_id = $arrData["customer_id"];
		
		if(count($arrErr) <= 0){
			// 送信データ生成
			$item_name = $arrMainProduct["name"] . "×" . $arrMainProduct["quantity"] . "個 (代表)";
			$arrSendData = array(
				'contract_code' => $arrPayment[0]["memo01"],						// 契約コード
				'user_id' => $user_id ,												// ユーザID
				'user_name' => $arrData["order_name01"].$arrData["order_name02"],	// ユーザ名
				'user_mail_add' => $arrData["order_email"],							// メールアドレス
				'order_number' => $arrData["order_id"],								// オーダー番号
				'item_code' => $arrMainProduct["product_code"],						// 商品コード(代表)
				'item_name' => $item_name,											// 商品名(代表)
				'item_price' => $arrData["payment_total"],							// 商品価格(税込み総額)
				'st_code' => $arrPayment[0]["memo04"],								// 決済区分
				'mission_code' => '1',												// 課金区分(固定)
				'process_code' => '1',												// 処理区分(固定)
				'xml' => '1',														// 応答形式(固定)
				'conveni_code' => $_POST["convenience"],							// コンビニコード
				'user_tel' => $_POST["order_tel01"].$_POST["order_tel02"].$_POST["order_tel03"],	// 電話番号
				'user_name_kana' => $_POST["order_kana01"].$_POST["order_kana02"],					// 氏名(カナ)
				'haraikomi_mail' => 0,												// 払込メール(送信しない)
				'memo1' => "",														// 予備01
				'memo2' => ECCUBE_PAYMENT . "_" . date("YmdHis"),					// 予備02
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
				$conveni_type = lfSetConvMSG("コンビニの種類",$arrConvenience[$conveni_code]);	// コンビニの種類
				$receipt_no   = lfSetConvMSG("払込票番号",sfGetXMLValue($arrXML,'RESULT','RECEIPT_NO'));	// 払込票番号
				$payment_url = lfSetConvMSG("払込票URL",sfGetXMLValue($arrXML,'RESULT','HARAIKOMI_URL'));	// 払込票URL
				$company_code = lfSetConvMSG("企業コード",sfGetXMLValue($arrXML,'RESULT','KIGYOU_CODE'));	// 企業コード
				$order_no = lfSetConvMSG("受付番号",sfGetXMLValue($arrXML,'RESULT','ORDER_NUMBER'));		// 受付番号
				$tel = lfSetConvMSG("電話番号",$_POST["order_tel01"]."-".$_POST["order_tel02"]."-".$_POST["order_tel03"]);	// 電話番号
				$payment_limit = lfSetConvMSG("支払期日",sfGetXMLValue($arrXML,'RESULT','CONVENI_LIMIT'));	// 支払期日
				$trans_code =  sfGetXMLValue($arrXML,'RESULT','TRANS_CODE');	// トランザクションコード
				
				//コンビニの種類
				switch($conveni_code) {
				//セブンイレブン
				case '11':
					$arrRet['cv_type'] = $conveni_type;			//コンビニの種類
					$arrRet['cv_payment_url'] = $payment_url;	//払込票URL(PC)
					$arrRet['cv_receipt_no'] = $receipt_no;		//払込票番号
					$arrRet['br1'] = lfSetConvMSG("","\n\n");
					$arrRet['cv_message'] = lfSetConvMSG("",$arrConveni_message[$conveni_code]);
					break;
				//ファミリーマート
				case '21':
					$arrRet['cv_type'] = $conveni_type;			//コンビニの種類
					$arrRet['cv_company_code'] = $company_code;	//企業コード
					$arrRet['cv_order_no'] = $receipt_no;		//受付番号
					$arrRet['br1'] = lfSetConvMSG("","\n\n");
					$arrRet['cv_message'] = lfSetConvMSG("",$arrConveni_message[$conveni_code]);
					break;
				//ローソン
				case '31':
					$arrRet['cv_type'] = $conveni_type;			//コンビニの種類
					$arrRet['cv_receipt_no'] = $receipt_no;		//払込票番号
					$arrRet['cv_tel'] = $tel;					//電話番号
					$arrRet['br1'] = lfSetConvMSG("","\n\n");
					$arrRet['cv_message'] = lfSetConvMSG("",$arrConveni_message[$conveni_code]);
					break;
				//セイコーマート
				case '32':
					$arrRet['cv_type'] =$conveni_type;			//コンビニの種類
					$arrRet['cv_receipt_no'] = $receipt_no;		//払込票番号
					$arrRet['cv_tel'] = $tel;					//電話番号
					$arrRet['br1'] = lfSetConvMSG("","\n\n");
					$arrRet['cv_message'] = lfSetConvMSG("",$arrConveni_message[$conveni_code]);
					break;
				//ミニストップ
				case '33':
					$arrRet['cv_type'] = $conveni_type;			//コンビニの種類
					$arrRet['cv_payment_url'] = $payment_url;	//払込票URL
					$arrRet['br1'] = lfSetConvMSG("","\n\n");
					$arrRet['cv_message'] = lfSetConvMSG("",$arrConveni_message[$conveni_code]);
					break;
				//デイリーヤマザキ
				case '34':
					$arrRet['cv_type'] = $conveni_type;			//コンビニの種類
					$arrRet['cv_payment_url'] = $payment_url;	//払込票URL
					$arrRet['br1'] = lfSetConvMSG("","\n\n");
					$arrRet['cv_message'] = lfSetConvMSG("",$arrConveni_message[$conveni_code]);
					break;
				}

				//支払期限
				$arrRet['br2'] = lfSetConvMSG("","\n\n");
				$arrRet['cv_payment_limit'] = $payment_limit;
				$arrRet['br3'] = lfSetConvMSG("","\n\n");

				// タイトル
				$arrRet['title'] = lfSetConvMSG("コンビニ決済", true);

				// 決済送信データ作成
				$arrModule['module_id'] = MDL_EPSILON_ID;
				$arrModule['payment_total'] = $arrData["payment_total"];
				$arrModule['payment_id'] = PAYMENT_CONVENIENCE_ID;
				
				// ステータスは未入金にする
				$sqlval['status'] = 2;

				//コンビニ決済情報を格納
				$sqlval['conveni_data'] = serialize($arrRet);
				$sqlval['memo01'] = PAYMENT_CONVENIENCE_ID;
				$sqlval['memo02'] = serialize($arrRet);
				$sqlval["memo03"] = $arrPayment[0]["module_id"];
				$sqlval["memo04"] = $trans_code;
				$sqlval['memo05'] = serialize($arrModule);

				// 受注一時テーブルに更新
				sfRegistTempOrder($uniqid, $sqlval);

				if (is_callable(GC_MobileUserAgent) && GC_MobileUserAgent::isMobile()) {
					header("Location: " . gfAddSessionId(URL_SHOP_COMPLETE));
				} else {
					header("Location: " . URL_SHOP_COMPLETE);
				}
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

// 購入金額が30万より大きければセブンイレブンは利用不可
if($arrData["payment_total"] > SEVEN_CHARGE_MAX){
	unset($arrConv[11]);
}

$objPage->arrConv = $arrConv;

$objPage->arrForm =$objFormParam->getHashArray();

$objView->assignobj($objPage);
// フレームを選択(キャンペーンページから遷移なら変更)
$objCampaignSess->pageView($objView);

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

function lfSetConvMSG($name, $value){
	return array("name" => $name, "value" => $value);
}

?>
