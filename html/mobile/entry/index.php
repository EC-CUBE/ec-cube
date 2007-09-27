<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'entry/index.tpl';		// メインテンプレート
		$this->tpl_title .= '会員登録(1/3)';			//　ページタイトル
	}
}

//---- ページ初期設定
$CONF = sf_getBasisData();					// 店舗基本情報
$objConn = new SC_DbConn();
$objPage = new LC_Page();
$objView = new SC_MobileView();
$objCustomer = new SC_Customer();
$objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
$objPage->arrPref = $arrPref;
$objPage->arrJob = $arrJob;
$objPage->arrReminder = $arrReminder;
$objPage->arrYear = $objDate->getYear('', 1950);	//　日付プルダウン設定
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

// 空メール
if (isset($_SESSION['mobile']['kara_mail_from'])) {
	$objPage->tpl_kara_mail_from = $_POST['email'] = $_SESSION['mobile']['kara_mail_from'];
} elseif (MOBILE_USE_KARA_MAIL) {
	$token = gfPrepareKaraMail('entry/index.php');
	if ($token !== false) {
		$objPage->tpl_mainpage = 'entry/mail.tpl';
		$objPage->tpl_title = '会員登録(空メール)';
		$objPage->tpl_kara_mail_to = MOBILE_KARA_MAIL_ADDRESS_USER . MOBILE_KARA_MAIL_ADDRESS_DELIMITER . 'entry_' . $token . '@' . MOBILE_KARA_MAIL_ADDRESS_DOMAIN;
		$objPage->tpl_from_address = $CONF['email03'];
	}
}

//SSLURL判定
if (SSLURL_CHECK == 1){
	$ssl_url= sfRmDupSlash(MOBILE_SSL_URL.$_SERVER['REQUEST_URI']);
	if (!ereg("^https://", $non_ssl_url)){
		sfDispSiteError(URL_ERROR, "", false, "", true);
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
							 array(  "column" => "password02", "convert" => "a" ),
							 array(  "column" => "mailmaga_flg", "convert" => "n" ),
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
		$objPage->tpl_mainpage = 'entry/index.tpl';
		$objPage->tpl_title = '会員登録(1/3)';
	} elseif ($_POST["mode"] == "set2") {
		$objPage->arrErr = lfErrorCheck2($objPage->arrForm);
		$objPage->tpl_mainpage = 'entry/set1.tpl';
		$objPage->tpl_title = '会員登録(2/3)';
	} else {
		$objPage->arrErr = lfErrorCheck3($objPage->arrForm);
		$objPage->tpl_mainpage = 'entry/set2.tpl';
		$objPage->tpl_title = '会員登録(3/3)';
	}

	foreach($objPage->arrForm as $key => $val) {
		$objPage->$key = $val;
	}

	if ($objPage->arrErr || !empty($_POST["return"])) {		// 入力エラーのチェック

		//-- データの設定
		if ($_POST["mode"] == "set1") {
			$checkVal = array("email", "password", "reminder", "reminder_answer", "name01", "name02", "kana01", "kana02");
		} elseif ($_POST["mode"] == "set2") {
			$checkVal = array("sex", "year", "month", "day", "zip01", "zip02");
		} else {
			$checkVal = array("pref", "addr01", "addr02", "tel01", "tel02", "tel03", "mailmaga_flg");
		}

		foreach($objPage->arrForm as $key => $val) {
			if ($key != "mode" && $key != "submit" && $key != "return" && $key != session_name() && !in_array($key, $checkVal))
				$objPage->list_data[ $key ] = $val;
		}



	} else {

		//--　テンプレート設定
		if ($_POST["mode"] == "set1") {
			$objPage->tpl_mainpage = 'entry/set1.tpl';
			$objPage->tpl_title = '会員登録(2/3)';
		} elseif ($_POST["mode"] == "set2") {
			$objPage->tpl_mainpage = 'entry/set2.tpl';
			$objPage->tpl_title = '会員登録(3/3)';

			if (@$objPage->arrForm['pref'] == "" && @$objPage->arrForm['addr01'] == "" && @$objPage->arrForm['addr02'] == "") {
				$address = lfGetAddress($_REQUEST['zip01'].$_REQUEST['zip02']);
				$objPage->pref = @$address[0]['state'];
				$objPage->addr01 = @$address[0]['city'] . @$address[0]['town'];
			}
		} elseif ($_POST["mode"] == "confirm") {
			//パスワード表示
			$passlen = strlen($objPage->arrForm['password']);
			$objPage->passlen = lfPassLen($passlen);
			
			//メール受け取り
			if (strtolower($objPage->arrForm['mailmaga_flg']) == "on") {
				$objPage->arrForm['mailmaga_flg']  = "2";
			} else {
				$objPage->arrForm['mailmaga_flg']  = "3";
			}

			$objPage->tpl_mainpage = 'entry/confirm.tpl';
			$objPage->tpl_title = '会員登録(確認ページ)';

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

		foreach($objPage->arrForm as $key => $val) {
			if ($key != "mode" && $key != "submit" && $key != "confirm" && $key != "return" && $key != session_name() && !in_array($key, $checkVal)) {
				$objPage->list_data[ $key ] = $val;
			}
		}


		//--　仮登録と完了画面
		if ($_POST["mode"] == "complete") {
            
            // 確認画面で再度エラーチェックを行う。（画面1）
            $arrErr = lfErrorCheck1($objPage->arrForm);
            if(count($arrErr) > 0){
                $objPage->tpl_mainpage = 'entry/index.tpl';
                $objPage->tpl_title = '会員登録(1/3)';
                $objPage->arrErr = $arrErr;
                //----　ページ表示
                $objView->assignobj($objPage);
                $objView->display(SITE_FRAME);
                exit();
            }
            
            // 確認画面で再度エラーチェックを行う。（画面2）
            $arrErr = lfErrorCheck2($objPage->arrForm);
            if(count($arrErr) > 0){
                $objPage->tpl_mainpage = 'entry/set1.tpl';
                $objPage->tpl_title = '会員登録(2/3)';
                $objPage->arrErr = $arrErr;
                //----　ページ表示
                $objView->assignobj($objPage);
                $objView->display(SITE_FRAME);
                exit();
            }

            // 確認画面で再度エラーチェックを行う。（画面3）
            $arrErr = lfErrorCheck3($objPage->arrForm);
            if(count($arrErr) > 0){
                $objPage->tpl_mainpage = 'entry/set2.tpl';
                $objPage->tpl_title = '会員登録(3/3)';
                $objPage->arrErr = $arrErr;
                //----　ページ表示
                $objView->assignobj($objPage);
                $objView->display(SITE_FRAME);
                exit();
            }
            
			$objPage->uniqid = lfRegistData ($objPage->arrForm, $arrRegistColumn, $arrRejectRegistColumn, CUSTOMER_CONFIRM_MAIL);

			// 空メールを受信済みの場合はすぐに本登録完了にする。
			if (isset($_SESSION['mobile']['kara_mail_from'])) {
				header("Location:" . gfAddSessionId(MOBILE_URL_DIR . "regist/index.php?mode=regist&id=" . $objPage->uniqid));
				exit;
			}

			$objPage->tpl_mainpage = 'entry/complete.tpl';
			$objPage->tpl_title = '会員登録(完了ページ)';

			sfMobileSetExtSessionId('id', $objPage->uniqid, 'regist/index.php');

			//　仮登録完了メール送信
			$objPage->CONF = $CONF;
			$objPage->to_name01 = $_POST['name01'];
			$objPage->to_name02 = $_POST['name02'];
			$objMailText = new SC_MobileView();
			$objMailText->assignobj($objPage);
            
            // 仮会員が有効の場合
			if(CUSTOMER_CONFIRM_MAIL == true) {
				$subject = sfMakesubject('会員登録のご確認');
				$toCustomerMail = $objMailText->fetch("mail_templates/customer_mail.tpl");
			} else {
				$subject = sfMakesubject('会員登録のご完了');
				$toCustomerMail = $objMailText->fetch("mail_templates/customer_regist_mail.tpl");
				// ログイン状態にする
				$objCustomer->setLogin($_POST["email"]);
			}
            
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
			header("Location:" . gfAddSessionId("./complete.php"));
			exit;
		}
	}
}

//----　ページ表示
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//----------------------------------------------------------------------------------------------------------------------

//---- function群
function lfRegistData ($array, $arrRegistColumn, $arrRejectRegistColumn, $confirm_flg) {
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

    // 仮会員登録の場合
	if($confirm_flg == true) {
        // 重複しない会員登録キーを発行する。
		$count = 1;
		while ($count != 0) {
			$uniqid = sfGetUniqRandomId("t");
			$count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer WHERE secret_key = ?", array($uniqid));
		}
	
		switch($array["mailmaga_flg"]) {
			case 1:
				$arrRegist["mailmaga_flg"] = 4; 
				break;
			case 2:
				$arrRegist["mailmaga_flg"] = 5; 
				break;
			default:
				$arrRegist["mailmaga_flg"] = 6;
				break;
		}
		$arrRegist["status"] = "1";				// 仮会員
	} else {
		// 重複しない会員登録キーを発行する。
		$count = 1;
		while ($count != 0) {
			$uniqid = sfGetUniqRandomId("r");
			$count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer WHERE secret_key = ?", array($uniqid));
		}
		$arrRegist["status"] = "2";				// 本会員
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

/* メルマガ会員機能は現在停止中　2007/03/07
	//--　非会員でメルマガ登録しているかの判定
	$sql = "SELECT count(*) FROM dtb_customer_mail WHERE email = ?";
	$mailResult = $objConn->getOne($sql, array($arrRegist["email"]));

	//--　メルマガ仮登録実行
	$arrRegistMail["email"] = $arrRegist["email"];	
	if ($array["mailmaga_flg"] == 1) {
		$arrRegistMail["mailmaga_flg"] = 4; 
	} elseif ($array["mailmaga_flg"] == 2) {
		$arrRegistMail["mailmaga_flg"] = 5; 
	} else {
		$arrRegistMail["mailmaga_flg"] = 6; 
	}
	$arrRegistMail["update_date"] = "now()";
	
	// 非会員でメルマガ登録している場合
	if ($mailResult == 1) {		
		$objQuery->update("dtb_customer_mail", $arrRegistMail, "email = '" .addslashes($arrRegistMail["email"]). "'");			
	} else {				//　新規登録の場合
		$arrRegistMail["create_date"] = "now()";
		$objQuery->insert("dtb_customer_mail", $arrRegistMail);		
	}
*/
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
function lfErrorCheck1($array) {

	global $objConn;
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お名前（カナ/姓）", 'kana01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("お名前（カナ/名）", 'kana02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK", "MOBILE_EMAIL_CHECK"));

	//現会員の判定 →　現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
	if (strlen($array["email"]) > 0) {
		$objQuery = new SC_Query();
		$arrRet = $objQuery->select("email, update_date, del_flg", "dtb_customer","email ILIKE ? OR email_mobile ILIKE ? ORDER BY del_flg", array($array["email"], $array["email"]));
				
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

//-----------------------------------------------------------------------------------------------------------------------------------
?>
