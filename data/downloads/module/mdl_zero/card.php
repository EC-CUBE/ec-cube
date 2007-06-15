<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once(MODULE_PATH . "mdl_zero/mdl_zero.inc");

class LC_Page {
	function LC_Page() {
        $this->tpl_mainpage = MODULE_PATH . 'mdl_zero/card_mobile.tpl';
		/*
		 session_start時のno-cacheヘッダーを抑制することで
		 「戻る」ボタン使用時の有効期限切れ表示を抑制する。
		 private-no-expire:クライアントのキャッシュを許可する。
		*/
		session_cache_limiter('private-no-expire');		
	} 
}

$objPage = new LC_Page();
$objView = new SC_MobileView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objCampaignSess = new SC_CampaignSession();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// ユーザユニークIDの取得と購入状態の正当性をチェック
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

// カート集計処理
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);

// 一時受注テーブルの読込
$arrData = sfGetOrderTemp($uniqid);

// カート集計を元に最終計算
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

// 支払い情報を取得
$arrPayment = $objQuery->getall("SELECT module_id, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10 FROM dtb_payment WHERE payment_id = ? ", array($arrData["payment_id"]));

// データ送信先CGI 携帯端末の場合は 携帯用に飛ばす
if(GC_MobileUserAgent::isMobile()) {
    $objPage = lfSendMobileCredit($arrData, $arrPayment, $objPage);
}else{
    lfSendPcCredit($arrData, $arrPayment);
}

$objPage = lfSendMobileCredit($arrData, $arrPayment, $objPage);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

// データ送信処理(PC)
function lfSendPcCredit($arrData, $arrPayment){
    global $objCartSess;
    global $objSiteSess;
 
	// 送信データ生成
	$arrSendData = array(
		'clientip' => $arrPayment[0]["memo02"],						                                // 番組コード
		'custom' => SEND_PARAM_CUSTOM ,										                        // yes固定
		'send' => SEND_PARAM_SEND,	                                                                // jpall固定
		'money' => $arrData["payment_total"],							                            // 金額
		'usrtel' => $arrData["order_tel01"] . $arrData["order_tel02"] . $arrData["order_tel03"],	// 電話番号
		'usrmail' => $arrData["order_email"],					                                    // メールアドレス
		'sendid' => $arrData["order_temp_id"] . SEND_PARAM_DELIMITER . $arrData["payment_id"],      // オーダーTEMPID , payment_id
		'sendpoint' => ECCUBE_PAYMENT	    									                    // EC-CUBE
	);
    
	// セッションカート内の商品を削除する。
	$objCartSess->delAllProducts();
	// 注文一時IDを解除する。
	$objSiteSess->unsetUniqId();
    
    $order_url = SEND_PARAM_PC_URL;
    $html = '';
    $html .= '<body onload="document.form1.submit();">';
    $html .= '<form name="form1" id="form1" method="post" action="' . $order_url . '">';
    foreach($arrSendData as $key => $val){
        $html .= '	<input type="hidden" name="' . $key . '" value="' . $val . '">';
    }
    $html .= '	</form>';
    $html .= '	</body>';
//    $html .= "<script type='text/javascript'>document.form1.submit();</script>";
    
    echo $html;
    exit();
}

// データ送信処理(MOBILE)
function lfSendMobileCredit($arrData, $arrPayment, $objPage){
    global $objCartSess;
    global $objSiteSess;
 
	// 非会員のときは user_id に not_memberと送る
	($arrData["customer_id"] == 0) ? $user_id = "not_member" : $user_id = $arrData["customer_id"];	
	
	// 送信データ生成
	$arrSendData = array(
		'clientip' => $arrPayment[0]["memo05"],						                                // 番組コード
		'act' => SEND_PARAM_ACT ,										                            // imode固定
		'send' => SEND_PARAM_SEND,	                                                                // jpall固定
		'money' => $arrData["payment_total"],						                    	        // 金額
		'usrtel' => $arrData["order_tel01"] . $arrData["order_tel02"] . $arrData["order_tel03"],	// 電話番号
		'usrmail' => $arrData["order_email"],					                                    // メールアドレス
        'sendid' => $arrData["order_temp_id"] . SEND_PARAM_DELIMITER . $arrData["payment_id"],		                // オーダーTEMPID , payment_id
		'sendpoint' => ECCUBE_PAYMENT,	    									                    // EC-CUBE
		'siteurl' => SITE_URL . "mobile/",	    							                        		    // 戻り先URL
		'sitestr' => "TOPへ戻る"                                						        	// 戻り先リンク名
	);
    
	// セッションカート内の商品を削除する。
	$objCartSess->delAllProducts();
	// 注文一時IDを解除する。
	$objSiteSess->unsetUniqId();
    
    // データ送信先CGI 携帯端末の場合は 携帯用に飛ばす
    $objPage->order_url = SEND_PARAM_MOBILE_URL;
    $objPage->arrSendData = $arrSendData;
    
    return $objPage;
}


?>
