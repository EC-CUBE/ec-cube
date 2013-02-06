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
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 特定商取引法 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Tradelaw extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/tradelaw.tpl';
        $this->tpl_subno = 'tradelaw';
        $this->tpl_mainno = 'basis';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrTAXRULE = $masterData->getMasterData('mtb_taxrule');
        $this->tpl_maintitle = t('c_Basic information_01');
        $this->tpl_subtitle = t('c_Transaction rules_01');
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

        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);

        if ($objDb->sfGetBasisExists()) {
            $this->tpl_mode = 'update';
        } else {
            $this->tpl_mode = 'insert';
        }

        if (!empty($_POST)) {
            // 入力値の変換
            $objFormParam->convParam();
            $this->arrErr = $this->lfCheckError($objFormParam);

            if (count($this->arrErr) == 0) {
                switch ($this->getMode()) {
                    case 'update':
                        $this->lfUpdateData($objFormParam->getHashArray()); // 既存編集
                        break;
                    case 'insert':
                        $this->lfInsertData($objFormParam->getHashArray()); // 新規作成
                        break;
                    default:
                        break;
                }
                // 再表示
                $this->tpl_onload = "window.alert('" . t('c_Registration of the Act on Specified Commercial Transactions is complete._01') . "');";
            }
        } else {
            $arrCol = $objFormParam->getKeyList(); // キー名一覧を取得
            $col    = SC_Utils_Ex::sfGetCommaList($arrCol);
            $arrRet = $objDb->sfGetBasisData(true, $col);
            $objFormParam->setParam($arrRet);
        }
        $this->arrForm = $objFormParam->getFormParamList();

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* パラメーター情報の初期化 */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam(t('c_Distributor_01'), 'law_company', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Operation director_01'), 'law_manager', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
//        $objFormParam->addParam(t('c_Postal code 1_01'), 'law_zip01', ZIP01_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));
//        $objFormParam->addParam(t('c_Postal code 2_01'), 'law_zip02', ZIP02_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam(t('c_Postal code_01'), 'law_zipcode', ZIPCODE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Prefecture_01'), 'law_pref', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('c_Location 1_01'), 'law_addr01', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Location 2_01'), 'law_addr02', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 1_01'), 'law_tel01', TEL_ITEM_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 2_01'), 'law_tel02', TEL_ITEM_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 3_01'), 'law_tel03', TEL_ITEM_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Fax number 1_01'), 'law_fax01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Fax number 2_01'), 'law_fax02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Fax number 3_01'), 'law_fax03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_E-mail address_01'), 'law_email', null, 'KVa', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam(t('c_URL_01'), 'law_url', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'URL_CHECK'));
        $objFormParam->addParam(t('c_Necessary fees_01'), 'law_term01', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Order method_01'), 'law_term02', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Payment method_01'), 'law_term03', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Payment deadline_01'), 'law_term04', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Delivery period_01'), 'law_term05', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Returns and exchanges_01'), 'law_term06', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
    }

    function lfUpdateData($sqlval) {
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // UPDATEの実行
        $ret = $objQuery->update('dtb_baseinfo', $sqlval);
    }

    function lfInsertData($sqlval) {
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // INSERTの実行
        $ret = $objQuery->insert('dtb_baseinfo', $sqlval);
    }

    /* 入力内容のチェック */
    function lfCheckError(&$objFormParam) {
        // 入力データを渡す。
        $arrRet =  $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrRet);
        $objErr->arrErr = $objFormParam->checkError();

        // 電話番号チェック
        $objErr->doFunc(array(t('c_TEL_01'), 'law_tel01', 'law_tel02', 'law_tel03'), array('TEL_CHECK'));
        $objErr->doFunc(array(t('c_FAX_01'), 'law_fax01', 'law_fax02', 'law_fax03'), array('TEL_CHECK'));
//        $objErr->doFunc(array(t('c_Postal code_01'), 'law_zip01', 'law_zip02'), array('ALL_EXIST_CHECK'));

        return $objErr->arrErr;
    }
}
