<?php
/**
 * 
 * @copyright	2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: ebis_tag.php,v 1.30 2007/07/18 04:02:40 adachi Exp $
 * @link		http://www.lockon.co.jp/
 *
 */

//require_once("./require.php");
//require_once MODULE_PATH . 'ebis_tag_conf.php';

// 認証確認
$objSess = new SC_Session();
sfIsSuccess($objSess);

//ページ管理クラス
class LC_Page {
	//コンストラクタ
	function LC_Page() {
		//メインテンプレートの指定
		$this->tpl_mainpage  = MODULE_PATH . 'mdl_ebis_tag/ebis_tag.tpl';
		$this->tpl_subtitle  = 'EBiSタグ埋め込み機能';
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
	$objFormParam->addParam("ユーザID", "user", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("パスワード", "pass", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
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

/* CSV取得 */
function lgGetCsvData() {
    $csv  = lfGetDetailPageCSV();
    $csv .= lfGetListPageCSV();
    $csv .= lfGetFrontPageCSV();
    return $csv;
}

/* 商品詳細ページのCSVを取得 */
function lfGetDetailPageCSV() {
    $table    = 'dtb_products';
    $colmuns  = 'product_id, name';
    $objQuery = new SC_Query();
    
    $arrRet = $objQuery->select($colmuns, $table);
    
    $arrCSV = array();
    foreach ($arrRet as $key => $product) {
        $id  = $product['product_id'];
        $url = SITE_URL . 'products/detail.php?product_id=';
        $title = str_replace('"', '\"', $product['name']);
        
        $arrCSV[$key]['page_id']    = '"' . 'detail-p' . $id . '"';
        $arrCSV[$key]['page_title'] = '"' . $title . '"';
        $arrCSV[$key]['url']        = '"' . $url . $id . '"';
    }
    
    return lfCreateCSV($arrCSV);
}

/* 商品一覧ページのCSVを取得 */
function lfGetListPageCSV() {
    $table    = 'dtb_category';
    $colmuns  = 'category_id, category_name';
    $objQuery = new SC_Query();
    
    $arrRet = $objQuery->select($colmuns, $table);
    
    $arrCSV = array();
    foreach ($arrRet as $key => $category) {
        $id  = $category['category_id'];
        $url = SITE_URL . 'products/list.php?category_id=';
        $title = str_replace('"', '\"', $category['category_name']);
        
        $arrCSV[$key]['page_id']    = '"' . 'list-c' . $id . '"';
        $arrCSV[$key]['page_title'] = '"' . $title . '"';
        $arrCSV[$key]['url']        = '"' . $url . $id . '"';
    }
    return lfCreateCSV($arrCSV);
}

function lfCreateCSV ($arrCSV) {
    $csv_str = '';
    foreach ($arrCSV as $csv) {
        $csv_str .= join(',', $csv) . "\n";
    }
    return $csv_str;
}

/* その他ページのCSVを取得 */
function lfGetFrontPageCSV() {
    // 項目追加の際は下記連想配列を追加。
    // page_title,urlは任意、ない場合はpage_idから自動生成される
    // 'page_id' => 'top', 'page_title' => '' , 'url' => 'index.php'
    $arrList = array(
        array('page_id' => 'top', 'page_title' => '' , 'url' => 'index.php'),
        array('page_id' => 'abouts_index'),
        array('page_id' => 'cart_index'),
        array('page_id' => 'contact_index'),
        array('page_id' => 'contact_confirm', 'page_title' => '', 'url' => 'contact/index.php'),
        array('page_id' => 'contact_complete'),
        array('page_id' => 'order_index'),
        array('page_id' => 'entry_kiyaku'),
        array('page_id' => 'entry_index'),
        array('page_id' => 'entry_confirm', 'page_title' => '', 'url' => 'entry/index.php'),
        array('page_id' => 'regist_complete', 'page_title' => '', 'url' => 'entry/complete.php'),
        array('page_id' => 'products_favorite'),
        array('page_id' => 'shopping_deliv'),
        array('page_id' => 'shopping_payment'),
        array('page_id' => 'shopping_confirm'),
        array('page_id' => 'thanks', 'page_title' => '', 'url' => 'shopping/complete.php'),
        array('page_id' => 'mypage_index'),
        array('page_id' => 'mypage_change'),
        array('page_id' => 'mypage_change_confirm', 'page_title' => '', 'url' => 'mypage/change.php'),
        array('page_id' => 'mypage_change_complete', 'page_title' => '', 'url' => 'mypage/change_complete.php'),
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