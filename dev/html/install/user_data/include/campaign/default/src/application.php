<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

//---- ページ表示クラス
class LC_Page {
	
	function LC_Page() {
		$this->tpl_mainpage = TEMPLATE_DIR . '/campaign/application.tpl';
		/*
		 session_start時のno-cacheヘッダーを抑制することで
		 「戻る」ボタン使用時の有効期限切れ表示を抑制する。
		 private-no-expire:クライアントのキャッシュを許可する。
		*/
		session_cache_limiter('private-no-expire');	
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView(false);
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();
$objCampaignSess = new SC_CampaignSession();
// クッキー管理クラス
$objCookie = new SC_Cookie(COOKIE_EXPIRE);

$objLoginFormParam = new SC_FormParam();	// ログインフォーム用
lfInitLoginFormParam();						// 初期設定
$objLoginFormParam->setParam($_POST);		// POST値の取得

// ディレクトリ名を取得
$dir_name = dirname($_SERVER['PHP_SELF']);
$arrDir = split('/', $dir_name);
$dir_name = $arrDir[count($arrDir) -1];

/* セッションにキャンペーンデータを書き込む */
// キャンペーンからの遷移という情報を保持
$objCampaignSess->setIsCampaign();
// キャンペーンIDを保持
$campaign_id = $objQuery->get("dtb_campaign", "campaign_id", "directory_name = ? AND del_flg = 0", array($dir_name));
$objCampaignSess->setCampaignId($campaign_id);
// キャンペーンディレクトリ名を保持
$objCampaignSess->setCampaignDir($dir_name);

// キャンペーンが開催中かをチェック
if(lfCheckActive($dir_name)) {
	$status = CAMPAIGN_TEMPLATE_ACTIVE;
	$objPage->is_active = true;
} else {
	$status = CAMPAIGN_TEMPLATE_END;
	$objPage->is_active = false;
}

switch($_POST['mode']) {
// ログインチェック
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
			$where = "email = ? AND status = 1 AND del_flg = 0";
			$ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email']));
			
			if($ret > 0) {
				sfDispSiteError(TEMP_LOGIN_ERROR);
			} else {
				sfDispSiteError(SITE_LOGIN_ERROR);
			}
		} else {
			// 重複申込チェック
			$orverlapping_flg = $objQuery->get("dtb_campaign", "orverlapping_flg", "campaign_id = ?", array($objCampaignSess->getCampaignId()));

			if($orverlapping_flg) {
				if(lfOverlappingCheck($objCustomer->getValue('customer_id'))) {
					$objPage->arrErr['login_email'] = "※ 複数回ご応募することは出来ません。";
				}
			}
	
			if(count($objPage->arrErr) == 0) {
				// 申込情報を登録
				lfRegistCampaignOrder($objCustomer->getValue('customer_id'));
				// 完了ページへリダイレクト
				header("location: ". CAMPAIGN_URL . "$dir_name/complete.php");
			}
		}
	}
	break;
default :
	break;
}
// 入力情報を渡す
$objPage->arrForm = $_POST;
$objPage->dir_name = $dir_name;
$objPage->tpl_dir_name = CAMPAIGN_TEMPLATE_PATH . $dir_name  . "/" . $status;

//----　ページ表示
$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);


//---------------------------------------------------------------------------------------------------------------------------------------------------------

/* 
 * 関数名：lfInitLoginFormParam()
 * 説明　：ログインフォームを初期化
 * 戻り値：無し
 */
function lfInitLoginFormParam() {
	global $objLoginFormParam;
	$objLoginFormParam->addParam("記憶する", "login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objLoginFormParam->addParam("メールアドレス", "login_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objLoginFormParam->addParam("パスワード", "login_pass", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
}

/* 
 * 関数名：lfCheckActive()
 * 引数1 ：ディレクトリ名
 * 説明　：キャンペーン中かチェック
 * 戻り値：キャンペーン中なら true 終了なら false
 */
function lfCheckActive($directory_name) {
	
	global $objQuery;
	$is_active = false;
	
	$col = "limit_count, total_count, start_date, end_date";
	$arrRet = $objQuery->select($col, "dtb_campaign", "directory_name = ? AND del_flg = 0", array($directory_name));

	// 開始日時・停止日時を成型
	$start_date = (date("YmdHis", strtotime($arrRet[0]['start_date'])));
	$end_date = (date("YmdHis", strtotime($arrRet[0]['end_date'])));
	$now_date = (date("YmdHis"));

	// キャンペーンが開催期間で、かつ申込制限内である
	if($now_date > $start_date && $now_date < $end_date
			&& ($arrRet[0]['limit_count'] > $arrRet[0]['total_count'] || $arrRet[0]['limit_count'] < 1)) {
		$is_active = true;
	}
		
	return $is_active;
}

/* 
 * 関数名：lfRegistCampaignOrder()
 * 説明　：キャンペーン受注情報を保存
 * 引数1 ：顧客ID
 * 戻り値：無し
 */
function lfRegistCampaignOrder($customer_id) {

	global $objQuery;
	global $objCampaignSess;
	$campaign_id = $objCampaignSess->getCampaignId();

	// 受注データを取得
	$cols = "
			customer_id,
			name01 as order_name01,
			name02 as order_name02,
			kana01 as order_kana01,
			kana02 as order_kana02,
			zip01 as order_zip01,
			zip02 as order_zip02,
			pref as order_pref,
			addr01 as order_addr01,
			addr02 as order_addr02,
			email as order_email,
			tel01 as order_tel01,
			tel02 as order_tel02,
			tel03 as order_tel03,
			fax01 as order_fax01,
			fax02 as order_fax02,
			fax03 as order_fax03,
			sex as order_sex,
			job as order_job,
			birth as order_birth
			";
			
	$arrCustomer = $objQuery->select($cols, "dtb_customer", "customer_id = ?", array($customer_id)); 

	$sqlval = $arrCustomer[0];
	$sqlval['campaign_id'] = $campaign_id;
    $sqlval['create_date'] = 'now()';
		
	// INSERTの実行
	$objQuery->insert("dtb_campaign_order", $sqlval);
	
	// 申し込み数の更新
	$total_count = $objQuery->get("dtb_campaign", "total_count", "campaign_id = ?", array($campaign_id));
	$arrCampaign['total_count'] = $total_count += 1;
	$objQuery->update("dtb_campaign", $arrCampaign, "campaign_id = ?", array($campaign_id));
	
}

/* 
 * 関数名：lfOverlappingCheck()
 * 説明　：重複応募チェック
 * 引数1 ：顧客ID
 * 戻り値：フラグ (重複があったら true 重複がなかったら false)
 */
function lfOverlappingCheck($customer_id) {
	
	global $objQuery;
	
	$count = $objQuery->count("dtb_campaign_order", "customer_id = ?", array($customer_id));
	if($count > 0) {
		return true;
	}
	
	return false;
}
?>