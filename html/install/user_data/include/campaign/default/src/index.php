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
		$this->tpl_mainpage = TEMPLATE_DIR . '/campaign/index.tpl';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView(false);
$objQuery = new SC_Query();
$objCampaignSess = new SC_CampaignSession();

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

// カートに入れないページの場合のページ(申込のみページ)へリダイレクト
$cart_flg = $objQuery->get("dtb_campaign", "cart_flg", "campaign_id = ?", array($campaign_id));
if(!$cart_flg) {
	header("location: ". CAMPAIGN_URL . "$dir_name/application.php");
}

// キャンペーンが開催中かをチェック
if(lfCheckActive($dir_name)) {
	$status = CAMPAIGN_TEMPLATE_ACTIVE;
} else {
	$status = CAMPAIGN_TEMPLATE_END;
}

if($_GET['init'] != "") {
	$objPage->tpl_init = 'false';
	lfDispProductsList($_GET['ids']);
} else {
	$objPage->tpl_init = 'true';	
}

switch($_POST['mode']) {

case 'cart':
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
		$objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
		$objCartSess->addProduct(array($_POST['product_id'], $classcategory_id1, $classcategory_id2), $_POST[$quantity], $campaign_id);
		header("Location: " . URL_CART_TOP);
		exit;
	}
	break;
default :
	break;
}
// 入力情報を渡す
$objPage->arrForm = $_POST;
$objPage->tpl_dir_name = CAMPAIGN_TEMPLATE_PATH . $dir_name  . "/" . $status;

//----　ページ表示
$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);


//---------------------------------------------------------------------------------------------------------------------------------------------------------
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

/* 商品一覧の表示 */
function lfDispProductsList($ids) {
	
	global $objQuery;
	global $objPage;

	// 規格名一覧
	$arrClassName = sfGetIDValueList("dtb_class", "class_id", "name");
	// 規格分類名一覧
	$arrClassCatName = sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
	
	$arrProductIds = split('-', $ids);
	if(!is_array($arrProductIds)) {
		$arrProductIds[0] = $ids;
	}
	
	// where句生成
	$count = 0;
	$where = "product_id IN (";
	foreach($arrProductIds as $key =>$val) {
		if($count > 0) $where .= ",";
		$where .= "?";
		$arrval[] = $val;
		$count++;
	}
	$where .= ")";

	// 商品一覧
	$arrProducts = $objQuery->select("*", "vw_products_allclass AS allcls", $where, $arrval);

	for($i = 0; $i < count($arrProducts); $i++) {
		$objPage = lfMakeSelect($arrProducts[$i]['product_id'], $arrClassName, $arrClassCatName);
		// 購入制限数を取得
		$objPage = lfGetSaleLimit($arrProducts);
	}

	foreach($arrProducts as $key =>$val) {
		$arrCamp[$val['product_id']] = $val;
	}
	
	$objPage->arrProducts = $arrCamp;
	
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