<?php

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css = '/css/layout/contact/index.css';	// メインCSSパス
		$this->tpl_mainpage = 'contact/index.tpl';
		$this->tpl_title = 'お問い合わせ(入力ページ)';
		$this->tpl_page_category = 'contact';
		global $arrPref;
		$this->arrPref = $arrPref;
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_SiteView();
$CONF = sf_getBasisData();			// 店舗基本情報
sfDomainSessionStart();	 

$objCustomer = new SC_Customer();

if ($objCustomer->isloginSuccess()){
	$objPage->arrData = $_SESSION['customer'];
}

//SSLURL判定
if (SSLURL_CHECK == 1){
	$ssl_url= sfRmDupSlash(SSL_URL.$_SERVER['REQUEST_URI']);
	if (!ereg("^https://", $non_ssl_url)){
		sfDispSiteError(URL_ERROR);
	}
}

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

//フォーム値変換用カラム
$arrConvertColumn = array(
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
							 array(  "column" => "contents",   "convert" => "aKV")
					      );

switch ($_POST['mode']){
	case 'confirm':
	// エラーチェック
	$objPage->arrForm = $_POST;
	$objPage->arrForm['email'] = strtolower($_POST['email']);
	$objPage->arrForm = lfConvertParam($objPage->arrForm,$arrConvertColumn);
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);
	if ( ! $objPage->arrErr ){
		// エラー無しで完了画面
		$objPage->tpl_mainpage = 'contact/confirm.tpl';
		$objPage->tpl_title = 'お問い合わせ(確認ページ)';
	} else {
		foreach ($objPage->arrForm as $key => $val){
		$objPage->$key = $val;
		}
	}
	break;
	
	case 'return':
	foreach ($_POST as $key => $val){
		$objPage->$key = $val;
		}
	break;
	
	case 'complete':
	$objPage->arrForm = $_POST;
	$objPage->arrForm['email'] = strtolower($_POST['email']);
	$objPage->arrForm = lfConvertParam($objPage->arrForm,$arrConvertColumn);
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);
	if(!$objPage->arrErr) {
		lfSendMail($CONF, $objPage);
		//完了ページへ移動する
		header("location: ./complete.php");
		exit;
	} else {
		sfDispSiteError(CUSTOMER_ERROR);
	}
	break;
	
	default:
	break;
}

//----　ページ表示
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//------------------------------------------------------------------------------------------------------------------------------------------

//エラーチェック処理部
function lfErrorCheck($array) {

	$objErr = new SC_CheckError($array);
	$objErr->doFunc(array("お名前(姓)", 'name01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お名前(名)", 'name02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("フリガナ(セイ)", 'kana01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("フリガナ(メイ)", 'kana02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("ご住所1", "addr01", MTEXT_LEN), array("SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("ご住所2", "addr02", MTEXT_LEN), array("SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お問い合わせ内容", "contents", MLTEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('メールアドレス(確認)', "email02", MTEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('メールアドレス', 'メールアドレス(確認)', "email", "email02") ,array("EQUAL_CHECK"));
	$objErr->doFunc(array("お電話番号1", 'tel01', TEL_ITEM_LEN), array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お電話番号2", 'tel02', TEL_ITEM_LEN), array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お電話番号3", 'tel03', TEL_ITEM_LEN), array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	

	return $objErr->arrErr;

}

//----　取得文字列の変換
function lfConvertParam($array, $arrConvertColumn) {
	/*
	 *	文字列の変換
	 *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
	 *	C :  「全角ひら仮名」を「全角かた仮名」に変換
	 *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します	
	 *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
	 *  a :  全角英数字を半角英数字に変換する
	 */
	// カラム名とコンバート情報
	foreach ($arrConvertColumn as $data) {
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

// ------------  メール送信 ------------ 

function lfSendMail($CONF, $objPage){
	//　パスワード変更お知らせメール送信
	
	$objMailText = new SC_SiteView();
	$objSiteInfo = new SC_SiteInfo();
	$arrInfo = $objSiteInfo->data;
	$objPage->tpl_shopname=$arrInfo['shop_name'];
	$objPage->tpl_infoemail = $arrInfo['email02'];
	$objMailText->assignobj($objPage);
	$toCustomerMail = $objMailText->fetch("mail_templates/contact_mail.tpl");
	$objMail = new GC_SendMail();
	
	if ( $objPage->arrForm['email'] ) {
		$fromMail_name = $objPage->arrForm['name01'] ." 様";
		$fromMail_address = $objPage->arrForm['email'];
	} else {
		$fromMail_name = $CONF["shop_name"];
		$fromMail_address = $CONF["email02"];
	}
	$subject = sfMakeSubject("お問い合わせがありました。");
	$objMail->setItem(
						  $CONF["email02"]					//　宛先
						, $subject							//　サブジェクト
						, $toCustomerMail					//　本文
						, $fromMail_address					//　配送元アドレス
						, $fromMail_name					//　配送元　名前
						, $fromMail_address					//　reply_to
						, $CONF["email04"]					//　return_path
						, $CONF["email04"]					//  Errors_to
														);
	$objMail->sendMail();
	
	$subject = sfMakeSubject("お問い合わせを受け付けました。");
	$objMail->setItem(
						  ''								//　宛先
						, $subject							//　サブジェクト
						, $toCustomerMail					//　本文
						, $CONF["email03"]					//　配送元アドレス
						, $CONF["shop_name"]				//　配送元　名前
						, $CONF["email02"]					//　reply_to
						, $CONF["email04"]					//　return_path
						, $CONF["email04"]					//  Errors_to
														);
	$objMail->setTo($objPage->arrForm['email'], $objPage->arrForm['name01'] ." 様");
	$objMail->sendMail();	
}
?>