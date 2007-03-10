<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $arrProductsClass;
	var $tpl_total_pretax;
	var $tpl_total_tax;
	var $tpl_total_point;
	var $tpl_message;
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = URL_DIR.'css/layout/cartin/index.css';	// メインCSSパス
		/** 必ず指定する **/
		$this->tpl_mainpage = 'cart/index.tpl';		// メインテンプレート
		$this->tpl_title = "カゴの中を見る";
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView(false);
$objCartSess = new SC_CartSession("", false);
$objSiteSess = new SC_SiteSession();
$objCampaignSess = new SC_CampaignSession();
$objSiteInfo = $objView->objSiteInfo;
$objCustomer = new SC_Customer();
// 基本情報の取得
$arrInfo = $objSiteInfo->data;

// 商品購入中にカート内容が変更された。
if($objCartSess->getCancelPurchase()) {
	$objPage->tpl_message = "商品購入中にカート内容が変更されましたので、お手数ですが購入手続きをやり直して下さい。";
}

switch($_POST['mode']) {
case 'up':
	$objCartSess->upQuantity($_POST['cart_no']);
	sfReload();
	break;
case 'down':
	$objCartSess->downQuantity($_POST['cart_no']);
	sfReload();
	break;
case 'delete':
	$objCartSess->delProduct($_POST['cart_no']);
	sfReload();
	break;
case 'confirm':
	// カート内情報の取得
	$arrRet = $objCartSess->getCartList();
	$max = count($arrRet);
	$cnt = 0;
	for ($i = 0; $i < $max; $i++) {
		// 商品規格情報の取得	
		$arrData = sfGetProductsClass($arrRet[$i]['id']);
		// DBに存在する商品
		if($arrData != "") {
			$cnt++;
		}
	}
	// カート商品が1件以上存在する場合
	if($cnt > 0) {
		// 正常に登録されたことを記録しておく
		$objSiteSess->setRegistFlag();
		$pre_uniqid = $objSiteSess->getUniqId();
		// 注文一時IDの発行
		$objSiteSess->setUniqId();
		$uniqid = $objSiteSess->getUniqId();
		// エラーリトライなどで既にuniqidが存在する場合は、設定を引き継ぐ
		if($pre_uniqid != "") {
			$sqlval['order_temp_id'] = $uniqid;
			$where = "order_temp_id = ?";
			$objQuery = new SC_Query();
			$objQuery->update("dtb_order_temp", $sqlval, $where, array($pre_uniqid));
		}
		// カートを購入モードに設定
		$objCartSess->saveCurrentCart($uniqid);
		// 購入ページへ
		header("Location: " . URL_SHOP_TOP);
	}
	break;
default:
	break;
}

// カート集計処理
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
$objPage->arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo, $objCustomer);

$objPage->arrInfo = $arrInfo;

// ログイン判定
if($objCustomer->isLoginSuccess()) {
	$objPage->tpl_login = true;
	$objPage->tpl_user_point = $objCustomer->getValue('point');
	$objPage->tpl_name = $objCustomer->getValue('name01');
}

// 送料無料までの金額を計算
$tpl_deliv_free = $objPage->arrInfo['free_rule'] - $objPage->tpl_total_pretax;
$objPage->tpl_deliv_free = $tpl_deliv_free;

// 前頁のURLを取得
$objPage->tpl_prev_url = $objCartSess->getPrevURL();

$objView->assignobj($objPage);
// フレームを選択(キャンペーンページから遷移なら変更)
$objCampaignSess->pageView($objView);

//--------------------------------------------------------------------------------------------------------------------------



?>