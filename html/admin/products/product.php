<?php

require_once("../require.php");

class LC_Page {
	var $arrCatList;
	var $arrSRANK;
	var $arrForm;
	var $arrSubList;
	var $arrHidden;
	var $arrTempImage;
	var $arrSaveImage;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_mainpage = 'products/product.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';		
		$this->tpl_subno = 'product';
		$this->tpl_subtitle = '商品登録';
		global $arrSRANK;
		$this->arrSRANK = $arrSRANK;
		global $arrDISP;
		$this->arrDISP = $arrDISP;
		global $arrCLASS;
		$this->arrCLASS = $arrCLASS;
		global $arrSTATUS;
		$this->arrSTATUS = $arrSTATUS;
		global $arrSTATUS_VALUE;
		$this->arrSTATUS_VALUE = $arrSTATUS_VALUE;
		global $arrSTATUS_IMAGE;
		$this->arrSTATUS_IMAGE = $arrSTATUS_IMAGE;
		global $arrDELIVERYDATE;
		$this->arrDELIVERYDATE = $arrDELIVERYDATE;
		$this->tpl_nonclass = true;
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSiteInfo = new SC_SiteInfo();
$objQuery = new SC_Query();

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// ファイル管理クラス
$objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);

// ファイル情報の初期化
lfInitFile();
// Hiddenからのデータを引き継ぐ
$objUpFile->setHiddenFileList($_POST);


// 検索パラメータの引き継ぎ
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key)) {
		$objPage->arrSearchHidden[$key] = $val;	
	}
}

// FORMデータの引き継ぎ
$objPage->arrForm = $_POST;

switch($_POST['mode']) {
// 検索画面からの編集
case 'pre_edit':
	// 編集時
	if(sfIsInt($_POST['product_id'])){
		// DBから商品情報の読込
		$objPage->arrForm = lfGetProduct($_POST['product_id']);
		// 商品ステータスの変換
		$arrRet = sfSplitCBValue($objPage->arrForm['product_flag'], "product_flag");
		$objPage->arrForm = array_merge($objPage->arrForm, $arrRet);
		// DBからおすすめ商品の読み込み
		$objPage->arrRecommend = lfPreGetRecommendProducts($_POST['product_id']);
		// DBデータから画像ファイル名の読込
		$objUpFile->setDBFileList($objPage->arrForm);
		// 規格登録ありなし判定
		$objPage->tpl_nonclass = lfCheckNonClass($_POST['product_id']);
		lfProductPage();		// 商品登録ページ
	}
	break;
		
// 商品登録・編集
case 'edit':
	// 規格登録ありなし判定
	$objPage->tpl_nonclass = lfCheckNonClass($_POST['product_id']);
	// 入力値の変換
	$objPage->arrForm = lfConvertParam($objPage->arrForm);
	// エラーチェック
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);
	// ファイル存在チェック
	$objPage->arrErr = array_merge((array)$objPage->arrErr, (array)$objUpFile->checkEXISTS());
	// エラーなしの場合
	if(count($objPage->arrErr) == 0) {
		lfProductConfirmPage(); // 確認ページ
	} else {
		lfProductPage();		// 商品登録ページ
	}
	break;
// 確認ページから完了ページへ
case 'complete':
	$objPage->tpl_mainpage = 'products/complete.tpl';
	
	$objPage->tpl_product_id = lfRegistProduct($_POST);		// データ登録
	
	$objQuery = new SC_Query();
	// 件数カウントバッチ実行
	sfCategory_Count($objQuery);
	// 一時ファイルを本番ディレクトリに移動する
	$objUpFile->moveTempFile();

	break;
// 画像のアップロード
case 'upload_image':
	// ファイル存在チェック
	$objPage->arrErr = array_merge((array)$objPage->arrErr, (array)$objUpFile->checkEXISTS($_POST['image_key']));
	// 画像保存処理
	$objPage->arrErr[$_POST['image_key']] = $objUpFile->makeTempFile($_POST['image_key']);
	lfProductPage(); // 商品登録ページ
	break;
// 画像の削除
case 'delete_image':
	$objUpFile->deleteFile($_POST['image_key']);
	lfProductPage(); // 商品登録ページ
	break;
// 確認ページからの戻り
case 'confirm_return':
	// 規格登録ありなし判定
	$objPage->tpl_nonclass = lfCheckNonClass($_POST['product_id']);
	lfProductPage();		// 商品登録ページ
	break;
// おすすめ商品選択
case 'recommend_select' :
	lfProductPage();		// 商品登録ページ
	break;
default:
	// 公開・非公開のデフォルト値
	$objPage->arrForm['status'] = DEFAULT_PRODUCT_DISP;
	lfProductPage();		// 商品登録ページ
	break;
}

if($_POST['mode'] != 'pre_edit') {
	// おすすめ商品の読み込み
	$objPage->arrRecommend = lfGetRecommendProducts();
}

// 基本情報を渡す
$objPage->arrInfo = $objSiteInfo->data;

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------

/* おすすめ商品の読み込み */
function lfGetRecommendProducts() {
	global $objPage;
	$objQuery = new SC_Query();
	
	for($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
		$keyname = "recommend_id" . $i;
		$delkey = "recommend_delete" . $i;
		$commentkey = "recommend_comment" . $i;

		if($_POST[$keyname] != "" && $_POST[$delkey] != 1) {
			$arrRet = $objQuery->select("main_list_image, product_code_min, name", "vw_products_allclass", "product_id = ?", array($_POST[$keyname])); 
			$arrRecommend[$i] = $arrRet[0];
			$arrRecommend[$i]['product_id'] = $_POST[$keyname];
			$arrRecommend[$i]['comment'] = $objPage->arrForm[$commentkey];
		}
	}
	return $arrRecommend;
}

/* おすすめ商品の登録 */
function lfInsertRecommendProducts($objQuery, $arrList, $product_id) {
	// 一旦オススメ商品をすべて削除する
	$objQuery->delete("dtb_recommend_products", "product_id = ?", array($product_id));
	$sqlval['product_id'] = $product_id;
	$rank = RECOMMEND_PRODUCT_MAX;
	for($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
		$keyname = "recommend_id" . $i;
		$commentkey = "recommend_comment" . $i;
		$deletekey = "recommend_delete" . $i;
		if($arrList[$keyname] != "" && $arrList[$deletekey] != '1') {
//			$sqlval['recommend_product_id'] = $arrList[$keyname];
			$sqlval['comment'] = $arrList[$commentkey];
			$sqlval['rank'] = $rank;
			$sqlval['creator_id'] = $_SESSION['member_id'];
			$sqlval['create_date'] = "now()";
			$objQuery->insert("dtb_recommend_products", $sqlval);
			$rank--;
		}
	}
}

/* 登録済みおすすめ商品の読み込み */
function lfPreGetRecommendProducts($product_id) {
	$objQuery = new SC_Query();
	$objQuery->setorder("rank DESC");
	$arrRet = $objQuery->select("recommend_product_id, comment", "dtb_recommend_products", "product_id = ?", array($product_id));
	$max = count($arrRet);
	$no = 1;
	
	for($i = 0; $i < $max; $i++) {
		$arrProductInfo = $objQuery->select("main_list_image, product_code_min, name", "vw_products_allclass", "product_id = ?", array($arrRet[$i]['recommend_product_id'])); 
		$arrRecommend[$no] = $arrProductInfo[0];
		$arrRecommend[$no]['product_id'] = $arrRet[$i]['recommend_product_id'];
		$arrRecommend[$no]['comment'] = $arrRet[$i]['comment'];
		$no++;
	}
	return $arrRecommend;
}

/* 商品情報の読み込み */
function lfGetProduct($product_id) {
	$objQuery = new SC_Query();
	$col = "*";
	$table = "vw_products_nonclass";
	$where = "product_id = ?";
	$arrRet = $objQuery->select($col, $table, $where, array($product_id));
		
	return $arrRet[0];
}

/* 商品登録ページ表示用 */
function lfProductPage() {
	global $objPage;
	global $objUpFile;
	
	// カテゴリの読込
	list($objPage->arrCatVal, $objPage->arrCatOut) = sfGetLevelCatList(false);

	if($objPage->arrForm['status'] == "") {
		$objPage->arrForm['status'] = 1;
	}
	
	if(!is_array($objPage->arrForm['product_flag'])) {
		// 商品ステータスの分割読込
		$objPage->arrForm['product_flag'] = sfSplitCheckBoxes($objPage->arrForm['product_flag']);
	}
	
	// HIDDEN用に配列を渡す。
	$objPage->arrHidden = array_merge((array)$objPage->arrHidden, (array)$objUpFile->getHiddenFileList());
	// Form用配列を渡す。
	$objPage->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
	
	$objPage->tpl_onload = "fnCheckSaleLimit('" . DISABLED_RGB . "'); fnCheckStockLimit('" . DISABLED_RGB . "');";
}

/* ファイル情報の初期化 */
function lfInitFile() {
	global $objUpFile;
	$objUpFile->addFile("一覧-メイン画像", 'main_list_image', array('jpg', 'gif'),IMAGE_SIZE, true, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
	$objUpFile->addFile("詳細-メイン画像", 'main_image', array('jpg', 'gif'), IMAGE_SIZE, true, NORMAL_IMAGE_WIDTH, NORMAL_IMAGE_HEIGHT);
	$objUpFile->addFile("詳細-メイン拡大画像", 'main_large_image', array('jpg', 'gif'), IMAGE_SIZE, false, LARGE_IMAGE_HEIGHT, LARGE_IMAGE_HEIGHT);
	for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
		$objUpFile->addFile("詳細-サブ画像$cnt", "sub_image$cnt", array('jpg', 'gif'), IMAGE_SIZE, false, NORMAL_SUBIMAGE_HEIGHT, NORMAL_SUBIMAGE_HEIGHT);	
		$objUpFile->addFile("詳細-サブ拡大画像$cnt", "sub_large_image$cnt", array('jpg', 'gif'), IMAGE_SIZE, false, LARGE_SUBIMAGE_HEIGHT, LARGE_SUBIMAGE_HEIGHT);
	}
	$objUpFile->addFile("商品比較画像", 'file1', array('jpg', 'gif'), IMAGE_SIZE, false, OTHER_IMAGE1_HEIGHT, OTHER_IMAGE1_HEIGHT);
	$objUpFile->addFile("商品詳細ファイル", 'file2', array('pdf'), PDF_SIZE, false, 0, 0, false);
}

/* 商品の登録 */
function lfRegistProduct($arrList) {
	global $objUpFile;
	global $arrSTATUS;
	$objQuery = new SC_Query();
	$objQuery->begin();
	
	// INSERTする値を作成する。
	$sqlval['name'] = $arrList['name'];
	$sqlval['category_id'] = $arrList['category_id'];
	$sqlval['status'] = $arrList['status'];
	$sqlval['product_flag'] = $arrList['product_flag'];
	$sqlval['main_list_comment'] = $arrList['main_list_comment'];
	$sqlval['main_comment'] = $arrList['main_comment'];
	$sqlval['point_rate'] = $arrList['point_rate'];
	
	$sqlval['deliv_fee'] = $arrList['deliv_fee'];
	$sqlval['comment1'] = $arrList['comment1'];
	$sqlval['comment2'] = $arrList['comment2'];
	$sqlval['comment3'] = $arrList['comment3'];
	$sqlval['comment4'] = $arrList['comment4'];
	$sqlval['comment5'] = $arrList['comment5'];
	$sqlval['comment6'] = $arrList['comment6'];
	$sqlval['main_list_comment'] = $arrList['main_list_comment'];
	$sqlval['sale_limit'] = $arrList['sale_limit'];
	$sqlval['sale_unlimited'] = $arrList['sale_unlimited'];
	$sqlval['deliv_date_id'] = $arrList['deliv_date_id'];
	$sqlval['update_date'] = "Now()";
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$arrRet = $objUpFile->getDBFileList();
	$sqlval = array_merge($sqlval, $arrRet);
		
	for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
		$sqlval['sub_title'.$cnt] = $arrList['sub_title'.$cnt];
		$sqlval['sub_comment'.$cnt] = $arrList['sub_comment'.$cnt];
	}

	if($arrList['product_id'] == "") {
		if (DB_TYPE == "pgsql") {
			$product_id = $objQuery->nextval("dtb_products", "product_id");
			$sqlval['product_id'] = $product_id;
		}
		// カテゴリ内で最大のランクを割り当てる
		$sqlval['rank'] = $objQuery->max("dtb_products", "rank", "category_id = ?", array($arrList['category_id'])) + 1;
		// INSERTの実行
		$sqlval['create_date'] = "Now()";
		$objQuery->insert("dtb_products", $sqlval);
		
		if (DB_TYPE == "mysql") {
			$product_id = $objQuery->nextval("dtb_products", "product_id");
			$sqlval['product_id'] = $product_id;
		}

	} else {
		$product_id = $arrList['product_id'];
		// 削除要求のあった既存ファイルの削除
		$arrRet = lfGetProduct($arrList['product_id']);
		$objUpFile->deleteDBFile($arrRet);
		
		// カテゴリ内ランクの調整処理
		$old_catid = $objQuery->get("dtb_products", "category_id", "product_id = ?", array($arrList['product_id']));
		sfMoveCatRank($objQuery, "dtb_products", "product_id", "category_id", $old_catid, $arrList['category_id'], $arrList['product_id']);
		
		// UPDATEの実行
		$where = "product_id = ?";
		$objQuery->update("dtb_products", $sqlval, $where, array($arrList['product_id']));
	}
	
	// 規格登録
	sfInsertProductClass($objQuery, $arrList, $product_id);
	
	// おすすめ商品登録
	lfInsertRecommendProducts($objQuery, $arrList, $product_id);
	
	$objQuery->commit();
	return $product_id;
}


/* 取得文字列の変換 */
function lfConvertParam($array) {
	/*
	 *	文字列の変換
	 *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
	 *	C :  「全角ひら仮名」を「全角かた仮名」に変換
	 *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します	
	 *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
	 */
	// 人物基本情報
	
	// スポット商品
	$arrConvList['name'] = "KVa";
	$arrConvList['main_list_comment'] = "KVa";
	$arrConvList['main_comment'] = "KVa";
	$arrConvList['price01'] = "n";
	$arrConvList['price02'] = "n";
	$arrConvList['stock'] = "n";
	$arrConvList['sale_limit'] = "n";
	$arrConvList['point_rate'] = "n";
	$arrConvList['product_code'] = "KVna";
	$arrConvList['comment1'] = "a";
	//ホネケーキ:送料の指定なし
	$arrConvList['deliv_fee'] = "n";
	
	// 詳細-サブ
	for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
		$arrConvList["sub_title$cnt"] = "KVa";
	}
	for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
		$arrConvList["sub_comment$cnt"] = "KVa";
	}
	
	// おすすめ商品
	for ($cnt = 1; $cnt <= RECOMMEND_PRODUCT_MAX; $cnt++) {
		$arrConvList["recommend_comment$cnt"] = "KVa";
	}

	// 文字変換
	foreach ($arrConvList as $key => $val) {
		// POSTされてきた値のみ変換する。
		if(isset($array[$key])) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	
	global $arrSTATUS;
	$array['product_flag'] = sfMergeCheckBoxes($array['product_flag'], count($arrSTATUS));
	
	return $array;
}

// 入力エラーチェック
function lfErrorCheck($array) {
	global $objPage;
	global $arrAllowedTag;
	
	$objErr = new SC_CheckError($array);
	$objErr->doFunc(array("商品名", "name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("商品カテゴリ", "category_id", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("一覧-メインコメント", "main_list_comment", MTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("詳細-メインコメント", "main_comment", LLTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("詳細-メインコメント", "main_comment", $arrAllowedTag), array("HTML_TAG_CHECK"));
	$objErr->doFunc(array("ポイント付与率", "point_rate", PERCENTAGE_LEN), array("EXIST_CHECK", "NUM_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("商品送料", "deliv_fee", PRICE_LEN), array("NUM_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("検索ワード", "comment3", LLTEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("メーカーURL", "comment1", URL_LEN), array("SPTAB_CHECK", "URL_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("発送日目安", "deliv_date_id", INT_LEN), array("NUM_CHECK"));
	
	if($objPage->tpl_nonclass) {
		$objErr->doFunc(array("商品コード", "product_code", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK","MAX_LENGTH_CHECK","MAX_LENGTH_CHECK"));
		$objErr->doFunc(array("通常価格", "price01", PRICE_LEN), array("ZERO_CHECK", "SPTAB_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
		$objErr->doFunc(array("商品価格", "price02", PRICE_LEN), array("EXIST_CHECK", "NUM_CHECK", "ZERO_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
			
		if($array['stock_unlimited'] != "1") {
			$objErr->doFunc(array("在庫数", "stock", AMOUNT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
		}
	}
	
	if($array['sale_unlimited'] != "1") {	
		$objErr->doFunc(array("購入制限", "sale_limit", AMOUNT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
	}
	
	if(isset($objErr->arrErr['category_id'])) {
		// 自動選択を防ぐためにダミー文字を入れておく
		$objPage->arrForm['category_id'] = "#";
	}
	
	for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
		$objErr->doFunc(array("詳細-サブタイトル$cnt", "sub_title$cnt", STEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
		$objErr->doFunc(array("詳細-サブコメント$cnt", "sub_comment$cnt", LLTEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
		$objErr->doFunc(array("詳細-サブコメント$cnt", "sub_comment$cnt", $arrAllowedTag),  array("HTML_TAG_CHECK"));	
	}
	
	for ($cnt = 1; $cnt <= RECOMMEND_PRODUCT_MAX; $cnt++) {
		if($_POST["recommend_id$cnt"] != "" && $_POST["recommend_delete$cnt"] != 1) {
			$objErr->doFunc(array("おすすめ商品コメント$cnt", "recommend_comment$cnt", LTEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
		}
	}
	
	return $objErr->arrErr;
}

/* 確認ページ表示用 */
function lfProductConfirmPage() {
	global $objPage;
	global $objUpFile;
	$objPage->tpl_mainpage = 'products/confirm.tpl';
	$objPage->arrForm['mode'] = 'complete';
	// カテゴリの読込
	$objPage->arrCatList = sfGetCategoryList();
	// Form用配列を渡す。
	$objPage->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
}

/* 規格あり判定用(規格が登録されていない場合:TRUE) */
function lfCheckNonClass($product_id) {
	if(sfIsInt($product_id)) {
		$objQuery  = new SC_Query();
		$where = "product_id = ? AND classcategory_id1 <> 0 AND classcategory_id1 <> 0";
		$count = $objQuery->count("dtb_products_class", $where, array($product_id));
		if($count > 0) {
			return false;
		}
	}
	return true;
}

?>