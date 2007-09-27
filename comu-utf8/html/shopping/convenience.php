<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = "shopping/convenience.tpl";
		$this->tpl_css = URL_DIR.'css/layout/shopping/pay.css';
		global $arrCONVENIENCE;
		$this->arrCONVENIENCE = $arrCONVENIENCE;
		/*
		 session_start時のno-cacheヘッダーを抑制することで
		 「戻る」ボタン使用時の有効期限切れ表示を抑制する。
		 private-no-expire:クライアントのキャッシュを許可する。
		*/
		session_cache_limiter('private-no-expire');		
	}
}

$objPage = new LC_Page;
$objView = new SC_SiteView;
$objSiteSess = new SC_SiteSession;
$objCartSess = new SC_CartSession;
$objCampaignSess = new SC_CampaignSession();
$objSiteInfo = $objView->objSiteInfo;
$objCustomer = new SC_Customer;

$arrInfo = $objSiteInfo->data;

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
// POST値の取得
$objFormParam->setParam($_POST);

// アクセスの正当性の判定
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

//コンビニの種類で処理ファイルを切り替える
switch($_POST['mode']) {
//完了
case 'complete':
	//エラーチェック
	$objPage->arrErr = lfCheckError();
	if($objPage->arrErr == "") {
		// マーチャント情報設定ファイルをインクルード
		//require("merchant.ini");
		// 決済処理パッケージをインクルード
		require_once(DATA_PATH . "vtcvsmdk/mdk/lib/BSCVS/Transaction.php");
		require_once(DATA_PATH . "vtcvsmdk/mdk/lib/BSCVS/Config.php");
		require_once(DATA_PATH . "vtcvsmdk/mdk/lib/BSCVS/Log.php");
	
		// トランザクションインスタンスを作成
		$objTran = new Transaction;
		
		// 設定ファイル cvsgwlib.conf によりインスタンスを初期化
		$objTran->setServer(DATA_PATH . "vtcvsmdk/mdk/conf/cvsgwlib.conf");
		
		// カート集計処理
		$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
		// 一時受注テーブルの読込
		$arrData = sfGetOrderTemp($uniqid);
		// カート集計を元に最終計算
		$arrPrice = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo, $objCustomer);
		
		// ログ出力インスタンスを取得
		$logger = $objTran->getLogger();
		
		// ログ出力(ここから)
		$logger->logprint('DEBUG', '<<< 支払結果画面処理開始... >>>');
		
		//コンビニの種類からCVSタイプを決定する
		switch($_POST['convenience']) {
		//セブンイレブン
		case '1':
			$cvs_type = '01';
			break;
		//ファミリーマート
		case '2':
			$cvs_type = '03';
			break;
		//サークルKサンクス
		case '3':
			$cvs_type = '04';
			break;
		//その他
		case '4':
		case '5':
			$cvs_type = '02';
			break;
		default:
			sfDispSiteError(PAGE_ERROR, "", "", "", $objCampaignSess);
			break;
		}
	
		//リクエスト電文
		$arrRequest = array(
			// 取引 ID
		    REQ_ORDER_ID => $uniqid,		
		    // CVSタイプ
		    REQ_CVS_TYPE => $cvs_type,
		    // 金額
		    REQ_AMOUNT => $arrPrice['payment_total'],
		    // 支払期限
		    REQ_PAY_LIMIT => lfGetPayLimit(),
		    // 氏名（注意：ベリトランスコンビニゲートウェイは UTF-8 の文字のみを
		    // 受け付けるため、ゲートウェイ接続の前に UTF-8 コードへ変換）
		    REQ_NAME1 => $objTran->jCode($arrData['order_name01'], ENCODE_UTF8),
		    REQ_NAME2 => $objTran->jCode($arrData['order_name02'], ENCODE_UTF8),
			REQ_KANA => $objTran->jCode($arrData['order_kana01'].$arrData['order_kana02'], ENCODE_UTF8),
		    // 電話番号
		    REQ_TEL_NO => $arrData['order_tel01']."-".$arrData['order_tel02']."-".$arrData['order_tel03']
		);

		//ベリトランスコンビニゲートウェイにリクエスト電文を投げ、取引結果を格納
		$arrResult = $objTran->doTransaction(CMD_ENTRY, $arrRequest);
		//取引成功
		if($arrResult[RES_ACTION_CODE] = '010') {
			//コンビニの種類
			switch($_POST['convenience']) {
			//セブンイレブン
			case '1':
				$arrRet['cv_type'] = '1';										//コンビニの種類
				$arrRet['cv_payment_url'] = $arrResult[RES_HARAIKOMI_URL];		//払込票URL(PC)
				$arrRet['cv_receipt_no'] = $arrResult[RES_RECEIPT_NO];			//払込票番号
				break;
			//ファミリーマート
			case '2':
				$company_code = substr($arrResult[RES_RECEIPT_NO], 0, 5);
				$order_no = substr($arrResult[RES_RECEIPT_NO], 6, 12);
				$arrRet['cv_type'] = '2';						//コンビニの種類
				$arrRet['cv_company_code'] = $company_code;	//企業コード
				$arrRet['cv_order_no'] = $order_no;			//受付番号
				break;
			//サークルKサンクス
			case '3':
				$mobile_url = preg_replace("/https:\/\/.+?\/JLPcon/","https://w2.kessai.info/JLM/JLMcon", $arrResult[RES_HARAIKOMI_URL]);
				$arrRet['cv_type'] = '3';										//コンビニの種類
				$arrRet['cv_payment_url'] = $arrResult[RES_HARAIKOMI_URL];		//払込票URL
				$arrRet['cv_payment_mobile_url'] = $mobile_url;					//払込票URL(モバイル)
				break;
			//ローソン、セイコーマート
			case '4':
				$arrRet['cv_type'] = '4';									//コンビニの種類
				$arrRet['cv_receipt_no'] = $arrResult[RES_RECEIPT_NO];		//払込票番号
				break;
			//ミニストップ、デイリーヤマザキ、ヤマザキデイリーストア
			case '5':
				$arrRet['cv_type'] = '5';										//コンビニの種類
				$arrRet['cv_payment_url'] = $arrResult[RES_HARAIKOMI_URL];		//払込票URL(PC)
				break;
			}
			//支払期限
			$arrRet['cv_payment_limit'] = lfGetPayLimit();
			//コンビニ決済情報を格納
			$sqlval['conveni_data'] = serialize($arrRet);
			$objQuery = new SC_Query;
			$objQuery->update("dtb_order_temp", $sqlval, "order_temp_id = ? ", array($uniqid));
			// 正常に登録されたことを記録しておく
			$objSiteSess->setRegistFlag();
			//購入完了ページへ
			header("Location: " . URL_SHOP_COMPLETE);
		//失敗
		} else {
			$objPage->arrErr = 'エラーが発生しました。';
		}
		
		# ログ出力(ここまで)
		$logger->logprint('DEBUG', '<<< 支払結果画面処理終了. >>>');
	
	}
	break;
//戻る
case 'return':
	// 正常に登録されたことを記録しておく
	$objSiteSess->setRegistFlag();
	// 確認ページへ移動
	header("Location: " . URL_SHOP_CONFIRM);
	exit;
	break;
}

$objView->assignobj($objPage);
// フレームを選択(キャンペーンページから遷移なら変更)
$objCampaignSess->pageView($objView);

//-------------------------------------------------------------------------------------------------------------

//支払期限の生成
function lfGetPayLimit() {
    $date = sprintf("%10s",
                    date("Y/m/d",mktime(0,0,0,date("m"),
                    date("d")+CV_PAYMENT_LIMIT,date("Y"))));
    return $date;
}

//パラメータの初期化
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("コンビニの種類", "convenience", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
}
	
// 入力内容のチェック
function lfCheckError() {
	global $objFormParam;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	return $objErr->arrErr;
}

?>