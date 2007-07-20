<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 * 配送先の追加
 */
require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = 'shopping/deliv_addr.tpl';
		$this->tpl_title = "新しいお届け先の追加";
	}
}

$objPage = new LC_Page();
$objView = new SC_MobileView(false);
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();
$objConn = new SC_DBConn();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();

//ログイン判定
if (!$objCustomer->isLoginSuccess()){
	sfDispSiteError(CUSTOMER_ERROR, "", false, "", true);
}

$objPage->arrForm = $_POST;
$objPage->arrPref = $arrPref;
//-- データ設定
foreach($_POST as $key => $val) {
	if ($key != "mode" && $key != "return" && $key != "submit" && $key != session_name()) {
		$objPage->list_data[ $key ] = $val;
	}
}
// ユーザユニークIDの取得と購入状態の正当性をチェック
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

//別のお届け先ＤＢ登録用カラム配列
$arrRegistColumn = array(
							 array(  "column" => "name01",		"convert" => "aKV" ),
							 array(  "column" => "name02",		"convert" => "aKV" ),
							 array(  "column" => "kana01",		"convert" => "CKV" ),
							 array(  "column" => "kana02",		"convert" => "CKV" ),
							 array(  "column" => "zip01",		"convert" => "n" ),
							 array(  "column" => "zip02",		"convert" => "n" ),
							 array(  "column" => "pref",		"convert" => "n" ),
							 array(  "column" => "addr01",		"convert" => "aKV" ),
							 array(  "column" => "addr02",		"convert" => "aKV" ),
							 array(  "column" => "tel01",		"convert" => "n" ),
							 array(  "column" => "tel02",		"convert" => "n" ),
							 array(  "column" => "tel03",		"convert" => "n" ),
						);

// 戻るボタン用処理
if (!empty($_POST["return"])) {
	switch ($_POST["mode"]) {
	case 'complete':
		$_POST["mode"] = "set2";
		break;
	case 'set2':
		$_POST["mode"] = "set1";
		break;
	default:
		header("Location: " . gfAddSessionId('deliv.php'));
		exit;
	}
}

switch ($_POST['mode']){
	case 'set1':
		$objPage->arrErr = lfErrorCheck1($objPage->arrForm);
		if (count($objPage->arrErr) == 0 && empty($_POST["return"])) {
			$objPage->tpl_mainpage = 'shopping/set1.tpl';

			$checkVal = array("pref", "addr01", "addr02", "addr03", "tel01", "tel02", "tel03");
			foreach($checkVal as $key) {
				unset($objPage->list_data[$key]);
			}

			// 郵便番号から住所の取得
			if (@$objPage->arrForm['pref'] == "" && @$objPage->arrForm['addr01'] == "" && @$objPage->arrForm['addr02'] == "") {
				$address = lfGetAddress($_REQUEST['zip01'].$_REQUEST['zip02']);

				$objPage->arrForm['pref'] = @$address[0]['state'];
				$objPage->arrForm['addr01'] = @$address[0]['city'] . @$address[0]['town'];
			}
		} else {
			$checkVal = array("name01", "name02", "kana01", "kana02", "zip01", "zip02");
			foreach($checkVal as $key) {
				unset($objPage->list_data[$key]);
			}
		}
		break;
	case 'set2':
		$objPage->arrErr = lfErrorCheck2($objPage->arrForm);
		if (count($objPage->arrErr) == 0 && empty($_POST["return"])) {
			$objPage->tpl_mainpage = 'shopping/set2.tpl';
		} else {
			$objPage->tpl_mainpage = 'shopping/set1.tpl';

			$checkVal = array("pref", "addr01", "addr02", "addr03", "tel01", "tel02", "tel03");
			foreach($checkVal as $key) {
				unset($objPage->list_data[$key]);
			}
		}
		break;
	case 'complete':
		$objPage->arrErr = lfErrorCheck($objPage->arrForm);
		if (count($objPage->arrErr) == 0) {
			// 登録
			$other_deliv_id = lfRegistData($_POST,$arrRegistColumn);

			// 登録済みの別のお届け先を受注一時テーブルに書き込む
			lfRegistOtherDelivData($uniqid, $objCustomer, $other_deliv_id);

			// 正常に登録されたことを記録しておく
			$objSiteSess->setRegistFlag();
			// お支払い方法選択ページへ移動
			header("Location: " . gfAddSessionId(MOBILE_URL_SHOP_PAYMENT));
			exit;
		} else {
			sfDispSiteError(CUSTOMER_ERROR, "", false, "", true);
		}
		break;
	default:
		$deliv_count = $objQuery->count("dtb_other_deliv", "customer_id=?", array($objCustomer->getValue('customer_id')));
		if ($deliv_count >= DELIV_ADDR_MAX){
			sfDispSiteError(FREE_ERROR_MSG, "", false, "最大登録件数を超えています。");
		}
}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-------------------------------------------------------------------------------------------------------------

/* エラーチェック */
function lfErrorCheck() {
	$objErr = new SC_CheckError();
	
	$objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お名前（カナ/姓）", 'kana01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("お名前（カナ/名）", 'kana02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("市区町村", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("番地", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("電話番号1", 'tel01'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("電話番号2", 'tel02'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("電話番号3", 'tel03'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("電話番号", "tel01", "tel02", "tel03", TEL_LEN) ,array("TEL_CHECK"));
	return $objErr->arrErr;
	
}

/* エラーチェック */
function lfErrorCheck1() {
	$objErr = new SC_CheckError();
	
	$objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お名前（カナ/姓）", 'kana01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("お名前（カナ/名）", 'kana02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	return $objErr->arrErr;
	
}

/* エラーチェック */
function lfErrorCheck2() {
	$objErr = new SC_CheckError();
	
	$objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("市区町村", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("番地", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("電話番号1", 'tel01'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("電話番号2", 'tel02'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("電話番号3", 'tel03'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("電話番号", "tel01", "tel02", "tel03", TEL_LEN) ,array("TEL_CHECK"));
	return $objErr->arrErr;
	
}



/* 登録実行 */
function lfRegistData($array, $arrRegistColumn) {
	global $objConn;
	global $objCustomer;
	
	foreach ($arrRegistColumn as $data) {
		if (strlen($array[ $data["column"] ]) > 0) {
			$arrRegist[ $data["column"] ] = $array[ $data["column"] ];
		}
	}
	
	$arrRegist['customer_id'] = $objCustomer->getvalue('customer_id');
	
	//-- 編集登録実行
	$objConn->query("BEGIN");
	if ($array['other_deliv_id'] != ""){
		$objConn->autoExecute("dtb_other_deliv", $arrRegist, "other_deliv_id='" .addslashes($array["other_deliv_id"]). "'");
	}else{
		$objConn->autoExecute("dtb_other_deliv", $arrRegist);

		$sqlse = "SELECT max(other_deliv_id) FROM dtb_other_deliv WHERE customer_id = ?";
		$array['other_deliv_id'] = $objConn->getOne($sqlse, array($arrRegist['customer_id']));
	}

	$objConn->query("COMMIT");

	return $array['other_deliv_id'];
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

// 郵便番号から住所の取得
function lfGetAddress($zipcode) {
	global $arrPref;

	$conn = new SC_DBconn(ZIP_DSN);

	// 郵便番号検索文作成
	$zipcode = mb_convert_kana($zipcode ,"n");
	$sqlse = "SELECT state, city, town FROM mtb_zip WHERE zipcode = ?";

	$data_list = $conn->getAll($sqlse, array($zipcode));

	// インデックスと値を反転させる。
	$arrREV_PREF = array_flip($arrPref);

	/*
		総務省からダウンロードしたデータをそのままインポートすると
		以下のような文字列が入っているので	対策する。
		・（１・１９丁目）
		・以下に掲載がない場合
	*/
	$town =  $data_list[0]['town'];
	$town = ereg_replace("（.*）$","",$town);
	$town = ereg_replace("以下に掲載がない場合","",$town);
	$data_list[0]['town'] = $town;
	$data_list[0]['state'] = $arrREV_PREF[$data_list[0]['state']];

	return $data_list;
}

/* 別のお届け先住所を一時受注テーブルへ */
function lfRegistOtherDelivData($uniqid, $objCustomer, $other_deliv_id) {
	// 登録データの作成
	$sqlval['order_temp_id'] = $uniqid;
	$sqlval['update_date'] = 'Now()';
	$sqlval['customer_id'] = $objCustomer->getValue('customer_id');
	$sqlval['order_birth'] = $objCustomer->getValue('birth');

	$objQuery = new SC_Query();
	$where = "other_deliv_id = ?";
	$arrRet = $objQuery->select("*", "dtb_other_deliv", $where, array($other_deliv_id));
	
	$sqlval['deliv_check'] = '1';
    $sqlval['deliv_name01'] = $arrRet[0]['name01'];
    $sqlval['deliv_name02'] = $arrRet[0]['name02'];
    $sqlval['deliv_kana01'] = $arrRet[0]['kana01'];
    $sqlval['deliv_kana02'] = $arrRet[0]['kana02'];
    $sqlval['deliv_zip01'] = $arrRet[0]['zip01'];
    $sqlval['deliv_zip02'] = $arrRet[0]['zip02'];
    $sqlval['deliv_pref'] = $arrRet[0]['pref'];
    $sqlval['deliv_addr01'] = $arrRet[0]['addr01'];
    $sqlval['deliv_addr02'] = $arrRet[0]['addr02'];
    $sqlval['deliv_tel01'] = $arrRet[0]['tel01'];
    $sqlval['deliv_tel02'] = $arrRet[0]['tel02'];
	$sqlval['deliv_tel03'] = $arrRet[0]['tel03'];
	sfRegistTempOrder($uniqid, $sqlval);
}

?>
