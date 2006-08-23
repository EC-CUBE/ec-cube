<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'mypage/history.tpl';
		$this->tpl_rightnavi = 'frontparts/rightnavi.tpl'; 
		$this->tpl_title = "MYページ/購入履歴詳細";
		$this->tpl_navi = 'mypage/navi.tpl';
		$this->tpl_mainno = 'mypage';
		$this->tpl_mypageno = 'index';
		session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

//不正アクセス判定
$from = "dtb_order";
$where = "delete = 0 AND customer_id = ? AND order_id = ? ";
$arrval = array($objCustomer->getValue('customer_id'), $_POST['order_id']);
//DBに情報があるか判定
$cnt = $objQuery->count($from, $where, $arrval);
//ログインしていない、またはDBに情報が無い場合
if (!$objCustomer->isLoginSuccess() || $cnt == 0){
	sfDispSiteError(CUSTOMER_ERROR);
} else {
	//受注詳細データの取得
	$objPage->arrDisp = lfGetOrderData($_POST['order_id']);
	// 支払い方法の取得
	$objPage->arrPayment = sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
	// 配送時間の取得
	$arrRet = sfGetDelivTime($objPage->arrDisp['payment_id']);
	$objPage->arrDelivTime = sfArrKeyValue($arrRet, 'time_id', 'time');
	$objSiteInfo = new SC_SiteInfo();
	$arrInfo = $objSiteInfo->data;
	// 基本情報を渡す
	$objPage->arrInfo = $arrInfo;
	
	//マイページトップ顧客情報表示用
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
	$objPage->CustomerPoint = $objCustomer->getvalue('point');
	
}



$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------

//受注詳細データの取得
function lfGetOrderData($order_id) {
	//受注番号が数字であれば
	if(sfIsInt($order_id)) {
		// DBから受注情報を読み込む
		$objQuery = new SC_Query();
		$col = "order_id, create_date, payment_id, subtotal, tax, use_point, add_point, discount, ";
		$col .= "deliv_fee, charge, payment_total, deliv_name01, deliv_name02, deliv_kana01, deliv_kana02, ";
		$col .= "deliv_zip01, deliv_zip02, deliv_pref, deliv_addr01, deliv_addr02, deliv_tel01, deliv_tel02, deliv_tel03, deliv_time_id, deliv_date ";
		$from = "dtb_order";
		$where = "order_id = ?";
		$arrRet = $objQuery->select($col, $from, $where, array($order_id));
		$arrOrder = $arrRet[0];
		// 受注詳細データの取得
		$arrRet = lfGetOrderDetail($order_id);
		$arrOrderDetail = sfSwapArray($arrRet);
		$arrData = array_merge($arrOrder, $arrOrderDetail);
	}
	return $arrData;
}

// 受注詳細データの取得
function lfGetOrderDetail($order_id) {
	$objQuery = new SC_Query();
	$col = "product_id, product_code, product_name, classcategory_name1, classcategory_name2, price, quantity, point_rate";
	$where = "order_id = ?";
	$objQuery->setorder("classcategory_id1, classcategory_id2");
	$arrRet = $objQuery->select($col, "dtb_order_detail", $where, array($order_id));
	return $arrRet;
}

?>