<?php

require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_css = '/css/layout/shopping/pay.css';
		$this->tpl_mainpage = 'shopping/payment.tpl';
		$this->tpl_onload = 'fnCheckInputPoint();';
		$this->tpl_title = "お支払方法・お届け時間等の指定";
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
$objCustomer = new SC_Customer();
$objSiteInfo = new SC_SiteInfo();
$arrInfo = $objSiteInfo->data;

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
// POST値の取得
$objFormParam->setParam($_POST);

// ユーザユニークIDの取得と購入状態の正当性をチェック
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);
// ユニークIDを引き継ぐ
$objPage->tpl_uniqid = $uniqid;

// 会員ログインチェック
if($objCustomer->isLoginSuccess()) {
	$objPage->tpl_login = '1';
	$objPage->tpl_user_point = $objCustomer->getValue('point');
}

// 金額の取得
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
$objPage->arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

switch($_POST['mode']) {
case 'confirm':
	// 入力値の変換
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($objPage->arrData );
	// 入力エラーなし
	if(count($objPage->arrErr) == 0) {
		// DBへのデータ登録
		lfRegistData($uniqid);
		// 正常に登録されたことを記録しておく
		$objSiteSess->setRegistFlag();
		// 確認ページへ移動
		header("Location: " . URL_SHOP_CONFIRM);
		exit;
	}else{
		// ユーザユニークIDの取得
		$uniqid = $objSiteSess->getUniqId();
		// 受注一時テーブルからの情報を格納
		lfSetOrderTempData($uniqid);
	}
	break;
// 前のページに戻る
case 'return':
	// 非会員の場合
	// 正常な推移であることを記録しておく
	$objSiteSess->setRegistFlag();
	header("Location: " . URL_SHOP_TOP);
	exit;
	break;
// 支払い方法が変更された場合
case 'payment':
	// ここのbreakは、意味があるので外さないで下さい。
	break;
default:
	// 受注一時テーブルからの情報を格納
	lfSetOrderTempData($uniqid);
	break;
}

// 店舗情報の取得
$arrInfo = $objSiteInfo->data;
// 購入金額の取得得
$total_pretax = $objCartSess->getAllProductsTotal($arrInfo);
// 支払い方法の取得
$objPage->arrPayment = lfGetPayment($total_pretax);
// 配送時間の取得
$arrRet = sfGetDelivTime($objFormParam->getValue('payment_id'));
$objPage->arrDelivTime = sfArrKeyValue($arrRet, 'time_id', 'time');
$objPage->objCustomer = $objCustomer;
//　配送日一覧の取得
$objPage->arrDelivDate = lfGetDelivDate();

$objPage->arrForm = $objFormParam->getFormParamList();

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//--------------------------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("お支払い方法", "payment_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("ポイント", "use_point", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK", "ZERO_START"));
	$objFormParam->addParam("配達時間", "deliv_time_id", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("ご質問", "message", LTEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("ポイントを使用する", "point_check", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), '2');
	$objFormParam->addParam("配達日", "deliv_date", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
}

function lfGetPayment($total_pretax) {
	$objQuery = new SC_Query();
	$objQuery->setorder("fix,rank DESC");
	//削除されていない支払方法を取得
	$arrRet = $objQuery->select("payment_id, payment_method, rule, upper_rule, note, payment_image", "dtb_payment", "delete = 0 AND deliv_id IN (SELECT deliv_id FROM dtb_deliv) ");
	//利用条件から支払可能方法を判定
	foreach($arrRet as $data) {
		//下限と上限が設定されている
		if($data['rule'] > 0 && $data['upper_rule'] > 0) {
			if($data['rule'] <= $total_pretax && $data['upper_rule'] >= $total_pretax) {
				$arrPayment[] = $data;
			}
		//下限のみ設定されている
		} elseif($data['rule'] > 0) {	
			if($data['rule'] <= $total_pretax) {
				$arrPayment[] = $data;
			}
		//上限のみ設定されている
		} elseif($data['upper_rule'] > 0) {
			if($data['upper_rule'] >= $total_pretax) {
				$arrPayment[] = $data;
			}
		//設定なし
		} else {
			$arrPayment[] = $data;
		}	
	}
	return $arrPayment;	
}

/* 入力内容のチェック */
function lfCheckError($arrData) {
	global $objFormParam;
	global $objCustomer;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	if($_POST['point_check'] == '1') {
		$objErr->doFunc(array("ポイントを使用する", "point_check"), array("EXIST_CHECK"));
		$objErr->doFunc(array("ポイント", "use_point"), array("EXIST_CHECK"));
		$max_point = $objCustomer->getValue('point');
		if($max_point == "") {
			$max_point = 0;
		}
		if($arrRet['use_point'] > $max_point) {
			$objErr->arrErr['use_point'] = "※ ご利用ポイントが所持ポイントを超えています。<br />";
		}
		if(($arrRet['use_point'] * POINT_VALUE) > $arrData['subtotal']) {
			$objErr->arrErr['use_point'] = "※ ご利用ポイントがご購入金額を超えています。<br />";
		}
	}
	return $objErr->arrErr;
}

/* 支払い方法文字列の取得 */
function lfGetPaymentInfo($payment_id) {
	$objQuery = new SC_Query();
	$where = "payment_id = ?";
	$arrRet = $objQuery->select("payment_method, charge", "dtb_payment", $where, array($payment_id));
	return (array($arrRet[0]['payment_method'], $arrRet[0]['charge']));
}

/* 配送時間文字列の取得 */
function lfGetDelivTimeInfo($time_id) {
	$objQuery = new SC_Query();
	$where = "time_id = ?";
	$arrRet = $objQuery->select("deliv_id, time", "dtb_delivtime", $where, array($time_id));
	return (array($arrRet[0]['deliv_id'], $arrRet[0]['time']));
}

/* DBへデータの登録 */
function lfRegistData($uniqid) {
	global $objFormParam;
	$arrRet = $objFormParam->getHashArray();
	$sqlval = $objFormParam->getDbArray();
	// 登録データの作成
	$sqlval['order_temp_id'] = $uniqid;
	$sqlval['update_date'] = 'Now()';
	
	if($sqlval['payment_id'] != "") {
		list($sqlval['payment_method'], $sqlval['charge']) = lfGetPaymentInfo($sqlval['payment_id']);
	} else {
		$sqlval['payment_id'] = '0';
		$sqlval['payment_method'] = "";
	}
	
	if($sqlval['deliv_time_id'] != "") {
		list($sqlval['deliv_id'], $sqlval['deliv_time']) = lfGetDelivTimeInfo($sqlval['deliv_time_id']);
	} else {
		$sqlval['deliv_time_id'] = '0';
		$sqlval['deliv_id'] = '0';
		$sqlval['deliv_time'] = "";
	}
	
	// 使用ポイントの設定
	if($sqlval['point_check'] != '1') {
		$sqlval['use_point'] = 0;
	}
	
	sfRegistTempOrder($uniqid, $sqlval);
}

/* 配達日一覧を取得する */
function lfGetDelivDate() {
	$objCartSess = new SC_CartSession();
	$objQuery = new SC_Query();
	// 商品IDの取得
	$max = $objCartSess->getMax();
	for($i = 1; $i <= $max; $i++) {
		if($_SESSION[$objCartSess->key][$i]['id'][0] != "") {
			$arrID['product_id'][$i] = $_SESSION[$objCartSess->key][$i]['id'][0];
		}
	}
	if(count($arrID['product_id']) > 0) {
		$id = implode(",", $arrID['product_id']);
		//商品から発送目安の取得
		$deliv_date = $objQuery->get("dtb_products", "MAX(deliv_date_id)", "product_id IN (".$id.")");
		//発送目安
		switch($deliv_date) {
		//即日発送
		case '1':
			$start_day = 1;
			break;
		//1-2日後
		case '2':
			$start_day = 3;
			break;
		//3-4日後
		case '3':
			/* 
				2006/06/13 Nakagawa
				トーカ堂様運用上3-4日後は、現在日+6日とする。
			*/
			$start_day = 6;
			break;
		//1週間以内
		case '4':
			$start_day = 8;
			break;
		//2週間以内
		case '5':
			$start_day = 15;
			break;
		//3週間以内
		case '6':
			$start_day = 22;
			break;
		//1ヶ月以内
		case '7':
			$start_day = 32;
			break;
		//2ヶ月以降
		case '8':
			$start_day = 62;			
			break;
		//お取り寄せ(商品入荷後)
		case '9':
			$start_day = "";
			break;
		default:
			//お届け日が設定されていない場合
			$start_day = "";
			break;
		}
		//配達可能日のスタート値から、配達日の配列を取得する
		$arrDelivDate = lfGetDateArray($start_day, DELIV_DATE_END_MAX);
	}
	return $arrDelivDate;
}

//配達可能日のスタート値から、配達日の配列を取得する
function lfGetDateArray($start_day, $end_day) {
	global $arrWDAY;
	//配達可能日のスタート値がセットされていれば
	if($start_day >= 1) {
		$now_time = time();
		$max_day = $start_day + $end_day;
		// 集計
		for ($i = $start_day; $i < $max_day; $i++) {
			// 基本時間から日数を追加していく
			$tmp_time = $now_time + ($i * 24 * 3600);
			list($y, $m, $d, $w) = split(" ", date("y m d w", $tmp_time));	
			$val = sprintf("%02d/%02d/%02d(%s)", $y, $m, $d, $arrWDAY[$w]);
			$arrDate[$val] = $val;
		}
	} else {
		$arrDate = false;
	}
	return $arrDate;
}

//一時受注テーブルからの情報を格納する
function lfSetOrderTempData($uniqid) {
	global $objQuery;
	global $objFormParam;
	
	$objQuery = new SC_Query();
	$col = "payment_id, use_point, deliv_time_id, message, point_check, deliv_date";
	$from = "dtb_order_temp";
	$where = "order_temp_id = ?";
	$arrRet = $objQuery->select($col, $from, $where, array($uniqid));
	// DB値の取得
	$objFormParam->setParam($arrRet[0]);
	return $objFormParam;
}


?>