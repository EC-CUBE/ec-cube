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
        $this->year = "";
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrJob = $masterData->getMasterData("mtb_job");
        $this->arrReminder = $masterData->getMasterData("mtb_reminder");
        
        // 生年月日選択肢の取得
        $objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
        $this->arrYear = $objDate->getYear('', 1950, '');
        $this->arrMonth = $objDate->getMonth(true);
        $this->arrDay = $objDate->getDay(true);
        
        $this->httpCacheControl('nocache');
        
        $this->isMobile = Net_UserAgent_Mobile::isMobile();
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
        
        if ($this->isMobile === false){
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
        $objView = new SC_SiteView();
        $objDb = new SC_Helper_DB_Ex();
        $CONF = $objDb->sfGetBasisData();
        $objQuery = new SC_Query();

        // PC時は規約ページからの遷移でなければエラー画面へ遷移する
        $this->lfCheckReferer();
        
        // mobile用（戻るボタンでの遷移かどうかを判定）
        if (!empty($_POST["return"])) {
            $_POST["mode"] = "return";
        }
        
        // パラメータ管理クラス,パラメータ情報の初期化
        $this->objFormParam = new SC_FormParam();
        $this->lfInitParam();
        $this->objFormParam->setParam($_POST);    // POST値の取得

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            
            //CSRF対策
            if (!SC_Helper_Session_Ex::isValidToken()) {
                SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
            }
            
            $this->objFormParam->convParam();
            $this->objFormParam->toLower('email');
            $this->objFormParam->toLower('email02');
            $this->arrForm = $this->objFormParam->getHashArray();
     
            //-- 確認
            if ($_POST["mode"] == "confirm") {

                $this->arrErr = $this->lfErrorCheck();
                
                // 入力エラーなし
                if(count($this->arrErr) == 0) {
                    
                    $this->list_data = $this->objFormParam->getHashArray();
                
                    //パスワード表示
                    $passlen = strlen($this->arrForm['password']);
                    $this->passlen = SC_Utils_Ex::lfPassLen($passlen);
    
                    $this->tpl_mainpage = 'entry/confirm.tpl';
                    $this->tpl_title = '会員登録(確認ページ)';
                }

            } elseif ($_POST["mode"] == "complete") {
                //-- 会員登録と完了画面
           
                // 会員情報の登録
                $this->CONF = $CONF;
                $this->uniqid = $this->lfRegistData();

                $this->tpl_mainpage = 'entry/complete.tpl';
                $this->tpl_title = '会員登録(完了ページ)';

                $this->lfSendMail();

                // 完了ページに移動させる。
                $customer_id = $objQuery->get("customer_id", "dtb_customer", "secret_key = ?", array($this->uniqid));
                SC_Response_Ex::sendRedirect('complete.php', array("ci" => $customer_id));
                exit;
                
            }
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

    // 会員情報の登録
    function lfRegistData() {
                
        $objQuery = new SC_Query();
        $arrRet = $this->objFormParam->getHashArray();
        $sqlval = $this->objFormParam->getDbArray();
        
        // 登録データの作成
        $sqlval['birth'] = SC_Utils_Ex::sfGetTimestamp($arrRet['year'], $arrRet['month'], $arrRet['day']);
        
        // パスワードの暗号化
        $sqlval["password"] = sha1($arrRegist["password"] . ":" . AUTH_MAGIC);

        // 重複しない会員登録キーを発行する。
        $count = 1;
        while ($count != 0) {
            $uniqid = SC_Utils_Ex::sfGetUniqRandomId("r");
            $count = $objQuery->count("dtb_customer", "secret_key = ?", array($uniqid));
        }
        
        // 仮会員登録の場合
        if(CUSTOMER_CONFIRM_MAIL == true) {
            $sqlval["status"] = "1";				// 仮会員
        } else {
            $sqlval["status"] = "2";				// 本会員
        }
        
        /*
          secret_keyは、テーブルで重複許可されていない場合があるので、
                          本会員登録では利用されないがセットしておく。
        */
        $sqlval["secret_key"] = $uniqid;		// 会員登録キー
        $sqlval["point"] = $this->CONF["welcome_point"]; // 入会時ポイント

        if ($this->isMobile === true) {
            // 携帯メールアドレス
            $sqlval['email_mobile'] = $sqlval['email'];
            //PHONE_IDを取り出す
            $sqlval['mobile_phone_id'] =  SC_MobileUserAgent::getId();
        }
       
        //-- 登録実行
        $objQuery->begin();
        SC_Helper_Customer_Ex::sfEditCustomerData($sqlval);
        $objQuery->commit();
        
        return $uniqid;
    }
    
    function lfSendMail(){
        // 完了メール送信
        $arrRet = $this->objFormParam->getHashArray();
        $this->name01 = $arrRet['name01'];
        $this->name02 = $arrRet['name02'];
        $objMailText = new SC_SiteView();
        $objMailText->assignobj($this);

        $objHelperMail = new SC_Helper_Mail_Ex();
        $objQuery = new SC_Query();
        $objCustomer = new SC_Customer();
        $CONF = SC_Helper_DB_Ex::sfGetBasisData();

        // 仮会員が有効の場合
        if(CUSTOMER_CONFIRM_MAIL == true) {
            $subject = $objHelperMail->sfMakeSubject('会員登録のご確認');
            $toCustomerMail = $objMailText->fetch("mail_templates/customer_mail.tpl");
        } else {
            $subject = $objHelperMail->sfMakeSubject('会員登録のご完了');
            $toCustomerMail = $objMailText->fetch("mail_templates/customer_regist_mail.tpl");
            // ログイン状態にする
            $objCustomer->setLogin($arrRet["email"]);
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
        $name = $arrRet["name01"] . $arrRet["name02"] ." 様";
        $objMail->setTo($arrRet["email"], $name);
        $objMail->sendMail();
    }


    //---- 入力エラーチェック
    function lfErrorCheck($array) {

        // 入力データを渡す。
        $arrRet = $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        $objErr->doFunc(array("お電話番号", "tel01", "tel02", "tel03"),array("TEL_CHECK"));
        $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_BIRTHDAY"));
        if ($this->isMobile === false){
            $objErr->doFunc(array('パスワード', 'パスワード(確認)', "password", "password02") ,array("EQUAL_CHECK"));
            $objErr->doFunc(array('メールアドレス', 'メールアドレス(確認)', "email", "email02") ,array("EQUAL_CHECK"));
            $objErr->doFunc(array("FAX番号", "fax01", "fax02", "fax03") ,array("TEL_CHECK"));
        }
        
        // 現会員の判定 → 現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
        $register_user_flg =  SC_Helper_Customer_Ex::lfCheckRegisterUserFromEmail($arrRet["email"]);
        switch($register_user_flg) {
            case 1:
                $objErr->arrErr["email"] .= "※ すでに会員登録で使用されているメールアドレスです。<br />";
                break;
            case 2:
                $objErr->arrErr["email"] .= "※ 退会から一定期間の間は、同じメールアドレスを使用することはできません。<br />";
                break;
            default:
                break;
        }
        return $objErr->arrErr;
    }
    
    function lfCheckReferer(){
    	/**
    	 * 規約ページからの遷移でなければエラー画面へ遷移する
    	 */ 
        if ($this->isMobile === FALSE
        	 && empty($_POST)
        	 && !preg_match('/kiyaku.php/', basename($_SERVER['HTTP_REFERER']))
        	) {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
        }
    }
}
?>
