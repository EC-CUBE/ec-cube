<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css = '/css/layout/entry/index.css';	// メインCSSパス
		$this->tpl_mainpage = 'entry/index.tpl';		// メインテンプレート
		$this->tpl_title .= '会員登録(入力ページ)';			//　ページタイトル
	}
}

//---- ページ初期設定
$CONF = sf_getBasisData();					// 店舗基本情報
$objConn = new SC_DbConn();
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
$objPage->arrPref = $arrPref;
$objPage->arrJob = $arrJob;
$objPage->arrReminder = $arrReminder;
$objPage->arrYear = $objDate->getYear('', 1950);	//　日付プルダウン設定
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

//SSLURL判定
if (SSLURL_CHECK == 1){
	$ssl_url= sfRmDupSlash(SSL_URL.$_SERVER['REQUEST_URI']);
	if (!ereg("^https://", $non_ssl_url)){
		sfDispSiteError(URL_ERROR);
	}
}

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
							 array(  "column" => "email2", "convert" => "a" ),
							 array(  "column" => "email_mobile", "convert" => "a" ),
							 array(  "column" => "email_mobile2", "convert" => "a" ),
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
							 array(  "column" => "password02", "convert" => "a" )
						 );

//---- 登録除外用カラム配列
$arrRejectRegistColumn = array("year", "month", "day", "email02", "email_mobile02", "password02");



if ($_SERVER["REQUEST_METHOD"] == "POST") {

	//-- POSTデータの引き継ぎ
	$objPage->arrForm = $_POST;
	
	if($objPage->arrForm['year'] == '----') {
		$objPage->arrForm['year'] = '';
	}
	
	$objPage->arrForm['email'] = strtolower($objPage->arrForm['email']);		// emailはすべて小文字で処理
	$objPage->arrForm['email02'] = strtolower($objPage->arrForm['email02']);	// emailはすべて小文字で処理
	
	//-- 入力データの変換
	$objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);
		
	//--　入力エラーチェック
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);

	if ($objPage->arrErr || $_POST["mode"] == "return") {		// 入力エラーのチェック
		foreach($objPage->arrForm as $key => $val) {
			$objPage->$key = $val;
		}

	} else {

		//--　確認
		if ($_POST["mode"] == "confirm") {
			foreach($objPage->arrForm as $key => $val) {
				if ($key != "mode" && $key != "subm") $objPage->list_data[ $key ] = $val;
			}
			//パスワード表示
			$passlen = strlen($objPage->arrForm['password']);
			$objPage->passlen = lfPassLen($passlen);
			
			$objPage->tpl_css = '/css/layout/entry/confirm.css';
			$objPage->tpl_mainpage = 'entry/confirm.tpl';
			$objPage->tpl_title = '会員登録(確認ページ)';

		}

		//--　仮登録と完了画面
		if ($_POST["mode"] == "complete") {
			$objPage->uniqid = lfRegistData ($objPage->arrForm, $arrRegistColumn, $arrRejectRegistColumn);
			
			$objPage->tpl_css = '/css/layout/entry/complete.css';
			$objPage->tpl_mainpage = 'entry/complete.tpl';
			$objPage->tpl_title = '会員登録(完了ページ)';
			
			//　仮登録完了メール送信
			$objPage->CONF = $CONF;
			$objPage->to_name01 = $_POST['name01'];
			$objPage->to_name02 = $_POST['name02'];
			$objMailText = new SC_SiteView();
			$objMailText->assignobj($objPage);
			$subject = sfMakesubject('会員登録のご確認');
			$toCustomerMail = $objMailText->fetch("mail_templates/customer_mail.tpl");
			$objMail = new GC_SendMail();
			$objMail->setItem(
								''									//　宛先
								, $subject							//　サブジェクト
								, $toCustomerMail					//　本文
								, $CONF["email03"]					//　配送元アドレス
								, $CONF["shop_name"]				//　配送元　名前
								, $CONF["email03"]					//　reply_to
								, $CONF["email04"]					//　return_path
								, $CONF["email04"]					//  Errors_to
								, $CONF["email01"]					//  Bcc
																);
			// 宛先の設定
			$name = $_POST["name01"] . $_POST["name02"] ." 様";
			$objMail->setTo($_POST["email"], $name);			
			$objMail->sendMail();
			
			// 完了ページに移動させる。
			header("Location: ./complete.php");
			exit;
		}
	}
}

if($objPage->year == '') {
	$objPage->year = '----';
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
	
	$arrRegist["secret_key"] = $uniqid;	//　仮登録ID発行
	
	//-- 仮登録実行
	$objConn->query("BEGIN");
	$objConn->autoExecute("dtb_customer", $arrRegist);
	
	//--　非会員でメルマガ登録しているかの判定
	$sql = "SELECT count(*) FROM dtb_customer_mail WHERE email = ?";
	$mailResult = $objConn->getOne($sql, array($arrRegist["email"]));

	//--　メルマガ仮登録実行
	$arrRegistMail["email"] = $arrRegist["email"];	
	if ($array["mail_flag"] == 1) {
		$arrRegistMail["mail_flag"] = 4; 
	} elseif ($array["mail_flag"] == 2) {
		$arrRegistMail["mail_flag"] = 5; 
	} else {
		$arrRegistMail["mail_flag"] = 6; 
	}
	
	// 非会員でメルマガ登録している場合
	if ($mailResult == 1) {		
		$objConn->autoExecute("dtb_customer_mail", $arrRegistMail, "email = '" .addslashes($arrRegistMail["email"]). "'");			
	} else {				//　新規登録の場合
		$objConn->autoExecute("dtb_customer_mail", $arrRegistMail);		
	}

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

//---- 入力エラーチェック
function lfErrorCheck($array) {

	global $objConn;
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("フリガナ（セイ）", 'kana01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("フリガナ（メイ）", 'kana02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("ご住所1", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("ご住所2", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('メールアドレス(確認)', "email02", MTEXT_LEN) ,array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK","SPTAB_CHECK" , "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('メールアドレス', 'メールアドレス(確認)', "email", "email02") ,array("EQUAL_CHECK"));

	//現会員の判定 →　現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
	if (strlen($array["email"]) > 0) {
		$objQuery = new SC_Query();
		$arrRet = $objQuery->select("email, update_date, del_flg", "dtb_customer","email ILIKE ? ORDER BY del_flg", array($array["email"]));
				
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

	$objErr->doFunc(array("お電話番号1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK" ));
	$objErr->doFunc(array("お電話番号2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK" ));
	$objErr->doFunc(array("お電話番号3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK" ));
	$objErr->doFunc(array("お電話番号", "tel01", "tel02", "tel03",TEL_ITEM_LEN) ,array("TEL_CHECK"));
	$objErr->doFunc(array("FAX番号1", 'fax01'), array("SPTAB_CHECK"));
	$objErr->doFunc(array("FAX番号2", 'fax02'), array("SPTAB_CHECK"));
	$objErr->doFunc(array("FAX番号3", 'fax03'), array("SPTAB_CHECK"));
	$objErr->doFunc(array("FAX番号", "fax01", "fax02", "fax03", TEL_ITEM_LEN) ,array("TEL_CHECK"));
	$objErr->doFunc(array("ご性別", "sex") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("パスワード", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "SPTAB_CHECK" ,"ALNUM_CHECK", "NUM_RANGE_CHECK"));
	$objErr->doFunc(array("パスワード(確認)", 'password02', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "SPTAB_CHECK" ,"ALNUM_CHECK", "NUM_RANGE_CHECK"));
	$objErr->doFunc(array('パスワード', 'パスワード(確認)', "password", "password02") ,array("EQUAL_CHECK"));
	$objErr->doFunc(array("パスワードを忘れたときのヒント 質問", "reminder") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("パスワードを忘れたときのヒント 答え", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK","SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("メールマガジン", "mail_flag") ,array("SELECT_CHECK", "NUM_CHECK"));
	
	$objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_DATE"));
	$objErr->doFunc(array("メールマガジン", 'mail_flag'), array("SELECT_CHECK"));
	
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

//-----------------------------------------------------------------------------------------------------------------------------------
?>
