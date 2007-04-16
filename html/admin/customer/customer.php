<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 * 顧客登録機能
 */
require_once("../require.php");

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// ページ表示用クラス
class LC_Page {
    var $arrSession;
    var $tpl_mode;
    var $list_data;

    var $arrErr;
    var $arrYear;
    var $arrMonth;
    var $arrDay;
    var $arrPref;
    var $arrJob;
    var $arrSex;
    var $arrReminder;
    var $count;
    
    var $tpl_strnavi;
                
    function LC_Page() {
        $this->tpl_mainpage = 'customer/customer.tpl';
        $this->tpl_mainno = 'customer';
        $this->tpl_subnavi = 'customer/subnavi.tpl';
        $this->tpl_subno = 'customer';
        $this->tpl_pager = DATA_PATH . 'Smarty/templates/admin/pager.tpl';
        $this->tpl_subtitle = '顧客登録';

        global $arrPref;
        $this->arrPref = $arrPref;
        global $arrJob;
        $this->arrJob = $arrJob;
        global $arrSex;
        $this->arrSex = $arrSex;
        global $arrReminder;
        $this->arrReminder = $arrReminder;
    }
}

// 店舗基本情報
$CONF = sf_getBasisData();
$objQuery = new SC_Query();
$objConn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objDate = new SC_Date(1901);
$objPage->arrYear = $objDate->getYear();    //　日付プルダウン設定
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

// 登録用カラム配列
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
							 array(  "column" => "note",		"convert" => "aKV" ),
							 array(  "column" => "point",		"convert" => "n" ),
                             array(  "column" => "status",        "convert" => "n" ),
                             array(  "column" => "mail_send",        "convert" => "n" )
                         );

// 登録除外用カラム配列
$arrRejectRegistColumn = array("year", "month", "day", "email02", "email_mobile02", "password02", "mail_send");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // POSTデータの引き継ぎ
    $objPage->arrForm = $_POST;
    $objPage->arrForm['email'] = strtolower($objPage->arrForm['email']);               // emailはすべて小文字で処理
    $objPage->arrForm['email_mobile'] = strtolower($objPage->arrForm['email_mobile']); // email_mobileはすべて小文字で処理

    // 入力データの変換
    $objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);
    // 入力チェック
    $objPage->arrErr = lfErrorCheck($objPage->arrForm);

    //-- 入力エラー発生 or リターン時
    if ($objPage->arrErr || $_POST["mode"] == "return") {
        foreach($objPage->arrForm as $key => $val) {
            $objPage->$key = $val;
        }

    } else {
        // 確認
        if ($_POST["mode"] == "confirm") {
            $objPage->tpl_mainpage = 'customer/customer_confirm.tpl';
            $passlen = strlen($objPage->arrForm['password']);
            $objPage->passlen = lfPassLen($passlen);
            
        }
        
        //　会員登録と完了画面
        if($_POST["mode"] == "complete") {
            $objPage->tpl_mainpage = 'customer/edit_complete.tpl';
            
            // 会員情報の登録
            $objPage->uniqid = lfRegistData ($objPage->arrForm, $arrRegistColumn, $arrRejectRegistColumn);
            // 登録完了メールの送信判定
            if ( $objPage->arrForm['mail_send'] == '1' ) {
                //　完了メール送信
                $objPage->CONF = $CONF;
                $objPage->name01 = $_POST['name01'];
                $objPage->name02 = $_POST['name02'];
                $objMailText = new SC_SiteView();
                $objMailText->assignobj($objPage);
                
                // 仮会員が有効の場合
                if($objPage->arrForm['status'] == '1') {
                    $subject = sfMakesubject('会員登録のご確認');
                    $toCustomerMail = $objMailText->fetch("mail_templates/customer_mail.tpl");
                } else {
                    $subject = sfMakesubject('会員登録のご完了');
                    $toCustomerMail = $objMailText->fetch("mail_templates/customer_regist_mail.tpl");
                }
                
                $objMail = new GC_SendMail();
                $objMail->setItem(
                                    ''                     //　宛先
                                    , $subject             //　サブジェクト
                                    , $toCustomerMail      //　本文
                                    , $CONF["email03"]     //　配送元アドレス
                                    , $CONF["shop_name"]   //　配送元　名前
                                    , $CONF["email03"]     //　reply_to
                                    , $CONF["email04"]     //　return_path
                                    , $CONF["email04"]     //  Errors_to
                                );
                // 宛先の設定
                $name = $_POST["name01"] . $_POST["name02"] ." 様";
                $objMail->setTo($_POST["email"], $name);
                $objMail->sendMail();
            }
            
        }
    }
}
//　ページ表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

/**
 * 会員情報の登録
 * @param array 画面からの入力配列
 * @param array 登録対象カラム配列
 * @param array 登録対象外カラム配列
 * @return array 会員登録キー
 */
function lfRegistData ($array, $arrRegistColumn, $arrRejectRegistColumn) {
    global $objConn;
    
    // 登録データの生成
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
        
        $arrRegist["status"] = "1";                // 仮会員
    } else {
        // 重複しない会員登録キーを発行する。
        $count = 1;
        while ($count != 0) {
            $uniqid = sfGetUniqRandomId("r");
            $count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer WHERE secret_key = ?", array($uniqid));
        }
        $arrRegist["status"] = "2";                // 本会員
    }
    
    /*
      secret_keyは、テーブルで重複許可されていない場合があるので、
            本会員登録では利用されないがセットしておく。
    */
    $arrRegist["secret_key"] = $uniqid;        // 会員登録キー
    $arrRegist["create_date"] = "now()";     // 作成日
    $arrRegist["update_date"] = "now()";     // 更新日
    $arrRegist["first_buy_date"] = "";         // 最初の購入日
    
    // 登録実行
    $objConn->query("BEGIN");

    $objQuery = new SC_Query();
    $objQuery->insert("dtb_customer", $arrRegist);

    $objConn->query("COMMIT");

    return $uniqid;
}


/**
 * 取得文字列の変換
 * @param array POSTデータ配列
 * @param array 登録用カラム配列
 * @return 変換後POSTデータ配列
 */
function lfConvertParam($array, $arrRegistColumn) {
    /*
     *    文字列の変換
     *    K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
     *    C :  「全角ひら仮名」を「全角かた仮名」に変換
     *    V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します    
     *    n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
     *    a :  全角英数字を半角英数字に変換する
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

/**
 * 入力エラーチェック
 * @param array $array
 * @return array エラーメッセージを格納した配列
 */
function lfErrorCheck($array) {

    global $objConn;
    $objErr = new SC_CheckError($array);

    $objErr->doFunc(array("会員状態", 'status'), array("EXIST_CHECK"));
    $objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    $objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    $objErr->doFunc(array("フリガナ（姓）", 'kana01', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
    $objErr->doFunc(array("フリガナ（名）", 'kana02', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
    $objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
    $objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK")); 
    $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
    $objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
    $objErr->doFunc(array("ご住所（1）", "addr01", MTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
    $objErr->doFunc(array("ご住所（2）", "addr02", MTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
    $objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("EXIST_CHECK", "NO_SPTAB", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
    
    // 現会員の判定 →　現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
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
    
    // 現会員の判定 →　現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
    if (strlen($array["email_mobile"]) > 0) {
        $objQuery = new SC_Query();
        $arrRet = $objQuery->select("email_mobile, update_date, del_flg", "dtb_customer","email_mobile ILIKE ? ORDER BY del_flg", array($array["email_mobile"]));
                
        if(count($arrRet) > 0) {
            if($arrRet[0]['del_flg'] != '1') {
                // 会員である場合
                $objErr->arrErr["email_mobile"] .= "※ すでに会員登録で使用されているメールアドレスです。<br />";
            } else {
                // 退会した会員である場合
                $leave_time = sfDBDatetoTime($arrRet[0]['update_date']);
                $now_time = time();
                $pass_time = $now_time - $leave_time;
                // 退会から何時間-経過しているか判定する。
                $limit_time = ENTRY_LIMIT_HOUR * 3600;                        
                if($pass_time < $limit_time) {
                    $objErr->arrErr["email_mobile"] .= "※ 退会から一定期間の間は、同じメールアドレスを使用することはできません。<br />";
                }
            }
        }
    }
    
    
    $objErr->doFunc(array("お電話番号1", 'tel01'), array("EXIST_CHECK"));
    $objErr->doFunc(array("お電話番号2", 'tel02'), array("EXIST_CHECK"));
    $objErr->doFunc(array("お電話番号3", 'tel03'), array("EXIST_CHECK"));
    $objErr->doFunc(array("お電話番号", "tel01", "tel02", "tel03", TEL_LEN) ,array("TEL_CHECK"));
    $objErr->doFunc(array("FAX番号", "fax01", "fax02", "fax03", TEL_LEN) ,array("TEL_CHECK"));
    $objErr->doFunc(array("ご性別", "sex") ,array("SELECT_CHECK", "NUM_CHECK")); 
    $objErr->doFunc(array("ご職業", "job") ,array("NUM_CHECK"));
    if ($array["password"] != DEFAULT_PASSWORD) {
        $objErr->doFunc(array("パスワード", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "ALNUM_CHECK", "NUM_RANGE_CHECK"));
    }
    $objErr->doFunc(array("パスワードを忘れたときのヒント 質問", "reminder") ,array("SELECT_CHECK", "NUM_CHECK")); 
    $objErr->doFunc(array("パスワードを忘れたときのヒント 答え", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    $objErr->doFunc(array("メールマガジン", "mailmaga_flg") ,array("SELECT_CHECK", "NUM_CHECK"));
    $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_DATE"));
    $objErr->doFunc(array("SHOP用メモ", 'note', LTEXT_LEN), array("MAX_LENGTH_CHECK"));
    $objErr->doFunc(array("所持ポイント", "point", TEL_LEN) ,array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    return $objErr->arrErr;
    
}

/**
 * 確認ページ用パスワード表示用
 * $passlen パスワード
 * $ret 確認ページ用パスワード
 */
function lfPassLen($passlen){
    $ret = "";
    for ($i=0;$i<$passlen;true){
        $ret.="*";
        $i++;
    }
    return $ret;
}
?>