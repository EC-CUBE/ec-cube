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
 * CSV項目設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Contents_CSV extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'contents/csv.tpl';
        $this->tpl_subno = 'csv';
        $this->tpl_mainno = 'contents';
        $this->tpl_maintitle = 'コンテンツ管理';
        $this->tpl_subtitle = 'CSV出力設定';

        $objCSV = new SC_Helper_CSV_Ex();
        $this->arrSubnavi = $objCSV->arrSubnavi; // 別名
        $this->tpl_subno_csv = $objCSV->arrSubnavi[1]; //デフォルト
        $this->arrSubnaviName = $objCSV->arrSubnaviName; // 表示名
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

        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメーター設定
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->setParam($_GET);
        $objFormParam->convParam();

        // CSV_IDの読み込み
        $this->tpl_subno_csv = $objFormParam->getValue('tpl_subno_csv');
        $this->tpl_csv_id = $this->lfGetCsvId($this->tpl_subno_csv);

        switch ($this->getMode()) {
            case 'confirm':
                // 入力パラメーターチェック
                $this->arrErr = $objFormParam->checkError();
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    // 更新
                    $this->tpl_is_update = $this->lfUpdCsvOutput($this->tpl_csv_id, $objFormParam->getValue('output_list'));
                }
                break;
            case 'defaultset':
                //初期値に戻す
                $this->tpl_is_update = $this->lfSetDefaultCsvOutput($this->tpl_csv_id);
                break;
            default:
                break;
        }
        $this->arrSelected = $this->lfGetSelected($this->tpl_csv_id);
        $this->arrOptions = $this->lfGetOptions($this->tpl_csv_id);
        $this->tpl_subtitle .= '＞' . $this->arrSubnaviName[ $this->tpl_csv_id ];

        if ($this->tpl_is_update) {
            $this->tpl_onload = "window.alert('正常に更新されました。');";
        }

    }

    /**
     * パラメーター情報の初期化
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('編集種別', 'tpl_subno_csv', STEXT_LEN, 'a', array('ALNUM_CHECK', 'MAX_LENGTH_CHECK'), 'product');
        $objFormParam->addParam('出力設定リスト', 'output_list', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        //デフォルト値