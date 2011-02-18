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
 * お問い合わせ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Contact extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
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
        $objFormParam = new SC_FormParam();

        $this->arrData = isset($_SESSION['customer']) ? $_SESSION['customer'] : "";

        switch ($this->getMode()) {
        case 'confirm':
            // エラーチェック
            $this->lfInitParam($objFormParam);
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            $objFormParam->toLower('email');
            $objFormParam->toLower('email02');
            $this->arrErr = $objFormParam->checkError();
            // 入力値の取得
            $this->arrForm = $objFormParam->getFormParamList();

            if (SC_Utils_Ex::isBlank($this->arrErr)) {
                // エラー無しで完了画面
                $this->tpl_mainpage = 'contact/confirm.tpl';
                $this->tpl_title = 'お問い合わせ(確認ページ)';
            }

            break;

        case 'return':
            $this->lfInitParam($objFormParam);
            $objFormParam->setParam($_POST);
            $this->arrForm = $objFormParam->getFormParamList();

            break;

        case 'complete':
            $this->lfInitParam($objFormParam);
            $objFormParam->setParam($_POST);
            $this->arrErr = $objFormParam->checkError();
            $this->arrForm = $objFormParam->getFormParamList();
            if (SC_Utils_Ex::isBlank($this->arrErr)) {
                $this->lfSendMail($this);
                // 完了ページへ移動する
                SC_Response_Ex::sendRedirect('complete.php');
                exit;
            } else {
                SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
                exit;
            }
            break;

        default:
            break;
        }
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
     * お問い合わせ入力時のパラメータ情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {

        $objFormParam->addParam("お名前(姓)", 'name01', STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前(名)", 'name02', STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前(フリガナ・姓)", 'kana01', STEXT_LEN, "KVCa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objFormParam->addParam("お名前(フリガナ・名)", 'kana02', STEXT_LEN, "KVCa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objFormParam->addParam("郵便番号1", "zip01", ZIP01_LEN, "n",array("SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $objFormParam->addParam("郵便番号2", "zip02", ZIP02_LEN, "n",array("SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $objFormParam->addParam("都道府県", "pref", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("住所1", "addr01", MTEXT_LEN, "KVa", array("SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objFormParam->addParam("住所2", "addr02", MTEXT_LEN, "KVa", array("SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お問い合わせ内容", "contents", MLTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam('メールアドレス', "email", MTEXT_LEN, "KVa",array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam('メールアドレス(確認)', "email02", MTEXT_LEN, "KVa",array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お電話番号1", 'tel01', TEL_ITEM_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お電話番号2", 'tel02', TEL_ITEM_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お電話番号3", 'tel03', TEL_ITEM_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
    }

    /**
     * メールの送信を行う。
     *
     * @return void
     */
    function lfSendMail(&$objPage){
        $CONF = SC_Helper_DB_Ex::sfGetBasisData();
        $objPage->tpl_shopname = $CONF['shop_name'];
        $objPage->tpl_infoemail = $CONF['email02'];
        $fromMail_name = $objPage->arrForm['name01']['value'] ." 様";
        $fromMail_address = $objPage->arrForm['email']['value'];
        $helperMail = new SC_Helper_Mail_Ex();
        $helperMail->sfSendTemplateMail($CONF["email02"], $CONF["shop_name"], "5", $objPage, $fromMail_address, $fromMail_name, $fromMail_address);
        $helperMail->sfSendTemplateMail($objPage->arrForm['email']['value'], $objPage->arrForm['name01']['value'] ." 様", "5", $objPage, $CONF["email03"], $CONF["shop_name"], $CONF["email02"]);
    }
}
?>
