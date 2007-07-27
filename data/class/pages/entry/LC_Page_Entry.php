<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 会員登録(入力ページ) のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Entry extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_css = URL_DIR.'css/layout/entry/index.css';
        $this->tpl_mainpage = 'entry/index.tpl';
        $this->tpl_title .= '会員登録(入力ページ)';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $CONF = SC_Utils_Ex::sf_getBasisData();					// 店舗基本情報
        $objConn = new SC_DbConn();
        $objView = new SC_SiteView();
        $objCustomer = new SC_Customer();
        $objCampaignSess = new SC_CampaignSession();
        $objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData("mtb_pref",
                                array("pref_id", "pref_name", "rank"));

        // TODO
        $this->arrJob = array(
                    1 => "公務員",
                    2 => "コンサルタント",
                    3 => "コンピュータ関連技術職",
                    4 => "コンピュータ関連以外の技術職",
                    5 => "金融関係",
                    6 => "医師",
                    7 => "弁護士",
                    8 => "総務・人事・事務",
                    9 => "営業・販売",
                    10 => "研究・開発",
                    11 => "広報・宣伝",
                    12 => "企画・マーケティング",
                    13 => "デザイン関係",
                    14 => "会社経営・役員",
                    15 => "出版・マスコミ関係",
                    16 => "学生・フリーター",
                    17 => "主婦",
                    18 => "その他"
                );
        // TODO
        $this->arrReminder = array(
                        1 => "母親の旧姓は？",
                        2 => "お気に入りのマンガは？",
                        3 => "大好きなペットの名前は？",
                        4 => "初恋の人の名前は？",
                        5 => "面白かった映画は？",
                        6 => "尊敬していた先生の名前は？",
                        7 => "好きな食べ物は？"
                    );
        $this->arrYear = $objDate->getYear('', 1950);	//　日付プルダウン設定
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        //SSLURL判定
        if (SSLURL_CHECK == 1){
            $ssl_url= SC_Utils_Ex::sfRmDupSlash(SSL_URL.$_SERVER['REQUEST_URI']);
            if (!ereg("^https://", $non_ssl_url)){
                // TODO エラーメッセージはデフォルト値でOK?
                SC_Utils_Ex::sfDispSiteError(URL_ERROR);
            }
        }

        // レイアウトデザインを取得
        $layout = new SC_Helper_PageLayout_Ex();
        $this = $layout->sfGetPageLayout($this, false, DEF_LAYOUT);

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

            // TODO transaction check
            if (!$this->isValidToken()) {
                SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
            }

            //-- POSTデータの引き継ぎ
            $this->arrForm = $_POST;

            if($this->arrForm['year'] == '----') {
                $this->arrForm['year'] = '';
            }

            $this->arrForm['email'] = strtolower($this->arrForm['email']);		// emailはすべて小文字で処理
            $this->arrForm['email02'] = strtolower($this->arrForm['email02']);	// emailはすべて小文字で処理

            //-- 入力データの変換
            $this->arrForm = $this->lfConvertParam($this->arrForm, $arrRegistColumn);

            //--　入力エラーチェック
            //$this->arrErr = $this->lfErrorCheck($this->arrForm); TODO

            if ($this->arrErr || $_POST["mode"] == "return") {		// 入力エラーのチェック
                foreach($this->arrForm as $key => $val) {
                    $this->$key = $val;
                }

            } else {

                //--　確認
                if ($_POST["mode"] == "confirm") {
                    foreach($this->arrForm as $key => $val) {
                        if ($key != "mode" && $key != "subm") $this->list_data[ $key ] = $val;
                    }
                    //パスワード表示
                    $passlen = strlen($this->arrForm['password']);
                    $this->passlen = SC_Utils_Ex::lfPassLen($passlen);

                    $this->tpl_css = '/css/layout/entry/confirm.css';
                    $this->tpl_mainpage = 'entry/confirm.tpl';
                    $this->tpl_title = '会員登録(確認ページ)';

                }

                //--　会員登録と完了画面
                if ($_POST["mode"] == "complete") {
                    // キャンペーンからの遷移の時用の値
                    if($objCampaignSess->getIsCampaign()) {
                        $this->etc_value = "&cp=".$objCampaignSess->getCampaignId();
                    }

                    // 会員情報の登録
                    //$this->uniqid = $this->lfRegistData ($this->arrForm, $arrRegistColumn, $arrRejectRegistColumn, CUSTOMER_CONFIRM_MAIL); TODO

                    $this->tpl_css = '/css/layout/entry/complete.css';
                    $this->tpl_mainpage = 'entry/complete.tpl';
                    $this->tpl_title = '会員登録(完了ページ)';

                    //　完了メール送信
                    $this->CONF = $CONF;
                    $this->name01 = $_POST['name01'];
                    $this->name02 = $_POST['name02'];
                    $objMailText = new SC_SiteView();
                    $objMailText->assignobj($this);

                    // 仮会員が有効の場合
                    if(CUSTOMER_CONFIRM_MAIL == true) {
                        $subject = SC_Utils_Ex::sfMakesubject('会員登録のご確認');
                        $toCustomerMail = $objMailText->fetch("mail_templates/customer_mail.tpl");
                    } else {
                        $subject = SC_Utils_Ex::sfMakesubject('会員登録のご完了');
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
                                    );
                    // 宛先の設定
                    $name = $_POST["name01"] . $_POST["name02"] ." 様";
                    $objMail->setTo($_POST["email"], $name);
                    //$objMail->sendMail(); TODO

                    // 完了ページに移動させる。
                    $this->sendRedirect($this->getLocation("./complete.php", array(), true));
                    exit;
                }
            }
        }

        if($this->year == '') {
            $this->year = '----';
        }

        $this->transactionid = $this->getToken();

        //----　ページ表示
        $objView->assignobj($this);
        // フレームを選択(キャンペーンページから遷移なら変更)
        $objCampaignSess->pageView($objView);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    // }}}
    // {{{ protected functions

    // 会員情報の登録
    function lfRegistData ($array, $arrRegistColumn, $arrRejectRegistColumn, $confirm_flg) {
        $objConn = new SC_DbConn();

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
                $uniqid = SC_Utils_Ex::sfGetUniqRandomId("t");
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
                $uniqid = SC_Utils_Ex::sfGetUniqRandomId("r");
                $count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer WHERE secret_key = ?", array($uniqid));
            }
            $arrRegist["status"] = "2";				// 本会員
        }

        /*
          secret_keyは、テーブルで重複許可されていない場合があるので、
          本会員登録では利用されないがセットしておく。
        */
        $arrRegist["secret_key"] = $uniqid;		// 会員登録キー
        $arrRegist["create_date"] = "now()"; 	// 作成日
        $arrRegist["update_date"] = "now()"; 	// 更新日
        $arrRegist["first_buy_date"] = "";	 	// 最初の購入日

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
        $objErr->doFunc(array("メールマガジン", "mailmaga_flg") ,array("SELECT_CHECK", "NUM_CHECK"));

        $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_DATE"));
        $objErr->doFunc(array("メールマガジン", 'mailmaga_flg'), array("SELECT_CHECK"));

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
}
?>
