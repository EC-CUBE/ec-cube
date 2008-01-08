<?php
/**
 * 
 * @copyright	2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: mdl_epsilon.inc 7162 2006-11-18 09:53:33Z kakinaka $
 * @link		http://www.lockon.co.jp/
 *
 */
require_once(DATA_PATH . "module/Request.php");

/**** ▼定数宣言 *********************************************************************************************/

define("MDL_ZEUS_ID", 10);

// クレジット分割回数
$arrPaymentClass = array(
	'01' => '一括払い',
	'03' => '3回払い',
	'05' => '5回払い',
	'06' => '6回払い',
	'10' => '10回払い'		
);

// 送信パラメータ
define ("SECURE_LINK_URL", "https://linkpt.cardservice.co.jp/cgi-bin/secure.cgi");

// 文字数制限
define ("CLIENTIP_LEN", 5);
define ("SEND_LEN", 20);

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
				memo01 as clientip
			FROM dtb_payment WHERE module_id = ? " . $where;
	$arrRet = $objQuery->getall($sql, $arrVal);

	return $arrRet[0];
}

/**************************************************************************************************************
 * 関数名	：sfPostPaymentData
 * 処理内容	：決済データ送信処理
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfPostPaymentData($arrData, $arrInput, $quick_charge = false){
	$arrPaymentDB = sfGetPaymentDB(MDL_ZEUS_ID);
	
	if(!$quick_charge) {
		$arrPost = array(
			'clientip' => $arrPaymentDB['clientip'],
			'cardnumber' =>  $arrInput['card_no01'].$arrInput['card_no02'].$arrInput['card_no03'].$arrInput['card_no04'],
			'expyy' => $arrInput['card_year'],
			'expmm' => $arrInput['card_month'],
			'telno' => $arrData['order_tel01'].$arrData['order_tel02'].$arrData['order_tel03'],
			'email' => $arrData['order_email'],
			'sendid' => $arrData['order_id'],
			'username' => $arrInput['card_name01'].$arrInput['card_name02'],
			'money' => $arrData['payment_total'],
			'sendpoint' => 'eccube',
			'send' => 'mall',
			'pubsec' => '',
			'div' => $arrInput['payment_class']
		);
		$arrVal["memo04"] = 'normal';
	} else {
		// クイックチャージを利用する場合
		$objQuery = new SC_Query();
		$objQuery->setlimit(1);
		$objQuery->setorder("create_date DESC");
		$col = "order_id, order_tel01, order_tel02, order_tel03";
		$where = "memo01 = ? AND memo03 = ? AND memo04 is 'normal' AND customer_id = ? AND del_flg = 0";
		$arrRet = $objQuery->select($col, "dtb_order", $where, array(MDL_ZEUS_ID, 'Success_order', $arrData['customer_id']));
		
		// 過去の注文があり、顧客登録されている。
		if(count($arrRet) > 0 && $arrData['customer_id'] > 0) {
           $arrPost = array(
	            'send' => 'mall',
	            'clientip' => $arrPaymentDB['clientip'],
	            'money' => $arrData['payment_total'],
	            'telno' => $arrRet[0]['order_tel01'].$arrRet[0]['order_tel02'].$arrRet[0]['order_tel03'],
	            'sendid' => $arrRet[0]['order_id'],
	            'cardnumber' => '8888888888888888',
	            'email' => $arrData['order_email'],
	            'sendpoint' => 'eccube',
	            'expyy' => '01',    // ダミー値
	            'expmm' => '01',    // ダミー値
	            'div' => '01',
	            'entry' => 'non',
	            'pubsec' => 'yes'
	        );
	        $arrVal["memo04"] = 'quick_charge:' . $arrRet[0]['order_id'];        // 処理結果
		} else {
		    // ダミーのPost値を生成する。
		    $arrPost = array(
                'send' => 'mall',
                'clientip' => $arrPaymentDB['clientip'],
                'money' => '0',
                'telno' => '0',
                'sendid' => '0',
                'cardnumber' => '8888888888888888',
                'email' => $arrData['order_email'],
                'sendpoint' => 'eccube',
                'expyy' => '01',    // ダミー値
                'expmm' => '01',    // ダミー値
                'div' => '01',
                'entry' => 'non',
                'pubsec' => 'yes'
            );
            $arrVal["memo04"] = 'quick_charge:0';        // 処理結果
		    gfPrintLog("過去の注文がないか、会員IDがありません。");
		}
	}
	
	$req = new HTTP_Request(SECURE_LINK_URL);
	$req->setMethod(HTTP_REQUEST_METHOD_POST);
	$req->addPostDataArray($arrPost);
	
	// カード番号を削除してログを残す。
	$arrTemp = $arrPost;
	$arrTemp['cardnumber'] = '****************';
	gfDebugLog($arrTemp);
	
	if (!PEAR::isError($req->sendRequest())) {
	    $response = $req->getResponseBody();
	    $response = mb_convert_encoding($response, "EUC-JP", "Shift-JIS");
	}
	$req->clearPostData();
	gfPrintLog("ゼウス応答結果：".$response);

	// 受注テーブルに記録する
	$arrVal["memo01"] = MDL_ZEUS_ID;	// モジュールID
	$arrVal["memo03"] = $response;		// 処理結果
	// ※memo04はquick_chargeで使用する。
	
	// 受注一時テーブルに更新
	sfRegistTempOrder($arrData['order_temp_id'], $arrVal);
	
	if(ereg('Success_order', $response)) {
		return true;
	} else {
		return false;
	}
	return $arrVal;
}

/**************************************************************************************************************
 * 関数名	：sfZeusDisp
 * 処理内容	：共通の表示処理
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfZeusDisp($objPage, $payment_id) {
	$objQuery = new SC_Query();
	// 支払い方法の説明画像を取得する。
	$arrRet = $objQuery->select("payment_method, payment_image", "dtb_payment", "payment_id = ?", array($payment_id));
	$objPage->tpl_payment_method = $arrRet[0]['payment_method'];
	$objPage->tpl_payment_image = $arrRet[0]['payment_image'];
	return $objPage;
}

/**************************************************************************************************************
 * 関数名  ：sfEnableQuickCharge
 * 処理内容 ：クイックチャージの使用判定
 * 引数1  ：
 * 引数2  ：
 * 引数3  ：
 * 戻り値  ：取得結果
 **************************************************************************************************************/
function sfEnableQuickCharge($customer_id) {
    $objQuery = new SC_Query();
    $objQuery->setlimit(1);
    $objQuery->setorder("create_date DESC");
    $col = "order_id, order_tel01, order_tel02, order_tel03";
    $where = "memo01 = ? AND memo03 = ? AND customer_id = ? AND del_flg = 0";
    $arrRet = $objQuery->select($col, "dtb_order", $where, array(MDL_ZEUS_ID, 'Success_order', $customer_id));
    if(count($arrRet) > 0 && $customer_id > 0) {
        return true;
    }
    return false;
}


?>