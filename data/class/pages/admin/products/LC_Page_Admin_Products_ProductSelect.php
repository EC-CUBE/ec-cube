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
 * 商品選択 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products_ProductSelect extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/product_select.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = '';
        $this->tpl_maintitle = t('TPL_MAINTITLE_007');
        $this->tpl_subtitle = t('LC_Page_Admin_Products_ProductSelect_001');

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPRODUCTSTATUS_COLOR = $masterData->getMasterData('mtb_product_status_color');
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
        $objFormParam->convParam();
        $this->arrForm = $objFormParam->getHashArray();

        switch ($this->getMode()) {
            case 'search':
                $this->arrProducts = $this->lfGetProducts($objDb);
                break;
            default:
                break;
        }

        // カテゴリ取得
        $this->arrCatList = $objDb->sfGetCategoryList();
        $this->setTemplate($this->tpl_mainpage);

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * パラメーター情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam(t('c_Category_01'), 'search_category_id', STEXT_LEN, 'n');
        $objFormParam->addParam(t('c_Product name_01'), 'search_name', STEXT_LEN, 'KVa');
        $objFormParam->addParam(t('c_Product code_01'), 'search_product_code', STEXT_LEN, 'KVa');
    }

    /* 商品検索結果取得 */
    function lfGetProducts(&$objDb) {
        $where = 'del_flg = 0';
        $arrWhereVal = array();

        /* 入力エラーなし */
        foreach ($this->arrForm AS $key=>$val) {
            if ($val == '') continue;

            switch ($key) {
                case 'search_name':
                    $where .= ' AND name ILIKE ?';
                    $arrWhereVal[] = "%$val%";
                    break;
                case 'search_category_id':
                    list($tmp_where, $arrTmp) = $objDb->sfGetCatWhere($val);
                    if ($tmp_where != '') {
                        $where.= ' AND product_id IN (SELECT product_id FROM dtb_product_categories WHERE ' . $tmp_where . ')';
                        $arrWhereVal = array_merge((array)$arrWhereVal, (array)$arrTmp);
                    }
                    break;
                case 'search_product_code':
                    $where .= ' AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code LIKE ? GROUP BY product_id)';
                    $arrWhereVal[] = "$val%";
                    break;
                default:
                    break;
            }
        }

        $order = 'update_date DESC, product_id DESC ';

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 行数の取得
        $linemax = $objQuery->count('dtb_products', $where, $arrWhereVal);
        $this->tpl_linemax = $linemax;              // 何件が該当しました。表示用

        // ページ送りの処理
        $page_max = SC_Utils_Ex::sfGetSearchPageMax($_POST['search_page_max']);

        // ページ送りの取得
        $objNavi = new SC_PageNavi_Ex($_POST['search_pageno'], $linemax, $page_max, 'fnNaviSearchOnlyPage', NAVI_PMAX);
        $this->tpl_strnavi = $objNavi->strnavi;     // 表示文字列
        $startno = $objNavi->start_row;

        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($page_max, $startno);
        // 表示順序
        $objQuery->setOrder($order);

        // 検索結果の取得
        // FIXME 商品コードの表示
        $arrProducts = $objQuery->select('*', SC_Product_Ex::alldtlSQL(), $where, $arrWhereVal);
        return $arrProducts;
    }
}
