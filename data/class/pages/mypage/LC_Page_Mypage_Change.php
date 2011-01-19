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
        $this->tpl_navi = TEMPLATE_REALDIR . 'mypage/navi.tpl';
        $this->tpl_mainno = 'mypage';
        $this->tpl_mypageno = 'change';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrReminder = $masterData->getMasterData("mtb_reminder");
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrJob = $masterData->getMasterData("mtb_job");
        $this->arrMAILMAGATYPE = $masterData->getMasterData("mtb_mail_magazine_type");
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->httpCacheControl('nocache');
        
        // 生年月日選択肢の取得
        $objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
        $this->arrYear = $objDate->getYear('', 1950, '');
        $this->arrMonth = $objDate->getMonth(true);
        $this->arrDay = $objDate->getDay(true);
        
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
    
    /**
     * Page のプロセス
     * @return void
     */
    function action() {
        $objDb = new SC_Helper_DB_Ex();
        $CONF = $objDb->sfGetBasisData();
        
        $objQuery = new SC_Query();
        $objCustomer = new SC_Customer();
        
        // ログインチェック
        if (!$objCustomer->isLoginSuccess(true)){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }else {
            //マイページトップ顧客情報表示用
            $this->CustomerName1 = $objCustomer->getvalue('name01');
            $this->CustomerName2 = $objCustomer->getvalue('name02');
            $this->CustomerPoint = $objCustomer->getvalue('point');
        }
        
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
            /*
            if (!SC_Helper_Session_Ex::isValidToken()) {
                SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
            }
            */
                        
            $this->objFormParam->convParam();
            $this->objFormParam->toLower('email');
            $this->objFormParam->toLower('email02');
            $this->objFormParam->toLower('email_mobile');
            $this->objFormParam->toLower('email_mobile02');
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
    
                    $this->tpl_mainpage = 'mypage/change_confirm.tpl';
                    $this->tpl_title = '会員登録(確認ページ)';
                }

            } elseif ($_POST["mode"] == "complete") {
                //-- 会員登録と完了画面
           
                // 会員情報の登録
                $this->CONF = $CONF;
                $this->lfRegistData();
                
                // 完了ページに移動させる。
                SC_Response_Ex::sendRedirect('change_complete.php');
                exit;
                
            }
        } else {
            $this->arrForm = $this->lfGetCustomerData();
            $this->arrForm['password'] = DEFAULT_PASSWORD;
            $this->arrForm['password02'] = DEFAULT_PASSWORD;
            $this->arrForm['reminder_answer'] = DEFAULT_PASSWORD;
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
            $this->objFormParam->addParam('携帯メールアドレス', "email_mobile", MTEXT_LEN, "a", array("NO_SPTAB", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
            $this->objFormParam->addParam('携帯メールアドレス(確認)', "email_mobile02", MTEXT_LEN, "a", array("NO_SPTAB", "EMAIL_CHECK","SPTAB_CHECK" , "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"), "", false);
        } else {
            $this->objFormParam->addParam('メールアドレス', "email", MTEXT_LEN, "a", array("EXIST_CHECK", "EMAIL_CHECK", "NO_SPTAB" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK","MOBILE_EMAIL_CHECK"));
        }
    }

    //---- 入力エラーチェック
    function lfErrorCheck($array) {

        // 入力データを渡す。
        $arrRet = $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();
        if(isset($objErr->arrErr['password']) and $arrRet['password'] == DEFAULT_PASSWORD) {
            unset($objErr->arrErr['password']);
            unset($objErr->arrErr['password02']);
        }
        if(isset($objErr->arrErr['reminder_answer']) and $arrRet['reminder_answer'] == DEFAULT_PASSWORD) {
            unset($objErr->arrErr['reminder_answer']);
        }
                        
        $objErr->doFunc(array("お電話番号", "tel01", "tel02", "tel03"),array("TEL_CHECK"));
        $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_BIRTHDAY"));
        if ($this->isMobile === false){
            if( $arrRet['password'] != DEFAULT_PASSWORD ) {
                $objErr->doFunc(array('パスワード', 'パスワード(確認)', "password", "password02") ,array("EQUAL_CHECK"));
            }
            $objErr->doFunc(array('メールアドレス', 'メールアドレス(確認)', "email", "email02") ,array("EQUAL_CHECK"));
            $objErr->doFunc(array("FAX番号", "fax01", "fax02", "fax03") ,array("TEL_CHECK"));
        }
                
        // 現会員の判定 → 現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
        $register_user_flg = SC_Helper_Customer_Ex::lfCheckRegisterUserFromEmail($arrRet["email"]);
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
    
    function lfRegistData() {
                
        $objQuery = new SC_Query();
        $objCustomer = new SC_Customer();
        
        $arrRet = $this->objFormParam->getHashArray();
        
        // 登録データの作成
        $sqlval = $this->objFormParam->getDbArray();
        $sqlval['birth'] = SC_Utils_Ex::sfGetTimestamp($arrRet['year'], $arrRet['month'], $arrRet['day']);
                       
        $objQuery->begin();
        SC_Helper_Customer_Ex::sfEditCustomerData($sqlval, $objCustomer->getValue('customer_id'));        
        $objQuery->commit();
    }
    
    /**
     * 顧客情報の取得
     *
     * @return array 顧客情報
     */
    function lfGetCustomerData(){
        $objQuery = new SC_Query();
        $objCustomer = new SC_Customer();
        
        // 顧客情報DB取得
        $ret = $objQuery->select("*","dtb_customer","customer_id=?", array($objCustomer->getValue('customer_id')));
        $arrForm = $ret[0];

        // 確認項目に複製
        $arrForm['email02'] = $arrForm['email'];
        $arrForm['email_mobile02'] = $arrForm['email_mobile'];

        // 誕生日を年月日に分ける
        if (isset($arrForm['birth'])){
            $birth = split(" ", $arrForm["birth"]);
            list($arrForm['year'], $arrForm['month'], $arrForm['day']) = split("-",$birth[0]);
        }
        return $arrForm;
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
    
}
?>
