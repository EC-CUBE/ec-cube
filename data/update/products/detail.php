<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");
require_once(DATA_PATH . "include/page_layout.inc");

class UC_Page {
	function UC_Page() {
		/** 必ず指定する **/
		global $arrSTATUS;
		$this->arrSTATUS = $arrSTATUS;
		global $arrSTATUS_IMAGE;
		$this->arrSTATUS_IMAGE = $arrSTATUS_IMAGE;
		global $arrDELIVERYDATE;
		$this->arrDELIVERYDATE = $arrDELIVERYDATE;
		global $arrRECOMMEND;
		$this->arrRECOMMEND = $arrRECOMMEND;
		session_cache_limiter('private-no-expire');
	}
}

ufDetailPHP();
exit;

function ufDetailPHP() {
	global $objPage;
	global $objView;
	global $objCustomer;
	global $objQuery;
	global $objUpFile;
	
	$objPage = new UC_Page();
	$objView = new SC_SiteView();
	$objCustomer = new SC_Customer();
	$objQuery = new SC_Query();
	
	// レイアウトデザインを取得
	$objPage = sfGetPageLayout($objPage, false, "products/detail.php");
	
	// パラメータ管理クラス
	$objFormParam = new SC_FormParam();
	// パラメータ情報の初期化
	lfInitParam();
	// POST値の取得
	$objFormParam->setParam($_POST);
	
	// ファイル管理クラス
	$objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);
	// ファイル情報の初期化
	lfInitFile();
	
	// 管理ページからの確認の場合は、非公開の商品も表示する。
	if($_GET['admin'] == 'on') {
		$where = "del_flg = 0";
	} else {
		$where = "del_flg = 0 AND status = 1";
	}
	
	if($_POST['mode'] != "") {
		$tmp_id = $_POST['product_id'];
	} else {
		$tmp_id = $_GET['product_id'];
	}
	
	// 値の正当性チェック
	if(!sfIsInt($_GET['product_id']) || !sfIsRecord("dtb_products", "product_id", $tmp_id, $where)) {
		sfDispSiteError(PRODUCT_NOT_FOUND);
	}
	// ログイン判定
	if($objCustomer->isLoginSuccess()) {
		//お気に入りボタン表示
		$objPage->tpl_login = true;
		
		$table = "dtb_customer_reading";
		$where = "customer_id = ? ";
		$arrval[] = $objCustomer->getValue('customer_id');
		//顧客の閲覧商品数
		$rpcnt = $objQuery->count($table, $where, $arrval);
	
		//閲覧数が設定数以下
		if ($rpcnt < CUSTOMER_READING_MAX){
			//閲覧履歴に新規追加
			lfRegistReadingData($tmp_id, $objCustomer->getValue('customer_id'));
		} else {
			//閲覧履歴の中で一番古いものを削除して新規追加
			$where = "customer_id = ? AND update_date = (SELECT MIN(update_date) FROM ".$table." WHERE customer_id = ? ) ";
			$arrval = array($objCustomer->getValue("customer_id"), $objCustomer->getValue("customer_id"));
			//削除
			$objQuery->delete($table, $where, $arrval);
			//追加
			lfRegistReadingData($tmp_id, $objCustomer->getValue('customer_id'));
		}
	}
	
	
	// 規格選択セレクトボックスの作成
	$objPage = lfMakeSelect($objPage, $tmp_id);
	
	// 商品IDをFORM内に保持する。
	$objPage->tpl_product_id = $tmp_id;
	
	switch($_POST['mode']) {
	case 'cart':
		// 入力値の変換
		$objFormParam->convParam();
		$objPage->arrErr = lfCheckError();
		if(count($objPage->arrErr) == 0) {
			$objCartSess = new SC_CartSession();
			$classcategory_id1 = $_POST['classcategory_id1'];
			$classcategory_id2 = $_POST['classcategory_id2'];
					
			// 規格1が設定されていない場合
			if(!$objPage->tpl_classcat_find1) {
				$classcategory_id1 = '0';
			}
			
			// 規格2が設定されていない場合
			if(!$objPage->tpl_classcat_find2) {
				$classcategory_id2 = '0';
			}
	
			$objCartSess->addProduct(array($_POST['product_id'], $classcategory_id1, $classcategory_id2), $objFormParam->getValue('quantity'));
			header("Location: " . URL_CART_TOP);
	
			exit;
		}
		break;
			
	default:
		break;
	}
	
	$objQuery = new SC_Query();
	// DBから商品情報を取得する。
	$arrRet = $objQuery->select("*", "vw_products_allclass_detail AS alldtl", "product_id = ?", array($tmp_id));
	$objPage->arrProduct = $arrRet[0];
	
	// 購入制限数を取得
	if($objPage->arrProduct['sale_unlimited'] == 1 || $objPage->arrProduct['sale_limit'] > SALE_LIMIT_MAX) {
	  $objPage->tpl_sale_limit = SALE_LIMIT_MAX;
	} else {
	  $objPage->tpl_sale_limit = $objPage->arrProduct['sale_limit'];
	}
	
	// サブタイトルを取得
	$arrFirstCat = GetFirstCat($arrRet[0]['category_id']);
	$tpl_subtitle = $arrFirstCat['name'];
	$objPage->tpl_subtitle = $tpl_subtitle;
	
	// DBからのデータを引き継ぐ
	$objUpFile->setDBFileList($objPage->arrProduct);
	// ファイル表示用配列を渡す
	$objPage->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
	// 支払方法の取得
	$objPage->arrPayment = lfGetPayment();
	// 入力情報を渡す
	$objPage->arrForm = $objFormParam->getFormParamList();
	//レビュー情報の取得
	$objPage->arrReview = lfGetReviewData($tmp_id);
	// タイトルに商品名を入れる
	$objPage->tpl_title = "商品詳細 ". $objPage->arrProduct["name"];
	//オススメ商品情報表示
	$objPage->arrRecommend = lfPreGetRecommendProducts($tmp_id);
	//この商品を買った人はこんな商品も買っています
	$objPage->arrRelateProducts = lfGetRelateProducts($tmp_id);
	
	$objView->assignobj($objPage);
	$objView->display(SITE_FRAME);
}
//-----------------------------------------------------------------------------------------------------------------------------------
?>