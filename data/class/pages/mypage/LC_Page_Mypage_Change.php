<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

if (file_exists(MODULE_PATH . "mdl_gmopg/inc/function.php")) {
    require_once(MODULE_PATH . "mdl_gmopg/inc/function.php");
}

/**
 * 登録内容変更 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_Change extends LC_Page {


    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = 'MYページ';
        $this->tpl_subtitle = '会員登録内容変更(入力ページ)';
        $this->tpl_navi = TEMPLATE_DIR . 'mypage/navi.tpl';
        $this->tpl_mainno = 'mypage';
        $this->tpl_mypageno = 'change';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrReminder = $masterData->getMasterData("mtb_reminder");
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrJob = $masterData->getMasterData("mtb_job");
        $this->arrMAILMAGATYPE = $masterData->getMasterData("mtb_mail_magazine_type");
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
        $this->action();
        $this->sendResponse();
    }
    
    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {
        //$objView = new SC_SiteView();
        $this->objQuery = new SC_Query();
        $this->objCustomer = new SC_Customer();
        $this->objFormParam = new SC_FormParam();
        
        // 退会判定用情報の取得
        $objCustomer = new SC_Customer();
        $this->tpl_login = $objCustomer->isLoginSuccess();

        // 生年月日選択肢の取得
        $objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
        $this->arrYear = $objDate->getYear('', 1950, '');
        $this->arrMonth = $objDate->getMonth(true);
        $this->arrDay = $objDate->getDay(true);

        // ログインチェック
        if (!$this->objCustomer->isLoginSuccess()){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }else {
            //マイページトップ顧客情報表示用
            $this->CustomerName1 = $this->objCustomer->getvalue('name01');
            $this->CustomerName2 = $this->objCustomer->getvalue('name02');
            $this->CustomerPoint = $this->objCustomer->getvalue('point');
        }

        //---- 登録用カラム配列
        $arrRegistColumn = array(
                                 array(  "column" => "name01",      "convert" => "aKV" ),
                                 array(  "column" => "name02",      "convert" => "aKV" ),
                                 array(  "column" => "kana01",      "convert" => "CKV" ),
                                 array(  "column" => "kana02",      "convert" => "CKV" ),
                                 array(  "column" => "zip01",       "convert" => "n" ),
                                 array(  "column" => "zip02",       "convert" => "n" ),
                                 array(  "column" => "pref",        "convert" => "n" ),
                                 array(  "column" => "addr01",      "convert" => "aKV" ),
                                 array(  "column" => "addr02",      "convert" => "aKV" ),
                                 array(  "column" => "email",       "convert" => "a" ),
                                 array(  "column" => "email_mobile", "convert" => "a" ),
                                 array(  "column" => "tel01",       "convert" => "n" ),
                                 array(  "column" => "tel02",       "convert" => "n" ),
                                 array(  "column" => "tel03",       "convert" => "n" ),
                                 array(  "column" => "fax01",       "convert" => "n" ),
                                 array(  "column" => "fax02",       "convert" => "n" ),
                                 array(  "column" => "fax03",       "convert" => "n" ),
                                 array(  "column" => "sex",         "convert" => "n" ),
                                 array(  "column" => "job",         "convert" => "n" ),
                                 array(  "column" => "birth",       "convert" => "n" ),
                                 array(  "column" => "password",    "convert" => "an" ),
                                 array(  "column" => "reminder",    "convert" => "n" ),
                                 array(  "column" => "reminder_answer", "convert" => "aKV" ),
                                 array(  "column" => "mailmaga_flg", "convert" => "n" )
                                 );

        //メールアドレス種別
        $arrMailType = array("email" => true, "email_mobile" => true);

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch ($_POST['mode']){

        case 'confirm':

            //エラーなしでかつメールアドレスが重複していない場合
            if ($this->checkErrorTotal($arrRegistColumn, $arrMailType)) {

                //確認ページへ
                $this->tpl_mainpage = TEMPLATE_DIR . 'mypage/change_confirm.tpl';
                $this->tpl_subtitle = '会員登録内容変更(確認ページ)';
                $passlen = strlen($this->arrForm['password']);
                $this->passlen = $this->lfPassLen($passlen);
            } else {
                $this->lfFormReturn($this->arrForm,$this);
            }

            break;

        case 'return':
            $this->arrForm = $_POST;
            $this->lfFormReturn($this->arrForm,$this);
            break;

        case 'gmo_oneclick':
            sfGMOMypageEdit();
            $this->arrForm = $this->lfGetCustomerData();
            $this->arrForm['password'] = DEFAULT_PASSWORD;
            $this->arrForm['password02'] = DEFAULT_PASSWORD;
            break;
        case 'complete':
            //エラーなしでかつメールアドレスが重複していない場合
            if ($this->checkErrorTotal($arrRegistColumn, $arrMailType)) {
                $this->arrForm['customer_id'] = $this->objCustomer->getValue('customer_id');
                //-- 編集登録
                $objDb = new SC_Helper_DB_Ex();
                $objDb->sfEditCustomerData($this->arrForm, $arrRegistColumn);
                //セッション情報を最新の状態に更新する
                $this->objCustomer->updateSession();

                // Do楽SNS連携モジュールユーザ情報更新処理
                if (function_exists('sfUpdateSourakuSNSUserInfo')) {
                    sfUpdateSourakuSNSUserInfo();
                }

                //完了ページへ
                $this->objDisplay->redirect($this->getLocation("./change_complete.php"));
                exit;
            } else {
                SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
            }
            break;

        default:
            //顧客情報取得
            $this->arrForm = $this->lfGetCustomerData();
            $this->arrForm['password'] = DEFAULT_PASSWORD;
            $this->arrForm['password02'] = DEFAULT_PASSWORD;
            break;
        }

        //誕生日データ登録の有無
        $arrCustomer = $this->lfGetCustomerData();
        if ($arrCustomer['birth'] != ""){
            $this->birth_check = true;
        }

        //$objView->assignobj($this);             //$objpage内の全てのテンプレート変数をsmartyに格納
        //$objView->display(SITE_FRAME);              //パスとテンプレート変数の呼び出し、実行


    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->init();
        $this->tpl_mainpage = 'mypage/change.tpl';		// メインテンプレート
        $this->tpl_title .= '登録変更(1/3)';			// ページタイトル

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrReminder = $masterData->getMasterData("mtb_reminder");
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrJob = $masterData->getMasterData("mtb_job");
        $this->arrMAILMAGATYPE = $masterData->getMasterData("mtb_mail_magazine_type");
        $this->arrSex = $masterData->getMasterData("mtb_sex");
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        parent::mobileProcess();
        $this->mobileAction();
        $this->sendResponse();
    }

    /**
     * Page のAction(モバイル).
     *
     * @return void
     */
    function mobileAction() {
        $objDb = new SC_Helper_DB_Ex();
        $CONF = $objDb->sfGetBasisData();					// 店舗基本情報
        //$objView = new SC_MobileView();
        
        // 生年月日選択肢の取得
        $objDate = new SC_Date();
        $this->arrMonth = $objDate->getMonth(true);
        $this->arrDay = $objDate->getDay(true);

        $this->objQuery = new SC_Query();
        $this->objCustomer = new SC_Customer();

        //メールアドレス種別
        $arrMailType = array("email" => true, "email_mobile" => true);

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

        $this->arrForm = $this->lfGetCustomerData();
        $this->arrForm['password'] = DEFAULT_PASSWORD;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            //-- POSTデータの引き継ぎ
            $this->arrForm = array_merge($this->arrForm, $_POST);

            //-- 入力データの変換
            $this->arrForm = $this->lfConvertParam($this->arrForm, $arrRegistColumn);

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
                $this->arrErr = $this->lfErrorCheck1($this->arrForm);
                $this->tpl_mainpage = 'mypage/change.tpl';
                $this->tpl_title = '登録変更(1/3)';
            } elseif ($_POST["mode"] == "set2") {
                $this->arrErr = $this->lfErrorCheck2($this->arrForm);
                $this->tpl_mainpage = 'mypage/set1.tpl';
                $this->tpl_title = '登録変更(2/3)';
            } else {
                $this->arrErr = $this->lfErrorCheck3($this->arrForm);
                $this->tpl_mainpage = 'mypage/set2.tpl';
                $this->tpl_title = '登録変更(3/3)';
            }

            if ($this->arrErr || !empty($_POST["return"])) {		// 入力エラーのチェック
                //-- データの設定
                if ($_POST["mode"] == "set1") {
                    $checkVal = array("email", "email_mobile", "password", "reminder", "reminder_answer", "name01", "name02", "kana01", "kana02");
                } elseif ($_POST["mode"] == "set2") {
                    $checkVal = array("sex", "year", "month", "day", "zip01", "zip02");
                } else {
                    $checkVal = array("pref", "addr01", "addr02", "tel01", "tel02", "tel03", "mailmaga_flg");
                }

                foreach($this->arrForm as $key => $val) {
                    if ($key != "return" && $key != "mode" && $key != "confirm" && $key != session_name() && !in_array($key, $checkVal)) {
                        $this->list_data[ $key ] = $val;
                    }
                }

            } else {

                //--　テンプレート設定
                if ($_POST["mode"] == "set1") {
                    $this->tpl_mainpage = 'mypage/set1.tpl';
                    $this->tpl_title = '登録変更(2/3)';
                } elseif ($_POST["mode"] == "set2") {
                    $this->tpl_mainpage = 'mypage/set2.tpl';
                    $this->tpl_title = '登録変更(3/3)';
                } elseif ($_POST["mode"] == "confirm") {
                    //パスワード表示
                    $passlen = strlen($this->arrForm['password']);
                    $this->passlen = $this->lfPassLen($passlen);

                    // メール受け取り
                    if (!isset($_POST['mailmaga_flg'])) $_POST['mailmaga_flg'] = "";
                    if (strtolower($_POST['mailmaga_flg']) == "on") {
                        $this->arrForm['mailmaga_flg'] = "2";
                    } else {
                        $this->arrForm['mailmaga_flg'] = "3";
                    }

                    $this->tpl_mainpage = 'mypage/change_confirm.tpl';
                    $this->tpl_title = '登録変更(確認ページ)';

                }

                //-- データ設定
                unset($this->list_data);
                if ($_POST["mode"] == "set1") {
                    $checkVal = array("sex", "year", "month", "day", "zip01", "zip02");
                } elseif ($_POST["mode"] == "set2") {
                    $checkVal = array("pref", "addr01", "addr02", "tel01", "tel02", "tel03", "mailmaga_flg");
                } else {
                    $checkVal = array();
                }

                foreach($this->arrForm as $key => $val) {
                    if ($key != "return" && $key != "mode" && $key != "confirm" && $key != session_name() && !in_array($key, $checkVal)) {
                        $this->list_data[ $key ] = $val;
                    }
                }


                //--　仮登録と完了画面
                if ($_POST["mode"] == "complete") {
                    //エラーなしでかつメールアドレスが重複していない場合
                    if($this->checkErrorTotal($arrRegistColumn, $arrMailType, true)) {
                        $this->arrForm['customer_id'] = $this->objCustomer->getValue('customer_id');
                        //-- 編集登録
                        $objDb->sfEditCustomerData($this->arrForm, $arrRegistColumn);
                        //セッション情報を最新の状態に更新する
                        $this->objCustomer->updateSession();
                        //完了ページへ
                        $this->objDisplay->redirect($this->getLocation("./change_complete.php"));
                        exit;
                    } else {
                        SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
                    }
                }
            }
        }

        $arrPrivateVariables = array('secret_key', 'first_buy_date', 'last_buy_date', 'buy_times', 'buy_total', 'point', 'note', 'status', 'create_date', 'update_date', 'del_flg', 'cell01', 'cell02', 'cell03', 'mobile_phone_id');
        foreach ($arrPrivateVariables as $key) {
            unset($this->list_data[$key]);
        }

        //---- ページ表示
        //$objView->assignobj($this);
        //$objView->display(SITE_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * すべてのエラーチェックを行う.
     *
     * @param array $arrRegistColumn 登録カラムの配列
     * @param array $arrMailType メール種別とフラグを格納した配列
     * @param bool $isMobile モバイル版登録チェックの場合 true
     * @return bool エラーの無い場合 true
     */
    function checkErrorTotal(&$arrRegistColumn, &$arrMailType, $isMobile = false) {
        // 入力データの変換
        $this->arrForm = $this->lfConvertParam($_POST, $arrRegistColumn);

        //エラーチェック
        $this->arrErr = $isMobile
            ? $this->lfErrorCheckMobile($this->arrForm)
            : $this->lfErrorCheck($this->arrForm);

        //メールアドレスを変更している場合、メールアドレスの重複チェック
        $arrMailType2 = $arrMailType;
        foreach ($arrMailType as $mailType => $mailTypeValue) {

            if ($this->arrForm[$mailType]
                != $this->objCustomer->getValue($mailType)){

                $email_cnt = $this->objQuery->count("dtb_customer",
                                 "del_flg=0 AND (email = ? OR email_mobile = ?) ",
                                  array($this->arrForm[$mailType], $this->arrForm[$mailType]));
                if ($email_cnt > 0){
                    $arrMailType2[$mailType] = false;
                    $this->arrErr[$mailType] .= "既に使用されているメールアドレスです。";
                }
            }
        }

        // エラーが存在せず, メールアドレスの重複が無い場合は true
        if (empty($this->arrErr)
            && $arrMailType2['email'] == true
            && $arrMailType2['email_mobile'] == true) {
            return true;
        } else {
            return false;
        }
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("お名前(姓)", "name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前(名)", "name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前(フリガナ・姓)", "kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前(フリガナ・名)", "kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("郵便番号1", "zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("郵便番号2", "zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("都道府県", "pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("住所1", "addr01", MTEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("住所2", "addr02", MTEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お電話番号1", "tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("お電話番号2", "tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("お電話番号3", "tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    }

    //エラーチェック

    function lfErrorCheck($array) {
        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("お名前(姓)", 'name01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前(名)", 'name02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前(フリガナ・姓)", 'kana01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("お名前(フリガナ・名)", 'kana02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("住所1", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("住所2", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "NO_SPTAB" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メールアドレス(確認)', "email02", MTEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK","NO_SPTAB" , "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メールアドレス', 'メールアドレス(確認)', "email", "email02") ,array("EQUAL_CHECK"));
        $objErr->doFunc(array('携帯メールアドレス', "email_mobile", MTEXT_LEN) ,array("EMAIL_CHECK", "NO_SPTAB" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK", "MOBILE_EMAIL_CHECK"));
        $objErr->doFunc(array('携帯メールアドレス(確認)', "email_mobile02", MTEXT_LEN), array("EMAIL_CHECK","NO_SPTAB" , "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK", "MOBILE_EMAIL_CHECK"));
        $objErr->doFunc(array('携帯メールアドレス', '携帯メールアドレス(確認)', "email_mobile", "email_mobile02") ,array("EQUAL_CHECK"));
        $objErr->doFunc(array("お電話番号1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK"));
        $objErr->doFunc(array("お電話番号2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK"));
        $objErr->doFunc(array("お電話番号3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK"));
        $objErr->doFunc(array("お電話番号", "tel01", "tel02", "tel03") ,array("TEL_CHECK"));
        $objErr->doFunc(array("FAX番号", "fax01", "fax02", "fax03") ,array("TEL_CHECK"));
        $objErr->doFunc(array("ご性別", "sex") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("ご職業", "job") ,array("NUM_CHECK"));
        $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_BIRTHDAY"));
        $objErr->doFunc(array("パスワード", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "ALNUM_CHECK", "NUM_RANGE_CHECK"));
        $objErr->doFunc(array("パスワード(確認)", 'password02', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "ALNUM_CHECK", "NUM_RANGE_CHECK"));
        $objErr->doFunc(array("パスワード", 'パスワード(確認)', 'password', 'password02'), array("EQUAL_CHECK"));
        $objErr->doFunc(array("パスワードを忘れたときの質問", "reminder") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("パスワードを忘れたときの答え", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("メールマガジン", "mailmaga_flg") ,array("SELECT_CHECK", "NUM_CHECK"));
        return $objErr->arrErr;

    }

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
        foreach ($arrRegistColumn as $registColumn) {
            $key = $registColumn["column"];
            $mb_convert_kana_option = $registColumn["convert"];
            $val =& $array[$key];

            // string 型以外は変換対象外とする
            if (!is_string($val)) continue;

            // 文字変換
            if (strlen($val) > 0) {
                $val = mb_convert_kana($val ,$mb_convert_kana_option);
            }
            
            // メールアドレスは小文字に変換
            if ($key == 'email' || $key == 'email02' || $key == 'email_mobile' || $key == 'email_mobile02') {
                $val = strtolower($val);
            }
        }
        return $array;
    }

    /**
     * 顧客情報の取得
     *
     * @return array 顧客情報
     */
    function lfGetCustomerData(){
        // 顧客情報DB取得
        $ret = $this->objQuery->select("*","dtb_customer","customer_id=?", array($this->objCustomer->getValue('customer_id')));
        $arrForm = $ret[0];

        // 確認項目に複製
        $arrForm['email02'] = $arrForm['email'];
        $arrForm['email_mobile02'] = $arrForm['email_mobile02'];

        // 誕生日を年月日に分ける
        if (isset($arrForm['birth'])){
            $birth = split(" ", $arrForm["birth"]);
            list($year, $month, $day) = split("-",$birth[0]);

            $arrForm['year'] = $year;
            $arrForm['month'] = $month;
            $arrForm['day'] = $day;

        }
        return $arrForm;
    }

    /**
     * 編集登録
     * TODO
     * @deprecated 未使用?
     */
    function lfRegistData($array, $arrRegistColumn) {

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
        if ($array["password"] != DEFAULT_PASSWORD) $arrRegist["password"] = sha1($array["password"] . ":" . AUTH_MAGIC);
        $arrRegist["update_date"] = "NOW()";

        //-- 編集登録実行
        $this->objQuery->begin();
        $this->objQuery->update("dtb_customer", $arrRegist, "customer_id = ? ", array($this->objCustomer->getValue('customer_id')));
        $this->objQuery->commit();
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
    function lfFormReturn($array, &$objPage){
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


    // }}}
    // {{{ mobile functions

    //エラーチェック

    function lfErrorCheckMobile($array) {
        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("お名前(姓)", 'name01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前(名)", 'name02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前(フリガナ・姓)", 'kana01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("お名前(フリガナ・名)", 'kana02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
        $objErr->doFunc(array('住所1', "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('住所2', "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "NO_SPTAB" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('携帯メールアドレス', "email_mobile", MTEXT_LEN) ,array("EMAIL_CHECK", "NO_SPTAB" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK", "MOBILE_EMAIL_CHECK"));
        $objErr->doFunc(array("電話番号1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK"));
        $objErr->doFunc(array("電話番号2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK"));
        $objErr->doFunc(array("電話番号3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK"));
        $objErr->doFunc(array("電話番号", "tel01", "tel02", "tel03") ,array("TEL_CHECK"));
        $objErr->doFunc(array("FAX番号", "fax01", "fax02", "fax03") ,array("TEL_CHECK"));
        $objErr->doFunc(array("性別", "sex") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("ご職業", "job") ,array("NUM_CHECK"));
        $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_BIRTHDAY"));
        $objErr->doFunc(array("パスワード", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "ALNUM_CHECK", "NUM_RANGE_CHECK"));
        $objErr->doFunc(array("パスワード確認用の質問", "reminder") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("パスワード確認用の質問の答え", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        return $objErr->arrErr;

    }


    //---- 入力エラーチェック
    function lfErrorCheck1($array) {

        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("お名前(姓)", 'name01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前(名)", 'name02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前(フリガナ・姓)", 'kana01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("お名前(フリガナ・名)", 'kana02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('携帯メールアドレス', "email_mobile", MTEXT_LEN) ,array("NO_SPTAB", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK", "MOBILE_EMAIL_CHECK"));

        //現会員の判定 →　現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
        $array["customer_id"] = $this->objCustomer->getValue('customer_id');
        if (strlen($array["email"]) > 0) {
            $arrRet = $this->objQuery->select("email, update_date, del_flg", "dtb_customer","customer_id <> ? and (email = ? OR email_mobile = ?) ORDER BY del_flg", array($array["customer_id"], $array["email"], $array["email"]));

            if(count($arrRet) > 0) {
                if($arrRet[0]['del_flg'] != '1') {
                    // 会員である場合
                    $objErr->arrErr["email"] .= "※ すでに会員登録で使用されているメールアドレスです。<br />";
                } else {
                    // 退会した会員である場合
                    $leave_time = SC_Utils_Ex::sfDBDatetoTime($arrRet[0]['update_date']);
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

        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));

        $objErr->doFunc(array("性別", "sex") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_BIRTHDAY"));

        return $objErr->arrErr;
    }

    //---- 入力エラーチェック
    function lfErrorCheck3($array) {

        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
        $objErr->doFunc(array('住所1', "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('住所2', "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("電話番号1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK" ));
        $objErr->doFunc(array("電話番号2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK" ));
        $objErr->doFunc(array("電話番号3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK" ));
        $objErr->doFunc(array("電話番号", "tel01", "tel02", "tel03") ,array("TEL_CHECK"));

        return $objErr->arrErr;
    }
}
?>
