<?php
require_once("../require.php");

$arrJPO_INFO['10'] = "一括払い";
$arrJPO_INFO['21'] = "ボーナス一括払い";
$arrJPO_INFO['80'] = "リボ払い";
$arrJPO_INFO['61C02'] = "分割2回払い";
$arrJPO_INFO['61C03'] = "分割3回払い";
$arrJPO_INFO['61C05'] = "分割5回払い";
$arrJPO_INFO['61C06'] = "分割6回払い";
$arrJPO_INFO['61C10'] = "分割10回払い";
$arrJPO_INFO['61C12'] = "分割12回払い";
$arrJPO_INFO['61C15'] = "分割15回払い";

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = '/css/layout/shopping/card.css';	// メインCSSパス
		/** 必ず指定する **/
		$this->tpl_mainpage = 'shopping/card.tpl';			// メインテンプレート
		global $arrJPO_INFO;
		$this->arrJPO_INFO = $arrJPO_INFO;
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
$objSiteInfo = new SC_SiteInfo();
$arrInfo = $objSiteInfo->data;

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
// POST値の取得
$objFormParam->setParam($_POST);

// アクセスの正当性の判定
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

switch($_POST['mode']) {
// 登録
case 'regist':
	// 入力値の変換
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($arrRet);
	// 入力エラーなしの場合
	if(count($objPage->arrErr) == 0) {
		// カート集計処理
		$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
		// 一時受注テーブルの読込
		$arrData = sfGetOrderTemp($uniqid);
		// カート集計を元に最終計算
		$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);
		
		// カードの認証を行う
		$arrVal = $objFormParam->getHashArray();
		$card_no = $arrVal['card_no01'].$arrVal['card_no02'].$arrVal['card_no03'].$arrVal['card_no04'];
		$card_exp = $arrVal['card_month']. "/" . $arrVal['card_year']; // MM/DD
		$result = sfGetAuthonlyResult(CGI_DIR, CGI_FILE, $arrVal['name01'], $arrVal['name02'], $card_no, $card_exp, $arrData['payment_total'], $uniqid, $arrVal['jpo_info']);
		
		// 応答内容の記録
		$sqlval['credit_result'] = $result['action-code'];
		$sqlval['credit_msg'] = $result['aux-msg'].$result['MErrMsg'];
		$objQuery = new SC_Query();
		$objQuery->update("dtb_order_temp", $sqlval, "order_temp_id = ?", array($uniqid));
				
		// 与信処理成功の場合
		if($result['action-code'] == '000') {
			// 正常に登録されたことを記録しておく
			$objSiteSess->setRegistFlag();
			// 処理完了ページへ
			header("Location: " . URL_SHOP_COMPLETE);
		} else {
			switch($result['action-code']) {
			case '115':
				$objPage->tpl_error = "※ カードの有効期限が切れています。";
				break;
			case '212':
				$objPage->tpl_error = "※ カード番号に誤りがあります。";
				break;
			case '100':
				$objPage->tpl_error = "※ カード会社でお取引が承認されませんでした。";
				break;
			default:
				$objPage->tpl_error = "※ クレジットカードの照合に失敗しました。";
				break;
			}
		}
	}
	break;
// 前のページに戻る
case 'return':
	// 正常に登録されたことを記録しておく
	$objSiteSess->setRegistFlag();
	// 確認ページへ移動
	header("Location: " . URL_SHOP_CONFIRM);
	exit;
	break;
}

$objDate = new SC_Date();
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(RELEASE_YEAR + CREDIT_ADD_YEAR);
$objPage->arrYear = $objDate->getZeroYear();
$objPage->arrMonth = $objDate->getZeroMonth();

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("カード番号1", "card_no01", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("カード番号2", "card_no02", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("カード番号3", "card_no03", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("カード番号4", "card_no04", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("カード期限年", "card_year", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("カード期限月", "card_month", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("姓", "card_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
	$objFormParam->addParam("名", "card_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
	$objFormParam->addParam("お支払い方法", "jpo_info", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALNUM_CHECK"));
}

/* 入力内容のチェック */
function lfCheckError() {
	global $objFormParam;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	return $objErr->arrErr;
}

?>
