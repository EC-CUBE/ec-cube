<?php
require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	var $arrAddr;
	function LC_Page() {
		$this->tpl_mainpage = 'shopping/deliv.tpl';
		$this->tpl_css = '/css/layout/shopping/index.css';
		global $arrPref;
		$this->arrPref = $arrPref;
		$this->tpl_title = "お届け先指定";		// タイトル

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
$objCustomer = new SC_Customer();
// クッキー管理クラス
$objCookie = new SC_Cookie(COOKIE_EXPIRE);
// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
// POST値の取得
$objFormParam->setParam($_POST);

$objLoginFormParam = new SC_FormParam();	// ログインフォーム用
lfInitLoginFormParam();						// 初期設定
$objLoginFormParam->setParam($_POST);		// POST値の取得

// ユーザユニークIDの取得と購入状態の正当性をチェック
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);
$objPage->tpl_uniqid = $uniqid;

// ログインチェック
if($_POST['mode'] != 'login' && !$objCustomer->isLoginSuccess()) {
	// 不正アクセスとみなす
	sfDispSiteError(CUSTOMER_ERROR);
}

switch($_POST['mode']) {
case 'login':
	$objLoginFormParam->toLower('login_email');
	$objPage->arrErr = $objLoginFormParam->checkError();
	$arrForm =  $objLoginFormParam->getHashArray();
	// クッキー保存判定
	if($arrForm['login_memory'] == "1" && $arrForm['login_email'] != "") {
		$objCookie->setCookie('login_email', $_POST['login_email']);
	} else {
		$objCookie->setCookie('login_email', '');
	}

	if(count($objPage->arrErr) == 0) {
		// ログイン判定
		if(!$objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'])) {
			// 仮登録の判定
			$objQuery = new SC_Query;
			$where = "email = ? AND status = 1 AND delete = 0";
			$ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email']));
			
			if($ret > 0) {
				sfDispSiteError(TEMP_LOGIN_ERROR);
			} else {
				sfDispSiteError(SITE_LOGIN_ERROR);
			}
		} 
	} else {
		// ログインページに戻る
		header("Location: " . URL_SHOP_TOP);
		exit;	
	}
	break;
// 削除
case 'delete':
	if (sfIsInt($_POST['other_deliv_id'])) {
		$objQuery = new SC_Query();
		$where = "other_deliv_id = ?";
		$arrRet = $objQuery->delete("dtb_other_deliv", $where, array($_POST['other_deliv_id']));
		$objFormParam->setValue('select_addr_id', '');
	}
	break;
// 会員登録住所に送る
case 'customer_addr':
	// お届け先がチェックされている場合には更新処理を行う
	if ($_POST['deli'] != "") {
		// 会員情報の住所を受注一時テーブルに書き込む
		lfRegistDelivData($uniqid, $objCustomer);
		// 正常に登録されたことを記録しておく
		$objSiteSess->setRegistFlag();
		// お支払い方法選択ページへ移動
		header("Location: " . URL_SHOP_PAYMENT);
		exit;
	}else{
		// エラーを返す
		$arrErr['deli'] = '※ お届け先を選択してください。';
	}
	break;
	
// 登録済みの別のお届け先に送る
case 'other_addr':
	// お届け先がチェックされている場合には更新処理を行う
	if ($_POST['deli'] != "") {
		if (sfIsInt($_POST['other_deliv_id'])) {
			// 登録済みの別のお届け先を受注一時テーブルに書き込む
			lfRegistOtherDelivData($uniqid, $objCustomer, $_POST['other_deliv_id']);
			// 正常に登録されたことを記録しておく
			$objSiteSess->setRegistFlag();
			// お支払い方法選択ページへ移動
			header("Location: " . URL_SHOP_PAYMENT);
			exit;
		}
	}else{
		// エラーを返す
		$arrErr['deli'] = '※ お届け先を選択してください。';
	}
	break;

/*
// 別のお届け先を指定
case 'new_addr':
	// 入力値の変換
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($arrRet);
	// 入力エラーなし
	if(count($objPage->arrErr) == 0) {
		// DBへお届け先を登録
		lfRegistNewAddrData($uniqid, $objCustomer);
		// 正常に登録されたことを記録しておく
		$objSiteSess->setRegistFlag();
		// お支払い方法選択ページへ移動
		header("Location: " . URL_SHOP_PAYMENT);
		exit;		
	}
	break;
*/

// 前のページに戻る
case 'return':
	// 確認ページへ移動
	header("Location: " . URL_CART_TOP);
	exit;
	break;
default:
	$objQuery = new SC_Query();
	$where = "order_temp_id = ?";
	$arrRet = $objQuery->select("*", "dtb_order_temp", $where, array($uniqid));
	$objFormParam->setParam($arrRet[0]);
	break;
}

/** 表示処理 **/

// 会員登録住所の取得
$col = "name01, name02, pref, addr01, addr02";
$where = "customer_id = ?";
$objQuery = new SC_Query();
$arrCustomerAddr = $objQuery->select($col, "dtb_customer", $where, array($_SESSION['customer']['customer_id']));
// 別のお届け先住所の取得
$col = "other_deliv_id, name01, name02, pref, addr01, addr02";
$objQuery->setorder("other_deliv_id DESC");
$objOtherAddr = $objQuery->select($col, "dtb_other_deliv", $where, array($_SESSION['customer']['customer_id']));
$objPage->arrAddr = $arrCustomerAddr;
$cnt = 1;
foreach($objOtherAddr as $val) {
	$objPage->arrAddr[$cnt] = $val;
	$cnt++;
}

// 入力値の取得
$objPage->arrForm = $objFormParam->getFormParamList();
$objPage->arrErr = $arrErr;
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//--------------------------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("お名前1", "deliv_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("お名前2", "deliv_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ1", "deliv_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ2", "deliv_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("郵便番号1", "deliv_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("郵便番号2", "deliv_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("都道府県", "deliv_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("住所1", "deliv_addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("住所2", "deliv_addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("電話番号1", "deliv_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号2", "deliv_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号3", "deliv_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
}

function lfInitLoginFormParam() {
	global $objLoginFormParam;
	$objLoginFormParam->addParam("記憶する", "login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objLoginFormParam->addParam("メールアドレス", "login_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objLoginFormParam->addParam("パスワード", "login_pass", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
}

/* DBへデータの登録 */
function lfRegistNewAddrData($uniqid, $objCustomer) {
	global $objFormParam;
	$arrRet = $objFormParam->getHashArray();
	$sqlval = $objFormParam->getDbArray();
	// 登録データの作成
	$sqlval['deliv_check'] = '1';
	$sqlval['order_temp_id'] = $uniqid;
	$sqlval['update_date'] = 'Now()';
	$sqlval['customer_id'] = $objCustomer->getValue('customer_id');
	$sqlval['order_birth'] = $objCustomer->getValue('birth');
	
	sfRegistTempOrder($uniqid, $sqlval);
}

/* 会員情報の住所を一時受注テーブルへ */
function lfRegistDelivData($uniqid, $objCustomer) {
	// 登録データの作成
	$sqlval['order_temp_id'] = $uniqid;
	$sqlval['update_date'] = 'Now()';
	$sqlval['customer_id'] = $objCustomer->getValue('customer_id');
    $sqlval['deliv_check'] = '1';
	$sqlval['deliv_name01'] = $objCustomer->getValue('name01');
    $sqlval['deliv_name02'] = $objCustomer->getValue('name02');
    $sqlval['deliv_kana01'] = $objCustomer->getValue('kana01');
    $sqlval['deliv_kana02'] = $objCustomer->getValue('kana02');
    $sqlval['deliv_zip01'] = $objCustomer->getValue('zip01');
    $sqlval['deliv_zip02'] = $objCustomer->getValue('zip02');
    $sqlval['deliv_pref'] = $objCustomer->getValue('pref');
    $sqlval['deliv_addr01'] = $objCustomer->getValue('addr01');
    $sqlval['deliv_addr02'] = $objCustomer->getValue('addr02');
    $sqlval['deliv_tel01'] = $objCustomer->getValue('tel01');
    $sqlval['deliv_tel02'] = $objCustomer->getValue('tel02');
	$sqlval['deliv_tel03'] = $objCustomer->getValue('tel03');

    $sqlval['deliv_fax01'] = $objCustomer->getValue('fax01');
    $sqlval['deliv_fax02'] = $objCustomer->getValue('fax02');
	$sqlval['deliv_fax03'] = $objCustomer->getValue('fax03');

	sfRegistTempOrder($uniqid, $sqlval);
}

/* 別のお届け先住所を一時受注テーブルへ */
function lfRegistOtherDelivData($uniqid, $objCustomer, $other_deliv_id) {
	// 登録データの作成
	$sqlval['order_temp_id'] = $uniqid;
	$sqlval['update_date'] = 'Now()';
	$sqlval['customer_id'] = $objCustomer->getValue('customer_id');
	$sqlval['order_birth'] = $objCustomer->getValue('birth');
		
	$objQuery = new SC_Query();
	$where = "other_deliv_id = ?";
	$arrRet = $objQuery->select("*", "dtb_other_deliv", $where, array($other_deliv_id));
	
	$sqlval['deliv_check'] = '1';
    $sqlval['deliv_name01'] = $arrRet[0]['name01'];
    $sqlval['deliv_name02'] = $arrRet[0]['name02'];
    $sqlval['deliv_kana01'] = $arrRet[0]['kana01'];
    $sqlval['deliv_kana02'] = $arrRet[0]['kana02'];
    $sqlval['deliv_zip01'] = $arrRet[0]['zip01'];
    $sqlval['deliv_zip02'] = $arrRet[0]['zip02'];
    $sqlval['deliv_pref'] = $arrRet[0]['pref'];
    $sqlval['deliv_addr01'] = $arrRet[0]['addr01'];
    $sqlval['deliv_addr02'] = $arrRet[0]['addr02'];
    $sqlval['deliv_tel01'] = $arrRet[0]['tel01'];
    $sqlval['deliv_tel02'] = $arrRet[0]['tel02'];
	$sqlval['deliv_tel03'] = $arrRet[0]['tel03'];
	sfRegistTempOrder($uniqid, $sqlval);
}

/* 入力内容のチェック */
function lfCheckError() {
	global $objFormParam;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	// 複数項目チェック
	if ($_POST['mode'] == 'login'){
	$objErr->doFunc(array("メールアドレス", "login_email", STEXT_LEN), array("EXIST_CHECK"));
	$objErr->doFunc(array("パスワード", "login_pass", STEXT_LEN), array("EXIST_CHECK"));
	}
	$objErr->doFunc(array("TEL", "deliv_tel01", "deliv_tel02", "deliv_tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
	return $objErr->arrErr;
}
?>