<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'shopping/loan.tpl';
		$this->tpl_css = URL_DIR.'css/layout/shopping/pay.css';
		// ホームアドレス
		$this->tpl_homeaddr = CF_HOMEADDR;
		// シュミレーション呼び出し
		$this->tpl_simulate = CF_SIMULATE;
		// 加盟店コード
		$this->tpl_storecode = CF_STORECODE;
		// 戻り先
		$this->tpl_returnurl = CF_RETURNURL;
		// 呼び出し区分(0:シュミレーションのみ、1:シュミレーション+申込)
		$this->tpl_continue = CF_CONTINUE;
		// 役務有無区分(0:無、1:有)
		$this->tpl_labor = CF_LABOR;
		// 結果応答(1:結果あり、2:結果なし)
		$this->tpl_result = CF_RESULT;
		// キャンセルURL
		$this->tpl_cancelurl = CF_CANCELURL;
		/*
		 session_start時のno-cacheヘッダーを抑制することで
		 「戻る」ボタン使用時の有効期限切れ表示を抑制する。
		 private-no-expire:クライアントのキャッシュを許可する。
		*/
		session_cache_limiter('private-no-expire');		
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objCampaignSess = new SC_CampaignSession();
$objCustomer = new SC_Customer();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// 注文一時IDの取得
$uniqid = $objSiteSess->getUniqId();

// ローン決済の戻り値をチェックする。
if($_GET['tranno'] == $uniqid) {
	// ローン決済受付番号をDBに書き込む
	$sqlval['loan_result'] = $_GET['receiptno'];
	$objQuery = new SC_Query();
	$objQuery->update("dtb_order_temp", $sqlval, "order_temp_id = ?", array($uniqid));
	// 正常に登録されたことを記録しておく
	$objSiteSess->setRegistFlag();
	// 処理完了ページへ
	header("Location: " . URL_SHOP_COMPLETE);
}

switch($_POST['mode']) {
// 前のページに戻る
case 'return':
	// 正常な推移であることを記録しておく
	$objSiteSess->setRegistFlag();
	header("Location: " . URL_SHOP_CONFIRM);
	exit;
	break;
default:
	break;
}

// カート集計処理
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
// 一時受注テーブルの読込
$arrData = sfGetOrderTemp($uniqid);
// カート集計を元に最終計算
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

// 支払い総額
$objPage->tpl_amount = $arrData['payment_total'];
// 受注仮番号
$objPage->tpl_tranno = $uniqid;
// 基本情報を渡す
$objPage->arrInfo = $arrInfo;

$objView->assignobj($objPage);
// フレームを選択(キャンペーンページから遷移なら変更)
$objCampaignSess->pageView($objView);
//--------------------------------------------------------------------------------------------------------------------------
?>
