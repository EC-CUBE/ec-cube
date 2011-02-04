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
require_once(CLASS_REALDIR . "pages/LC_Page.php");

/**
 * 会員登録のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Entry.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Entry extends LC_Page {

    // {{{ properties

    /** フォームパラメータの配列 */
    var $objFormParam;


    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     * @return void
     */
    function init() {
        parent::init();
        $masterData         = new SC_DB_MasterData_Ex();
        $this->arrPref      = $masterData->getMasterData('mtb_pref');
        $this->arrJob       = $masterData->getMasterData("mtb_job");
        $this->arrReminder  = $masterData->getMasterData("mtb_reminder");

        // 生年月日選択肢の取得
        $objDate            = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
        $this->arrYear      = $objDate->getYear('', 1950, '');
        $this->arrMonth     = $objDate->getMonth(true);
        $this->arrDay       = $objDate->getDay(true);

        $this->httpCacheControl('nocache');

        // パラメータ管理クラス,パラメータ情報の初期化
        $this->objFormParam = new SC_FormParam();
        $this->lfInitParam();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {

        $this->objFormParam->addParam("お名前(姓)", 'name01', STEXT_LEN, "aKV", array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前(名)", 'name02', STEXT_LEN, "aKV", array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前(フリガナ・姓)", 'kana01', STEXT_LEN, "CKV", array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
        $this->objFormParam->addParam("お名前(フリガナ・名)", 'kana02', STEXT_LEN, "CKV", array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
        $this->objFormParam->addParam("パスワード", 'password', STEXT_LEN, "a", array("EXIST_CHECK", "SPTAB_CHECK" ,"ALNUM_CHECK"));
        $this->objFormParam->addParam("パスワード確認用の質問", "reminder", STEXT_LEN, "n", array("EXIST_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("パスワード確認用の質問の答え", "reminder_answer", STEXT_LEN, "aKV", array("EXIST_CHECK","SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("郵便番号1", "zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("郵便番号2", "zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("都道府県", 'pref', INT_LEN, "n", array("EXIST_CHECK","NUM_CHECK"));
        $this->objFormParam->addParam("住所1", "addr01", MTEXT_LEN, "aKV", array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("住所2", "addr02", MTEXT_LEN, "aKV", array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お電話番号1", 'tel01', TEL_ITEM_LEN, "n", array("EXIST_CHECK","SPTAB_CHECK" ));
        $this->objFormParam->addParam("お電話番号2", 'tel02', TEL_ITEM_LEN, "n", array("EXIST_CHECK","SPTAB_CHECK" ));
        $this->objFormParam->addParam("お電話番号3", 'tel03', TEL_ITEM_LEN, "n", array("EXIST_CHECK","SPTAB_CHECK" ));
        $this->objFormParam->addParam("性別", "sex", INT_LEN, "n", array("EXIST_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("職業", "job", INT_LEN, "n", array("NUM_CHECK"));
        $this->objFormParam->addParam("年", "year", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
        $this->objFormParam->addParam("月", "month", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
        $this->objFormParam->addParam("日", "day", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
        $this->objFormParam->addParam("メールマガジン", "mailmaga_flg", INT_LEN, "n", array("EXIST_CHECK", "NUM_CHECK"));

        if (SC_Display::detectDevice() !== DEVICE_TYPE_MOBILE){
            $this->objFormParam->addParam("FAX番号1", 'fax01', TEL_ITEM_LEN, "n", array("SPTAB_CHECK"));
            $this->objFormParam->addParam("FAX番号2", 'fax02', TEL_ITEM_LEN, "n", array("SPTAB_CHECK"));
            $this->objFormParam->addParam("FAX番号3", 'fax03', TEL_ITEM_LEN, "n", array("SPTAB_CHECK"));
            $this->objFormParam->addParam("パスワード(確認)", 'password02', STEXT_LEN, "a", array("EXIST_CHECK", "SPTAB_CHECK" ,"ALNUM_CHECK"), "", false);
            $this->objFormParam->addParam('メールアドレス', "email", MTEXT_LEN, "a", array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
            $this->objFormParam->addParam('メールアドレス(確認)', "email02", MTEXT_LEN, "a", array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK","SPTAB_CHECK" , "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"), "", false);
        } else {
            $this->objFormParam->addParam('メールアドレス', "email", MTEXT_LEN, "a", array("EXIST_CHECK", "EMAIL_CHECK", "NO_SPTAB" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK","MOBILE_EMAIL_CHECK"));
        }
    }

    /**
     * Page のプロセス
     * @return void
     */
    function action() {
        // PC時は規約ページからの遷移でなければエラー画面へ遷移する
        if ($this->lfCheckReferer($_POST, $_SERVER['HTTP_REFERER']) === false) {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
        }

        // mobile用（戻るボタンでの遷移かどうかを判定）
        if (!empty($_POST["return"])) {
            $_POST["mode"] = "return";
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!SC_Helper_Session_Ex::isValidToken()) {
                SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
            }
        }

        $this->objFormParam->setParam($_POST);    // POST値の取得
        $this->objFormParam->convParam();
        $this->objFormParam->toLower('email');
        $this->objFormParam->toLower('email02');
        $this->arrForm  = $this->objFormParam->getHashArray();

        switch ($this->getMode()) {
        case 'confirm':
            //-- 確認
            $this->arrErr = $this->lfErrorCheck($this->arrForm);
            // 入力エラーなし
            if(empty($this->arrErr)) {
                //パスワード表示
                $this->passlen      = SC_Utils_Ex::sfPassLen(strlen($this->arrForm['password']));

                $this->tpl_mainpage = 'entry/confirm.tpl';
                $this->tpl_title    = '会員登録(確認ページ)';
            }
            break;
        case 'complete':
            //-- 会員登録と完了画面
            $this->arrErr = $this->lfErrorCheck($this->arrForm);
            if(empty($this->arrErr)) {
                $uniqid             = $this->lfRegistData($this->arrForm, $this->objFormParam->getDbArray());

                $this->tpl_mainpage = 'entry/complete.tpl';
                $this->tpl_title    = '会員登録(完了ページ)';
                $this->lfSendMail($uniqid, $this->arrForm);

                // 仮会員が無効の場合
                if(CUSTOMER_CONFIRM_MAIL == false) {
                    // ログイン状態にする
                    $objCustomer = new SC_Customer();
                    $objCustomer->setLogin($this->arrForm["email"]);
                }

                // 完了ページに移動させる。
                SC_Response_Ex::sendRedirect('complete.php', array("ci" => SC_Helper_Customer_Ex::sfGetCustomerId($uniqid)));
            }
            break;
        default:
            break;
        }

        $this->transactionid = SC_Helper_Session_Ex::getToken();
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
    /**
     * lfRegistData
     *
     * 会員情報の登録
     *
     * @access public
     * @return void
     */
    function lfRegistData($arrForm, $arrResults) {
        $objQuery   = SC_Query::getSingletonInstance();
        //-- 登録実行
        $sqlval     = $this->lfMakeSqlVal($arrForm, $arrResults);
        $objQuery->begin();
        SC_Helper_Customer_Ex::sfEditCustomerData($sqlval);
        $objQuery->commit();

        return $sqlval["secret_key"];
    }


    /**
     * 会員登録に必要なSQLパラメータの配列を生成する.
     *
     * フォームに入力された情報を元に, SQLパラメータの配列を生成する.
     * モバイル端末の場合は, email を email_mobile にコピーし,
     * mobile_phone_id に携帯端末IDを格納する.
     *
     * @access protected
     * @param array $arrForm フォームパラメータの配列
     * @param array $arrResults 結果用の配列. SC_FormParam::getDbArray() の結果
     * @return array SQLパラメータの配列
     * @see SC_FormParam::getDbArray()
     */
    function lfMakeSqlVal($arrForm, $arrResults) {
        // 生年月日の作成
        $arrResults['birth']  = SC_Utils_Ex::sfGetTimestamp($arrForm['year'], $arrForm['month'], $arrForm['day']);

        // 仮会員 1 本会員 2
        $arrResults["status"] = (CUSTOMER_CONFIRM_MAIL == true) ? "1" : "2";

        /*
         * secret_keyは、テーブルで重複許可されていない場合があるので、
         * 本会員登録では利用されないがセットしておく。
         */
        $arrResults["secret_key"] = SC_Helper_Customer_Ex::sfGetUniqSecretKey();

        // 入会時ポイント
        $CONF = SC_Helper_DB_Ex::sfGetBasisData();
        $arrResults["point"] = $CONF["welcome_point"];

        if (SC_Display::detectDevice() == DEVICE_TYPE_MOBILE) {
            // 携帯メールアドレス
            $arrResults['email_mobile']     = $arrResults['email'];
            // PHONE_IDを取り出す
            $arrResults['mobile_phone_id']  =  SC_MobileUserAgent::getId();
        }
        return $arrResults;
    }


    /**
     * 会員登録完了メール送信する
     *
     * @access public
     * @return void
     */
    function lfSendMail($uniqid, $arrForm){
        $CONF           = SC_Helper_DB_Ex::sfGetBasisData();

        $objMailText    = new SC_SiteView();
        $objMailText->assign("CONF", $CONF);
        $objMailText->assign("name01", $arrForm['name01']);
        $objMailText->assign("name02", $arrForm['name02']);
        $objMailText->assign("uniqid", $uniqid);
        $objMailText->assignobj($this);

        $objHelperMail  = new SC_Helper_Mail_Ex();

        // 仮会員が有効の場合
        if(CUSTOMER_CONFIRM_MAIL == true) {
            $subject        = $objHelperMail->sfMakeSubject('会員登録のご確認');
            $toCustomerMail = $objMailText->fetch("mail_templates/customer_mail.tpl");
        } else {
            $subject        = $objHelperMail->sfMakeSubject('会員登録のご完了');
            $toCustomerMail = $objMailText->fetch("mail_templates/customer_regist_mail.tpl");
        }

        $objMail = new SC_SendMail();
        $objMail->setItem(
            ''                    // 宛先
            , $subject              // サブジェクト
            , $toCustomerMail       // 本文
            , $CONF["email03"]      // 配送元アドレス
            , $CONF["shop_name"]    // 配送元 名前
            , $CONF["email03"]      // reply_to
            , $CONF["email04"]      // return_path
            , $CONF["email04"]      // Errors_to
            , $CONF["email01"]      // Bcc
        );
        // 宛先の設定
        $objMail->setTo($arrForm["email"],
                        $arrForm["name01"] . $arrForm["name02"] ." 様");

        $objMail->sendMail();
    }

    /**
     * lfErrorCheck
     *
     * 入力エラーチェック
     *
     * @param mixed $array
     * @access public
     * @return void
     */
    function lfErrorCheck($arrForm) {

        // 入力データを渡す。
        $objErr = new SC_CheckError($arrForm);
        $objErr->arrErr = $this->objFormParam->checkError();

        $objErr->doFunc(array("お電話番号", "tel01", "tel02", "tel03"),array("TEL_CHECK"));
        $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_BIRTHDAY"));

        if (SC_Display::detectDevice() !== DEVICE_TYPE_MOBILE){
            $objErr->doFunc(array('パスワード', 'パスワード(確認)', "password", "password02") ,array("EQUAL_CHECK"));
            $objErr->doFunc(array('メールアドレス', 'メールアドレス(確認)', "email", "email02") ,array("EQUAL_CHECK"));
            $objErr->doFunc(array("FAX番号", "fax01", "fax02", "fax03") ,array("TEL_CHECK"));
        }

        // 現会員の判定 → 現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
        $objErr->doFunc(array("メールアドレス", "email"), array("CHECK_REGIST_CUSTOMER_EMAIL"));

        return $objErr->arrErr;
    }

    /**
     * kiyaku.php からの遷移の妥当性をチェックする
     *
     * 以下の内容をチェックし, 妥当であれば true を返す.
     * 1. 規約ページからの遷移かどうか
     * 2. PC及びスマートフォンかどうか
     * 3. $post に何も含まれていないかどうか
     *
     * @access protected
     * @param array $post $_POST のデータ
     * @param string $referer $_SERVER['HTTP_REFERER'] のデータ
     * @return boolean kiyaku.php からの妥当な遷移であれば true
     */
    function lfCheckReferer(&$post, $referer){

        if (SC_Display::detectDevice() !== DEVICE_TYPE_MOBILE
            && empty($post)
            && (preg_match('/kiyaku.php/', basename($referer)) === 0)) {
            return false;
            }
        return true;
    }
}
