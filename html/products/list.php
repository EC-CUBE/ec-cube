<?php
$LIST_PHP_DIR = realpath(dirname( __FILE__));
require_once($LIST_PHP_DIR  . "/../../data/lib/slib.php");
require_once($LIST_PHP_DIR  . "/../../data/class/SC_View.php");
require_once($LIST_PHP_DIR  . "/../../data/class/SC_Query.php");
require_once($LIST_PHP_DIR  . "/../../data/class/SC_Customer.php");
require_once($LIST_PHP_DIR  . "/../../data/class/SC_Cookie.php");
require_once($LIST_PHP_DIR  . "/../../data/class/SC_SiteInfo.php");
require_once($LIST_PHP_DIR  . "/../../data/class/SC_PageNavi.php");
require_once($LIST_PHP_DIR  . "/../../data/class/SC_CheckError.php");
require_once($LIST_PHP_DIR  . "/../../data/class/SC_CartSession.php");
require_once(ROOT_DIR."data/include/page_layout.inc");

class LC_Page {
	function LC_Page() {
		global $arrSTATUS;
		$this->arrSTATUS = $arrSTATUS;
		global $arrSTATUS_IMAGE;
		$this->arrSTATUS_IMAGE = $arrSTATUS_IMAGE;
		global $arrDELIVERYDATE;
		$this->arrDELIVERYDATE = $arrDELIVERYDATE;
		global $arrPRODUCTLISTMAX;
		$this->arrPRODUCTLISTMAX = $arrPRODUCTLISTMAX;		
		/*
		 session_start時のno-cacheヘッダーを抑制することで
		 「戻る」ボタン使用時の有効期限切れ表示を抑制する。
		 private-no-expire:クライアントのキャッシュを許可する。
		*/
		session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$conn = new SC_DBConn();

//表示件数の選択
if(sfIsInt($_POST['disp_number'])) {
	$objPage->disp_number = $_POST['disp_number'];
} else {
	//最小表示件数を選択
	$objPage->disp_number = current(array_keys($arrPRODUCTLISTMAX));
}

//表示順序の保存
$objPage->orderby = $_POST['orderby'];

// GETのカテゴリIDを元に正しいカテゴリIDを取得する。
$category_id = sfGetCategoryId("", $_GET['category_id']);

// タイトル編集
$tpl_subtitle = "";
if($_GET['mode'] == 'search'){
	$tpl_subtitle = "検索結果";
}elseif ($category_id == "" ) {
	$tpl_subtitle = "全商品";
}else{
	$arrFirstCat = GetFirstCat($category_id);
	$tpl_subtitle = $arrFirstCat['name'];
}

sfprintr($_POST);exit;

$objQuery = new SC_Query();
$count = $objQuery->count("dtb_best_products", "category_id = ?", array($category_id));

// 以下の条件でBEST商品を表示する
// ・BEST最大数の商品が登録されている。
// ・カテゴリIDがルートIDである。
// ・検索モードでない。
if(($count >= BEST_MIN) && lfIsRootCategory($category_id) && ($_GET['mode'] != 'search') ) {
	// 商品TOPの表示処理
	/** 必ず指定する **/
	$objPage->tpl_mainpage = ROOT_DIR . 'html/user_data/templates/list.tpl';		// メインテンプレート	
	
	$objPage->arrBestItems = sfGetBestProducts($conn, $category_id);
	$objPage->BEST_ROOP_MAX = ceil((BEST_MAX-1)/2);
} else {
	if ($_GET['mode'] == 'search' && strlen($_GET['category_id']) == 0 ){
		// 検索時にcategory_idがGETに存在しない場合は、仮に埋めたIDを空白に戻す
		$category_id = '';	
	}
	
	// 商品一覧の表示処理
	$objPage = lfDispProductsList($category_id, $_GET['name'], $objPage->disp_number, $_POST['orderby']);
	
	// 検索条件を画面に表示
	// カテゴリー検索条件
	if (strlen($_GET['category_id']) == 0) {
		$arrSearch['category'] = "指定なし";
	}else{
		$arrCat = $conn->getOne("SELECT category_name FROM dtb_category WHERE category_id = ?",array($category_id));
		$arrSearch['category'] = $arrCat;
	}
	
	// 商品名検索条件
	if ($_GET['name'] === "") {
		$arrSearch['name'] = "指定なし";
	}else{
		$arrSearch['name'] = $_GET['name'];
	}
}

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, "products/list.php");

if($_POST['mode'] == "cart" && $_POST['product_id'] != "") {
	// 値の正当性チェック
	if(!sfIsInt($_POST['product_id']) || !sfIsRecord("dtb_products", "product_id", $_POST['product_id'], "del_flg = 0 AND status = 1")) {
		sfDispSiteError(PRODUCT_NOT_FOUND);
	} else {
		// 入力値の変換
		$objPage->arrErr = lfCheckError($_POST['product_id']);
		if(count($objPage->arrErr) == 0) {
			$objCartSess = new SC_CartSession();
			$classcategory_id = "classcategory_id". $_POST['product_id'];
			$classcategory_id1 = $_POST[$classcategory_id. '_1'];
			$classcategory_id2 = $_POST[$classcategory_id. '_2'];
			$quantity = "quantity". $_POST['product_id'];
			// 規格1が設定されていない場合
			if(!$objPage->tpl_classcat_find1[$_POST['product_id']]) {
				$classcategory_id1 = '0';
			}
			// 規格2が設定されていない場合
			if(!$objPage->tpl_classcat_find2[$_POST['product_id']]) {
				$classcategory_id2 = '0';
			}
			$objCartSess->addProduct(array($_POST['product_id'], $classcategory_id1, $classcategory_id2), $_POST[$quantity]);
			header("Location: /cart/index.php");
			exit;
		}
	}
}


$objPage->tpl_subtitle = $tpl_subtitle;

// 支払方法の取得
$objPage->arrPayment = lfGetPayment();
// 入力情報を渡す
$objPage->arrForm = $_POST;

$objPage->category_id = $category_id;
$objPage->arrSearch = $arrSearch;

sfCustomDisplay($objPage);

//-----------------------------------------------------------------------------------------------------------------------------------
/* カテゴリIDがルートかどうかの判定 */
function lfIsRootCategory($category_id) {
	$objQuery = new SC_Query();
	$level = $objQuery->get("dtb_category", "level", "category_id = ?", array($category_id));
	if($level == 1) {
		return true;
	}
	return false;
}

/* 商品一覧の表示 */
function lfDispProductsList($category_id, $name, $disp_num, $orderby) {
	global $objPage;
	$objQuery = new SC_Query();	
	$objPage->tpl_pageno = $_POST['pageno'];

	//表示件数でテンプレートを切り替える
	$objPage->tpl_mainpage = ROOT_DIR . 'html/user_data/templates/list.tpl';		// メインテンプレート		

	//表示順序
	switch($orderby) {
	//価格順
	case 'price':
		$order = "price02_min ASC";
		break;
	//新着順
	case 'date':
		$order = "create_date DESC";
		break;
	default:
		$order = "category_rank DESC, rank DESC";
		break;
	}
	
	// 商品検索条件の作成（未削除、表示）
	$where = "del_flg = 0 AND status = 1 ";
	// カテゴリからのWHERE文字列取得
	if ( $category_id ) {
		list($tmp_where, $arrval) = sfGetCatWhere($category_id);
		if($tmp_where != "") {
			$where.= " AND $tmp_where";
		}
	}
		
	// 商品名をwhere文に
	$name = ereg_replace(",", "", $name);
	if ( strlen($name) > 0 ){
		$where .= " AND ( name ILIKE ? OR comment3 ILIKE ?) ";
		$ret = sfManualEscape($name);		
		$arrval[] = "%$ret%";
		$arrval[] = "%$ret%";
	}
			
	// 行数の取得
	$linemax = $objQuery->count("vw_products_allclass AS allcls", $where, $arrval);
	$objPage->tpl_linemax = $linemax;	// 何件が該当しました。表示用
	
	// ページ送りの取得
	$objNavi = new SC_PageNavi($_POST['pageno'], $linemax, $disp_num, "fnNaviPage", NAVI_PMAX);
	
	
	sfprintr($objPage->tpl_strnavi );
	$objPage->tpl_strnavi = $objNavi->strnavi;		// 表示文字列
	$startno = $objNavi->start_row;					// 開始行
	
	// 取得範囲の指定(開始行番号、行数のセット)
	$objQuery->setlimitoffset($disp_num, $startno);
	// 表示順序
	$objQuery->setorder($order);
	// 検索結果の取得
	$objPage->arrProducts = $objQuery->select("*", "vw_products_allclass AS allcls", $where, $arrval);
	
	// 規格名一覧
	$arrClassName = sfGetIDValueList("dtb_class", "class_id", "name");
	// 規格分類名一覧
	$arrClassCatName = sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
	// 企画セレクトボックス設定
	if($disp_num == 15) {
		for($i = 0; $i < count($objPage->arrProducts); $i++) {
			$objPage = lfMakeSelect($objPage->arrProducts[$i]['product_id'], $arrClassName, $arrClassCatName);
			// 購入制限数を取得
			$objPage = lfGetSaleLimit($objPage->arrProducts[$i]);
		}
	}

	return $objPage;
}

/* 規格セレクトボックスの作成 */
function lfMakeSelect($product_id, $arrClassName, $arrClassCatName) {
	global $objPage;
	
	$classcat_find1 = false;
	$classcat_find2 = false;
	// 在庫ありの商品の有無
	$stock_find = false;
	
	// 商品規格情報の取得	
	$arrProductsClass = lfGetProductsClass($product_id);
	
	// 規格1クラス名の取得
	$objPage->tpl_class_name1[$product_id] = $arrClassName[$arrProductsClass[0]['class_id1']];
	// 規格2クラス名の取得
	$objPage->tpl_class_name2[$product_id] = $arrClassName[$arrProductsClass[0]['class_id2']];
	
	// すべての組み合わせ数	
	$count = count($arrProductsClass);
	
	$classcat_id1 = "";
	
	$arrSele = array();
	$arrList = array();
	
	$list_id = 0;
	$arrList[0] = "\tlist". $product_id. "_0 = new Array('選択してください'";
	$arrVal[0] = "\tval". $product_id. "_0 = new Array(''";
	
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
			$arrSele[$classcat_id1] = $arrClassCatName[$classcat_id1];
			$list_id++;
		}
		
		// 規格2のセレクトボックス用
		$classcat_id2 = $arrProductsClass[$i]['classcategory_id2'];
		
		// セレクトボックス表示値
		if($arrList[$list_id] == "") {
			$arrList[$list_id] = "\tlist". $product_id. "_". $list_id. " = new Array('選択してください', '". $arrClassCatName[$classcat_id2]. "'";
		} else {
			$arrList[$list_id].= ", '".$arrClassCatName[$classcat_id2]."'";
		}
		
		// セレクトボックスPOST値
		if($arrVal[$list_id] == "") {
			$arrVal[$list_id] = "\tval". $product_id. "_". $list_id. " = new Array('', '". $classcat_id2. "'";
		} else {
			$arrVal[$list_id].= ", '".$classcat_id2."'";
		}
	}	
	
	$arrList[$list_id].=");\n";
	$arrVal[$list_id].=");\n";
		
	// 規格1
	$objPage->arrClassCat1[$product_id] = $arrSele;
	
	$lists = "\tlists".$product_id. " = new Array(";
	$no = 0;
	foreach($arrList as $val) {
		$objPage->tpl_javascript.= $val;
		if ($no != 0) {
			$lists.= ",list". $product_id. "_". $no;
		} else {
			$lists.= "list". $product_id. "_". $no;
		}
		$no++;
	}
	$objPage->tpl_javascript.= $lists.");\n";
	
	$vals = "\tvals".$product_id. " = new Array(";
	$no = 0;
	foreach($arrVal as $val) {
		$objPage->tpl_javascript.= $val;
		if ($no != 0) {
			$vals.= ",val". $product_id. "_". $no;
		} else {
			$vals.= "val". $product_id. "_". $no;
		}
		$no++;
	}
	$objPage->tpl_javascript.= $vals.");\n";
	
	// 選択されている規格2ID
	$classcategory_id = "classcategory_id". $product_id;
	$objPage->tpl_onload .= "lnSetSelect('".$classcategory_id."_1','".$classcategory_id."_2','".$product_id."','".$_POST[$classcategory_id."_2"]."'); ";

	// 規格1が設定されている
	if($arrProductsClass[0]['classcategory_id1'] != '0') {
		$classcat_find1 = true;
	}
	
	// 規格2が設定されている
	if($arrProductsClass[0]['classcategory_id2'] != '0') {
		$classcat_find2 = true;
	}
		
	$objPage->tpl_classcat_find1[$product_id] = $classcat_find1;
	$objPage->tpl_classcat_find2[$product_id] = $classcat_find2;
	$objPage->tpl_stock_find[$product_id] = $stock_find;
		
	return $objPage;
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

/* 入力内容のチェック */
function lfCheckError($id) {
	global $objPage;
	
	// 入力データを渡す。
	$objErr = new SC_CheckError();
	
	$classcategory_id1 = "classcategory_id". $id. "_1";
	$classcategory_id2 = "classcategory_id". $id. "_2";
	$quantity = "quantity". $id;
	// 複数項目チェック
	if ($objPage->tpl_classcat_find1[$id]) {
		$objErr->doFunc(array("規格1", $classcategory_id1, INT_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
	}
	if ($objPage->tpl_classcat_find2[$id]) {
		$objErr->doFunc(array("規格2", $classcategory_id2, INT_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
	}
	$objErr->doFunc(array("個数", $quantity, INT_LEN), array("EXIST_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
			
	return $objErr->arrErr;
}

// 購入制限数の設定
function lfGetSaleLimit($product) {
	global $objPage;
	//在庫が無限または購入制限値が設定値より大きい場合
	if($product['sale_unlimited'] == 1 || $product['sale_limit'] > SALE_LIMIT_MAX) {
		$objPage->tpl_sale_limit[$product['product_id']] = SALE_LIMIT_MAX;
	} else {
		$objPage->tpl_sale_limit[$product['product_id']] = $product['sale_limit'];
	}
	
	return $objPage;
}

//支払方法の取得
//payment_id	1:代金引換　2:銀行振り込み　3:現金書留
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