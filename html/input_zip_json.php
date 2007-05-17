<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("./require.php");
//header("Content-Type: text/json; charset=euc-jp");


$conn = new SC_DBconn(ZIP_DSN);

// 入力エラーチェック
$arrErr = fnErrorCheck();

// 入力エラーの場合は終了
if(count($arrErr) == 0) {

// 郵便番号検索文作成
$zipcode = $_GET['zip1'].$_GET['zip2'];
$zipcode = mb_convert_kana($zipcode ,"n");
$sqlse = "SELECT state, city, town FROM mtb_zip WHERE zipcode = ?";

$data_list = $conn->getAll($sqlse, array($zipcode));

// インデックスと値を反転させる。
$arrREV_PREF = array_flip($arrPref);

$state = $arrREV_PREF[$data_list[0]['state']];
$city = $data_list[0]['city'];
$town =  $data_list[0]['town'];
/*
	総務省からダウンロードしたデータをそのままインポートすると
	以下のような文字列が入っているので	対策する。
	・（１~１９丁目）
	・以下に掲載がない場合
*/
$town = ereg_replace("（.*）$","",$town);
$town = ereg_replace("以下に掲載がない場合","",$town);

// 郵便番号が発見された場合
if(count($data_list) > 0) {
	echo "{ 'POST' : 'test' , 'GET' : 'test' }";
} else {
    $zip = $_GET['zip01'].$_GET['zip02'];
echo "{'MSG' : '住所が見つかりませんでした。' , 'ZIP' : '$zip','DATA_LIST':'$data_list'}" ;
    }
}
/* 入力エラーのチェック */
function fnErrorCheck() {
	// エラーメッセージ配列の初期化
	$objErr = new SC_CheckError();
	
	// 郵便番号
	$objErr->doFunc( array("郵便番号1",'zip1',ZIP01_LEN ) ,array( "NUM_COUNT_CHECK" ) );
	$objErr->doFunc( array("郵便番号2",'zip2',ZIP02_LEN ) ,array( "NUM_COUNT_CHECK" ) );
	
	return $objErr->arrErr;
}

?>