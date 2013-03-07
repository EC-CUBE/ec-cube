<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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
class LC_Page_Admin_Basis extends LC_Page_Admin_Ex 
{

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init()
    {
        parent::init();
        $this->tpl_mainpage = 'basis/index.tpl';
        $this->tpl_subno = 'index';
        $this->tpl_mainno = 'basis';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrTAXRULE = $masterData->getMasterData('mtb_taxrule');
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = 'SHOPマスター';

        //定休日用配列
        $this->arrRegularHoliday[0] = '日';
        $this->arrRegularHoliday[1] = '月';
        $this->arrRegularHoliday[2] = '火';
        $this->arrRegularHoliday[3] = '水';
        $this->arrRegularHoliday[4] = '木';
        $this->arrRegularHoliday[5] = '金';
        $this->arrRegularHoliday[6] = '土';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action()
    {
        $objDb = new SC_Helper_DB_Ex();
        $objFormParam = new SC_FormParam_Ex();


        $this->lfInitParam($objFormParam, $_POST);
        $this->tpl_onload = "fnCheckLimit('downloadable_days', 'downloadable_days_unlimited', '" . DISABLED_RGB . "');";

        if ($this->getMode() === 'confirm') {
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();

            $this->arrErr = $this->lfCheckError($objFormParam);

            if (!empty($this->arrErr)) {
                $this->arrForm = $objFormParam->getHashArray();
                return;
            }

            $arrData = $objFormParam->getDbArray();
            SC_Helper_DB_Ex::registerBasisData($arrData);

            // キャッシュファイル更新
            $objDb->sfCreateBasisDataCache();
            $this->tpl_onload .= "window.alert('SHOPマスターの登録が完了しました。');";
        }

        $arrRet = $objDb->sfGetBasisData(true);
        $objFormParam->setParam($arrRet);
        $this->arrForm = $objFormParam->getHashArray();
        $this->arrForm['regular_holiday_ids'] = explode('|', $this->arrForm['regular_holiday_ids']);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy()
    {
        parent::destroy();
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.4
     */
    function lfUpdateData($arrData)
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);
        SC_Helper_DB_Ex::registerBasisData($arrData);
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.4
     */
    function lfInsertData($arrData)
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);
        SC_Helper_DB_Ex::registerBasisData($arrData);
    }

    function lfInitParam(&$objFormParam, $post)
    {
        $objFormParam->addParam('会社名', 'company_name', STEXT_LEN, 'KVa',  array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('会社名(フリガナ)', 'company_kana', STEXT_LEN, 'KVC',  array('KANA_CHECK','MAX_LENGTH_CHECK'));

        $objFormParam->addParam('店名', 'shop_name', STEXT_LEN, 'KVa', array('EXIST_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam('店名(フリガナ)', 'shop_kana',  STEXT_LEN, 'KVC', array('KANA_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam('店名(英語表記)', 'shop_name_eng',MTEXT_LEN, 'a', array('GRAPH_CHECK','MAX_LENGTH_CHECK'));
        // 郵便番号チェック
        $objFormParam->addParam('郵便番号1', 'zip01', ZIP01_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK','NUM_COUNT_CHECK'));
        $objFormParam->addParam('郵便番号2', 'zip02', ZIP02_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK','NUM_COUNT_CHECK'));
        // 所在地チェック
        $objFormParam->addParam('都道府県', 'pref', '', 'n', array('EXIST_CHECK'));
        $objFormParam->addParam('所在地1', 'addr01', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('所在地2', 'addr02', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        // メールチェック
        $objFormParam->addParam('商品注文受付メールアドレス', 'email01', null, 'a', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam('問い合わせ受付メールアドレス', 'email02', null, 'a', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam('メール送信元メールアドレス', 'email03', null, 'a', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam('送信エラー受付メールアドレス', 'email04', null, 'a', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));

        // 電話番号
        $objFormParam->addParam('電話番号1', 'tel01', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('電話番号2', 'tel02', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('電話番号3', 'tel03', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

        // FAX番号
        $objFormParam->addParam('FAX番号1', 'fax01', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('FAX番号2', 'fax02', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('FAX番号3', 'fax03', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

        // その他
        $objFormParam->addParam('消費税率', 'tax', PERCENTAGE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('課税規則 ', 'tax_rule', PERCENTAGE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('送料無料条件', 'free_rule', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('店舗営業時間', 'business_hour', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));

        $objFormParam->addParam('取扱商品', 'good_traded', LLTEXT_LEN, '', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('メッセージ', 'message', LLTEXT_LEN, '', array('MAX_LENGTH_CHECK'));

        if (!isset($post['downloadable_days_unlimited']) && $post['downloadable_days_unlimited'] != '1') {
            $objFormParam->addParam('ダウンロード可能日数', 'downloadable_days', DOWNLOAD_DAYS_LEN, 'n', array('EXIST_CHECK', 'ZERO_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        } else {
            $objFormParam->addParam('ダウンロード無制限', 'downloadable_days_unlimited', array('EXIST_CHECK'));
        }
        $objFormParam->addParam('緯度', 'latitude', STEXT_LEN, '',  array('MAX_LENGTH_CHECK', 'NUM_POINT_CHECK'));
        $objFormParam->addParam('軽度', 'longitude', STEXT_LEN, '',  array('MAX_LENGTH_CHECK', 'NUM_POINT_CHECK'));

        $objFormParam->addParam('定休日', 'regular_holiday_ids', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
    }

    // 入力エラーチェック
    function lfCheckError(&$objFormParam)
    {
        $arrErr = $objFormParam->checkError();
        $post = $objFormParam->getHashArray();

        $objErr = new SC_CheckError_Ex($post);
        $objErr->doFunc(array('郵便番号', 'zip01', 'zip02'), array('ALL_EXIST_CHECK'));

        // 電話番号チェック
        $objErr->doFunc(array('TEL', 'tel01', 'tel02', 'tel03'), array('TEL_CHECK'));
        $objErr->doFunc(array('FAX', 'fax01', 'fax02', 'fax03'), array('TEL_CHECK'));

        return array_merge((array)$arrErr, (array)$objErr->arrErr);
    }
}
