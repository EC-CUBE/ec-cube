<?php

require_once("../require.php");

class LC_Page {
	var $arrSession;
	function LC_Page() {
		$this->tpl_mainpage = 'products/review_edit.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';		
		$this->tpl_subno = 'review';
		global $arrRECOMMEND;
		$this->arrRECOMMEND = $arrRECOMMEND;
		$this->tpl_subtitle = 'レビュー管理';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();
// 認証可否の判定
sfIsSuccess($objSess);

//検索ワードの引継ぎ
foreach ($_POST as $key => $val){
	if (ereg("^search_", $key)){
	$objPage->arrSearchHidden[$key] = $val;
	}
}

//取得文字列の変換用カラム
$arrRegistColumn = array (		
								array( "column" => "status"),
								array( "column" => "recommend_level"),		
								array(	"column" => "title",		"convert" => "KVa"),
								array(	"column" => "comment",		"convert" => "KVa")	
							);

//レビューIDを渡す
$objPage->tpl_review_id = $_POST['review_id'];
//レビュー情報のカラムの取得
$objPage->arrReview = lfGetReviewData($_POST['review_id']);
//登録済みのステータスを渡す
$objPage->tpl_pre_status = $objPage->arrReview['status'];
//商品ごとのレビュー表示数取得
$count = $objQuery->count("dtb_review", "del_flg=0 AND status=1 AND product_id=?", array($objPage->arrReview['product_id']));
//レビュー表示数が設定値以上の場合
if ($count >= REVIEW_REGIST_MAX){
	//表示は選択できない
	$objPage->tpl_status_change = false;
}else{
	//両方選択可能
	$objPage->tpl_status_change = true;
}
					
switch($_POST['mode']) {
//登録
case 'complete':
	//フォーム値の変換
	$arrReview = lfConvertParam($_POST, $arrRegistColumn);
	$objPage->arrErr = lfCheckError($arrReview);
	//非表示から表示にステータスを切り替えて、商品ごとの表示レビュー数が設定値を超えているとき
	if ($arrReview['pre_status'] == '2' && $arrReview['status'] == '1' && $count >= REVIEW_REGIST_MAX){
		$objPage->arrErr['status'] = '※ レビュー表示数は5件までです。';
		$objPage->arrReview = $arrReview;
	} else {
		//エラー無し
		if (!$objPage->arrErr){
			//レビュー情報の編集登録
			lfRegistReviewData($arrReview, $arrRegistColumn);
			$objPage->arrReview = $arrReview;
			$objPage->tpl_onload = "confirm('登録が完了しました。');";
		}
	}
	break;
default:
	break;
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//------------------------------------------------------------------------------------------------------------------------------------

// 入力エラーチェック
function lfCheckError($array) {
	$objErr = new SC_CheckError($array);
		$objErr->doFunc(array("おすすめレベル", "recommend_level"), array("SELECT_CHECK"));
		$objErr->doFunc(array("タイトル", "title", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
		$objErr->doFunc(array("コメント", "comment", LTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
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

//レビュー情報の取得
function lfGetReviewData($review_id){
	global $objPage;
	global $objQuery;
	$select="review_id, A.product_id, reviewer_name, sex, recommend_level, ";
	$select.="reviewer_url, title, comment, A.status, A.create_date, A.update_date, name";
	$from = "dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id ";
	$where = "A.del_flg = 0 AND B.del_flg = 0 AND review_id = ? ";
	$arrReview = $objQuery->select($select, $from, $where, array($review_id));
	if(!empty($arrReview)) {
		$objPage->arrReview = $arrReview[0];
	} else {
		sfDispError("");
	}
	return $objPage->arrReview;
}

//レビュー情報の編集登録
function lfRegistReviewData($array, $arrRegistColumn){
	global $objQuery;
	foreach ($arrRegistColumn as $data) {
		if (strlen($array[ $data["column"] ]) > 0 ) {
			$arrRegist[ $data["column"] ] = $array[ $data["column"] ];
		}
	}
	//登録実行
	$objQuery->begin();
	$objQuery->update("dtb_review", $arrRegist, "review_id='".$_POST['review_id']."'");
	$objQuery->getlastquery();
	$objQuery->commit();
}
?>