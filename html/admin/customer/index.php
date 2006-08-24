<?php

require_once("../require.php");
require_once(ROOT_DIR."data/include/csv_output.inc");

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

//---- ページ表示用クラス
class LC_Page {
	var $arrSession;
	var $tpl_mode;
	var $list_data;
	var $search_data;
	var $arrErr;
	var $arrYear;
	var $arrMonth;
	var $arrDay;
	var $arrJob;
	var $arrSex;
	var $arrPageMax;
	var $count;
	var $search_SQL;
	
	var $tpl_strnavi;
	
	var $arrHtmlmail;

	function LC_Page() {
		$this->tpl_mainpage = 'customer/index.tpl';
		$this->tpl_mainno = 'customer';
		$this->tpl_subnavi = '';
		$this->tpl_subno = "index";
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrJob;
		$arrJob["不明"] = "不明";
		$this->arrJob = $arrJob;
		global $arrSex;		
		$this->arrSex = $arrSex;
		global $arrPageRows;
		$this->arrPageRows = $arrPageRows;
		
		global $arrMAILMAGATYPE;
		$this->arrMAILMAGATYPE = $arrMAILMAGATYPE;
		$this->arrHtmlmail[''] = "すべて";
		$this->arrHtmlmail[1] = $arrMAILMAGATYPE[1];
		$this->arrHtmlmail[2] = $arrMAILMAGATYPE[2];		
	}
}



//---- 検索用項目配列
$arrSearchColumn = array(
							array(  "column" => "customer_id",		"convert" => "n" ),
							array(  "column" => "name",				"convert" => "aKV" ),
							array(  "column" => "pref",				"convert" => "n" ),
							array(  "column" => "kana",				"convert" => "CKV" ),
							array(  "column" => "sex",				"convert" => "" ),
							array(  "column" => "b_start_year",		"convert" => "n" ),
							array(  "column" => "b_start_month",	"convert" => "n" ),
							array(  "column" => "b_start_day",		"convert" => "n" ),
							array(  "column" => "b_end_year",		"convert" => "n" ),
							array(  "column" => "b_end_month",		"convert" => "n" ),
							array(  "column" => "b_end_day",		"convert" => "n" ),
							array(  "column" => "tel",				"convert" => "n" ),
							array(  "column" => "job",				"convert" => "" ),
							array(  "column" => "birth_month",		"convert" => "n" ),
							array(  "column" => "email",			"convert" => "a" ),
							array(  "column" => "buy_total_from",	"convert" => "n" ),
							array(  "column" => "buy_total_to",		"convert" => "n" ),
							array(  "column" => "buy_times_from",	"convert" => "n" ),
							array(  "column" => "buy_times_to",		"convert" => "n" ),
							array(  "column" => "start_year",		"convert" => "n" ),
							array(  "column" => "start_month",		"convert" => "n" ),
							array(  "column" => "start_day",		"convert" => "n" ),
							array(  "column" => "end_year",			"convert" => "n" ),
							array(  "column" => "end_month",		"convert" => "n" ),
							array(  "column" => "end_day",			"convert" => "n" ),
							array(  "column" => "page_rows",		"convert" => "n" )

							// 2006/04/20 KAKINAKA-ADD:最終購入日、購入商品コード、購入商品名称、カテゴリを検索項目に追加する START
							,array(  "column" => "buy_start_year",		"convert" => "n" )		//　最終購入日 START 年
							,array(  "column" => "buy_start_month",		"convert" => "n" )		//　最終購入日 START 月
							,array(  "column" => "buy_start_day",		"convert" => "n" )		//　最終購入日 START 日
							,array(  "column" => "buy_end_year",		"convert" => "n" )		//　最終購入日 END 年
							,array(  "column" => "buy_end_month",		"convert" => "n" )		//　最終購入日 END 月
							,array(  "column" => "buy_end_day",			"convert" => "n" )		//　最終購入日 END 日
							,array(  "column" => "buy_product_name",	"convert" => "aKV" )	//　購入商品名
							,array(  "column" => "buy_product_code",	"convert" => "aKV" )	//　購入商品コード
							,array(  "column" => "category_id",			"convert" => "" )		//　カテゴリ
							// 2006/04/20 KAKINAKA-ADD:最終購入日、購入商品コード、購入商品名称、カテゴリを検索項目に追加する END
							
							,array(  "column" => "cell",				"convert" => "n" )		// 2006/05/10 KAKINAKA-ADD:携帯電話を検索項目に追加する END

						 );

//---- ページ初期設定
$objConn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objDate = new SC_Date(1901);
$objPage->arrYear = $objDate->getYear();	//　日付プルダウン設定
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();
$objPage->objDate = $objDate;

//----　CSVダウンロード用
$arrColumnCSV= array(
						0  => array("sql" => "customer_id", "csv" => "customer_id", "header" => "顧客ID"),
						1  => array("sql" => "name01", "csv" => "name01", "header" => "名前1"),
						2  => array("sql" => "name02", "csv" => "name02", "header" => "名前2"),
						3  => array("sql" => "kana01", "csv" => "kana01", "header" => "フリガナ1"),
						4  => array("sql" => "kana02", "csv" => "kana02", "header" => "フリガナ2"),
						5  => array("sql" => "zip01", "csv" => "zip01", "header" => "郵便番号1"),
						6  => array("sql" => "zip02", "csv" => "zip02", "header" => "郵便番号2"),
						7  => array("sql" => "pref", "csv" => "pref", "header" => "都道府県"),
						8  => array("sql" => "addr01", "csv" => "addr01", "header" => "住所1"),
						9  => array("sql" => "addr02", "csv" => "addr02", "header" => "住所2"),
						10 => array("sql" => "email", "csv" => "email", "header" => "E-MAIL"),
						11 => array("sql" => "tel01", "csv" => "tel01", "header" => "TEL1"),
						12 => array("sql" => "tel02", "csv" => "tel02", "header" => "TEL2"),
						13 => array("sql" => "tel03", "csv" => "tel03", "header" => "TEL3"),

						// 2006/05/12 KAKINAKA ADD:携帯電話番号もcsv出力する START
						14 => array("sql" => "cell01", "csv" => "cell01", "header" => "携帯電話番号1"),
						15 => array("sql" => "cell02", "csv" => "cell02", "header" => "携帯電話番号2"),
						16 => array("sql" => "cell03", "csv" => "cell03", "header" => "携帯電話番号3"),
						// 2006/05/12 KAKINAKA ADD:携帯電話番号もcsv出力する END

						17 => array("sql" => "fax01", "csv" => "fax01", "header" => "FAX1"),
						18 => array("sql" => "fax02", "csv" => "fax02", "header" => "FAX2"),
						19 => array("sql" => "fax03", "csv" => "fax03", "header" => "FAX3"),
						20 => array("sql" => "CASE WHEN sex = 1 THEN '男性' ELSE '女性' END AS sex", "csv" => "sex", "header" => "性別"),

						// 2006/05/12 KAKINAKA DEL:職業は出力しない START
						//21 => array("sql" => "job", "csv" => "job", "header" => "職業"),
						// 2006/05/12 KAKINAKA DEL:職業は出力しない END

						21 => array("sql" => "to_char(birth, 'YYYY年MM月DD日') AS birth", "csv" => "birth", "header" => "誕生日"),
						22 => array("sql" => "to_char(first_buy_date, 'YYYY年MM月DD日HH24:MI') AS first_buy_date", "csv" => "first_buy_date", "header" => "初回購入日"),
						23 => array("sql" => "to_char(last_buy_date, 'YYYY年MM月DD日HH24:MI') AS last_buy_date", "csv" => "last_buy_date", "header" => "最終購入日"),
						24 => array("sql" => "buy_times", "csv" => "buy_times", "header" => "購入回数"),
						25 => array("sql" => "point", "csv" => "point", "header" => "ポイント残高"),
						26 => array("sql" => "note", "csv" => "note", "header" => "備考"),
						27 => array("sql" => "to_char(create_date, 'YYYY年MM月DD日HH24:MI') AS create_date", "csv" => "create_date", "header" => "登録日"),
						28 => array("sql" => "to_char(update_date, 'YYYY年MM月DD日HH24:MI') AS update_date", "csv" => "update_date", "header" => "更新日")
					);

//----　顧客情報検索
if($_POST['mode'] == "search") {

	//-- 入力値コンバート
	$objPage->list_data = lfConvertParam($_POST, $arrSearchColumn);

	//-- 入力エラーのチェック
	$objPage->arrErr = lfErrorCheck($objPage->list_data);
	//-- 検索開始と会員情報削除
	if (! is_array($objPage->arrErr)) {

		//-- 顧客削除時		
		if ($_POST["del_mode"] == "delete" && is_numeric($_POST["del_customer_id"])) {
			$sql = "SELECT status,email FROM dtb_customer WHERE customer_id = ? AND delete = 0";
			$result_customer = $objConn->getAll($sql, array($_POST["del_customer_id"]));

			if ($result_customer[0]["status"] == 2) {			//本会員削除
				$arrDel = array("delete" => 1, "update_date" => "NOW()"); 
				$objConn->autoExecute("dtb_customer", $arrDel, "customer_id = " .addslashes($_POST["del_customer_id"]) );
			} elseif ($result_customer[0]["status"] == 1) {		//仮会員削除
				$sql = "DELETE FROM dtb_customer WHERE customer_id = ?";
				$objConn->query($sql, array($_POST["del_customer_id"]));
			}
			$sql = "DELETE FROM dtb_customer_mail WHERE email = ?";
			$objConn->query($sql, array($result_customer[0]["email"]));
		}
			
		$objSelect = new SC_CustomerList($objPage->list_data, "customer");
	
		//-- ページ送りの処理
		if(is_numeric($_POST['page_rows'])) {	
			$page_max = $_POST['page_rows'];
		} else {
			$page_max = SEARCH_PMAX;
		}
				
		$objPage->count = $objConn->getOne( $objSelect->getListCount(), $objSelect->arrVal);
		$objNavi = new SC_PageNavi($_POST['pageno'], $objPage->count, $page_max, "fnCustomerPage", NAVI_PMAX);

		$objPage->tpl_strnavi = $objNavi->strnavi;
		$startno = $objNavi->start_row;

		//-- 検索データ取得
		if ($_POST["csv_mode"] == 'csv') {
			$searchSql = $objSelect->getListCSV($arrColumnCSV);
		} else {
			$objSelect->setLimitOffset($_POST["page_rows"], $startno);
			$searchSql = $objSelect->getList();
		}
	
		$objPage->search_data = $objConn->getAll($searchSql, $objSelect->arrVal);

		//--　CSVダウンロード時
		if ($_POST["csv_mode"] == "csv") {
			$i = 0;
			foreach($arrColumnCSV as $data) {
				$arrColumn[] = $data["csv"];
				if ($i != 0) $header .= ", ";
				$header .= $data["header"];
				$i ++;
			}
			$header .= "\n";

			//-　都道府県/職業の変換
			for($i = 0; $i < count($objPage->search_data); $i ++) {
				$objPage->search_data[$i]["pref"] = $arrPref[ $objPage->search_data[$i]["pref"] ];
				$objPage->search_data[$i]["job"]  = $arrJob[ $objPage->search_data[$i]["job"] ];
			}

			//-　CSV出力
			$data = lfGetCSVData($objPage->search_data, $arrColumn);
			sfCSVDownload($header.$data);
			exit();
		}
	}

}

// 2006/04/18 KAKINAKA-ADD:カテゴリの読込を追加
$objPage->arrCatList = sfGetCategoryList();

//----　ページ表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);


//--------------------------------------------------------------------------------------------------------------------------------------

//---- CSV出力用データ取得
function lfGetCSVData( $array, $arrayIndex){	
	
	for ($i=0; $i<count($array); $i++){
		
		for ($j=0; $j<count($array[$i]); $j++ ){
			if ( $j > 0 ) $return .= ",";
			$return .= "\"";			
			if ( $arrayIndex ){
				$return .= mb_ereg_replace("<","＜",mb_ereg_replace( "\"","\"\"",$array[$i][$arrayIndex[$j]] )) ."\"";	
			} else {
				$return .= mb_ereg_replace("<","＜",mb_ereg_replace( "\"","\"\"",$array[$i][$j] )) ."\"";
			}
		}
		$return .= "\n";			
	}
	
	return $return;
}


//----　取得文字列の変換
function lfConvertParam($array, $arrSearchColumn) {
	/*
	 *	文字列の変換
	 *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
	 *	C :  「全角ひら仮名」を「全角かた仮名」に変換
	 *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します	
	 *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
	 *  a :  全角英数字を半角英数字に変換する
	 */
	// カラム名とコンバート情報
	foreach ($arrSearchColumn as $data) {
		$arrConvList[ $data["column"] ] = $data["convert"];
	}
	// 文字変換
	foreach ($arrConvList as $key => $val) {
		// POSTされてきた値のみ変換する。
		if (! is_array($array[$key]) && strlen($array[$key]) > 0) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}


//---- 入力エラーチェック
function lfErrorCheck($array) {

	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("顧客コード", "customer_id", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("都道府県", "pref", 2), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("顧客名", "name", STEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("顧客名(カナ)", "kana", STEXT_LEN), array("KANA_CHECK", "MAX_LENGTH_CHECK"));

	$objErr->doFunc(array("誕生日(開始日)", "b_start_year", "b_start_month", "b_start_day"), array("CHECK_DATE"));
	$objErr->doFunc(array("誕生日(終了日)", "b_end_year", "b_end_month", "b_end_day"), array("CHECK_DATE"));
	$objErr->doFunc(array("誕生日(開始日)","誕生日(終了日)", "b_start_year", "b_start_month", "b_start_day", "b_end_year", "b_end_month", "b_end_day"), array("CHECK_SET_TERM"));
	
	$objErr->doFunc(array("誕生月", "birth_month", 2), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('メールアドレス', "email", STEXT_LEN) ,array("EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("電話番号", "tel", TEL_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("購入金額(開始)", "buy_total_from", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("購入金額(終了)", "buy_total_to", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	if ( (is_numeric($array["buy_total_from"]) && is_numeric($array["buy_total_to"]) ) && ($array["buy_total_from"] > $array["buy_total_to"]) ) $objErr->arrErr["buy_total_from"] .= "※ 購入金額の指定範囲が不正です。";
	$objErr->doFunc(array("購入回数(開始)", "buy_times_from", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("購入回数(終了)", "buy_times_to", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	if ( (is_numeric($array["buy_times_from"]) && is_numeric($array["buy_times_to"]) ) && ($array["buy_times_from"] > $array["buy_times_to"]) ) $objErr->arrErr["buy_times_from"] .= "※ 購入回数の指定範囲が不正です。";

	$objErr->doFunc(array("登録・更新日(開始日)", "start_year", "start_month", "start_day",), array("CHECK_DATE"));
	$objErr->doFunc(array("登録・更新日(終了日)", "end_year", "end_month", "end_day"), array("CHECK_DATE"));	
	$objErr->doFunc(array("登録・更新日(開始日)","登録・更新日(終了日)", "start_year", "start_month", "start_day", "end_year", "end_month", "end_day"), array("CHECK_SET_TERM"));
	
	$objErr->doFunc(array("表示件数", "page_rows", 3), array("NUM_CHECK","MAX_LENGTH_CHECK"));	

	// 2006/04/20 KAKINAKA-ADD:最終購入日、購入商品コード、購入商品名称を検索項目に追加する START
	$objErr->doFunc(array("最終購入日(開始日)", "buy_start_year", "buy_start_month", "buy_start_day",), array("CHECK_DATE"));	//最終購入日(開始日)
	$objErr->doFunc(array("最終購入(終了日)", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_DATE"));			//最終購入日(終了日)
	//購入金額(from) ＞ 購入金額(to) の場合はエラーとする
	$objErr->doFunc(array("最終購入日(開始日)","登録・更新日(終了日)", "buy_start_year", "buy_start_month", "buy_start_day", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_SET_TERM"));	
	
	$objErr->doFunc(array("購入商品コード", "buy_product_code", STEXT_LEN), array("MAX_LENGTH_CHECK"));						//購入商品コード
	$objErr->doFunc(array("購入商品名", "buy_product_name", STEXT_LEN), array("MAX_LENGTH_CHECK"));							//購入商品名称
	// 2006/04/20 KAKINAKA-ADD:最終購入日、購入商品コード、購入商品名称を検索項目に追加する END

	$objErr->doFunc(array("携帯電話番号", "cell", TEL_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));	// 2006/05/10 KAKINAKA ADD:携帯電話検索を追加
	
	return $objErr->arrErr;
}

?>