
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
require_once(CLASS_REALDIR . "pages/admin/LC_Page_Admin.php");

/**
 * 商品選択 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Order_ProductSelect extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/product_select.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subnavi = '';
        $this->tpl_subno = "";
        $this->tpl_subtitle = '商品選択';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPRODUCTSTATUS_COLOR = $masterData->getMasterData("mtb_product_status_color");
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
        $objSess = new SC_Session();
        $objDb = new SC_Helper_DB_Ex();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        if ($_GET['no'] != '') {
            $this->tpl_no = strval($_GET['no']);
        } elseif ($_POST['no'] != '') {
            $this->tpl_no = strval($_POST['no']);
        }

        if ($_POST['mode'] == "search") {

            // POST値の引き継ぎ
            $this->arrForm = $_POST;
            // 入力文字の強制変換
            $this->lfConvertParam();

            $where = "alldtl.del_flg = 0";
            $arrval = array();

            /* 入力エラーなし */
            foreach ($this->arrForm as $key => $val) {
                if($val == "") {
                    continue;
                }

                switch ($key) {
                case 'search_name':
                    $where .= " AND name ILIKE ?";
                    $arrval[] = "%$val%";
                    break;
                case 'search_category_id':
                    list($tmp_where, $tmp_arrval) = $objDb->sfGetCatWhere($val);
                    if($tmp_where != "") {
                        $where.= " AND alldtl.product_id IN (SELECT product_id FROM dtb_product_categories WHERE " . $tmp_where . ")";
                        $arrval = array_merge((array)$arrval, (array)$tmp_arrval);
                    }
                    break;
                case 'search_product_code':
                    $where .= " AND alldtl.product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code LIKE ? GROUP BY product_id)";
                    $arrval[] = "$val%";
                    break;
                default:
                    break;
                }
            }

            // 検索結果対象となる商品の数を取得
            $objQuery =& SC_Query::getSingletonInstance();
            $objQuery->setWhere($where);
            $objProduct = new SC_Product();
            $linemax = $objProduct->findProductCount($objQuery, $arrval);
            $this->tpl_linemax = $linemax;   // 何件が該当しました。表示用

            // ページ送りの処理
            if(isset($_POST['search_page_max'])
               && is_numeric($_POST['search_page_max'])) {
                $page_max = $_POST['search_page_max'];
            } else {
                $page_max = SEARCH_PMAX;
            }

            // ページ送りの取得
            $objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnNaviSearchOnlyPage", NAVI_PMAX);
            $this->tpl_strnavi = $objNavi->strnavi;     // 表示文字列
            $startno = $objNavi->start_row;

            $objProduct = new SC_Product();
            $objQuery =& SC_Query::getSingletonInstance();
            $objQuery->setWhere($where);

            // 取得範囲の指定(開始行番号、行数のセット)
            $objQuery->setLimitOffset($page_max, $startno);
            // 表示順序
            $objQuery->setOrder($order);

            // 検索結果の取得
            $arrProduct_id = $objProduct->findProductIdsOrder($objQuery, $arrval);

            $where = "";
            if (is_array($arrProduct_id) && !empty($arrProduct_id)) {
                $where = 'product_id IN (' . implode(',', $arrProduct_id) . ')';
            } else {
                // 一致させない
                $where = '0<>0';
            }
            $objQuery =& SC_Query::getSingletonInstance();
            $objQuery->setWhere($where);
            $arrProducts = $objProduct->lists($objQuery, $arrProduct_id);

            //取得している並び順で並び替え
            $arrProducts2 = array();
            foreach($arrProducts as $item) {
                $arrProducts2[ $item['product_id'] ] = $item;
            }
            $this->arrProducts = array();
            foreach($arrProduct_id as $product_id) {
                $this->arrProducts[] = $arrProducts2[$product_id];
            }

            $objProduct->setProductsClassByProductIds($arrProduct_id);
            $objJson = new Services_JSON();
            $this->tpl_javascript .= 'productsClassCategories = ' . $objJson->encode($objProduct->classCategories) . '; ';

            foreach ($this->arrProducts as $arrProduct) {
                $js_fnOnLoad .= "fnSetClassCategories(document.product_form{$arrProduct['product_id']});\n";
            }

            $this->tpl_javascript .= 'function fnOnLoad(){' . $js_fnOnLoad . '}';
            $this->tpl_onload .= 'fnOnLoad(); ';

            // 規格1クラス名
            $this->tpl_class_name1 = $objProduct->className1;

            // 規格2クラス名
            $this->tpl_class_name2 = $objProduct->className2;

            // 規格1
            $this->arrClassCat1 = $objProduct->classCats1;

            // 規格1が設定されている
            $this->tpl_classcat_find1 = $objProduct->classCat1_find;
            // 規格2が設定されている
            $this->tpl_classcat_find2 = $objProduct->classCat2_find;
            $this->tpl_product_class_id = $objProduct->product_class_id;
            $this->tpl_stock_find = $objProduct->stock_find;
        }

        // カテゴリ取得
        $this->arrCatList = $objDb->sfGetCategoryList();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* 取得文字列の変換 */
    function lfConvertParam() {
        /*
         *  文字列の変換
         *  K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *  C :  「全角ひら仮名」を「全角かた仮名」に変換
         *  V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *  n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         */
        $arrConvList['search_name'] = "KVa";
        $arrConvList['search_product_code'] = "KVa";

        // 文字変換
        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(isset($this->arrForm[$key])) {
                $this->arrForm[$key] = mb_convert_kana($this->arrForm[$key] ,$val);
            }
        }
    }
}
?>
