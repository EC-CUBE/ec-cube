<?php

require_once("../../require.php");
require_once(ROOT_DIR."data/include/page_layout.inc");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_mainpage = 'basis/seo.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_subno = 'seo';
		$this->tpl_mainno = 'basis';
		$this->tpl_subtitle = 'SEO管理';
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrTAXRULE;
		$this->arrTAXRULE = $arrTAXRULE;
		
	}
}


$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// 認証可否の判定
sfIsSuccess($objSess);

// データの取得
$arrPageData = lfgetPageData(" edit_flg = 2 ");
$objPage->arrPageData = $arrPageData;

$page_id = $_POST['page_id'];

if($_POST['mode'] == "confirm") {
	
	// エラーチェック
	$objPage->arrErr[$page_id] = lfErrorCheck($_POST['meta'][$page_id]);
	
	// エラーがなければデータを更新
	if(count($objPage->arrErr[$page_id]) == 0) {
		// 更新データ配列生成
		$arrUpdData = array($_POST['meta'][$page_id]['author'], $_POST['meta'][$page_id]['description'], $_POST['meta'][$page_id]['keyword'], $page_id);
		// データ更新
		lfUpdPageData($arrUpdData);
	}else{	
		// POSTのデータを再表示
		$arrPageData = lfSetData($arrPageData, $_POST['meta']);
		$objPage->arrPageData = $arrPageData;
	}
}

// エラーがなければデータの取得
if(count($objPage->arrErr[$page_id]) == 0) {
	// データの取得
	$arrPageData = lfgetPageData(" edit_flg = 2 ");
	$objPage->arrPageData = $arrPageData;
}

// 表示･非表示切り替え
$arrDisp_flg = array();
foreach($arrPageData as $key => $val){
	$arrDisp_flg[$val['page_id']] = $_POST['disp_flg'.$val['page_id']];
}

$objPage->disp_flg = $arrDisp_flg;

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//--------------------------------------------------------------------------------------------------------------------------------------
/**************************************************************************************************************
 * 関数名	：lfUpdPageData
 * 処理内容	：ページレイアウトテーブルにデータ更新を行う
 * 引数		：更新データ
 * 戻り値	：更新結果
 **************************************************************************************************************/
function lfUpdPageData($arrUpdData = array()){
	$objQuery = new SC_Query();
	$sql = "";

	// SQL生成
	$sql .= " UPDATE ";
	$sql .= "     dtb_pagelayout ";
	$sql .= " SET ";
	$sql .= "     author = ? , ";
	$sql .= "     description = ? , ";
	$sql .= "     keyword = ? ";
	$sql .= " WHERE ";
	$sql .= "     page_id = ? ";
	$sql .= " ";

	// SQL実行
	$ret = $objQuery->query($sql, $arrUpdData);
	
	return $ret;	
}

/**************************************************************************************************************
 * 関数名	：lfErrorCheck
 * 処理内容	：入力項目のエラーチェックを行う
 * 引数		：エラーチェック対象データ
 * 戻り値	：エラー内容
 **************************************************************************************************************/
function lfErrorCheck($array) {
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("メタタグ:Author", "author", STEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("メタタグ:Description", "description", STEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("メタタグ:Keywords", "keyword", STEXT_LEN), array("MAX_LENGTH_CHECK"));

	return $objErr->arrErr;
}

/**************************************************************************************************************
 * 関数名	：lfSetData
 * 処理内容	：テンプレート表示データに値をセットする
 * 引数1	：表示元データ
 * 引数2	：表示データ
 * 戻り値	：表示データ
 **************************************************************************************************************/
function lfSetData($arrPageData, $arrDispData){
	
	foreach($arrPageData as $key => $val){
		$page_id = $val['page_id'];
		$arrPageData[$key]['author'] = $arrDispData[$page_id]['author'];
		$arrPageData[$key]['description'] = $arrDispData[$page_id]['description'];
		$arrPageData[$key]['keyword'] = $arrDispData[$page_id]['keyword'];
	}
	
	return $arrPageData;
}

?>