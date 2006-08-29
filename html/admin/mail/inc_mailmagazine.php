<?php
/**
 * CSV配信機能実装のため、共通部分を外部ファイル化しました。<br>
 * @author hirokazu_fukuda
 * @version 2005/12/27
 */


//---- 検索用項目配列
$objPage->arrHtmlmail = array( "" => "両方",  1 => "HTML", 2 => "TEXT" );


//---- 配列内容専用項目の配列
$arrRegistColumn = array(
							 array(  "column" => "template_id",		"convert" => "n" )
							,array(  "column" => "mail_method",		"convert" => "n" )
							,array(  "column" => "send_year",		"convert" => "n" )
							,array(  "column" => "send_month", 		"convert" => "n" )
							,array(  "column" => "send_day",		"convert" => "n" )
							,array(  "column" => "send_hour",		"convert" => "n" )
							,array(  "column" => "send_minutes",	"convert" => "n" )
							,array(  "column" => "subject",			"convert" => "aKV" )
							,array(  "column" => "body",			"convert" => "aKV" )
						);

//---- メルマガ会員種別
$arrCustomerType = array(
						1 => "会員",
						2 => "非会員",
						//3 => "CSV登録"
						);

//---- 検索項目
$arrSearchColumn = array(
							array(  "column" => "name",				"convert" => "aKV"),
							array(  "column" => "pref",				"convert" => "n" ),
							array(  "column" => "kana",				"convert" => "CKV"),
							array(  "column" => "sex",				"convert" => "" ),
							array(  "column" => "tel",				"convert" => "n" ),
							array(  "column" => "job",				"convert" => "" ),
							array(  "column" => "email",			"convert" => "a" ),
							array(  "column" => "htmlmail",			"convert" => "n" ),
							array(  "column" => "customer",			"convert" => "" ),
							array(  "column" => "buy_total_from",	"convert" => "n" ),
							array(  "column" => "buy_total_to",		"convert" => "n" ),
							array(  "column" => "buy_times_from",	"convert" => "n" ),
							array(  "column" => "buy_times_to",		"convert" => "n" ),
							array(  "column" => "birth_month",		"convert" => "n" ),
							array(  "column" => "b_start_year",		"convert" => "n" ),
							array(  "column" => "b_start_month",	"convert" => "n" ),
							array(  "column" => "b_start_day",		"convert" => "n" ),
							array(  "column" => "b_end_year",		"convert" => "n" ),
							array(  "column" => "b_end_month",		"convert" => "n" ),
							array(  "column" => "b_end_day",		"convert" => "n" ),
							array(  "column" => "start_year",		"convert" => "n" ),
							array(  "column" => "start_month",		"convert" => "n" ),
							array(  "column" => "start_day",		"convert" => "n" ),
							array(  "column" => "end_year",			"convert" => "n" ),
							array(  "column" => "end_month",		"convert" => "n" ),
							array(  "column" => "end_day",			"convert" => "n" ),
							array(  "column" => "buy_start_year",	"convert" => "n" ),
							array(  "column" => "buy_start_month",	"convert" => "n" ),
							array(  "column" => "buy_start_day",	"convert" => "n" ),
							array(  "column" => "buy_end_year",		"convert" => "n" ),
							array(  "column" => "buy_end_month",	"convert" => "n" ),
							array(  "column" => "buy_end_day",		"convert" => "n" ),
							array(  "column" => "buy_product_code",	"convert" => "aKV" )
							,array(  "column" => "buy_product_name",	"convert" => "aKV" )
							,array(  "column" => "category_id",	"convert" => "" )			
							,array(  "column" => "buy_total_from",	"convert" => "n" )		
							,array(  "column" => "buy_total_to",	"convert" => "n" )		
						 );

//--------------------------------------------------------------------------------------------------------------------------------------

//---- HTMLテンプレートを使用する場合、データを取得する。	
function lfGetHtmlTemplateData($id) {
	
	global $conn;
	$sql = "SELECT * FROM dtb_mailmaga_template WHERE template_id = ?";
	$result = $conn->getAll($sql, array($id));
	$list_data = $result[0];

	// メイン商品の情報取得
	$sql = "SELECT name, main_image, point_rate, deliv_fee, price01_min, price01_max, price02_min, price02_max FROM vw_products_allclass WHERE product_id = ?";
	$main = $conn->getAll($sql, array($list_data["main_product_id"]));
	$list_data["main"] = $main[0];

	// サブ商品の情報取得
	$sql = "SELECT product_id, name, main_list_image, price01_min, price01_max, price02_min, price02_max FROM vw_products_allclass WHERE product_id = ?";
	$k = 0;
	$l = 0;
	for ($i = 1; $i <= 12; $i ++) {
		if ($l == 4) {
			$l = 0;
			$k ++;
		}
		$result = "";
		$j = sprintf("%02d", $i);
		if ($i > 0 && $i < 5 ) $k = 0;
		if ($i > 4 && $i < 9 ) $k = 1;
		if ($i > 8 && $i < 13 ) $k = 2;	
		
		if (is_numeric($list_data["sub_product_id" .$j])) {
			$result = $conn->getAll($sql, array($list_data["sub_product_id" .$j]));
			$list_data["sub"][$k][$l] = $result[0];
			$list_data["sub"][$k]["data_exists"] = "OK";	//当該段にデータが１つ以上存在するフラグ
		}
		$l ++;
	}
	return $list_data;
}

//---   テンプレートの種類を返す
function lfGetTemplateMethod($conn, $templata_id){
	
	if ( sfCheckNumLength($template_id) ){
		$sql = "SELECT mail_method FROM dtb_mailmaga_template WEHRE template_id = ?";
	}	
}

//---   hidden要素出力用配列の作成
function lfGetHidden( $array ){
	if ( is_array($array) ){
		foreach( $array as $key => $val ){
			if ( is_array( $val )){
				for ( $i=0; $i<count($val); $i++){
					$return[ $key.'['.$i.']'] = $val[$i];
				}				
			} else {
				$return[$key] = $val;			
			}
		}
	}
	return $return;
}

//----　取得文字列の変換
function lfConvertParam($array, $arrSearchColumn) {
	
	// 文字変換
	foreach ($arrSearchColumn as $data) {
		$arrConvList[ $data["column"] ] = $data["convert"];
	}

	$new_array = array();
	foreach ($arrConvList as $key => $val) {
		if ( strlen($array[$key]) > 0 ){						// データのあるものだけ返す
			$new_array[$key] = $array[$key];
			if( strlen($val) > 0) {
				$new_array[$key] = mb_convert_kana($new_array[$key] ,$val);
			}
		}
	}
	return $new_array;
	
}


//---- 入力エラーチェック
function lfErrorCheck($array, $flag = '') {

	// flag は登録時用
	
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("顧客コード", "customer_id", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("都道府県", "pref", 2), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("顧客名", "name", STEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("顧客名(カナ)", "kana", STEXT_LEN), array("KANA_CHECK", "MAX_LENGTH_CHECK"));

	$objErr->doFunc(array('メールアドレス', "email", STEXT_LEN) ,array("EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("電話番号", "tel", TEL_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
		
	$objErr->doFunc(array("購入回数(開始)", "buy_times_from", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("購入回数(終了)", "buy_times_to", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	if ((is_numeric($array["buy_total_from"]) && is_numeric($array["buy_total_to"]) ) && ($array["buy_times_from"] > $array["buy_times_to"]) ) $objErr->arrErr["buy_times_from"] .= "※ 購入回数の指定範囲が不正です。";
	
	$objErr->doFunc(array("誕生月", "birth_month", 2), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	
	$objErr->doFunc(array("誕生日(開始日)", "b_start_year", "b_start_month", "b_start_day",), array("CHECK_DATE"));
	$objErr->doFunc(array("誕生日(終了日)", "b_end_year", "b_end_month", "b_end_day"), array("CHECK_DATE"));	
	$objErr->doFunc(array("誕生日(開始日)","誕生日(終了日)", "b_start_year", "b_start_month", "b_start_day", "b_end_year", "b_end_month", "b_end_day"), array("CHECK_SET_TERM"));
	
	$objErr->doFunc(array("登録・更新日(開始日)", "start_year", "start_month", "start_day",), array("CHECK_DATE"));
	$objErr->doFunc(array("登録・更新日(終了日)", "end_year", "end_month", "end_day"), array("CHECK_DATE"));	
	$objErr->doFunc(array("登録・更新日(開始日)","登録・更新日(終了日)", "start_year", "start_month", "start_day", "end_year", "end_month", "end_day"), array("CHECK_SET_TERM"));
	
	$objErr->doFunc(array("最終購入日(開始日)", "buy_start_year", "buy_start_month", "buy_start_day",), array("CHECK_DATE"));
	$objErr->doFunc(array("最終購入(終了日)", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_DATE"));	
	$objErr->doFunc(array("最終購入日(開始日)","登録・更新日(終了日)", "buy_start_year", "buy_start_month", "buy_start_day", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_SET_TERM"));
	
	$objErr->doFunc(array("購入商品コード", "buy_product_code", STEXT_LEN), array("MAX_LENGTH_CHECK"));

	$objErr->doFunc(array("購入商品名", "buy_product_name", STEXT_LEN), array("MAX_LENGTH_CHECK"));
	
	$objErr->doFunc(array("購入金額(開始)", "buy_total_from", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));	
	$objErr->doFunc(array("購入金額(終了)", "buy_total_to", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));	
	
	//購入金額(from) ＞ 購入金額(to) の場合はエラーとする
	if ( (is_numeric($array["buy_total_from"]) && is_numeric($array["buy_total_to"]) ) && 
		 ($array["buy_total_from"] > $array["buy_total_to"]) ) {
		 $objErr->arrErr["buy_total_from"] .= "※ 購入金額の指定範囲が不正です。";
	 }

	if ( $flag ){
		$objErr->doFunc(array("テンプレートID", "template_id"), array("EXIST_CHECK", "NUM_CHECK"));
		$objErr->doFunc(array("メール送信法法", "mail_method"), array("EXIST_CHECK", "NUM_CHECK"));
		/* 自動配信機能はサーバーの設定が必要なため、削除
		
		$objErr->doFunc(array("配信日（年）","send_year"), array("EXIST_CHECK", "NUM_CHECK"));
		$objErr->doFunc(array("配信日（月）","send_month"), array("EXIST_CHECK", "NUM_CHECK"));
		$objErr->doFunc(array("配信日（日）","send_day"), array("EXIST_CHECK", "NUM_CHECK"));
		$objErr->doFunc(array("配信日（時）","send_hour"), array("EXIST_CHECK", "NUM_CHECK"));
		$objErr->doFunc(array("配信日（分）","send_minutes"), array("EXIST_CHECK", "NUM_CHECK"));
		$objErr->doFunc(array("配信日", "send_year", "send_month", "send_day"), array("CHECK_DATE"));
		$objErr->doFunc(array("配信日", "send_year", "send_month", "send_day","send_hour", "send_minutes"), array("ALL_EXIST_CHECK"));
		*/
		$objErr->doFunc(array("Subject", "subject", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
		$objErr->doFunc(array("本文", 'body', LLTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));	// HTMLテンプレートを使用しない場合
	}
	
	return $objErr->arrErr;
}

/* テンプレートIDとsubjectの配列を返す */ 
function getTemplateList($conn){
	global $arrMagazineTypeAll;
	
	$sql = "SELECT template_id, subject, mail_method FROM dtb_mailmaga_template WHERE delete = 0 ";
	if ($_POST["htmlmail"] == 2) {
		$sql .= " AND mail_method = 2 ";	//TEXT希望者へのTESTメールテンプレートリスト
	}
	$sql .= " ORDER BY template_id DESC";
	$result = $conn->getAll($sql);
	
	if ( is_array($result) ){ 
		foreach( $result as $line ){
			$return[$line['template_id']] = "【" . $arrMagazineTypeAll[$line['mail_method']] . "】" . $line['subject'];  
		}
	}
	
	return $return;
}

/* テンプレートIDからテンプレートデータを取得 */ 
function getTemplateData($conn, $id){
	
	if ( sfCheckNumLength($id) ){
		$sql = "SELECT * FROM dtb_mailmaga_template WHERE template_id = ? ORDER BY template_id DESC";
		$result = $conn->getAll( $sql, array($id) );
		if ( is_array($result) ) {
			$return = $result[0];
		}
	}
	return $return;
}



?>