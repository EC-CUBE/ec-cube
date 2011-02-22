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
        $objFormParam = new SC_FormParam();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        $this->tpl_no = $this->getNo(array($_GET,$_POST));

        switch ($this->getMode()) {
            case 'search':
                $objProduct = new SC_Product();
                // 入力文字の強制変換とPOST値の引き継ぎ
                //                $this->arrForm = $this->lfConvertParam($_POST,$this->getConvertRule());
                $this->arrForm = $objFormParam->getHashArray();
                $wheres = $this->createWhere($objFormParam,$objDb);
                $this->tpl_linemax = $this->getLineCount($wheres,$objProduct);

                //ぶった斬りポイント==================================================================
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
                $arrProduct_id = $this->getProducts($wheres, $objProduct);
                $productList = $this->getProductList($arrProduct_id,$objProduct);
                //取得している並び順で並び替え
                $this->arrProducts = $this->sortProducts($arrProduct_id,$productList);
                $objProduct->setProductsClassByProductIds($arrProduct_id);
                $this->tpl_javascript .= $this->getTplJavascript($objProduct);
                $js_fnOnLoad = $this->getFnOnload($this->arrProducts);
                $this->tpl_javascript .= 'function fnOnLoad(){' . $js_fnOnLoad . '}';
                $this->tpl_onload .= 'fnOnLoad();';
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
                break;
            default:
                break;
        }

        // カテゴリ取得
        $this->arrCatList = $objDb->sfGetCategoryList();
        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     * トランザクショントークンを unset しないようオーバーライド.
     *
     * @return void
     */
    function doValidToken() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!SC_Helper_Session_Ex::isValidToken(false)) {
                SC_Utils_Ex::sfDispError(INVALID_MOVE_ERRORR);
            }
        }
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
        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->setWhere($where);
        return $objProduct->lists($objQuery, $arrProduct_id);
    }

    /**
     * ロード時に実行するJavascriptを生成
     * @param array $arrProducts
     */
    function getFnOnload($arrProducts){
        foreach ($arrProducts as $arrProduct) {
            $js_fnOnLoad .= "fnSetClassCategories(document.product_form{$arrProduct['product_id']});\n";
        }
    }

    /**
     * 規格クラス用JavaScript生成
     * @param SC_Product $objProduct
     */
    function getTplJavascript(&$objProduct){
        $objJson = new Services_JSON();
        return  'productsClassCategories = ' . $objJson->encode($objProduct->classCategories) . '; ';
    }


    /**
     * 検索結果の取得
     * @param array $whereAndBind string whereと array bindの連想配列
     * @param SC_Product $objProduct
     */
    function getProducts($whereAndBind,&$objProduct){
        $where = $whereAndBind['where'];
        $bind = $whereAndBind['bind'];
        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->setWhere($where);
        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($page_max, $startno);
        // 表示順序
        $objQuery->setOrder($order);
         
        // 検索結果の取得
        return $objProduct->findProductIdsOrder($objQuery, $bind);
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
        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->setWhere($where);
        $linemax = $objProduct->findProductCount($objQuery, $bind);
        return  $linemax;   // 何件が該当しました。表示用
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
                    //                            $arrval[] = "$val%";
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
     * リクエストパラメータnoを取ってくる。
     * @param unknown_type $globalParams
     */
    function getNo($globalParams){
        foreach ($globalParams as $params){
            if(isset($params['no']) && $params['no']!= ''){
                return strval($params['no']);
            }
        }
        return null;
    }

    /**
     * 取得している並び順で並び替え
     * @param $arrProduct_id
     * @param $productList
     */
    function sortProducts($arrProduct_id,$productList){
        $products  = array();
        foreach($productList as $item) {
            $products[ $item['product_id'] ] = $item;
        }
        $arrProducts = array();
        foreach($arrProduct_id as $product_id) {
            $arrProducts[] = $products[$product_id];
        }
        return $arrProducts;
    }


    /**
     * デストラクタ.
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * 文字列の変換ルールを返す
     */
    function getConvertRule(){
        /*
         *  文字列の変換
         *  K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *  C :  「全角ひら仮名」を「全角かた仮名」に変換
         *  V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *  n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         */
        $arrConvList = array();
        $arrConvList['search_name'] = "KVa";
        $arrConvList['search_product_code'] = "KVa";
        return $arrConvList;
    }
    
    /**
     * パラメータ情報の初期化
     * @param SC_FormParam $objFormParam
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam("オーダーID", "order_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("商品名", "search_name", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("カテゴリID", "search_category_id", STEXT_LEN, "KVa",  array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));
        $objFormParam->addParam("商品コード", "search_product_code", LTEXT_LEN, "KVa", array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));
        $objFormParam->addParam("フッター", "footer", LTEXT_LEN, "KVa", array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));
    }

    /**
     * 取得文字列の変換
     * @param Array $param 取得文字列
     * @param Array $convList 変換ルール
     */
    function lfConvertParam($param,$convList){
        $convedParam = array();
        foreach ($convList as $key => $value){
            if(isset($param[$key])) {
                $convedParam[$key] = mb_convert_kana($param[$key],$value);
            }else{
                $convedParam[$key] = $param[$key];
            }
        }
        return $convedParam;
    }
}
?>
