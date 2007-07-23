<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * 情報変更
 */

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'mypage/change.tpl';		// メインテンプレート
		$this->tpl_title .= '登録変更(1/3)';			//　ページタイトル
	}
}

//---- ページ初期設定
$CONF = sf_getBasisData();					// 店舗基本情報
$objConn = new SC_DbConn();
$objPage = new LC_Page();
$objView = new SC_MobileView();
$objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();
$objPage->arrPref = $arrPref;
$objPage->arrJob = $arrJob;
$objPage->arrReminder = $arrReminder;
$objPage->arrYear = $objDate->getYear('', 1950);	//　日付プルダウン設定
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

//---- 登録用カラム配列
$arrRegistColumn = array(
							 array(  "column" => "name01", "convert" => "aKV" ),
							 array(  "column" => "name02", "convert" => "aKV" ),
							 array(  "column" => "kana01", "convert" => "CKV" ),
							 array(  "column" => "kana02", "convert" => "CKV" ),
							 array(  "column" => "zip01", "convert" => "n" ),
							 array(  "column" => "zip02", "convert" => "n" ),
							 array(  "column" => "pref", "convert" => "n" ),
							 array(  "column" => "addr01", "convert" => "aKV" ),
							 array(  "column" => "addr02", "convert" => "aKV" ),
							 array(  "column" => "email", "convert" => "a" ),
							 array(  "column" => "email_mobile", "convert" => "a" ),
							 array(  "column" => "tel01", "convert" => "n" ),
							 array(  "column" => "tel02", "convert" => "n" ),
							 array(  "column" => "tel03", "convert" => "n" ),
							 array(  "column" => "fax01", "convert" => "n" ),
							 array(  "column" => "fax02", "convert" => "n" ),
							 array(  "column" => "fax03", "convert" => "n" ),
							 array(  "column" => "sex", "convert" => "n" ),
							 array(  "column" => "job", "convert" => "n" ),
							 array(  "column" => "birth", "convert" => "n" ),
							 array(  "column" => "reminder", "convert" => "n" ),
							 array(  "column" => "reminder_answer", "convert" => "aKV"),
							 array(  "column" => "password", "convert" => "a" ),
							 array(  "column" => "mailmaga_flg", "convert" => "n" )			 
						 );

//---- 登録除外用カラム配列
$arrRejectRegistColumn = array("year", "month", "day", "email02", "email_mobile02", "password02");

$objPage->arrForm = lfGetCustomerData();
$objPage->arrForm['password'] = DEFAULT_PASSWORD;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	//-- POSTデータの引き継ぎ
	$objPage->arrForm = array_merge($objPage->arrForm, $_POST);

	if($objPage->arrForm['year'] == '----') {
		$objPage->arrForm['year'] = '';
	}
	
	$objPage->arrForm['email'] = strtolower($objPage->arrForm['email']);		// emailはすべて小文字で処理
	
	//-- 入力データの変換
	$objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);

	// 戻るボタン用処理
	if (!empty($_POST["return"])) {
		switch ($_POST["mode"]) {
		case "complete":
			$_POST["mode"] = "set3";
			break;
		case "confirm":
			$_POST["mode"] = "set2";
			break;
		default:
			$_POST["mode"] = "set1";
			break;
		}
	}

	//--　入力エラーチェック
	if ($_POST["mode"] == "set1") {
		$objPage->arrErr = lfErrorCheck1($objPage->arrForm);
		$objPage->tpl_mainpage = 'mypage/change.tpl';
		$objPage->tpl_title = '登録変更(1/3)';
	} elseif ($_POST["mode"] == "set2") {
		$objPage->arrErr = lfErrorCheck2($objPage->arrForm);
		$objPage->tpl_mainpage = 'mypage/set1.tpl';
		$objPage->tpl_title = '登録変更(2/3)';
	} else {
		$objPage->arrErr = lfErrorCheck3($objPage->arrForm);
		$objPage->tpl_mainpage = 'mypage/set2.tpl';
		$objPage->tpl_title = '登録変更(3/3)';
	}

	if ($objPage->arrErr || !empty($_POST["return"])) {		// 入力エラーのチェック
		foreach($objPage->arrForm as $key => $val) {
			$objPage->$key = $val;
		}

		//-- データの設定
		if ($_POST["mode"] == "set1") {
			$checkVal = array("email", "password", "reminder", "reminder_answer", "name01", "name02", "kana01", "kana02");
		} elseif ($_POST["mode"] == "set2") {
			$checkVal = array("sex", "year", "month", "day", "zip01", "zip02");
		} else {
			$checkVal = array("pref", "addr01", "addr02", "tel01", "tel02", "tel03", "mail_flag");
		}

		foreach($objPage->arrForm as $key => $val) {
			if ($key != "return" && $key != "mode" && $key != "confirm" && $key != session_name() && !in_array($key, $checkVal)) {
				$objPage->list_data[ $key ] = $val;
			}
		}

	} else {

		//--　テンプレート設定
		if ($_POST["mode"] == "set1") {
			$objPage->tpl_mainpage = 'mypage/set1.tpl';
			$objPage->tpl_title = '登録変更(2/3)';
		} elseif ($_POST["mode"] == "set2") {
			$objPage->tpl_mainpage = 'mypage/set2.tpl';
			$objPage->tpl_title = '登録変更(3/3)';
		} elseif ($_POST["mode"] == "confirm") {
			//パスワード表示
			$passlen = strlen($objPage->arrForm['password']);
			$objPage->passlen = lfPassLen($passlen);

			// メール受け取り
			if (strtolower($_POST['mailmaga_flg']) == "on") {
				$_POST['mailmaga_flg']  = "2";
			} else {
				$_POST['mailmaga_flg']  = "3";
			}

			$objPage->tpl_mainpage = 'mypage/change_confirm.tpl';
			$objPage->tpl_title = '登録変更(確認ページ)';

		}

		//-- データ設定
		unset($objPage->list_data);
		if ($_POST["mode"] == "set1") {
			$checkVal = array("sex", "year", "month", "day", "zip01", "zip02");
		} elseif ($_POST["mode"] == "set2") {
			$checkVal = array("pref", "addr01", "addr02", "tel01", "tel02", "tel03", "mail_flag");
		} else {
			$checkVal = array();
		}

		foreach($_POST as $key => $val) {
			if ($key != "return" && $key != "mode" && $key != "confirm" && $key != session_name() && !in_array($key, $checkVal)) {
				$objPage->list_data[ $key ] = $val;
			}
		}


		//--　仮登録と完了画面
		if ($_POST["mode"] == "complete") {

			//-- 入力データの変換
			$arrForm = lfConvertParam($_POST, $arrRegistColumn);
			$arrForm['email'] = strtolower($arrForm['email']);		// emailはすべて小文字で処理
	
			//エラーチェック
			$objPage->arrErr = lfErrorCheck($objPage->arrForm);
			$email_flag = true;

			if($objPage->arrForm['email'] != $objCustomer->getValue('email_mobile')) {
				//メールアドレスの重複チェック
				$email_cnt = $objQuery->count("dtb_customer","del_flg=0 AND (email=? OR email_mobile=?)", array($objPage->arrForm['email'], $objPage->arrForm['email']));
				if ($email_cnt > 0){
					$email_flag = false;
				}
			}

			//エラーなしでかつメールアドレスが重複していない場合
			if($objPage->arrErr == "" && $email_flag) {
				$arrForm['customer_id'] = $objCustomer->getValue('customer_id');
				//-- 編集登録
				sfEditCustomerData($arrForm, $arrRegistColumn);
				//セッション情報を最新の状態に更新する
				$objCustomer->updateSession();
				//完了ページへ
				header("Location: " . gfAddSessionId("change_complete.php"));
				exit;
			} else {
				sfDispSiteError(CUSTOMER_ERROR, "", false, "", true);
			}

		}
	}
}

$arrPrivateVariables = array('secret_key', 'first_buy_date', 'last_buy_date', 'buy_times', 'buy_total', 'point', 'note', 'status', 'create_date', 'update_date', 'del_flg', 'cell01', 'cell02', 'cell03', 'mobile_phone_id');
foreach ($arrPrivateVariables as $key) {
	unset($objPage->list_data[$key]);
}

//----　ページ表示
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//----------------------------------------------------------------------------------------------------------------------

//---- function群
function lfRegistData ($array, $arrRegistColumn, $arrRejectRegistColumn) {
	global $objConn;

	// 仮登録
	foreach ($arrRegistColumn as $data) {
		if (strlen($array[ $data["column"] ]) > 0 && ! in_array($data["column"], $arrRejectRegistColumn)) {
			$arrRegist[ $data["column"] ] = $array[ $data["column"] ];
		}
	}
		
	// 誕生日が入力されている場合
	if (strlen($array["year"]) > 0 ) {
		$arrRegist["birth"] = $array["year"] ."/". $array["month"] ."/". $array["day"] ." 00:00:00";
	}
	
	// パスワードの暗号化
	$arrRegist["password"] = sha1($arrRegist["password"] . ":" . AUTH_MAGIC);
	
	$count = 1;
	while ($count != 0) {
		$uniqid = sfGetUniqRandomId("t");
		$count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer WHERE secret_key = ?", array($uniqid));
	}
	
	$arrRegist["secret_key"] = $uniqid;		// 仮登録ID発行
	$arrRegist["create_date"] = "now()"; 	// 作成日
	$arrRegist["update_date"] = "now()"; 	// 更新日
	$arrRegist["first_buy_date"] = "";	 	// 最初の購入日
	
	// 携帯メールアドレス
	$arrRegist['email_mobile'] = $arrRegist['email'];

	//-- 仮登録実行
	$objConn->query("BEGIN");

	$objQuery = new SC_Query();
	$objQuery->insert("dtb_customer", $arrRegist);
	$objConn->query("COMMIT");

	return $uniqid;
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


//エラーチェック

function lfErrorCheck($array) {
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お名前（カナ/姓）", 'kana01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("お名前（カナ/名）", 'kana02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("市区町村", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("番地", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "NO_SPTAB" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("電話番号1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK"));
	$objErr->doFunc(array("電話番号2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK"));
	$objErr->doFunc(array("電話番号3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK"));
	$objErr->doFunc(array("電話番号", "tel01", "tel02", "tel03", TEL_LEN) ,array("TEL_CHECK"));
	$objErr->doFunc(array("FAX番号", "fax01", "fax02", "fax03", TEL_LEN) ,array("TEL_CHECK"));
	$objErr->doFunc(array("性別", "sex") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("ご職業", "job") ,array("NUM_CHECK"));
	$objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_DATE"));
	$objErr->doFunc(array("パスワード", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "ALNUM_CHECK", "NUM_RANGE_CHECK"));
	$objErr->doFunc(array("パスワード確認用の質問", "reminder") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("パスワード確認用の質問の答え", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	return $objErr->arrErr;
	
}

//---- 入力エラーチェック
function lfErrorCheck1($array) {

	global $objConn;
	global $objCustomer;
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お名前（カナ/姓）", 'kana01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("お名前（カナ/名）", 'kana02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK", "MOBILE_EMAIL_CHECK"));

	//現会員の判定 →　現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
	$array["customer_id"] = $objCustomer->getValue('customer_id');
	if (strlen($array["email"]) > 0) {
		$objQuery = new SC_Query();
		$arrRet = $objQuery->select("email, update_date, del_flg", "dtb_customer","customer_id <> ? and (email ILIKE ? OR email_mobile ILIKE ?) ORDER BY del_flg", array($array["customer_id"], $array["email"], $array["email"]));

		if(count($arrRet) > 0) {
			if($arrRet[0]['del_flg'] != '1') {
				// 会員である場合
				$objErr->arrErr["email"] .= "※ すでに会員登録で使用されているメールアドレスです。<br />";
			} else {
				// 退会した会員である場合
				$leave_time = sfDBDatetoTime($arrRet[0]['update_date']);
				$now_time = time();
				$pass_time = $now_time - $leave_time;
				// 退会から何時間-経過しているか判定する。
				$limit_time = ENTRY_LIMIT_HOUR * 3600;						
				if($pass_time < $limit_time) {
					$objErr->arrErr["email"] .= "※ 退会から一定期間の間は、同じメールアドレスを使用することはできません。<br />";
				}
			}
		}
	}

	$objErr->doFunc(array("パスワード", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "SPTAB_CHECK" ,"ALNUM_CHECK", "NUM_RANGE_CHECK"));
	$objErr->doFunc(array("パスワード確認用の質問", "reminder") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("パスワード確認用の質問の答え", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK","SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
	
	return $objErr->arrErr;
}

//---- 入力エラーチェック
function lfErrorCheck2($array) {

	global $objConn, $objDate;
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));

	$objErr->doFunc(array("性別", "sex") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("生年月日 (年)", "year", 4), array("EXIST_CHECK", "SPTAB_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	if (!isset($objErr->arrErr['year'])) {
		$objErr->doFunc(array("生年月日 (年)", "year", $objDate->getStartYear()), array("MIN_CHECK"));
		$objErr->doFunc(array("生年月日 (年)", "year", $objDate->getEndYear()), array("MAX_CHECK"));
	}
	$objErr->doFunc(array("生年月日 (月日)", "month", "day"), array("SELECT_CHECK"));
	if (!isset($objErr->arrErr['year']) && !isset($objErr->arrErr['month']) && !isset($objErr->arrErr['day'])) {
		$objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_DATE"));
	}
	
	return $objErr->arrErr;
}

//---- 入力エラーチェック
function lfErrorCheck3($array) {

	global $objConn;
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("市区町村", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("番地", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("電話番号1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK" ));
	$objErr->doFunc(array("電話番号2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK" ));
	$objErr->doFunc(array("電話番号3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK" ));
	$objErr->doFunc(array("電話番号", "tel01", "tel02", "tel03",TEL_ITEM_LEN) ,array("TEL_CHECK"));
	
	return $objErr->arrErr;
}

//確認ページ用パスワード表示用

function lfPassLen($passlen){
	$ret = "";
	for ($i=0;$i<$passlen;true){
	$ret.="*";
	$i++;
	}
	return $ret;
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

//顧客情報の取得
function lfGetCustomerData(){
	global $objQuery;
	global $objCustomer;
	//顧客情報取得
	$ret = $objQuery->select("*","dtb_customer","customer_id=?", array($objCustomer->getValue('customer_id')));
	$arrForm = $ret[0];
	$arrForm['email'] = $arrForm['email_mobile'];

	//メルマガフラグ取得
	$arrForm['mailmaga_flg'] = $objQuery->get("dtb_customer","mailmaga_flg","email=?", array($objCustomer->getValue('email_mobile')));
	
	//誕生日の年月日取得
	if (isset($arrForm['birth'])){
		$birth = split(" ", $arrForm["birth"]);
		list($year, $month, $day) = split("-",$birth[0]);
		
		$arrForm['year'] = $year;
		$arrForm['month'] = $month;
		$arrForm['day'] = $day;
		
	}
	return $arrForm;
}


//-----------------------------------------------------------------------------------------------------------------------------------
?>
