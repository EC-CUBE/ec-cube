<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'products/review.tpl';
		global $arrRECOMMEND;
		$this->arrRECOMMEND = $arrRECOMMEND;
		global $arrSex;
		$this->arrSex = $arrSex;
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query(); 

//---- 登録用カラム配列
$arrRegistColumn = array(
							 array(  "column" => "review_id", "convert" => "aKV" ),
							 array(  "column" => "product_id", "convert" => "aKV" ),
							 array(  "column" => "reviewer_name", "convert" => "aKV" ),
							 array(  "column" => "reviewer_url", "convert" => "a"),
							 array(  "column" => "sex", "convert" => "n" ),
							 array(  "column" => "email", "convert" => "a" ),
							 array(  "column" => "recommend_level", "convert" => "n" ),
							 array(  "column" => "title", "convert" => "aKV" ),
							 array(  "column" => "comment", "convert" => "aKV" ),

						);
switch ($_POST['mode']){
case 'confirm':
	$arrForm = lfConvertParam($_POST, $arrRegistColumn);
	$objPage->arrErr = lfErrorCheck($arrForm);
	//重複メッセージの判定
	$flag = $objQuery->count("dtb_review","product_id = ? AND title = ? ", array($arrForm['product_id'], $arrForm['title']));

	if ($flag > 0){
		$objPage->arrErr['title'] .= "重複したタイトルは登録できません。";
	}
		
	//エラーチェック
	if($objPage->arrErr == ""){
		//重複タイトルでない
		if($flag == 0){
			//商品名の取得
			$arrForm['name'] = $objQuery->get("dtb_products", "name", "product_id = ? ", array($arrForm['product_id']));
			$objPage->arrForm = $arrForm;
			$objPage->tpl_mainpage = 'products/review_confirm.tpl';
		}
	} else {
		//商品名の取得
		$arrForm['name'] = $objQuery->get("dtb_products", "name", "product_id = ? ", array($arrForm['product_id']));	
		$objPage->arrForm = $arrForm;
	}
	break;

case 'return':
	foreach($_POST as $key => $val){
		$objPage->arrForm[ $key ] = $val;
	}
	
	//商品名の取得
	$objPage->arrForm['name'] = $objQuery->get("dtb_products", "name", "product_id = ? ", array($objPage->arrForm['product_id']));
	if(empty($objPage->arrForm['name'])) {
		sfDispSiteError(PAGE_ERROR);
	}
	break;

case 'complete':
	$arrForm = lfConvertParam($_POST, $arrRegistColumn);
	$arrErr = lfErrorCheck($arrForm);
	//重複メッセージの判定
	$flag = $objQuery->count("dtb_review","product_id = ? AND title = ? ", array($arrForm['product_id'], $arrForm['title']));
	//エラーチェック
	if ($arrErr == ""){
		//重複タイトルでない
		if($flag == 0) {
			//登録実行
			lfRegistRecommendData($arrForm, $arrRegistColumn);
			//レビュー書き込み完了ページへ
			header("Location: " . sfGetCurrentUri() . "/review_complete.php");
			exit;
		}
	} else {
		if($flag > 0) {
			sfDispSiteError(PAGE_ERROR);
		}
	}
	break;

default:
	if(sfIsInt($_GET['product_id'])) {
		//商品情報の取得
		$arrForm = $objQuery->select("product_id, name", "dtb_products", "del_flg = 0 AND status = 1 AND product_id=?", array($_GET['product_id']));
		if(empty($arrForm)) {
			sfDispSiteError(PAGE_ERROR);
		}
		$objPage->arrForm = $arrForm[0];
	}
	break;

}

$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);					

//-----------------------------------------------------------------------------------------------------------------------------------


//エラーチェック

function lfErrorCheck() {
	$objErr = new SC_CheckError();
	$objErr->doFunc(array("商品ID", "product_id", INT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));			
	$objErr->doFunc(array("投稿者名", "reviewer_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("URL", "reviewer_url", MTEXT_LEN), array("MAX_LENGTH_CHECK", "URL_CHECK"));
	$objErr->doFunc(array("おすすめレベル", "recommend_level"), array("SELECT_CHECK"));
	$objErr->doFunc(array("タイトル", "title", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("コメント", "comment", LTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    
    if (REVIEW_ALLOW_URL == false) {
        // コメント欄へのURLの入力を禁止
        global $arrReviewDenyURL;
        $objErr->doFunc(array("URL", "comment", $arrReviewDenyURL), array("PROHIBITED_STR_CHECK"));
    }
    
	return $objErr->arrErr;
}

//----　取得文字列の変換
function lfConvertParam($array, $arrRegistColumn) {
	/*
	 *	文字列の変換
	 *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
	 *	C :  「全角ひら仮名」を「全角かた仮名」に変換
	 *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します	
	 *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
	 *  a :  全角英数字を半角英数字に変換する
	 */
	// カラム名とコンバート情報
	foreach ($arrRegistColumn as $data) {
		$arrConvList[ $data["column"] ] = $data["convert"];
	}
	// 文字変換
	foreach ($arrConvList as $key => $val) {
		// POSTされてきた値のみ変換する。
		if(strlen(($array[$key])) > 0) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}

//登録実行
function lfRegistRecommendData ($array, $arrRegistColumn) {
	global $objQuery;
	
	// 仮登録
	foreach ($arrRegistColumn as $data) {
		if (strlen($array[ $data["column"] ]) > 0 ) {
			$arrRegist[ $data["column"] ] = $array[ $data["column"] ];
		}
	}
	$arrRegist['create_date'] = 'now()';
	$arrRegist['update_date'] = 'now()';
	$arrRegist['creator_id'] = '0';
	//-- 登録実行
	$objQuery->begin();
	$objQuery->insert("dtb_review", $arrRegist);
	$objQuery->commit();
}

?>