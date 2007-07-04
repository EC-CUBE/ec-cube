<?php
/* 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'shopping/complete.tpl';
		$this->tpl_css = URL_DIR.'css/layout/shopping/complete.css';
		$this->tpl_title = "ご注文完了";
		global $arrCONVENIENCE;
		$this->arrCONVENIENCE = $arrCONVENIENCE;
		global $arrCONVENIMESSAGE;
		$this->arrCONVENIMESSAGE = $arrCONVENIMESSAGE;
		global $arrCONVENIENCE;
		global $arrCONVENIMESSAGE;
		$objPage->arrCONVENIENCE = $arrCONVENIENCE;
		$objPage->arrCONVENIMESSAGE = $arrCONVENIMESSAGE;
		/*
		 session_start時のno-cacheヘッダーを抑制することで
		 「戻る」ボタン使用時の有効期限切れ表示を抑制する。
		 private-no-expire:クライアントのキャッシュを許可する。
		*/
		session_cache_limiter('private-no-expire');		

	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objCampaignSess = new SC_CampaignSession();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;
$objCustomer = new SC_Customer();

// 前のページで正しく登録手続きが行われたか判定
sfIsPrePage($objSiteSess);
// ユーザユニークIDの取得と購入状態の正当性をチェック
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);
if ($uniqid != "") {
	
	// 完了処理
	$objQuery = new SC_Query();
	$objQuery->begin();
	$order_id = lfDoComplete($objQuery, $uniqid);
	$objQuery->commit();
	
	// セッションに保管されている情報を更新する
	$objCustomer->updateSession();

	// 完了メール送信
	if($order_id != "") {
		sfSendOrderMail($order_id, '1');
	}

	// その他情報の取得
	$arrResults = $objQuery->getall("SELECT memo02, memo05 FROM dtb_order WHERE order_id = ? ", array($order_id));

	if (count($arrResults) > 0) {	
		if (isset($arrResults[0]["memo02"]) || isset($arrResults[0]["memo05"])) {
			// 完了画面で表示する決済内容
			$arrOther = unserialize($arrResults[0]["memo02"]);
			// 完了画面から送信する決済内容
			$arrModuleParam = unserialize($arrResults[0]["memo05"]);
			
			// データを編集
			foreach($arrOther as $key => $val){
				// URLの場合にはリンクつきで表示させる
				if (preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $val["value"])) {
					$arrOther[$key]["value"] = "<a href='#' onClick=\"window.open('". $val["value"] . "'); \" >" . $val["value"] ."</a>";
				}
			}
					
			$objPage->arrOther = $arrOther;
			$objPage->arrModuleParam = $arrModuleParam;
		}
	}
	
	// アフィリエイト用コンバージョンタグの設定
	$objPage->tpl_conv_page = AFF_SHOPPING_COMPLETE;
	$objPage->tpl_aff_option = "order_id=$order_id";
	//合計価格の取得
	$total = $objQuery->get("dtb_order", "total", "order_id = ? ", array($order_id));
	if($total != "") {
		$objPage->tpl_aff_option.= "|total=$total";
	}
}

$objPage->arrInfo = $arrInfo;
// キャンペーンからの遷移かチェック
$objPage->is_campaign = $objCampaignSess->getIsCampaign();
$objPage->campaign_dir = $objCampaignSess->getCampaignDir();

$objView->assignobj($objPage);
// フレームを選択(キャンペーンページから遷移なら変更)
$objCampaignSess->pageView($objView);

// セッション開放
$objCampaignSess->delCampaign();
//--------------------------------------------------------------------------------------------------------------------------
// エビスタグ引渡し用データを生成する
function lfGetEbisData($order_id) {
	$objQuery = new SC_Query();
	$col = "customer_id, total, order_sex, order_job, to_number(to_char(age(current_timestamp, order_birth), 'YYY'), 999) AS order_age";
	$arrRet = $objQuery->select($col, "dtb_order", "order_id = ?", array($order_id));
	
	if($arrRet[0]['customer_id'] > 0) {
		// 会員番号
		$arrEbis['m1id'] = $arrRet[0]['customer_id'];
		// 非会員or会員
		$arrEbis['o5id'] = '1';
	} else {
		// 会員番号
		$arrEbis['m1id'] = '';
		// 非会員or会員
		$arrEbis['o5id'] = '2';	
	}
	
	// 購入金額
	$arrEbis['a1id'] = $arrRet[0]['total'];
	// 性別
	$arrEbis['o2id'] = $arrRet[0]['order_sex'];
	// 年齢
	$arrEbis['o3id'] = $arrRet[0]['order_age'];
	// 職業
	$arrEbis['o4id'] = $arrRet[0]['order_job'];
		
	$objQuery->setgroupby("product_id");
	$arrRet = $objQuery->select("product_id", "dtb_order_detail", "order_id = ?", array($order_id));
	$arrProducts = sfSwapArray($arrRet);
	
	$line = "";
	// 商品IDをアンダーバーで接続する。
	foreach($arrProducts['product_id'] as $val) {
		if($line != "") {
			$line .= "_$val";		
		} else {
			$line .= "$val";
		}
	}
	
	// 商品ID	
	$arrEbis['o1id'] = $line;
	
	return $arrEbis;
}

// 完了処理
function lfDoComplete($objQuery, $uniqid) {
	global $objCartSess;
	global $objSiteSess;
	global $objCampaignSess;
	global $objCustomer;
	global $arrInfo;
	
	// 一時受注テーブルの読込
	$arrData = sfGetOrderTemp($uniqid);

	// 会員情報登録処理
	if ($objCustomer->isLoginSuccess()) {
		// 新お届け先の登録
		lfSetNewAddr($uniqid, $objCustomer->getValue('customer_id'));
		// 購入集計を顧客テーブルに反映
		lfSetCustomerPurchase($objCustomer->getValue('customer_id'), $arrData, $objQuery);
	} else {
		//購入時強制会員登録
		switch(PURCHASE_CUSTOMER_REGIST) {
		//無効
		case '0':
			// 購入時会員登録
			if($arrData['member_check'] == '1') {
				// 仮会員登録
				$customer_id = lfRegistPreCustomer($arrData, $arrInfo);
				// 購入集計を顧客テーブルに反映
				lfSetCustomerPurchase($customer_id, $arrData, $objQuery);
			}
			break;
		//有効
		case '1':
			// 仮会員登録
			$customer_id = lfRegistPreCustomer($arrData, $arrInfo);
			// 購入集計を顧客テーブルに反映
			lfSetCustomerPurchase($customer_id, $arrData, $objQuery);
			break;
		}
		
	}
	// 一時テーブルを受注テーブルに格納する
	$order_id = lfRegistOrder($objQuery, $arrData, $objCampaignSess);
	// カート商品を受注詳細テーブルに格納する
	lfRegistOrderDetail($objQuery, $order_id, $objCartSess);
	// 受注一時テーブルの情報を削除する。
	lfDeleteTempOrder($objQuery, $uniqid);
	// キャンペーンからの遷移の場合登録する。
	if($objCampaignSess->getIsCampaign() and $objCartSess->chkCampaign($objCampaignSess->getCampaignId())) {
		lfRegistCampaignOrder($objQuery, $objCampaignSess, $order_id);
	}
	
	// セッションカート内の商品を削除する。
	$objCartSess->delAllProducts();
	// 注文一時IDを解除する。
	$objSiteSess->unsetUniqId();
	
	return $order_id;
}

// 会員登録（仮登録）
function lfRegistPreCustomer($arrData, $arrInfo) {
	// 購入時の会員登録
	$sqlval['name01'] = $arrData['order_name01'];
	$sqlval['name02'] = $arrData['order_name02'];
	$sqlval['kana01'] = $arrData['order_kana01'];
	$sqlval['kana02'] = $arrData['order_kana02'];
	$sqlval['zip01'] = $arrData['order_zip01'];
	$sqlval['zip02'] = $arrData['order_zip02'];
	$sqlval['pref'] = $arrData['order_pref'];
	$sqlval['addr01'] = $arrData['order_addr01'];
	$sqlval['addr02'] = $arrData['order_addr02'];
	$sqlval['email'] = $arrData['order_email'];
	$sqlval['tel01'] = $arrData['order_tel01'];
	$sqlval['tel02'] = $arrData['order_tel02'];
	$sqlval['tel03'] = $arrData['order_tel03'];
	$sqlval['fax01'] = $arrData['order_fax01'];
	$sqlval['fax02'] = $arrData['order_fax02'];
	$sqlval['fax03'] = $arrData['order_fax03'];
	$sqlval['sex'] = $arrData['order_sex'];
	$sqlval['password'] = $arrData['password'];
	$sqlval['reminder'] = $arrData['reminder'];
	$sqlval['reminder_answer'] = $arrData['reminder_answer'];
	
	// メルマガ配信用フラグの判定
	switch($arrData['mail_flag']) {
	case '1':	// HTMLメール
		$mail_flag = 4;
		break;
	case '2':	// TEXTメール
		$mail_flag = 5;
		break;
	case '3':	// 希望なし
		$mail_flag = 6;
		break;
	default:
		$mail_flag = 6;
		break;
	}
	// メルマガフラグ
	$sqlval['mailmaga_flg'] = $mail_flag;
		
	// 会員仮登録
	$sqlval['status'] = 1;
	// URL判定用キー
	$sqlval['secret_key'] = sfGetUniqRandomId("t"); 
	
	$objQuery = new SC_Query();
	$sqlval['create_date'] = "now()";
	$sqlval['update_date'] = "now()";
	$objQuery->insert("dtb_customer", $sqlval);
	
	// 顧客IDの取得
	$arrRet = $objQuery->select("customer_id", "dtb_customer", "secret_key = ?", array($sqlval['secret_key']));
	$customer_id = $arrRet[0]['customer_id'];
	
	//　仮登録完了メール送信
	$objMailPage = new LC_Page();
	$objMailPage->to_name01 = $arrData['order_name01'];
	$objMailPage->to_name02 = $arrData['order_name02'];
	$objMailPage->CONF = $arrInfo;
	$objMailPage->uniqid = $sqlval['secret_key'];
	$objMailView = new SC_SiteView();
	$objMailView->assignobj($objMailPage);
	$body = $objMailView->fetch("mail_templates/customer_mail.tpl");
	
	$objMail = new GC_SendMail();
	$objMail->setItem(
						''										//　宛先
						, sfMakeSubject("会員登録のご確認")		//　サブジェクト
						, $body									//　本文
						, $arrInfo['email03']					//　配送元アドレス
						, $arrInfo['shop_name']					//　配送元　名前
						, $arrInfo["email03"]					//　reply_to
						, $arrInfo["email04"]					//　return_path
						, $arrInfo["email04"]					//  Errors_to
						, $arrInfo["email01"]					//  Bcc
														);
	// 宛先の設定
	$name = $arrData['order_name01'] . $arrData['order_name02'] ." 様";
	$objMail->setTo($arrData['order_email'], $name);			
	$objMail->sendMail();
	
	return $customer_id;
}

// 受注テーブルへ登録
function lfRegistOrder($objQuery, $arrData, $objCampaignSess) {
	$sqlval = $arrData;

	// 受注テーブルに書き込まない列を除去
	unset($sqlval['mailmaga_flg']);		// メルマガチェック
	unset($sqlval['deliv_check']);		// 別のお届け先チェック
	unset($sqlval['point_check']);		// ポイント利用チェック
	unset($sqlval['member_check']);		// 購入時会員チェック
	unset($sqlval['password']);			// ログインパスワード
	unset($sqlval['reminder']);			// リマインダー質問
	unset($sqlval['reminder_answer']);	// リマインダー答え
	unset($sqlval['mail_flag']);		// メールフラグ
	unset($sqlval['session']);		    // セッション情報
	
	// 注文ステータス:指定が無ければ新規受付に設定
	if($sqlval["status"] == ""){
		$sqlval['status'] = '1';			
	}
	
	// 別のお届け先を指定していない場合、配送先に登録住所をコピーする。
	if($arrData["deliv_check"] == "-1") {
		$sqlval['deliv_name01'] = $arrData['order_name01'];
		$sqlval['deliv_name02'] = $arrData['order_name02'];
		$sqlval['deliv_kana01'] = $arrData['order_kana01'];
		$sqlval['deliv_kana02'] = $arrData['order_kana02'];
		$sqlval['deliv_pref'] = $arrData['order_pref'];
		$sqlval['deliv_zip01'] = $arrData['order_zip01'];
		$sqlval['deliv_zip02'] = $arrData['order_zip02'];
		$sqlval['deliv_addr01'] = $arrData['order_addr01'];
		$sqlval['deliv_addr02'] = $arrData['order_addr02'];
		$sqlval['deliv_tel01'] = $arrData['order_tel01'];
		$sqlval['deliv_tel02'] = $arrData['order_tel02'];
		$sqlval['deliv_tel03'] = $arrData['order_tel03'];
	}
	
	$order_id = $arrData['order_id'];		// オーダーID
	$sqlval['create_date'] = 'now()';		// 受注日
	
	// キャンペーンID
	if($objCampaignSess->getIsCampaign()) $sqlval['campaign_id'] = $objCampaignSess->getCampaignId();

	// ゲットの値をインサート
	//$sqlval = lfGetInsParam($sqlval);
	
	// INSERTの実行
	$objQuery->insert("dtb_order", $sqlval);
	
	return $order_id;
}

// 受注詳細テーブルへ登録
function lfRegistOrderDetail($objQuery, $order_id, $objCartSess) {
	// カート内情報の取得
	$arrCart = $objCartSess->getCartList();
	$max = count($arrCart);
	
	// 既に存在する詳細レコードを消しておく。
	$objQuery->delete("dtb_order_detail", "order_id = $order_id");

	// 規格名一覧
	$arrClassName = sfGetIDValueList("dtb_class", "class_id", "name");
	// 規格分類名一覧
	$arrClassCatName = sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
			
	for ($i = 0; $i < $max; $i++) {
		// 商品規格情報の取得	
		$arrData = sfGetProductsClass($arrCart[$i]['id']);
		
		// 存在する商品のみ表示する。
		if($arrData != "") {
			$sqlval['order_id'] = $order_id;
			$sqlval['product_id'] = $arrCart[$i]['id'][0];
			$sqlval['classcategory_id1'] = $arrCart[$i]['id'][1];
			$sqlval['classcategory_id2'] = $arrCart[$i]['id'][2];
			$sqlval['product_name'] = $arrData['name'];
			$sqlval['product_code'] = $arrData['product_code'];
			$sqlval['classcategory_name1'] = $arrClassCatName[$arrData['classcategory_id1']];
			$sqlval['classcategory_name2'] = $arrClassCatName[$arrData['classcategory_id2']];
			$sqlval['point_rate'] = $arrCart[$i]['point_rate'];			
			$sqlval['price'] = $arrCart[$i]['price'];
			$sqlval['quantity'] = $arrCart[$i]['quantity'];
			lfReduceStock($objQuery, $arrCart[$i]['id'], $arrCart[$i]['quantity']);
			// INSERTの実行
			$objQuery->insert("dtb_order_detail", $sqlval);
		} else {
			sfDispSiteError(CART_NOT_FOUND);
		}
	}
}

// キャンペーン受注テーブルへ登録
function lfRegistCampaignOrder($objQuery, $objCampaignSess, $order_id) {

	// 受注データを取得
	$cols = "order_id, campaign_id, customer_id, message, order_name01, order_name02,".
			"order_kana01, order_kana02, order_email, order_tel01, order_tel02, order_tel03,".
			"order_fax01, order_fax02, order_fax03, order_zip01, order_zip02, order_pref, order_addr01,".
			"order_addr02, order_sex, order_birth, order_job, deliv_name01, deliv_name02, deliv_kana01,".
			"deliv_kana02, deliv_tel01, deliv_tel02, deliv_tel03, deliv_fax01, deliv_fax02, deliv_fax03,".
			"deliv_zip01, deliv_zip02, deliv_pref, deliv_addr01, deliv_addr02, payment_total";

	$arrOrder = $objQuery->select($cols, "dtb_order", "order_id = ?", array($order_id)); 
			
	$sqlval = $arrOrder[0];
    $sqlval['create_date'] = 'now()';
		
	// INSERTの実行
	$objQuery->insert("dtb_campaign_order", $sqlval);
	
	// 申し込み数の更新
	$total_count = $objQuery->get("dtb_campaign", "total_count", "campaign_id = ?", array($sqlval['campaign_id']));
	$arrCampaign['total_count'] = $total_count += 1;
	$objQuery->update("dtb_campaign", $arrCampaign, "campaign_id = ?", array($sqlval['campaign_id']));
	
}



/* 受注一時テーブルの削除 */
function lfDeleteTempOrder($objQuery, $uniqid) {
	$where = "order_temp_id = ?";
	$sqlval['del_flg'] = 1;
	$objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
	// $objQuery->delete("dtb_order_temp", $where, array($uniqid));
}

// 受注一時テーブルの住所が登録済みテーブルと異なる場合は、別のお届け先に追加する
function lfSetNewAddr($uniqid, $customer_id) {
	$objQuery = new SC_Query();
	$diff = false;
	$find_same = false;
	
	$col = "deliv_name01,deliv_name02,deliv_kana01,deliv_kana02,deliv_tel01,deliv_tel02,deliv_tel03,deliv_zip01,deliv_zip02,deliv_pref,deliv_addr01,deliv_addr02";
	$where = "order_temp_id = ?";
	$arrRet = $objQuery->select($col, "dtb_order_temp", $where, array($uniqid));
	
	// 要素名のdeliv_を削除する。
	foreach($arrRet[0] as $key => $val) {
		$keyname = ereg_replace("^deliv_", "", $key);
		$arrNew[$keyname] = $val;
	}
	
	// 会員情報テーブルとの比較
	$col = "name01,name02,kana01,kana02,tel01,tel02,tel03,zip01,zip02,pref,addr01,addr02";
	$where = "customer_id = ?";
	$arrCustomerAddr = $objQuery->select($col, "dtb_customer", $where, array($customer_id));
	
	// 会員情報の住所と異なる場合
	if($arrNew != $arrCustomerAddr[0]) {
		// 別のお届け先テーブルの住所と比較する
		$col = "name01,name02,kana01,kana02,tel01,tel02,tel03,zip01,zip02,pref,addr01,addr02";
		$where = "customer_id = ?";
		$arrOtherAddr = $objQuery->select($col, "dtb_other_deliv", $where, array($customer_id));

		foreach($arrOtherAddr as $arrval) {
			if($arrNew == $arrval) {
				// すでに同じ住所が登録されている
				$find_same = true;
			}
		}
		
		if(!$find_same) {
			$diff = true;
		}
	}
	
	// 新しいお届け先が登録済みのものと異なる場合は別のお届け先テーブルに登録する
	if($diff) {
		$sqlval = $arrNew;
		$sqlval['customer_id'] = $customer_id;
		$objQuery->insert("dtb_other_deliv", $sqlval);
	}
}

/* 購入情報を会員テーブルに登録する */
function lfSetCustomerPurchase($customer_id, $arrData, $objQuery) {
	$col = "first_buy_date, last_buy_date, buy_times, buy_total, point";
	$where = "customer_id = ?";
	$arrRet = $objQuery->select($col, "dtb_customer", $where, array($customer_id));
	$sqlval = $arrRet[0];
	
	if($sqlval['first_buy_date'] == "") {
		$sqlval['first_buy_date'] = "Now()";
	}
	$sqlval['last_buy_date'] = "Now()";
	$sqlval['buy_times']++;
	$sqlval['buy_total']+= $arrData['total'];
	$sqlval['point'] = ($sqlval['point'] + $arrData['add_point'] - $arrData['use_point']);
	
	// ポイントが不足している場合
	if($sqlval['point'] < 0) {
		$objQuery->rollback();
		sfDispSiteError(LACK_POINT);
	}
	
	$objQuery->update("dtb_customer", $sqlval, $where, array($customer_id));
}

// 在庫を減らす処理
function lfReduceStock($objQuery, $arrID, $quantity) {
	$where = "product_id = ? AND classcategory_id1 = ? AND classcategory_id2 = ?";
	$arrRet = $objQuery->select("stock, stock_unlimited", "dtb_products_class", $where, $arrID);
	
	// 売り切れエラー
	if(($arrRet[0]['stock_unlimited'] != '1' && $arrRet[0]['stock'] < $quantity) || $quantity == 0) {
		$objQuery->rollback();
		sfDispSiteError(SOLD_OUT, "", true);
	// 無制限の場合、在庫はNULL
	} elseif($arrRet[0]['stock_unlimited'] == '1') {
		$sqlval['stock'] = null;
		$objQuery->update("dtb_products_class", $sqlval, $where, $arrID);
	// 在庫を減らす
	} else {
		$sqlval['stock'] = ($arrRet[0]['stock'] - $quantity);
		if($sqlval['stock'] == "") {
			$sqlval['stock'] = '0';
		}		
		$objQuery->update("dtb_products_class", $sqlval, $where, $arrID);
	}
}

// GETの値をインサート用に整える
function lfGetInsParam($sqlVal){
	
	foreach($_GET as $key => $val){
		// カラムの存在チェック
		if(sfColumnExists("dtb_order", $key)) $sqlVal[$key] = $val;
	}
	
	return $sqlVal;
}

?>