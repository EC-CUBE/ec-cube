<?php
//データベースから商品検索を実行する。（ECキット動作試験用の開発）
require_once("../require.php");

class LC_Page{
	function LC_Page() {
		$this->tpl_css = '/css/layout/mypage/change.css';
		$this->tpl_mainpage = 'mypage/change.tpl';
		$this->tpl_title = 'MYページ/会員登録内容変更(入力ページ)';
		$this->tpl_navi = 'mypage/navi.tpl';
		$this->tpl_mainno = 'mypage';
		$this->tpl_mypageno = 'change';
		global $arrReminder;
		global $arrPref;
		global $arrJob;
		global $arrMAILMAGATYPE;
		global $arrSex;
		$this->arrReminder = $arrReminder;
		$this->arrPref = $arrPref;
		$this->arrJob = $arrJob;
		$this->arrMAILMAGATYPE = $arrMAILMAGATYPE;
		$this->arrSex = $arrSex;
		session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();				
$objView = new SC_SiteView();			
$objQuery = new SC_Query();             
$objCustomer = new SC_Customer();
$objFormParam = new SC_FormParam();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

//日付プルダウン設定
$objDate = new SC_Date(1901);
$objPage->arrYear = $objDate->getYear();	
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

// ログインチェック
if (!$objCustomer->isLoginSuccess()){
	sfDispSiteError(CUSTOMER_ERROR); 
}else {
	//マイページトップ顧客情報表示用
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
	$objPage->CustomerPoint = $objCustomer->getvalue('point');
}

//---- 登録用カラム配列
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
							 array(  "column" => "email",		"convert" => "a" ),
							 array(  "column" => "tel01",		"convert" => "n" ),
							 array(  "column" => "tel02",		"convert" => "n" ),
							 array(  "column" => "tel03",		"convert" => "n" ),
							 array(  "column" => "fax01",		"convert" => "n" ),
							 array(  "column" => "fax02",		"convert" => "n" ),
							 array(  "column" => "fax03",		"convert" => "n" ),
							 array(  "column" => "sex",			"convert" => "n" ),
							 array(  "column" => "job",			"convert" => "n" ),
							 array(  "column" => "birth",		"convert" => "n" ),
							 array(  "column" => "password",	"convert" => "an" ),
							 array(  "column" => "reminder",	"convert" => "n" ),
							 array(  "column" => "reminder_answer", "convert" => "aKV" ),
						);


switch ($_POST['mode']){
	
case 'confirm':
	//-- 入力データの変換
	$objPage->arrForm = $_POST;
	$objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);
	$objPage->arrForm['email'] = strtolower($objPage->arrForm['email']);		// emailはすべて小文字で処理
	
	//誕生日不正変更のチェック
	$arrCustomer = lfGetCustomerData();
	if ($arrCustomer['birth'] != "" && ($objPage->arrForm['year'] != $arrCustomer['year'] || $objPage->arrForm['month'] != $arrCustomer['month'] || $objPage->arrForm['day'] != $arrCustomer['day'])){
		sfDispSiteError(CUSTOMER_ERROR);
	}else{
		//エラーチェック
		$objPage->arrErr = lfErrorCheck($objPage->arrForm);
		$email_flag = true;
		//メールアドレスを変更している場合、メールアドレスの重複チェック
		if ($objPage->arrForm['email'] != $objCustomer->getValue('email')){
			$email_cnt = $objQuery->count("dtb_customer","delete=0 AND email=?", array($objPage->arrForm['email']));
			if ($email_cnt > 0){
				$email_flag = false;
			}
		}
		//エラーなしでかつメールアドレスが重複していない場合
		if ($objPage->arrErr == "" && $email_flag == true){
			//確認ページへ
			$objPage->tpl_mainpage = 'mypage/change_confirm.tpl';
			$objPage->tpl_title = 'MYページ/会員登録内容変更(確認ページ)';
			$passlen = strlen($objPage->arrForm['password']);
			$objPage->passlen = lfPassLen($passlen);
		} else {
			lfFormReturn($objPage->arrForm,$objPage);
			if ($email_flag == false){
				$objPage->arrErr['email'].="既に使用されているメールアドレスです。";
			}
		}
	}
	break;
	
case 'return':
	$objPage->arrForm = $_POST;
	lfFormReturn($objPage->arrForm,$objPage);
	break;
	
case 'complete':

	//-- 入力データの変換
	$arrForm = lfConvertParam($_POST, $arrRegistColumn);
	$arrForm['email'] = strtolower($arrForm['email']);		// emailはすべて小文字で処理
	//誕生日不正変更のチェック
	$arrCustomer = lfGetCustomerData();
	if ($arrCustomer['birth'] != "" && ($arrForm['year'] !=  $arrCustomer['year'] || $arrForm['month'] != $arrCustomer['month'] || $arrForm['day'] != $arrCustomer['day'])){
		sfDispSiteError(CUSTOMER_ERROR);
	} else {
		//エラーチェック
		$objPage->arrErr = lfErrorCheck($objPage->arrForm);
		$email_flag = true;
		if($objPage->arrForm['email'] != $objCustomer->getValue('email')) {
			//メールアドレスの重複チェック
			$email_cnt = $objQuery->count("dtb_customer","delete=0 AND email=?", array($objPage->arrForm['email']));
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
			header("Location: ./change_complete.php");
			exit;
		} else {
			sfDispSiteError(CUSTOMER_ERROR);
		}
	}
	break;
	
default:
	//顧客情報取得
	$objPage->arrForm = lfGetCustomerData();
	$objPage->arrForm['password'] = DEFAULT_PASSWORD;
	$objPage->arrForm['password02'] = DEFAULT_PASSWORD;
	break;
}

//誕生日データ登録の有無
$arrCustomer = lfGetCustomerData();
if ($arrCustomer['birth'] != ""){	
	$objPage->birth_check = true;
}

$objView->assignobj($objPage);				//$objpage内の全てのテンプレート変数をsmartyに格納
$objView->display(SITE_FRAME);				//パスとテンプレート変数の呼び出し、実行

//-------------------------------------------------------------------------------------------------------------------------

/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("お名前(姓)", "name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("お名前(名)", "name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ(セイ)", "kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ(メイ)", "kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("郵便番号1", "zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("郵便番号2", "zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("都道府県", "pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("ご住所1", "addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("ご住所2", "addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("お電話番号1", "tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("お電話番号2", "tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("お電話番号3", "tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
}
											
//エラーチェック

function lfErrorCheck($array) {
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("フリガナ（セイ）", 'kana01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("フリガナ（メイ）", 'kana02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("ご住所1", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("ご住所2", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "NO_SPTAB" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('メールアドレス(確認)', "email02", MTEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK","NO_SPTAB" , "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('メールアドレス', 'メールアドレス(確認)', "email", "email02") ,array("EQUAL_CHECK"));
	$objErr->doFunc(array("お電話番号1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK"));
	$objErr->doFunc(array("お電話番号2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK"));
	$objErr->doFunc(array("お電話番号3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK"));
	$objErr->doFunc(array("お電話番号", "tel01", "tel02", "tel03", TEL_LEN) ,array("TEL_CHECK"));
	$objErr->doFunc(array("FAX番号", "fax01", "fax02", "fax03", TEL_LEN) ,array("TEL_CHECK"));
	$objErr->doFunc(array("ご性別", "sex") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("ご職業", "job") ,array("NUM_CHECK"));
	$objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_DATE"));
	$objErr->doFunc(array("パスワード", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "ALNUM_CHECK", "NUM_RANGE_CHECK"));
	$objErr->doFunc(array("パスワード(確認)", 'password02', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "ALNUM_CHECK", "NUM_RANGE_CHECK"));
	$objErr->doFunc(array("パスワード", 'パスワード(確認)', 'password', 'password02'), array("EQUAL_CHECK"));
	$objErr->doFunc(array("パスワードリマインダー 質問", "reminder") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("パスワードリマインダー 答え", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("メールマガジン", "mail_flag") ,array("SELECT_CHECK", "NUM_CHECK"));
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

//顧客情報の取得
function lfGetCustomerData(){
	global $objQuery;
	global $objCustomer;
	//顧客情報取得
	$ret = $objQuery->select("*","dtb_customer","customer_id=?", array($objCustomer->getValue('customer_id')));
	$arrForm = $ret[0];

	//メルマガフラグ取得
	$arrForm['mail_flag'] = $objQuery->get("dtb_customer_mail","mail_flag","email=?", array($objCustomer->getValue('email')));
	
	//誕生日の年月日取得
	if (isset($arrForm['birth'])){
		$arrDate = sfDispDBDate($arrForm['birth'],false);
		list($year, $month, $day) = split("/", $arrDate);
		
		$arrForm['year'] = $year;
		$arrForm['month'] = $month;
		$arrForm['day'] = $day;
	}
	return $arrForm;
}
	
// 編集登録
function lfRegistData($array, $arrRegistColumn) {
	global $objQuery;
	global $objCustomer;
	
	foreach ($arrRegistColumn as $data) {
		if ($data["column"] != "password") {
			if($array[ $data['column'] ] == "") {
				$arrRegist[ $data['column'] ] = NULL;
			} else {
				$arrRegist[ $data["column"] ] = $array[ $data["column"] ];
			}
		}
	}
	if (strlen($array["year"]) > 0 && strlen($array["month"]) > 0 && strlen($array["day"]) > 0) {
		$arrRegist["birth"] = $array["year"] ."/". $array["month"] ."/". $array["day"] ." 00:00:00";
	} else {
		$arrRegist["birth"] = NULL;
	}

	//-- パスワードの更新がある場合は暗号化。（更新がない場合はUPDATE文を構成しない）
	if ($array["password"] != DEFAULT_PASSWORD) $arrRegist["password"] = crypt($array["password"]);
	$arrRegist["update_date"] = "NOW()";
	
	//-- 編集登録実行
	$objQuery->begin();
	$objQuery->update("dtb_customer", $arrRegist, "customer_id = ? ", array($objCustomer->getValue('customer_id')));
	$objQuery->commit();
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

//エラー、戻る時にフォームに入力情報を返す
function lfFormReturn($array,$objPage){
	foreach($array as $key => $val){
		switch ($key){
			case 'password':
			case 'password02':
			$objPage->$key = $val;
			break;
			default:
			$array[ $key ] = $val;
			break;
		}
	}
}

?>