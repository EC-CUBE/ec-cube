<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 */
require_once("../require.php");
require_once(DATA_PATH . "include/page_layout.inc");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		global $arrSTATUS;
		$this->arrSTATUS = $arrSTATUS;
		global $arrSTATUS_IMAGE;
		$this->arrSTATUS_IMAGE = $arrSTATUS_IMAGE;
		global $arrDELIVERYDATE;
		$this->arrDELIVERYDATE = $arrDELIVERYDATE;
		global $arrRECOMMEND;
		$this->arrRECOMMEND = $arrRECOMMEND;
		
		$this->tpl_mainpage="products/detail.tpl";
		
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
	sfDispSiteError(PRODUCT_NOT_FOUND, "", false, "", true);
}
// ログイン判定
if($objCustomer->isLoginSuccess()) {
	//お気に入りボタン表示
	$objPage->tpl_login = true;

/* 閲覧ログ機能は現在未使用
	
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
		$oldsql = "SELECT MIN(update_date) FROM ".$table." WHERE customer_id = ?";
		$old = $objQuery->getone($oldsql, array($objCustomer->getValue("customer_id")));
		$where = "customer_id = ? AND update_date = ? ";
		$arrval = array($objCustomer->getValue("customer_id"), $old);
		//削除
		$objQuery->delete($table, $where, $arrval);
		//追加
		lfRegistReadingData($tmp_id, $objCustomer->getValue('customer_id'));
	}
*/
}


// 規格選択セレクトボックスの作成
$objPage = lfMakeSelect($objPage, $tmp_id);

// 商品IDをFORM内に保持する。
$objPage->tpl_product_id = $tmp_id;

switch($_POST['mode']) {
case 'select':
	// 規格1が設定されている場合
	if($objPage->tpl_classcat_find1) {
		// templateの変更
		$objPage->tpl_mainpage = "products/select_find1.tpl";
		break;
	}

case 'select2':
	$objPage->arrErr = lfCheckError();

	// 規格1が設定されている場合
	if($objPage->tpl_classcat_find1 and $objPage->arrErr['classcategory_id1']) {
		// templateの変更
		$objPage->tpl_mainpage = "products/select_find1.tpl";
		break;
	}

	// 規格2が設定されている場合
	if($objPage->tpl_classcat_find2) {
		$objPage->arrErr = array();

		$objPage->tpl_mainpage = "products/select_find2.tpl";
		break;
	}

case 'selectItem':
	$objPage->arrErr = lfCheckError();

	// 規格1が設定されている場合
	if($objPage->tpl_classcat_find2 and $objPage->arrErr['classcategory_id2']) {
		// templateの変更
		$objPage->tpl_mainpage = "products/select_find2.tpl";
		break;
	}
	// 商品数の選択を行う
	$objPage->tpl_mainpage = "products/select_item.tpl";
	break;

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
		
		$objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
		$objCartSess->addProduct(array($_POST['product_id'], $classcategory_id1, $classcategory_id2), $objFormParam->getValue('quantity'));

		header("Location: " . gfAddSessionId(MOBILE_URL_CART_TOP));

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

// 商品コードの取得
$code_sql = "SELECT product_code FROM dtb_products_class AS prdcls WHERE prdcls.product_id = ? GROUP BY product_code ORDER BY product_code";
$arrProductCode = $objQuery->getall($code_sql, array($tmp_id));
$arrProductCode = sfswaparray($arrProductCode);
$objPage->arrProductCode = $arrProductCode["product_code"];

// 購入制限数を取得
if($objPage->arrProduct['sale_unlimited'] == 1 || $objPage->arrProduct['sale_limit'] > SALE_LIMIT_MAX) {
  $objPage->tpl_sale_limit = SALE_LIMIT_MAX;
} else {
  $objPage->tpl_sale_limit = $objPage->arrProduct['sale_limit'];
}

// サブタイトルを取得
$arrFirstCat = sfGetFirstCat($arrRet[0]['category_id']);
$tpl_subtitle = $arrFirstCat['name'];
$objPage->tpl_subtitle = $tpl_subtitle;

// DBからのデータを引き継ぐ
$objUpFile->setDBFileList($objPage->arrProduct);
// ファイル表示用配列を渡す
$objPage->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL, true);
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

// 拡大画像のウィンドウサイズをセット
list($large_width, $large_height) = getimagesize(IMAGE_SAVE_DIR . basename($objPage->arrFile["main_large_image"]["filepath"]));
$objPage->tpl_large_width = $large_width + 60;
$objPage->tpl_large_height = $large_height + 80;

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
/* ファイル情報の初期化 */
function lfInitFile() {
	global $objUpFile;
	$objUpFile->addFile("一覧-メイン画像", 'main_list_image', array('jpg','gif'),IMAGE_SIZE, true, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
	$objUpFile->addFile("詳細-メイン画像", 'main_image', array('jpg'), IMAGE_SIZE, true, NORMAL_IMAGE_WIDTH, NORMAL_IMAGE_HEIGHT);
	$objUpFile->addFile("詳細-メイン拡大画像", 'main_large_image', array('jpg'), IMAGE_SIZE, false, LARGE_IMAGE_HEIGHT, LARGE_IMAGE_HEIGHT);
	for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
		$objUpFile->addFile("詳細-サブ画像$cnt", "sub_image$cnt", array('jpg'), IMAGE_SIZE, false, NORMAL_SUBIMAGE_HEIGHT, NORMAL_SUBIMAGE_HEIGHT);	
		$objUpFile->addFile("詳細-サブ拡大画像$cnt", "sub_large_image$cnt", array('jpg'), IMAGE_SIZE, false, LARGE_SUBIMAGE_HEIGHT, LARGE_SUBIMAGE_HEIGHT);
	}
	$objUpFile->addFile("商品比較画像", 'file1', array('jpg'), IMAGE_SIZE, false, NORMAL_IMAGE_HEIGHT, NORMAL_IMAGE_HEIGHT);
	$objUpFile->addFile("商品詳細ファイル", 'file2', array('pdf'), PDF_SIZE, false, 0, 0, false);
}

/* 規格選択セレクトボックスの作成 */
function lfMakeSelect($objPage, $product_id) {
	global $objPage;
	$classcat_find1 = false;
	$classcat_find2 = false;
	// 在庫ありの商品の有無
	$stock_find = false;
	
	// 規格名一覧
	$arrClassName = sfGetIDValueList("dtb_class", "class_id", "name");
	// 規格分類名一覧
	$arrClassCatName = sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
	// 商品規格情報の取得	
	$arrProductsClass = lfGetProductsClass($product_id);
	
	// 規格1クラス名の取得
	$objPage->tpl_class_name1 = $arrClassName[$arrProductsClass[0]['class_id1']];
	// 規格2クラス名の取得
	$objPage->tpl_class_name2 = $arrClassName[$arrProductsClass[0]['class_id2']];
	
	// すべての組み合わせ数	
	$count = count($arrProductsClass);
	
	$classcat_id1 = "";
	
	$arrSele1 = array();
	$arrSele2 = array();
	$arrList = array();
	
	$list_id = 0;
	$arrList[0] = "\tlist0 = new Array('選択してください'";
	$arrVal[0] = "\tval0 = new Array(''";
	
	for ($i = 0; $i < $count; $i++) {
		// 在庫のチェック
		if($arrProductsClass[$i]['stock'] <= 0 && $arrProductsClass[$i]['stock_unlimited'] != '1') {
			continue;
		}
		
		$stock_find = true;
		
		// 規格1のセレクトボックス用
		if($classcat_id1 != $arrProductsClass[$i]['classcategory_id1']){
			$arrList[$list_id].=");\n";
			$arrVal[$list_id].=");\n";
			$classcat_id1 = $arrProductsClass[$i]['classcategory_id1'];
			$arrSele1[$classcat_id1] = $arrClassCatName[$classcat_id1];
		}

		// 規格2のセレクトボックス用
		if($arrProductsClass[$i]['classcategory_id1'] == $_POST['classcategory_id1'] and $classcat_id2 != $arrProductsClass[$i]['classcategory_id2']) {
			$classcat_id2 = $arrProductsClass[$i]['classcategory_id2'];
			$arrSele2[$classcat_id2] = $arrClassCatName[$classcat_id2];
		}

		$list_id++;

		// セレクトボックス表示値
		if($arrList[$list_id] == "") {
			$arrList[$list_id] = "\tlist".$list_id." = new Array('選択してください', '".$arrClassCatName[$classcat_id2]."'";
		} else {
			$arrList[$list_id].= ", '".$arrClassCatName[$classcat_id2]."'";
		}
		
		// セレクトボックスPOST値
		if($arrVal[$list_id] == "") {
			$arrVal[$list_id] = "\tval".$list_id." = new Array('', '".$classcat_id2."'";
		} else {
			$arrVal[$list_id].= ", '".$classcat_id2."'";
		}
	}	
	
	//$arrList[$list_id].=");\n";
	$arrVal[$list_id].=");\n";
		
	// 規格1
	$objPage->arrClassCat1 = $arrSele1;
	$objPage->arrClassCat2 = $arrSele2;
	
	//$lists = "\tlists = new Array(";
	//$no = 0;
	
	//foreach($arrList as $val) {
	//	$objPage->tpl_javascript.= $val;
	//	if ($no != 0) {
	//		$lists.= ",list".$no;
	//	} else {
	//		$lists.= "list".$no;
	//	}
	//	$no++;
	//}
	//$objPage->tpl_javascript.=$lists.");\n";
	
	$vals = "\tvals = new Array(";
	$no = 0;
	
	//foreach($arrVal as $val) {
	//	$objPage->tpl_javascript.= $val;
	//	if ($no != 0) {
	//		$vals.= ",val".$no;
	//	} else {
	//		$vals.= "val".$no;
	//	}
	//	$no++;
	//}
	//$objPage->tpl_javascript.=$vals.");\n";
	
	// 選択されている規格2ID
	$objPage->tpl_onload = "lnSetSelect('form1', 'classcategory_id1', 'classcategory_id2', '" . $_POST['classcategory_id2'] . "');";

	// 規格1が設定されている
	if($arrProductsClass[0]['classcategory_id1'] != '0') {
		$classcat_find1 = true;
	}
	
	// 規格2が設定されている
	if($arrProductsClass[0]['classcategory_id2'] != '0') {
		$classcat_find2 = true;
	}
		
	$objPage->tpl_classcat_find1 = $classcat_find1;
	$objPage->tpl_classcat_find2 = $classcat_find2;
	$objPage->tpl_stock_find = $stock_find;
		
	return $objPage;
}

/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;

	$objFormParam->addParam("規格1", "classcategory_id1", INT_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("規格2", "classcategory_id2", INT_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("個数", "quantity", INT_LEN, "n", array("EXIST_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
}

/* 商品規格情報の取得 */
function lfGetProductsClass($product_id) {
	$arrRet = array();
	if(sfIsInt($product_id)) {
		// 商品規格取得
		$objQuery = new SC_Query();
		$col = "product_class_id, classcategory_id1, classcategory_id2, class_id1, class_id2, stock, stock_unlimited";
		$table = "vw_product_class AS prdcls";
		$where = "product_id = ?";
		$objQuery->setorder("rank1 DESC, rank2 DESC");
		$arrRet = $objQuery->select($col, $table, $where, array($product_id));
	}
	return $arrRet;
}

/* 登録済みオススメ商品の読み込み */
function lfPreGetRecommendProducts($product_id) {
	$objQuery = new SC_Query();
	$objQuery->setorder("rank DESC");
	$arrRet = $objQuery->select("recommend_product_id, comment", "dtb_recommend_products", "product_id = ?", array($product_id));
	$max = count($arrRet);
	$no = 0;
	for($i = 0; $i < $max; $i++) {
		$where = "del_flg = 0 AND product_id = ? AND status = 1";
		$arrProductInfo = $objQuery->select("main_list_image, price02_min, price02_max, price01_min, price01_max, name, point_rate", "vw_products_allclass  AS allcls", $where, array($arrRet[$i]['recommend_product_id'])); 
				
		if(count($arrProductInfo) > 0) {
			$arrRecommend[$no] = $arrProductInfo[0];
			$arrRecommend[$no]['product_id'] = $arrRet[$i]['recommend_product_id'];
			$arrRecommend[$no]['comment'] = $arrRet[$i]['comment'];
			$no++;
		}	
	}
	return $arrRecommend;
}

/* 入力内容のチェック */
function lfCheckError() {
	global $objFormParam;
	global $objPage;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
		
	// 複数項目チェック
	if ($objPage->tpl_classcat_find1) {
		$objErr->doFunc(array("規格1", "classcategory_id1"), array("EXIST_CHECK"));
	}
	if ($objPage->tpl_classcat_find2) {
		$objErr->doFunc(array("規格2", "classcategory_id2"), array("EXIST_CHECK"));
	}

	return $objErr->arrErr;
}

//閲覧履歴新規登録
function lfRegistReadingData($tmp_id, $customer_id){
	$objQuery = new SC_Query;
	$sqlval['customer_id'] = $customer_id;
	$sqlval['reading_product_id'] = $tmp_id;
	$sqlval['create_date'] = 'NOW()';
	$sqlval['update_date'] = 'NOW()';
	$objQuery->insert("dtb_customer_reading", $sqlval);
}

//この商品を買った人はこんな商品も買っています
function lfGetRelateProducts($tmp_id) {
	$objQuery = new SC_Query;
	//自動抽出
	$objQuery->setorder("random()");
	//表示件数の制限
	$objQuery->setlimit(RELATED_PRODUCTS_MAX);
	//検索条件
	$col = "name, main_list_image, price01_min, price02_min, price01_max, price02_max, point_rate";
	$from = "vw_products_allclass AS allcls ";
	$where = "del_flg = 0 AND status = 1 AND (stock_max <> 0 OR stock_max IS NULL) AND product_id = ? ";
	$arrval[] = $tmp_id;
	//結果の取得
	$arrProducts = $objQuery->select($col, $from, $where, $arrval);
	
	return $arrProducts;
}

//商品ごとのレビュー情報を取得する
function lfGetReviewData($id) {
	$objQuery = new SC_Query;
	//商品ごとのレビュー情報を取得する
	$col = "create_date, reviewer_url, reviewer_name, recommend_level, title, comment";
	$from = "dtb_review";
	$where = "del_flg = 0 AND status = 1 AND product_id = ? ";
	$arrval[] = $id;
	$arrReview = $objQuery->select($col, $from, $where, $arrval);
	return $arrReview; 
}

//支払方法の取得
//payment_id	1:クレジット　2:ショッピングローン	
function lfGetPayment() {
	$objQuery = new SC_Query;
	$col = "payment_id, rule, payment_method";
	$from = "dtb_payment";
	$where = "del_flg = 0";
	$order = "payment_id";
	$objQuery->setorder($order);
	$arrRet = $objQuery->select($col, $from, $where);
	return $arrRet;
}
?>
