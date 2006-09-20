<?php

require_once("../require.php");
require_once(ROOT_DIR."data/include/csv_output.inc");

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
		$this->tpl_subnavi = 'customer/subnavi.tpl';
		$this->tpl_subno = 'index';
		$this->tpl_pager = ROOT_DIR . 'data/Smarty/templates/admin/pager.tpl';
		$this->tpl_subtitle = '顧客マスタ';
		
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
						14 => array("sql" => "fax01", "csv" => "fax01", "header" => "FAX1"),
						15 => array("sql" => "fax02", "csv" => "fax02", "header" => "FAX2"),
						16 => array("sql" => "fax03", "csv" => "fax03", "header" => "FAX3"),
						17 => array("sql" => "CASE WHEN sex = 1 THEN '男性' ELSE '女性' END AS sex", "csv" => "sex", "header" => "性別"),
						18 => array("sql" => "job", "csv" => "job", "header" => "職業"),
						19 => array("sql" => "to_char(birth, 'YYYY年MM月DD日') AS birth", "csv" => "birth", "header" => "誕生日"),
						20 => array("sql" => "to_char(first_buy_date, 'YYYY年MM月DD日HH24:MI') AS first_buy_date", "csv" => "first_buy_date", "header" => "初回購入日"),
						21 => array("sql" => "to_char(last_buy_date, 'YYYY年MM月DD日HH24:MI') AS last_buy_date", "csv" => "last_buy_date", "header" => "最終購入日"),
						22 => array("sql" => "buy_times", "csv" => "buy_times", "header" => "購入回数"),
						23 => array("sql" => "point", "csv" => "point", "header" => "ポイント残高"),
						24 => array("sql" => "note", "csv" => "note", "header" => "備考"),
						25 => array("sql" => "to_char(create_date, 'YYYY年MM月DD日HH24:MI') AS create_date", "csv" => "create_date", "header" => "登録日"),
						26 => array("sql" => "to_char(update_date, 'YYYY年MM月DD日HH24:MI') AS update_date", "csv" => "update_date", "header" => "更新日")
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

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// POST値の引き継ぎ
$objPage->arrForm = $_POST;

// ページ送り用
$objPage->arrHidden['search_pageno'] = $_POST['search_pageno'];

// 検索ワードの引き継ぎ
foreach ($_POST as $key => $val) {
	switch($key) {
		case 'sex':
			$objPage->arrHidden[$key] = sfMergeParamCheckBoxes($val);
			if(!is_array($val)) {
				$objPage->arrForm[$key] = split("-", $val);
			}
			break;
		default:
			$objPage->arrHidden[$key] = $val;
			break;
	}
}

// 顧客削除
if ($_POST['mode'] == "delete") {
	$sql = "SELECT status,email FROM dtb_customer WHERE customer_id = ? AND del_flg = 0";
	$result_customer = $objConn->getAll($sql, array($_POST["edit_customer_id"]));

	if ($result_customer[0]["status"] == 2) {			//本会員削除
		$arrDel = array("del_flg" => 1, "update_date" => "NOW()"); 
		$objConn->autoExecute("dtb_customer", $arrDel, "customer_id = " .addslashes($_POST["edit_customer_id"]) );
	} elseif ($result_customer[0]["status"] == 1) {		//仮会員削除
		$sql = "DELETE FROM dtb_customer WHERE customer_id = ?";
		$objConn->query($sql, array($_POST["edit_customer_id"]));
	}
	$sql = "DELETE FROM dtb_customer_mail WHERE email = ?";
	$objConn->query($sql, array($result_customer[0]["email"]));
}
if ($_POST['mode'] == "search" || $_POST['mode'] == "csv"  || $_POST['mode'] == "delete" || $_POST['mode'] == "delete_all") {
	// 入力文字の強制変換
	lfConvertParam();
	// エラーチェック
	$objPage->arrErr = lfCheckError($objPage->arrForm);

	$where = "del_flg = 0";

	/* 入力エラーなし */
	if (count($objPage->arrErr) == 0) {
		
		//-- 検索データ取得
		$objSelect = new SC_CustomerList($objPage->arrForm, "customer");
		
		// 表示件数設定
		$page_rows = $objPage->arrForm['page_rows'];
		if(is_numeric($page_rows)) {	
			$page_max = $page_rows;
		} else {
			$page_max = SEARCH_PMAX;
		}
		
		if ($objPage->arrForm['search_pageno'] == 0){
			$objPage->arrForm['search_pageno'] = 1;
		}
		
		$offset = $page_max * ($objPage->arrForm['search_pageno'] - 1);
		$objSelect->setLimitOffset($page_max, $offset);		
		
		if ($_POST["mode"] == 'csv') {
			$searchSql = $objSelect->getListCSV($arrColumnCSV);
		}else{
			$searchSql = $objSelect->getList();
		}
		
		$objPage->search_data = $objConn->getAll($searchSql, $objSelect->arrVal);

		switch($_POST['mode']) {
		case 'csv':
			$i = 0;
			$header = "";
			
			// CSVカラム取得
			$arrCsvOutput = (sfgetCsvOutput(2, " WHERE csv_id = 2 AND status = 1"));

			if (count($arrCsvOutput) <= 0) break;

			foreach($arrCsvOutput as $data) {
				$arrColumn[] = $data["col"];
				if ($i != 0) $header .= ", ";
				$header .= $data["disp_name"];
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
			exit;
			break;
		case 'delete_all':
			// 検索結果をすべて削除
			$where = "product_id IN (SELECT product_id FROM vw_products_nonclass AS noncls WHERE $where)";
			$sqlval['del_flg'] = 1;
			$objQuery->update("dtb_products", $sqlval, $where, $arrval);

			$sql = "SELECT status,email FROM dtb_customer WHERE customer_id = ? AND del_flg = 0";
			$result_customer = $objConn->getAll($sql, array($_POST["del_customer_id"]));

			if ($result_customer[0]["status"] == 2) {			//本会員削除
				$arrDel = array("del_flg" => 1, "update_date" => "NOW()");
				$objConn->autoExecute("dtb_customer", $arrDel, "customer_id = " .addslashes($_POST["del_customer_id"]) );
			} elseif ($result_customer[0]["status"] == 1) {		//仮会員削除
				$sql = "DELETE FROM dtb_customer WHERE customer_id = ?";
				$objConn->query($sql, array($_POST["del_customer_id"]));
			}
			$sql = "DELETE FROM dtb_customer_mail WHERE email = ?";
			$objConn->query($sql, array($result_customer[0]["email"]));	
			
			break;
		default:

			// 行数の取得
			$linemax = $objConn->getOne( $objSelect->getListCount(), $objSelect->arrVal);
			$objPage->tpl_linemax = $linemax;				// 何件が該当しました。表示用

			// ページ送りの取得
			$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnCustomerPage", NAVI_PMAX);
			$startno = $objNavi->start_row;
			$objPage->arrPagenavi = $objNavi->arrPagenavi;		
		}
	}
}

$objPage->arrCatList = sfGetCategoryList();

//----　ページ表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);


//--------------------------------------------------------------------------------------------------------------------------------------

//----　取得文字列の変換
function lfConvertParam() {
	global $objPage;
	
	/*
	 *	文字列の変換
	 *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
	 *	C :  「全角ひら仮名」を「全角かた仮名」に変換
	 *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します	
	 *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
	 *  a :  全角英数字を半角英数字に変換する
	 */
	// カラム名とコンバート情報
	$arrConvList['customer_id'] = "n" ;
	$arrConvList['name'] = "aKV" ;
	$arrConvList['pref'] = "n" ;
	$arrConvList['kana'] = "CKV" ;
	$arrConvList['b_start_year'] = "n" ;
	$arrConvList['b_start_month'] = "n" ;
	$arrConvList['b_start_day'] = "n" ;
	$arrConvList['b_end_year'] = "n" ;
	$arrConvList['b_end_month'] = "n" ;
	$arrConvList['b_end_day'] = "n" ;
	$arrConvList['tel'] = "n" ;
	$arrConvList['birth_month'] = "n" ;
	$arrConvList['email'] = "a" ;
	$arrConvList['buy_total_from'] = "n" ;
	$arrConvList['buy_total_to'] = "n" ;
	$arrConvList['buy_times_from'] = "n" ;
	$arrConvList['buy_times_to'] = "n" ;
	$arrConvList['start_year'] = "n" ;
	$arrConvList['start_month'] = "n" ;
	$arrConvList['start_day'] = "n" ;
	$arrConvList['end_year'] = "n" ;
	$arrConvList['end_month'] = "n" ;
	$arrConvList['end_day'] = "n" ;
	$arrConvList['page_rows'] = "n" ;
	$arrConvList['buy_start_year'] = "n" ;		//　最終購入日 START 年
	$arrConvList['buy_start_month'] = "n" ;		//　最終購入日 START 月
	$arrConvList['buy_start_day'] = "n" ;		//　最終購入日 START 日
	$arrConvList['buy_end_year'] = "n" ;			//　最終購入日 END 年
	$arrConvList['buy_end_month'] = "n" ;		//　最終購入日 END 月
	$arrConvList['buy_end_day'] = "n" ;			//　最終購入日 END 日
	$arrConvList['buy_product_name'] = "aKV" ;	//　購入商品名
	$arrConvList['buy_product_code'] = "aKV" ;	//　購入商品コード
	$arrConvList['category_id'] = "" ;			//　カテゴリ
		
	// 文字変換
	foreach ($arrConvList as $key => $val) {
		// POSTされてきた値のみ変換する。
		if(isset($objPage->arrForm[$key])) {
			$objPage->arrForm[$key] = mb_convert_kana($objPage->arrForm[$key] ,$val);
		}
	}
}


//---- 入力エラーチェック
function lfCheckError($array) {

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
	$objErr->doFunc(array("最終購入日(開始日)", "buy_start_year", "buy_start_month", "buy_start_day",), array("CHECK_DATE"));	//最終購入日(開始日)
	$objErr->doFunc(array("最終購入(終了日)", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_DATE"));			//最終購入日(終了日)
	//購入金額(from) ＞ 購入金額(to) の場合はエラーとする
	$objErr->doFunc(array("最終購入日(開始日)","登録・更新日(終了日)", "buy_start_year", "buy_start_month", "buy_start_day", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_SET_TERM"));	
	$objErr->doFunc(array("購入商品コード", "buy_product_code", STEXT_LEN), array("MAX_LENGTH_CHECK"));						//購入商品コード
	$objErr->doFunc(array("購入商品名", "buy_product_name", STEXT_LEN), array("MAX_LENGTH_CHECK"));							//購入商品名称

	return $objErr->arrErr;
}

function lfSetWhere($arrForm){
	foreach ($arrForm as $key => $val) {
		
		$val = sfManualEscape($val);
		
		if($val == "") continue;
		
		switch ($key) {
			case 'product_id':
				$where .= " AND product_id = ?";
				$arrval[] = $val;
				break;
			case 'product_class_id':
				$where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_class_id = ?)";
				$arrval[] = $val;
				break;
			case 'name':
				$where .= " AND name ILIKE ?";
				$arrval[] = "%$val%";
				break;
			case 'category_id':
				list($tmp_where, $tmp_arrval) = sfGetCatWhere($val);
				if($tmp_where != "") {
					$where.= " AND $tmp_where";
					$arrval = array_merge($arrval, $tmp_arrval);
				}
				break;
			case 'product_code':
				$where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code ILIKE ? GROUP BY product_id)";
				$arrval[] = "%$val%";
				break;
			case 'startyear':
				$date = sfGetTimestamp($_POST['startyear'], $_POST['startmonth'], $_POST['startday']);
				$where.= " AND update_date >= ?";
				$arrval[] = $date;
				break;
			case 'endyear':
				$date = sfGetTimestamp($_POST['endyear'], $_POST['endmonth'], $_POST['endday']);
				$where.= " AND update_date <= ?";
				$arrval[] = $date;
				break;
			case 'product_flag':
				global $arrSTATUS;
				$product_flag = sfSearchCheckBoxes($val);
				if($product_flag != "") {
					$where.= " AND product_flag LIKE ?";
					$arrval[] = $product_flag;					
				}
				break;
			case 'status':
				$tmp_where = "";
				foreach ($val as $element){
					if ($element != ""){
						if ($tmp_where == ""){
							$tmp_where.="AND (status LIKE ? "; 
						}else{
							$tmp_where.="OR status LIKE ? ";
						}
						$arrval[]=$element;
					}
				}
				if ($tmp_where != ""){
					$tmp_where.=")";
					$where.= "$tmp_where";
				}	
				break;
			default:
				break;
		}
	}
}

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


?>