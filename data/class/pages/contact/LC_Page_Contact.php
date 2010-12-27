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

/**
 * お問い合わせ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Contact.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Contact extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * フォーム値変換用カラム
     *
     *
     */
    var $arrConvertColumn = array(
        array("column" => "name01",    "convert" => "aKV"),
        array("column" => "name02",    "convert" => "aKV"),
        array("column" => "kana01",    "convert" => "CKV"),
        array("column" => "kana02",    "convert" => "CKV"),
        array("column" => "zip01",     "convert" => "n"),
        array("column" => "zip02",     "convert" => "n"),
        array("column" => "pref",      "convert" => "n"),
        array("column" => "addr01",    "convert" => "aKV"),
        array("column" => "addr02",    "convert" => "aKV"),
        array("column" => "email",     "convert" => "a"),
        array("column" => "email02",   "convert" => "a"),
        array("column" => "tel01",     "convert" => "n"),
        array("column" => "tel02",     "convert" => "n"),
        array("column" => "tel03",     "convert" => "n"),
        array("column" => "contents",  "convert" => "aKV"),
    );

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'contact/index.tpl';
        $this->tpl_title = 'お問い合わせ(入力ページ)';
        $this->tpl_page_category = 'contact';
        $this->httpCacheControl('nocache');

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
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
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objDb = new SC_Helper_DB_Ex();
        $CONF = $objDb->sfGetBasisData();			// 店舗基本情報

        $objCustomer = new SC_Customer();

        $this->arrData = isset($_SESSION['customer']) ? $_SESSION['customer'] : "";

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch ($_POST['mode']) {
            case 'confirm':
              $this->lfContactConfirm();
              break;

            case 'return':
              $this->lfContactReturn();
              break;

            case 'complete':
              $this->lfContactComplete();
              break;

            default:
              break;
        }
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->init();
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        $this->mobileAction();
        $this->sendResponse();
    }

    /**
     * Page のアクション(モバイル).
     *
     * @return void
     */
    function mobileAction() {
        $objDb = new SC_Helper_DB_Ex();
        $this->CONF = $objDb->sfGetBasisData();			// 店舗基本情報
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
     * 確認画面
     *
     * @return void
     */
    function lfContactConfirm() {
        // エラーチェック
        $arrForm = $_POST;
        $arrForm['email'] = strtolower($_POST['email']);
        $this->arrForm = $this->lfConvertParam($arrForm, $this->arrConvertColumn);
        $this->arrErr = $this->lfErrorCheck($this->arrForm);
        if ( ! $this->arrErr ){
            // エラー無しで完了画面
            $this->tpl_mainpage = 'contact/confirm.tpl';
            $this->tpl_title = 'お問い合わせ(確認ページ)';
        }
    }

    /**
     * 前に戻る
     *
     * @return void
     */
    function lfContactReturn() {
        $this->arrForm = $_POST;
    }

    /**
     * 完了ページへ
     *
     * @return void
     */
    function lfContactComplete() {
        $arrForm = $_POST;
        $arrForm['email']   = isset($_POST['email']) ? strtolower($_POST['email']) : '';
        $arrForm['email02'] = isset($_POST['email02']) ? strtolower($_POST['email02']) : '';
        $this->arrForm = $this->lfConvertParam($arrForm, $this->arrConvertColumn);
        $this->arrErr = $this->lfErrorCheck($this->arrForm);
        if(!$this->arrErr) {
            $this->lfSendMail($this);
            // 完了ページへ移動する
            $this->objDisplay->redirect($this->getLocation("./complete.php", array(), true));
            exit;
        } else {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }
    }

    //エラーチェック処理部
    function lfErrorCheck($array) {
        $objErr = new SC_CheckError($array);
        $objErr->doFunc(array("お名前(姓)", 'name01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前(名)", 'name02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前(フリガナ・姓)", 'kana01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("お名前(フリガナ・名)", 'kana02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("住所1", "addr01", MTEXT_LEN), array("SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("住所2", "addr02", MTEXT_LEN), array("SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お問い合わせ内容", "contents", MLTEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メールアドレス(確認)', "email02", MTEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メールアドレス', 'メールアドレス(確認)', "email", "email02") ,array("EQUAL_CHECK"));
        $objErr->doFunc(array("お電話番号1", 'tel01', TEL_ITEM_LEN), array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お電話番号2", 'tel02', TEL_ITEM_LEN), array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お電話番号3", 'tel03', TEL_ITEM_LEN), array("NUM_CHECK", "MAX_LENGTH_CHECK"));

        if (REVIEW_ALLOW_URL == false) {
            // URLの入力を禁止
            $masterData = new SC_DB_MasterData_Ex();
            $objErr->doFunc(array("URL", "contents", $masterData->getMasterData("mtb_review_deny_url")), array("PROHIBITED_STR_CHECK"));
        }

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

    function lfSendMail(&$objPage){
        $objDb = new SC_Helper_DB_Ex();
        $CONF = $objDb->sfGetBasisData();			// 店舗基本情報
        $objQuery = new SC_Query();
        $objSiteInfo = $this->objView->objSiteInfo;
        $arrInfo = $objSiteInfo->data;
        $objPage->tpl_shopname = $arrInfo['shop_name'];
        $objPage->tpl_infoemail = $arrInfo['email02'];

        $fromMail_name = $objPage->arrForm['name01'] ." 様";
        $fromMail_address = $objPage->arrForm['email'];

        $helperMail = new SC_Helper_Mail_Ex();
        $helperMail->sfSendTemplateMail($CONF["email02"], $CONF["shop_name"], "5", $objPage, $fromMail_address, $fromMail_name, $fromMail_address);
        $helperMail->sfSendTemplateMail($objPage->arrForm['email'], $objPage->arrForm['name01'] ." 様", "5", $objPage, $CONF["email03"], $CONF["shop_name"], $CONF["email02"]);
    }
}
?>
