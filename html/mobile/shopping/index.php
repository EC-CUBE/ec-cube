<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 */

require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	var $tpl_login_email;
	function LC_Page() {
		$this->tpl_mainpage = 'shopping/index.tpl';
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrSex;
		$this->arrSex = $arrSex;
		global $arrJob;
		$this->arrJob = $arrJob;
		$this->tpl_onload = 'fnCheckInputDeliv();';
		
		/*
		 session_start時のno-cacheヘッダーを抑制することで
		 「戻る」ボタン使用時の有効期限切れ表示を抑制する。
		 private-no-expire:クライアントのキャッシュを許可する。
		*/
		session_cache_limiter('private-no-expire');				
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_MobileView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objCustomer = new SC_Customer();
$objCookie = new SC_Cookie();
$objFormParam = new SC_FormParam();			// フォーム用
lfInitParam();								// パラメータ情報の初期化
$objFormParam->setParam($_POST);			// POST値の取得


//-------------------------------------▼NONMEMBER----------------------------------------------
//---- ページ初期設定

$CONF = sf_getBasisData();                  // 店舗基本情報
$objView = new SC_MobileView();
$objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
$objPage->arrPref = $arrPref;
$objPage->arrJob = $arrJob;
$objPage->arrReminder = $arrReminder;
$objPage->arrYear = $objDate->getYear('', 1950);    //　日付プルダウン設定
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

//SSLURL判定
if (SSLURL_CHECK == 1){
    $ssl_url= sfRmDupSlash(MOBILE_SSL_URL.$_SERVER['REQUEST_URI']);
    if (!ereg("^https://", $non_ssl_url)){
        sfDispSiteError(URL_ERROR, "", false, "", true);
    }
}

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);
/*
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
                             array(  "column" => "password02", "convert" => "a" ),
                             array(  "column" => "mailmaga_flg", "convert" => "n" ),
                         );

//---- 登録除外用カラム配列
//$arrRejectRegistColumn = array("year", "month", "day", "email02", "email_mobile02","password","password02","reminder","reminder_answer");
$arrRejectRegistColumn = array("year", "month", "day");
*/
//-------------------------------------▲NONMEMBER----------------------------------------------


// ユーザユニークIDの取得と購入状態の正当性をチェック
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

$objPage->tpl_uniqid = $uniqid;

// ログインチェック
if($objCustomer->isLoginSuccess()) {
	// すでにログインされている場合は、お届け先設定画面に転送
	header("Location: " . gfAddSessionId('deliv.php'));
	exit;
}

// 携帯端末IDが一致する会員が存在するかどうかをチェックする。
$objPage->tpl_valid_phone_id = $objCustomer->checkMobilePhoneId();

switch($_POST['mode']) {
case 'nonmember_confirm':
	$objPage = lfSetNonMember($objPage);
	// ※breakなし
case 'confirm':
	// 入力値の変換
	$objFormParam->convParam();
	$objFormParam->toLower('order_mail');
	$objFormParam->toLower('order_mail_check');
	
	$objPage->arrErr = lfCheckError();

	// 入力エラーなし
	if(count($objPage->arrErr) == 0) {
		// DBへのデータ登録
		lfRegistData($uniqid);
		// 正常に登録されたことを記録しておく
		$objSiteSess->setRegistFlag();
		// お支払い方法選択ページへ移動
		header("Location: " . gfAddSessionId(MOBILE_URL_SHOP_PAYMENT));
		exit;		
	}
	
	break;
// 前のページに戻る
case 'return':
	// 確認ページへ移動
	header("Location: " . gfAddSessionId(MOBILE_URL_CART_TOP));
	exit;
	break;
case 'nonmember':
	$objPage = lfSetNonMember($objPage);
	// ※breakなし
default:
	if($_GET['from'] == 'nonmember') {
		$objPage = lfSetNonMember($objPage);
	}
	// ユーザユニークIDの取得
	$uniqid = $objSiteSess->getUniqId();
	$objQuery = new SC_Query();
	$where = "order_temp_id = ?";
	$arrRet = $objQuery->select("*", "dtb_order_temp", $where, array($uniqid));
	// DB値の取得
	$objFormParam->setParam($arrRet[0]);
	$objFormParam->setValue('order_email_check', $arrRet[0]['order_email']);
	$objFormParam->setDBDate($arrRet[0]['order_birth']);
	break;
}

// クッキー判定
$objPage->tpl_login_email = $objCookie->getCookie('login_email');
if($objPage->tpl_login_email != "") {
	$objPage->tpl_login_memory = "1";
}

// 選択用日付の取得
$objDate = new SC_Date(START_BIRTH_YEAR);
$objPage->arrYear = $objDate->getYear('', 1950);	//　日付プルダウン設定
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

if($objPage->year == '') {
	$objPage->year = '----';
}

// 入力値の取得
$objPage->arrForm = $objFormParam->getFormParamList();

if($objPage->arrForm['year']['value'] == ""){
	$objPage->arrForm['year']['value'] = '----';	
}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//--------------------------------------------------------------------------------------------------------------------------
/* 非会員入力ページのセット */
function lfSetNonMember($objPage) {
	
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
                             array(  "column" => "password02", "convert" => "a" ),
                             array(  "column" => "mailmaga_flg", "convert" => "n" ),
                         );

//---- 登録除外用カラム配列
//$arrRejectRegistColumn = array("year", "month", "day", "email02", "email_mobile02","password","password02","reminder","reminder_answer");
$arrRejectRegistColumn = array("year", "month", "day", "email02", "email_mobile02", "password02");
        
    $objPage->tpl_mainpage = 'shopping/nonmember_set1.tpl';
	$objPage->tpl_css = array();
	$objPage->tpl_css[] = '/css/layout/login/nonmember.css';
    
    //-- POSTデータの引き継ぎ
    $objPage->arrForm = $_POST;
    
    if($objPage->arrForm['year'] == '----') {
        $objPage->arrForm['year'] = '';
    }
    
    //$objPage->arrForm['email'] = strtolower($objPage->arrForm['email']);        // emailはすべて小文字で処理
    
    //-- 入力データの変換
    $objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);

    // 戻るボタン用処理
    //returnの中に元のページの名前が入っている
	if (!empty($_POST["return"])) {
        switch ($_POST["mode2"]) {
        case "deliv_date":	
        
        	break;
        case "deliv":
            $_POST["mode2"] = "set3";
            break;
        case "set3":
            $_POST["mode2"] = "set2";
            break;
        default:
            $_POST["mode2"] = "set1";
            break;
        }
    }    

    //--　入力エラーチェック
    if (!empty($_POST["mode2"])) {
            if ($_POST["mode2"] == "set2") {
            $objPage->arrErr = lfErrorCheck1($objPage->arrForm);
            $objPage->tpl_mainpage = 'shopping/nonmember_set1.tpl';
            $objPage->tpl_title = 'お客様情報入力(1/3)';
        } elseif ($_POST["mode2"] == "set3") {
            $objPage->arrErr = lfErrorCheck2($objPage->arrForm);
            $objPage->tpl_mainpage = 'shopping/nonmember_set2.tpl';
            $objPage->tpl_title = 'お客様情報入力(2/3)';
        } elseif ($_POST["mode2"] == "deliv"){
            $objPage->arrErr = lfErrorCheck3($objPage->arrForm);
            $objPage->tpl_mainpage = 'shopping/nonmember_set3.tpl';
            $objPage->tpl_title = 'お客様情報入力(3/3)';
        }
    
    //フォームの値を$objPageのキーとして代入していく
   foreach($objPage->arrForm as $key => $val) {
        $objPage->$key = $val;
        }
    }

		// 入力エラーのチェック
    if ($objPage->arrErr || !empty($_POST["return"])) {     

        //-- データの設定
        if ($_POST["mode2"] == "set1") {
            $checkVal = array("email", "name01", "name02", "kana01", "kana02");
        } elseif ($_POST["mode2"] == "set2") {
            $checkVal = array("sex", "year", "month", "day", "zip01", "zip02");
        } else {
            $checkVal = array("pref", "addr01", "addr02", "tel01", "tel02", "tel03", "mail_flag");
        }

        foreach($objPage->arrForm as $key => $val) {
            if ($key != "mode2" && $key != "submit" && $key != "return" && $key != session_name() && !in_array($key, $checkVal))
                $objPage->list_data[ $key ] = $val;
        }

    } else {

        //--　テンプレート設定
        if ($_POST["mode2"] == "set2") {
            $objPage->tpl_mainpage = 'shopping/nonmember_set2.tpl';
            $objPage->tpl_title = 'お客様情報入力(2/3)';
        } elseif ($_POST["mode2"] == "set3") {
            $objPage->tpl_mainpage = 'shopping/nonmember_set3.tpl';
            $objPage->tpl_title = 'お客様情報入力(3/3)';

            if (@$objPage->arrForm['pref'] == "" && @$objPage->arrForm['addr01'] == "" && @$objPage->arrForm['addr02'] == "") {
                $address = lfGetAddress($_REQUEST['zip01'].$_REQUEST['zip02']);
                $objPage->pref = @$address[0]['state'];
                $objPage->addr01 = @$address[0]['city'] . @$address[0]['town'];
            }
        }

        //-- データ設定
        unset($objPage->list_data);
        if ($_POST["mode2"] == "set1") {
            $checkVal = array("sex", "year", "month", "day", "zip01", "zip02");
        } elseif ($_POST["mode2"] == "set2") {
            $checkVal = array("pref", "addr01", "addr02", "tel01", "tel02", "tel03", "mail_flag");
        } else {
            $checkVal = array();
        }

//$objPage->list_data
        foreach($objPage->arrForm as $key => $val) {
            if ($key != "mode2" && $key != "submit" && $key != "confirm" && $key != "return" && $key != session_name() && !in_array($key, $checkVal)) {
                $objPage->list_data[ $key ] = $val;
            }
        }

        if ($_POST["mode2"] == "deliv") {
            
            $objFormParam = new SC_FormParam();
            // パラメータ情報の初期化
           
            // POST値の取得
            $objFormParam->setParam($_POST);
            print_r($_POST);
           	$arrRet = $objFormParam->getHashArray();
			$sqlval = $objFormParam->getDbArray();
            
            // 入力値の取得
            $objPage->arrForm = $objFormParam->getFormParamList();
            $objPage->arrErr = $arrErr;
            
//            $cnt = 1;
//            foreach($objOtherAddr as $val) {
//                $objPage->arrAddr[$cnt] = $val;
//                $cnt++;
//            }
         
            lfRegistData($objPage->tpl_uniqid); 
            
            lfCopyDeliv($objPage->tpl_uniqid, $_POST);
            
           $objPage->arrAddr[0]['zip01'] = $objPage->zip01;
           $objPage->arrAddr[0]['zip02'] = $objPage->zip02;
           $objPage->arrAddr[0]['pref'] = $objPage->pref;
           $objPage->arrAddr[0]['addr01'] = $objPage->addr01;
           $objPage->arrAddr[0]['addr02'] = $objPage->addr02; 
           $arrData = $_POST;
           
            $objPage->tpl_mainpage = 'shopping/nonmember_deliv.tpl';
            $objPage->tpl_title = 'お届け先情報';
        }
        
         if ($_POST["mode2"] == "customer_addr") {
          	print_r($_POST);
          	if ($_POST['deli'] != "") {
           lfRegistData($objPage->tpl_uniqid); 
           setcookie('eccube_mobile',$_POST);
           echo $_COOKIE['eccube_mobile'];
           header("Location:" . gfAddSessionId("./payment.php"));
     		exit;
	}else{
		// エラーを返す
		$arrErr['deli'] = '※ お届け先を選択してください。';
	}
	break;
        }
    }

	return $objPage;
}


function lfRegistData($uniqid) {
    global $objFormParam;
    $arrRet = $objFormParam->getHashArray();
    $sqlval = $objFormParam->getDbArray();
    // 登録データの作成
    $sqlval['order_temp_id'] = $uniqid;
    $sqlval['order_birth'] = sfGetTimestamp($arrRet['year'], $arrRet['month'], $arrRet['day']);
    $sqlval['update_date'] = 'Now()';
    $sqlval['customer_id'] = '0';
    
    // 既存データのチェック
    $objQuery = new SC_Query();
    $where = "order_temp_id = ?";
    $cnt = $objQuery->count("dtb_order_temp", $where, array($uniqid));
    // 既存データがない場合
    if ($cnt == 0) {
        $sqlval['create_date'] = 'Now()';
        $objQuery->insert("dtb_order_temp", $sqlval);
    } else {
        $objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
    }
}

/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("お名前（姓）", "order_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("お名前（名）", "order_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ（セイ）", "order_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ（メイ）", "order_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("郵便番号1", "order_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("郵便番号2", "order_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("都道府県", "order_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("住所1", "order_addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("住所2", "order_addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("電話番号1", "order_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号2", "order_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号3", "order_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("FAX番号1", "order_fax01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("FAX番号2", "order_fax02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("FAX番号3", "order_fax03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("メールアドレス", "order_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"));
	$objFormParam->addParam("メールアドレス（確認）", "order_email_check", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"), "", false);
	$objFormParam->addParam("年", "year", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
	$objFormParam->addParam("月", "month", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
	$objFormParam->addParam("日", "day", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
	$objFormParam->addParam("性別", "order_sex", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("職業", "order_job", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("別のお届け先", "deliv_check", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("お名前（姓）", "deliv_name01", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("お名前（名）", "deliv_name02", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ（セイ）", "deliv_kana01", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ（メイ）", "deliv_kana02", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("郵便番号1", "deliv_zip01", ZIP01_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("郵便番号2", "deliv_zip02", ZIP02_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("都道府県", "deliv_pref", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("住所1", "deliv_addr01", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("住所2", "deliv_addr02", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("電話番号1", "deliv_tel01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号2", "deliv_tel02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号3", "deliv_tel03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("メールマガジン", "mail_flag", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
}

/* DBへデータの登録 */


/* 入力内容のチェック */
function lfCheckError() {
	global $objFormParam;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
		
	// 別のお届け先チェック
	if($_POST['deliv_check'] == "1") { 
		$objErr->doFunc(array("お名前（姓）", "deliv_name01"), array("EXIST_CHECK"));
		$objErr->doFunc(array("お名前（名）", "deliv_name02"), array("EXIST_CHECK"));
		$objErr->doFunc(array("フリガナ（セイ）", "deliv_kana01"), array("EXIST_CHECK"));
		$objErr->doFunc(array("フリガナ（メイ）", "deliv_kana02"), array("EXIST_CHECK"));
		$objErr->doFunc(array("郵便番号1", "deliv_zip01"), array("EXIST_CHECK"));
		$objErr->doFunc(array("郵便番号2", "deliv_zip02"), array("EXIST_CHECK"));
		$objErr->doFunc(array("都道府県", "deliv_pref"), array("EXIST_CHECK"));
		$objErr->doFunc(array("住所1", "deliv_addr01"), array("EXIST_CHECK"));
		$objErr->doFunc(array("住所2", "deliv_addr02"), array("EXIST_CHECK"));
		$objErr->doFunc(array("電話番号1", "deliv_tel01"), array("EXIST_CHECK"));
		$objErr->doFunc(array("電話番号2", "deliv_tel02"), array("EXIST_CHECK"));
		$objErr->doFunc(array("電話番号3", "deliv_tel03"), array("EXIST_CHECK"));
	}
	
	// 複数項目チェック
	$objErr->doFunc(array("TEL", "order_tel01", "order_tel02", "order_tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
	$objErr->doFunc(array("FAX", "order_fax01", "order_fax02", "order_fax03", TEL_ITEM_LEN), array("TEL_CHECK"));
	$objErr->doFunc(array("郵便番号", "order_zip01", "order_zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("TEL", "deliv_tel01", "deliv_tel02", "deliv_tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
	$objErr->doFunc(array("FAX", "deliv_fax01", "deliv_fax02", "deliv_fax03", TEL_ITEM_LEN), array("TEL_CHECK"));
	$objErr->doFunc(array("郵便番号", "deliv_zip01", "deliv_zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_DATE"));
	$objErr->doFunc(array("メールアドレス", "メールアドレス（確認）", "order_email", "order_email_check"), array("EQUAL_CHECK"));
	
	// すでにメルマガテーブルに会員としてメールアドレスが登録されている場合
	if(sfCheckCustomerMailMaga($arrRet['order_email'])) {
		$objErr->arrErr['order_email'] = "このメールアドレスはすでに登録されています。<br>";
	}
		
	return $objErr->arrErr;
}

// 受注一時テーブルのお届け先をコピーする
function lfCopyDeliv($uniqid, $arrData) {
	$objQuery = new SC_Query();
	
	// 別のお届け先を指定していない場合、配送先に登録住所をコピーする。
	if($arrData["deliv_check"] != "1") {
		$sqlval['deliv_name01'] = $arrData['order_name01'];
		$sqlval['deliv_name02'] = $arrData['order_name02'];
		$sqlval['deliv_kana01'] = $arrData['order_kana01'];
		$sqlval['deliv_kana02'] = $arrData['order_kana02'];
		$sqlval['deliv_pref'] = $arrData['order_pref'];
		$sqlval['deliv_zip01'] = $arrData['order_zip01'];
		$sqlval['deliv_zip02'] = $arrData['order_zip02'];
		$sqlval['deliv_addr01'] = $arrData['order_addr01'];
		$sqlval['deliv_addr02'] = $arrData['order_addr02'];
		$sqlval['deliv_tel01'] = $arrData['order_tel01'];
		$sqlval['deliv_tel02'] = $arrData['order_tel02'];
		$sqlval['deliv_tel03'] = $arrData['order_tel03'];
		$where = "order_temp_id = ?";
		$objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
	}
}

//-----------------------------NONMEMBER関数群▼------------------------------------------------------------------
//----　取得文字列の変換
function lfConvertParam($array, $arrRegistColumn) {
    /*
     *  文字列の変換
     *  K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
     *  C :  「全角ひら仮名」を「全角かた仮名」に変換
     *  V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します 
     *  n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
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
function lfErrorCheck1($array) {

    global $objConn;
    $objErr = new SC_CheckError($array);
    
    $objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
    $objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
    $objErr->doFunc(array("お名前（カナ/姓）", 'kana01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
    $objErr->doFunc(array("お名前（カナ/名）", 'kana02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
    $objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK", "MOBILE_EMAIL_CHECK"));

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
        以下のような文字列が入っているので   対策する。
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
//NONMEMBER_関数群---------------------------------------------------------------------------------------
?>