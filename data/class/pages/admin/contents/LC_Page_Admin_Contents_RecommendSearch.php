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
require_once(CLASS_EX_REALDIR . "page_extends/admin/LC_Page_Admin_Ex.php");

/**
 * おすすめ商品管理 商品検索のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Contents_RecommendSearch extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainno = 'contents';
        $this->tpl_subnavi = '';
        $this->tpl_subno = "";

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

        switch ($this->getMode()) {
        case 'search':
            // POST値の引き継ぎ
            $this->arrErr = $this->lfCheckError($objFormParam);
            $arrPost = $objFormParam->getHashArray();
            // 入力された値にエラーがない場合、検索処理を行う。
            // 検索結果の数に応じてページャの処理も入れる。
            if (SC_Utils_Ex::isBlank($this->arrErr)) {
                $objProduct = new SC_Product_Ex();

                $wheres = $this->createWhere($objFormParam,$objDb);
                $this->tpl_linemax = $this->getLineCount($wheres,$objProduct);

                $page_max = SC_Utils_Ex::sfGetSearchPageMax($arrPost['search_page_max']);

                // ページ送りの取得
                $objNavi = new SC_PageNavi_Ex($arrPost['search_pageno'], $this->tpl_linemax, $page_max, "fnNaviSearchOnlyPage", NAVI_PMAX);
                $this->tpl_strnavi = $objNavi->strnavi;      // 表示文字列
                $startno = $objNavi->start_row;

                $arrProduct_id = $this->getProducts($wheres, $objProduct, $page_max, $startno);
                $this->arrProducts = $this->getProductList($arrProduct_id,$objProduct);
            }
            break;
        default:
            break;
        }

        // カテゴリ取得
        $this->arrCatList = $objDb->sfGetCategoryList();
        $this->setTemplate('contents/recommend_search.tpl');
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
     * パラメータの初期化を行う
     * @param Object $objFormParam
     */
    function lfInitParam(&$objFormParam){
        $objFormParam->addParam("商品ID", "search_name", LTEXT_LEN, "KVa", array( "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("商品ID", "search_category_id", INT_LEN, "n", array( "MAX_LENGTH_CHECK","NUM_CHECK"));
        $objFormParam->addParam("商品コード", "search_product_code", LTEXT_LEN, "KVa", array( "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("ページ番号", "search_pageno", INT_LEN, "n", array( "MAX_LENGTH_CHECK","NUM_CHECK"));
    }

    /**
     * 入力されたパラメータのエラーチェックを行う。
     * @param Object $objFormParam
     * @return Array エラー内容
     */
    function lfCheckError(&$objFormParam){
        $objErr = new SC_CheckError_Ex($objFormParam->getHashArray());
        $objErr->arrErr = $objFormParam->checkError();
        return $objErr->arrErr;
    }

    /**
     *
     * POSTされた値からSQLのWHEREとBINDを配列で返す。
     * @return array ('where' => where string, 'bind' => databind array)
     * @param SC_FormParam $objFormParam
     */
    function createWhere(&$objFormParam,&$objDb){
        $arrForm = $objFormParam->getHashArray();
        $where = "alldtl.del_flg = 0";
        $bind = array();
        foreach ($arrForm as $key => $val) {
            if($val == "") {
                continue;
            }

            switch ($key) {
                case 'search_name':
                    $where .= " AND name ILIKE ?";
                    $bind[] = "%".$val."%";
                    break;
                case 'search_category_id':
                    list($tmp_where, $tmp_bind) = $objDb->sfGetCatWhere($val);
                    if($tmp_where != "") {
                        $where.= " AND alldtl.product_id IN (SELECT product_id FROM dtb_product_categories WHERE " . $tmp_where . ")";
                        $bind = array_merge((array)$bind, (array)$tmp_bind);
                    }
                    break;
                case 'search_product_code':
                    $where .=    " AND alldtl.product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code LIKE ? GROUP BY product_id)";
                    $bind[] = '%'.$val.'%';
                    break;

                default:
                    break;
            }
        }
        return array(
            'where'=>$where,
            'bind' => $bind
        );
    }

    /**
     *
     * 検索結果対象となる商品の数を返す。
     * @param array $whereAndBind
     * @param SC_Product $objProduct
     */
    function getLineCount($whereAndBind,&$objProduct){
        $where = $whereAndBind['where'];
        $bind = $whereAndBind['bind'];
        // 検索結果対象となる商品の数を取得
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setWhere($where);
        $linemax = $objProduct->findProductCount($objQuery, $bind);
        return  $linemax;   // 何件が該当しました。表示用
    }

    /**
     * 検索結果の取得
     * @param array $whereAndBind string whereと array bindの連想配列
     * @param SC_Product $objProduct
     */
    function getProducts($whereAndBind,&$objProduct, $page_max, $startno){
        $where = $whereAndBind['where'];
        $bind = $whereAndBind['bind'];
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setWhere($where);
        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($page_max, $startno);

        // 検索結果の取得
        return $objProduct->findProductIdsOrder($objQuery, $bind);
    }

    /**
     * 
     * 商品取得
     * @param array $arrProduct_id
     * @param SC_Product $objProduct
     */
    function getProductList($arrProduct_id,&$objProduct){
        $where = "";
        if (is_array($arrProduct_id) && !empty($arrProduct_id)) {
            $where = 'product_id IN (' . implode(',', $arrProduct_id) . ')';
        } else {
            // 一致させない
            $where = '0<>0';
        }
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setWhere($where);
        // 表示順序
        $order = "update_date DESC, product_id DESC";
        $objQuery->setOrder($order);
        return $objProduct->lists($objQuery, $arrProduct_id);
    }
}
?>
