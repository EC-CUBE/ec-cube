<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * お問い合わせ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Contact extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
            $this->tpl_title = t('LC_Page_Contact_001');
            //$this->tpl_title = 'お問い合わせ';
        } else {
            $this->tpl_title = t('LC_Page_Contact_002');
            //$this->tpl_title = 'お問い合わせ(入力ページ)';
        }
        $this->tpl_page_category = 'contact';
        $this->httpCacheControl('nocache');

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');

        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
            // @deprecated EC-CUBE 2.11 テンプレート互換用
            $this->CONF = SC_Helper_DB_Ex::sfGetBasisData();
        }
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
     * Page のアクション.
     *
     * @return void
     */
    function action() {

        $objDb = new SC_Helper_DB_Ex();
        $objFormParam = new SC_FormParam_Ex();

        $this->arrData = isset($_SESSION['customer']) ? $_SESSION['customer'] : '';

        switch ($this->getMode()) {
            case 'confirm':
                // エラーチェック
                $this->lfInitParam($objFormParam);
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $objFormParam->toLower('email');
                $objFormParam->toLower('email02');
                $this->arrErr = $this->lfCheckError($objFormParam);
                // 入力値の取得
                $this->arrForm = $objFormParam->getFormParamList();

                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    // エラー無しで完了画面
                    $this->tpl_mainpage = 'contact/confirm.tpl';
                    //$this->tpl_title = 'お問い合わせ(確認ページ)';
                    $this->tpl_title = t('LC_Page_Contact_003');
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
                    SC_Response_Ex::actionExit();
                } else {
                    SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
                    SC_Response_Ex::actionExit();
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
     * お問い合わせ入力時のパラメーター情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {

        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_LASTNAME'), 'name01', STEXT_LEN, 'KVa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_FIRSTNAME'), 'name02', STEXT_LEN, 'KVa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_LASTKANA'), 'kana01', STEXT_LEN, 'KVCa', array('SPTAB_CHECK','MAX_LENGTH_CHECK', 'KANA_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_FIRSTKANA'), 'kana02', STEXT_LEN, 'KVCa', array('SPTAB_CHECK','MAX_LENGTH_CHECK', 'KANA_CHECK'));
//        $objFormParam->addParam(t('PARAM_LABEL_ZIP01'), 'zip01', ZIP01_LEN, 'n',array('SPTAB_CHECK' ,'NUM_CHECK', 'NUM_COUNT_CHECK'));
//        $objFormParam->addParam(t('PARAM_LABEL_ZIP02'), 'zip02', ZIP02_LEN, 'n',array('SPTAB_CHECK' ,'NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_ZIP'), 'zipcode', ZIPCODE_LEN, 'n',array('SPTAB_CHECK' ,'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_PREF'), 'pref', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_ADDR1'), 'addr01', MTEXT_LEN, 'KVa', array('SPTAB_CHECK' ,'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_ADDR2'), 'addr02', MTEXT_LEN, 'KVa', array('SPTAB_CHECK' ,'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CONTACT'), 'contents', MLTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_EMAIL'), 'email', null, 'KVa',array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_EMAIL_CONFIRM'), 'email02', null, 'KVa',array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_TEL1'), 'tel01', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_TEL2'), 'tel02', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_TEL3'), 'tel03', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * 入力内容のチェックを行なう.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array 入力チェック結果の配列
     */
    function lfCheckError(&$objFormParam) {
        // 入力データを渡す。
        $arrForm =  $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->arrErr = $objFormParam->checkError();
        $objErr->doFunc(array(t('PARAM_LABEL_EMAIL'), t('PARAM_LABEL_EMAIL_CONFIRM'), 'email', 'email02') ,array('EQUAL_CHECK'));
        return $objErr->arrErr;
    }

    /**
     * メールの送信を行う。
     *
     * @return void
     */
    function lfSendMail(&$objPage) {
        $CONF = SC_Helper_DB_Ex::sfGetBasisData();
        $objPage->tpl_shopname = $CONF['shop_name'];
        $objPage->tpl_infoemail = $CONF['email02'];
        $helperMail = new SC_Helper_Mail_Ex();
        $helperMail->setPage($this);
        $helperMail->sfSendTemplateMail(
            $objPage->arrForm['email']['value'],            // to
            t('LC_Page_Contact_004', array('T_ARG1' => $objPage->arrForm['name01']['value'])), // to_name
            5,                                              // template_id
            $objPage,                                       // objPage
            $CONF['email03'],                               // from_address
            $CONF['shop_name'],                             // from_name
            $CONF['email02'],                               // reply_to
            $CONF['email02']                                // bcc
        );
    }
}
