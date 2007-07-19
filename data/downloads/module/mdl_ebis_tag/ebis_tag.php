<?php
/**
 * 
 * @copyright	2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: ebis_tag.php,v 1.30 2007/07/18 04:02:40 adachi Exp $
 * @link		http://www.lockon.co.jp/
 *
 */

// 認証確認
$objSess = new SC_Session();
sfIsSuccess($objSess);

//ページ管理クラス
class LC_Page {
	//コンストラクタ
	function LC_Page() {
		//メインテンプレートの指定
		$this->tpl_mainpage  = MODULE_PATH . 'mdl_ebis_tag/ebis_tag.tpl';
		$this->tpl_subtitle  = 'AD EBiSタグ埋め込み機能';
        $this->tpl_uniqid    = '';
        
        global $arrEBiSTagCustomerId;
        $this->arrEBiSTagCustomerId = $arrEBiSTagCustomerId;
        global $arrEBiSTagPayment;
        $this->arrEBiSTagPayment = $arrEBiSTagPayment;
        global $arrEBiSTagOptions;
        $this->arrEBiSTagOptions = $arrEBiSTagOptions;
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
$objFormParam = lfInitParam($objFormParam);
// POST値の取得
$objFormParam->setParam($_POST);

switch($_POST['mode']) {
case 'edit':
    // 画面遷移の正当性チェック
    //if (sfIsValidTransition($objSess) == false) {
    //    sfDispError(INVALID_MOVE_ERRORR);
    //}
    
	// 入力エラー判定
	$objPage->arrErr = $objFormParam->checkError();
	if(count($objPage->arrErr) == 0) {
		$arrRet = $objFormParam->getHashArray();
		$sqlval['sub_data'] = serialize($arrRet);
		$objQuery = new SC_Query();
		$objQuery->update("dtb_module", $sqlval, "module_id = ?", array(EBIS_TAG_MID));
		// javascript実行
		$objPage->tpl_onload = "window.close();";
	}
	break;
case 'csv':
    // 画面遷移の正当性チェック
    //if (sfIsValidTransition($objSess) == false) {
    //    sfDispError(INVALID_MOVE_ERRORR);
    //}
    $csv = lgGetCsvData();
    sfCSVDownload($csv, 'ebis_tag_');
    exit;
default:
	$arrRet = $objQuery->select("sub_data", "dtb_module", "module_id = ?", array(EBIS_TAG_MID));
	$arrSubData = unserialize($arrRet[0]['sub_data']);
	$objFormParam->setParam($arrSubData);
    
    // ユニークIDを埋め込み
    // $objPage->tpl_uniqid = $objSess->getUniqId();
	break;
}

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);		//変数をテンプレートにアサインする
$objView->display($objPage->tpl_mainpage);		//テンプレートの出力
//-------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam($objFormParam) {
    $objFormParam->addParam("ログインURL", "login_url", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "URL_CHECK"));
    $objFormParam->addParam("EBiS引数", "cid", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    
    $objFormParam->addParam("顧客ID", "m1id", INT_LEN, "", array("MAX_LENGTH_CHECK", 'NUM_CHECK'));
    $objFormParam->addParam("購入金額", "a1id", INT_LEN, "", array("MAX_LENGTH_CHECK", 'NUM_CHECK'));
    
    for ($i = 1; $i <= EBiS_TAG_OPTIONS_MAX; $i++) {
        $title = "任意項目$i";
        $name  = 'o' . $i . 'id';
        $objFormParam->addParam(
            $title, $name, INT_LEN, "",
            array("MAX_LENGTH_CHECK", "NUM_CHECK")
        );
    }
	return $objFormParam;
}

/**
 * カテゴリ文字列を取得(カテゴリ＞カテゴリ＞カテゴリ＞)
 * 
 * @param int $category_id カテゴリID
 * @param str $sep カテゴリの区切り文字
 * return カテゴリ文字列(カテゴリ＞カテゴリ＞カテゴリ＞)
 */ 
function lfGetCategoriesStr($category_id, $sep = ' > ') {
    $tbl_category = 'dtb_category';
    
    // 親カテゴリIDの配列
    $arrParentsCatId = sfGetParents(null, $tbl_category, 'parent_category_id', 'category_id', $category_id);
    
    // WHERE句を構築
    $where = str_repeat('category_id = ? OR ' , count($arrParentsCatId));
    $where = preg_replace('/OR $/', '', $where);

    // カテゴリ名を取得
    $objQuery = new SC_Query();
    $arrRet   = $objQuery->select('category_name', $tbl_category, $where, $arrParentsCatId);
    
    // カテゴリ＞カテゴリ＞カテゴリ...を構築
    $categories_str = '';
    foreach($arrRet as $category) {
        $categories_str .= $category['category_name'] . $sep;
    }
    
    return $categories_str;
}

/** CSV取得 **/
function lgGetCsvData() {
    $csv  = '"ページID","タイトル","URL"' . "\n";
    $csv .= lfGetDetailPageCSV();
    $csv .= lfGetListPageCSV();
    $csv .= lfGetFrontPageCSV();
    return $csv;
}

/** 商品詳細ページのCSVを取得 **/
function lfGetDetailPageCSV() {
    $table    = 'dtb_products';
    $colmuns  = 'product_id, name, category_id';
    $objQuery = new SC_Query();
    
    $arrRet = $objQuery->select($colmuns, $table);
    
    $arrCSV = array();
    foreach ($arrRet as $index => $product) {
        $id  = $product['product_id'];
        $url = SITE_URL . 'products/detail.php?product_id=';
        $title = lfGetCategoriesStr($product['category_id']) . $product['name'];
        
        $arrCSV[$index]['page_id']    = 'detail-p' . $id;
        $arrCSV[$index]['page_title'] = $title;
        $arrCSV[$index]['url']        = $url . $id;
    }
    
    return lfCreateCSV($arrCSV);
}

/** 商品一覧ページのCSVを取得 **/
function lfGetListPageCSV() {
    $table    = 'dtb_category';
    $colmuns  = 'category_id, category_name';
    $objQuery = new SC_Query();
    
    $arrRet = $objQuery->select($colmuns, $table);
    
    $arrCSV = array();
    foreach ($arrRet as $index => $category) {
        $id  = $category['category_id'];
        $url = SITE_URL . 'products/list.php?category_id=';
        $title = $category['category_name'];
        
        $arrCSV[$index]['page_id']    = 'list-c' . $id;
        $arrCSV[$index]['page_title'] = $title;
        $arrCSV[$index]['url']        = $url . $id;
    }
    return lfCreateCSV($arrCSV);
}

function lfCreateCSV ($arrCSV) {
    $csv_str = '';
    $max = count($arrCSV);
    for ($i=0; $i < $max; $i++) {
        foreach (array('page_id', 'page_title', 'url') as $key) {
            $arrCSV[$i][$key] = sprintf(
                '"%s"',
                str_replace('"', '""', $arrCSV[$i][$key])
            );
        }
        $csv_str .= join(',', $arrCSV[$i]) . "\n";
    }
    
    return $csv_str;
}

/** その他ページのCSVを取得 **/
function lfGetFrontPageCSV() {
    // 項目追加の際は下記連想配列を追加。
    // page_title,urlは任意、ない場合はpage_idから自動生成される
    // 'page_id' => 'top', 'page_title' => 'トップ' , 'url' => 'index.php'
    $arrList = array(
        array('page_id' => 'top', 'page_title' => 'トップ' , 'url' => 'index.php'),
        array('page_id' => 'abouts_index', 'page_title' => '当サイトについて'),
        array('page_id' => 'cart_index', 'page_title' => '買い物かご（トップ）'),
        array('page_id' => 'contact_index', 'page_title' => 'お問い合わせ（入力）'),
        array('page_id' => 'contact_confirm', 'page_title' => 'お問い合わせ（確認）', 'url' => 'contact/index.php'),
        array('page_id' => 'contact_complete', 'page_title' => 'お問い合わせ（完了）'),
        array('page_id' => 'order_index', 'page_title' => '購入（入力）'),
        array('page_id' => 'entry_kiyaku', 'page_title' => 'ご利用規約'),
        array('page_id' => 'entry_index', 'page_title' => '会員登録（入力'),
        array('page_id' => 'entry_confirm', 'page_title' => '会員登録（確認）', 'url' => 'entry/index.php'),
        array('page_id' => 'regist_complete', 'page_title' => '会員登録（完了）', 'url' => 'entry/complete.php'),
        array('page_id' => 'shopping_deliv', 'page_title' => '購入（お届け先指定）'),
        array('page_id' => 'shopping_payment', 'page_title' => '購入（お支払い方法指定）'),
        array('page_id' => 'shopping_confirm', 'page_title' => '購入（確認）'),
        array('page_id' => 'thanks', 'page_title' => '購入（完了)', 'url' => 'shopping/complete.php'),
        array('page_id' => 'mypage_index', 'page_title' => 'MYページ（トップ）'),
        array('page_id' => 'mypage_change', 'page_title' => 'MYページ > 会員登録内容変更（入力）'),
        array('page_id' => 'mypage_change_confirm', 'page_title' => 'MYページ > 会員登録内容変更（確認）', 'url' => 'mypage/change.php'),
        array('page_id' => 'mypage_change_complete', 'page_title' => 'MYページ > 会員登録内容変更（完了）', 'url' => 'mypage/change_complete.php'),
    );
    
    foreach ($arrList as $key => $list) {
        if ( empty($arrList[$key]['page_title']) ) {
            $arrList[$key]['page_title'] = $arrList[$key]['page_id'];
        }
        
        if ( empty($arrList[$key]['url']) ) {
            $url = SITE_URL . str_replace('_', '/', $arrList[$key]['page_id']);
            $arrList[$key]['url'] = $url . '.php';
        } else {
            $arrList[$key]['url'] = SITE_URL . $arrList[$key]['url'];
        }
    }
    
    return lfCreateCSV($arrList);
}

?>