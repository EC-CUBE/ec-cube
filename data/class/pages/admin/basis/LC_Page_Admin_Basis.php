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
 * 店舗基本情報 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/index.tpl';
        $this->tpl_subno = 'index';
        $this->tpl_mainno = 'basis';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrTAXRULE = $masterData->getMasterData('mtb_taxrule');
        $this->tpl_maintitle = t('c_Basic information_01');
        $this->tpl_subtitle = t('c_SHOP master_01');;

        //定休日用配列
        $this->arrRegularHoliday[0] = t('c_Sunday_01');
        $this->arrRegularHoliday[1] = t('c_Monday_01');
        $this->arrRegularHoliday[2] = t('c_Tuesday_01');
        $this->arrRegularHoliday[3] = t('c_Wednesday_01');
        $this->arrRegularHoliday[4] = t('c_Thursday_01');
        $this->arrRegularHoliday[5] = t('c_Friday_01');
        $this->arrRegularHoliday[6] = t('c_Saturday_01');
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

        if ($objDb->sfGetBasisExists()) {
            $this->tpl_mode = 'update';
        } else {
            $this->tpl_mode = 'insert';
        }

        if (!empty($_POST)) {

            $objFormParam = new SC_FormParam_Ex();
            $this->lfInitParam($objFormParam, $_POST);
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();

            $this->arrErr = $this->lfCheckError($objFormParam);
            $post = $objFormParam->getHashArray();

            $this->arrForm = $post;

            if (count($this->arrErr) == 0) {
                switch ($this->getMode()) {
                    // 既存編集
                    case 'update':
                        $this->lfUpdateData($this->arrForm);
                        break;
                    // 新規作成
                    case 'insert':
                        $this->lfInsertData($this->arrForm);
                        break;
                    default:
                        break;
                }
                $this->tpl_onload = "fnCheckLimit('downloadable_days', 'downloadable_days_unlimited', '" . DISABLED_RGB . "'); window.alert(\"" . t('c_SHOP master registration is complete._01') . "\");";
            }
            if (empty($this->arrForm['regular_holiday_ids'])) {
                $this->arrSel = array();
            } else {
                $this->arrSel = $this->arrForm['regular_holiday_ids'];
            }
        } else {
            $arrCol = $this->lfGetCol();
            $col    = SC_Utils_Ex::sfGetCommaList($arrCol);
            $arrRet = $objDb->sfGetBasisData(true, $col);
            $this->arrForm = $arrRet;

            $regular_holiday_ids = explode('|', $this->arrForm['regular_holiday_ids']);
            $this->arrForm['regular_holiday_ids'] = $regular_holiday_ids;
            $this->tpl_onload = "fnCheckLimit('downloadable_days', 'downloadable_days_unlimited', '" . DISABLED_RGB . "');";
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

    // 基本情報用のカラムを取り出す。
    function lfGetCol() {
        $arrCol = array(
            'company_name',
            'company_kana',
            'shop_name',
            'shop_kana',
            'shop_name_eng',
//            'zip01',
//            'zip02',
            'zipcode',
            'pref',
            'addr01',
            'addr02',
            'tel01',
            'tel02',
            'tel03',
            'fax01',
            'fax02',
            'fax03',
            'business_hour',
            'email01',
            'email02',
            'email03',
            'email04',
            'tax',
            'tax_rule',
            'free_rule',
            'good_traded',
            'message',
            'regular_holiday_ids',
            'latitude',
            'longitude',
            'downloadable_days',
            'downloadable_days_unlimited'
        );
        return $arrCol;
    }

    function lfUpdateData($array) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrCol = $this->lfGetCol();
        foreach ($arrCol as $val) {
            //配列の場合は、パイプ区切りの文字列に変換
            if (is_array($array[$val])) {
                $sqlval[$val] = implode('|', $array[$val]);
            } else {
                $sqlval[$val] = $array[$val];
            }
        }
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        // UPDATEの実行
        $ret = $objQuery->update('dtb_baseinfo', $sqlval);

        GC_Utils_Ex::gfPrintLog(t('c_UPDATE was executed for dtb_baseinfo._01'));
    }

    function lfInsertData($array) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrCol = $this->lfGetCol();
        foreach ($arrCol as $val) {
            $sqlval[$val] = $array[$val];
        }
        $sqlval['id'] = 1;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        // INSERTの実行
        $ret = $objQuery->insert('dtb_baseinfo', $sqlval);

        GC_Utils_Ex::gfPrintLog(t('c_INSERT was executed for dtb_baseinfo._01'));
    }

    function lfInitParam(&$objFormParam, $post) {
        $objFormParam->addParam(t('c_Company name_01'), 'company_name', STEXT_LEN, 'KVa',  array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Company name KANA_01'), 'company_kana', STEXT_LEN, 'KVC',  array('KANA_CHECK','MAX_LENGTH_CHECK'));

        $objFormParam->addParam(t('c_Store name_01'), 'shop_name', STEXT_LEN, 'KVa', array('EXIST_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Store name KANA_01'), 'shop_kana',  STEXT_LEN, 'KVC', array('KANA_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Store name (in English)_01'), 'shop_name_eng',MTEXT_LEN, 'a', array('GRAPH_CHECK','MAX_LENGTH_CHECK'));
        // 郵便番号チェック
//        $objFormParam->addParam(t('c_Postal code 1_01'), 'zip01', ZIP01_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK','NUM_COUNT_CHECK'));
//        $objFormParam->addParam(t('c_Postal code 2_01'), 'zip02', ZIP02_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK','NUM_COUNT_CHECK'));
        $objFormParam->addParam(t('c_Postal code_01'), 'zipcode', ZIPCODE_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));

        // 所在地チェック
        $objFormParam->addParam(t('c_Prefecture_01'), 'pref', '', 'n');
        $objFormParam->addParam(t('c_Location 1_01'), 'addr01', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Location 2_01'), 'addr02', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        // メールチェック
        $objFormParam->addParam(t('c_Product order receipt e-mail address_01'), 'email01', null, 'a', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam(t('c_E-mail address for receiving inquiries_01'), 'email02', null, 'a', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam(t('c_E-mail address of sender_01'), 'email03', null, 'a', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam(t('c_E-mail address for receiving sending errors_01'), 'email04', null, 'a', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));

        // 電話番号
        $objFormParam->addParam(t('c_Telephone number 1_01'), 'tel01', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 2_01'), 'tel02', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 3_01'), 'tel03', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

        // FAX番号
        $objFormParam->addParam(t('c_Fax number 1_01'), 'fax01', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Fax number 2_01'), 'fax02', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Fax number 3_01'), 'fax03', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

        // その他
        $objFormParam->addParam(t('c_Consumption sales tax rate_01'), 'tax', PERCENTAGE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Taxation rules_01'), 'tax_rule', PERCENTAGE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Conditions for free shipping_01'), 'free_rule', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Store business hours_01'), 'business_hour', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));

        $objFormParam->addParam(t('c_Available products_01'), 'good_traded', LLTEXT_LEN, '', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Message_01'), 'message', LLTEXT_LEN, '', array('MAX_LENGTH_CHECK'));

        if (!isset($post['downloadable_days_unlimited']) && $post['downloadable_days_unlimited'] != '1') {
            $objFormParam->addParam(t('c_Number of days during which download is possible_01'), 'downloadable_days', DOWNLOAD_DAYS_LEN, 'n', array('EXIST_CHECK', 'ZERO_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        } else {
            $objFormParam->addParam(t('c_Unlimited downloads_01'), 'downloadable_days_unlimited', array('EXIST_CHECK'));
        }
        $objFormParam->addParam(t('c_Latitude_01'), 'latitude', STEXT_LEN, '',  array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Longitude_01'), 'longitude', STEXT_LEN, '',  array('MAX_LENGTH_CHECK'));

        $objFormParam->addParam(t('c_Regular holiday_01'), 'regular_holiday_ids', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
    }

    // 入力エラーチェック
    function lfCheckError(&$objFormParam) {
        $arrErr = $objFormParam->checkError();
        $post = $objFormParam->getHashArray();

        $objErr = new SC_CheckError_Ex($post);
//        $objErr->doFunc(array(t('c_Postal code_01'), 'zip01', 'zip02'), array('ALL_EXIST_CHECK'));

        // 電話番号チェック
        $objErr->doFunc(array(t('c_TEL_01'), 'tel01', 'tel02', 'tel03'), array('TEL_CHECK'));
        $objErr->doFunc(array(t('c_Fax number_01'), 'fax01', 'fax02', 'fax03'), array('TEL_CHECK'));

        $objErr->doFunc(array(t('c_Latitude_01'), 'latitude', STEXT_LEN), array('NUM_POINT_CHECK', 'MAX_LENGTH_CHECK'));
        $objErr->doFunc(array(t('c_Longitude_01'), 'longitude', STEXT_LEN), array('NUM_POINT_CHECK', 'MAX_LENGTH_CHECK'));

        return array_merge((array)$arrErr, (array)$objErr->arrErr);
    }
}
