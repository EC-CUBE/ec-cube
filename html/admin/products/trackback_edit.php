<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
/*
 * FIXME トラックバック機能の移植完了後に修正する
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	function LC_Page() {
		$this->tpl_mainpage = 'products/trackback_edit.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';
		$this->tpl_subno = 'trackback';
		$this->tpl_subtitle = 'トラックバック管理';
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

// 状態の設定
$objPage->arrTrackBackStatus = $arrTrackBackStatus;

//取得文字列の変換用カラム
$arrRegistColumn = array (
						array( "column" => "update_date"),
						array( "column" => "status"),
						array(	"column" => "title","convert" => "KVa"),
						array(	"column" => "excerpt","convert" => "KVa"),
						array(	"column" => "blog_name","convert" => "KVa"),
						array(	"column" => "url","convert" => "KVa"),
						array(	"column" => "del_flg","convert" => "n")
					);

// トラックバックIDを渡す
$objPage->tpl_trackback_id = $_POST['trackback_id'];
// トラックバック情報のカラムの取得
$objPage->arrTrackback = lfGetTrackbackData($_POST['trackback_id']);

// 商品ごとのトラックバック表示数取得
$count = $objQuery->count("dtb_trackback", "del_flg = 0 AND product_id = ?", array($objPage->arrTrackback['product_id']));
// 両方選択可能
$objPage->tpl_status_change = true;

switch($_POST['mode']) {
	// 登録
	case 'complete':
		//フォーム値の変換
		$arrTrackback = lfConvertParam($_POST, $arrRegistColumn);
		$objPage->arrErr = lfCheckError($arrTrackback);
		//エラー無し

		if (!$objPage->arrErr) {
			//レビュー情報の編集登録
			lfRegistTrackbackData($arrTrackback, $arrRegistColumn);
			$objPage->arrTrackback = $arrTrackback;
			$objPage->tpl_onload = "confirm('登録が完了しました。');";
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
	$objErr->doFunc(array("ブログ名", "blog_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("ブログ記事タイトル", "title", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("ブログ記事内容", "excerpt", LTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("ブログURL", "url", URL_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("状態", "status"), array("SELECT_CHECK"));
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

// トラックバック情報の取得
function lfGetTrackbackData($trackback_id) {
	global $objPage;
	global $objQuery;
	$select = "tra.trackback_id, tra.product_id, tra.blog_name, tra.title, tra.excerpt, ";
	$select .= "tra.url, tra.status, tra.create_date, tra.update_date, pro.name ";
	$from = "dtb_trackback AS tra LEFT JOIN dtb_products AS pro ON tra.product_id = pro.product_id ";
	$where = "tra.del_flg = 0 AND pro.del_flg = 0 AND tra.trackback_id = ? ";
	$arrTrackback = $objQuery->select($select, $from, $where, array($trackback_id));
	if(!empty($arrTrackback)) {
		$objPage->arrTrackback = $arrTrackback[0];
	} else {
		sfDispError("");
	}
	return $objPage->arrTrackback;
}

// トラックバック情報の編集登録
function lfRegistTrackbackData($array, $arrRegistColumn) {
	global $objQuery;

	foreach ($arrRegistColumn as $data) {
		if (strlen($array[ $data["column"] ]) > 0 ) {
			$arrRegist[ $data["column"] ] = $array[ $data["column"] ];
		}
		if ($data['column'] == 'update_date'){
			$arrRegist['update_date'] = 'now()';
		}
	}
	//登録実行
	$objQuery->begin();
	$objQuery->update("dtb_trackback", $arrRegist, "trackback_id = '".$_POST['trackback_id']."'");
	$objQuery->commit();
}
?>