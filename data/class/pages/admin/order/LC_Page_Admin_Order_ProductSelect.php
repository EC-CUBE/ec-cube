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
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 商品選択 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Order_ProductSelect extends LC_Page {

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
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objDb = new SC_Helper_DB_Ex();
        $objQuery = new SC_Query();

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

            $where = "del_flg = 0";

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
                        $where.= " AND product_id IN (SELECT product_id FROM dtb_product_categories WHERE " . $tmp_where . ")";
                        $arrval = array_merge((array)$arrval, (array)$tmp_arrval);
                    }
                    break;
                case 'search_product_code':
                    $where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code LIKE ? GROUP BY product_id)";
                    $arrval[] = "$val%";
                    break;
                default:
                    break;
                }
            }

            // 読み込む列とテーブルの指定
            $col = "DISTINCT T1.product_id, product_code_min, product_code_max,"
                . " price01_min, price01_max, price02_min, price02_max,"
                . " stock_min, stock_max, stock_unlimited_min,"
                . " stock_unlimited_max, del_flg, status, name, comment1,"
                . " comment2, comment3, main_list_comment, main_image,"
                . " main_list_image, product_flag, deliv_date_id, sale_limit,"
                . " point_rate, create_date, deliv_fee, "
                . " T4.product_rank, T4.category_rank";
            $from = "vw_products_allclass AS T1"
                . " JOIN ("
                . " SELECT max(T3.rank) AS category_rank,"
                . "        max(T2.rank) AS product_rank,"
                . "        T2.product_id"
                . "   FROM dtb_product_categories T2"
                . "   JOIN dtb_category T3 USING (category_id)"
                . " GROUP BY product_id) AS T4 USING (product_id)";
            $order = "T4.category_rank DESC, T4.product_rank DESC";

            // 行数の取得
            if (empty($arrval)) {
                $arrval = array();
            }
            $linemax = $objQuery->count("dtb_products", $where, $arrval);
            $this->tpl_linemax = $linemax;              // 何件が該当しました。表示用

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

            // 取得範囲の指定(開始行番号、行数のセット)
            if(DB_TYPE != "mysql") $objQuery->setLimitOffset($page_max, $startno);
            // 表示順序
            $objQuery->setOrder($order);

            // viewも絞込みをかける(mysql用)
            //sfViewWhere("&&noncls_where&&", $where, $arrval, $objQuery->order . " " .  $objQuery->setLimitOffset($page_max, $startno, true));

            // 検索結果の取得
            $this->arrProducts = $objQuery->select($col, $from, $where, $arrval);

            // 規格名一覧
            $arrClassName = $objDb->sfGetIDValueList("dtb_class", "class_id", "name");

            // 規格分類名一覧
            $arrClassCatName = $objDb->sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");

            // 規格セレクトボックス設定
            for($i = 0; $i < count($this->arrProducts); $i++) {
                $this->lfMakeSelect($this->arrProducts[$i]['product_id'], $arrClassName, $arrClassCatName);
            }
        }

        // カテゴリ取得
        $this->arrCatList = $objDb->sfGetCategoryList();

        //---- ページ表示
        $objView->assignobj($this);
        $objView->display($this->tpl_mainpage);
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

    /* 規格セレクトボックスの作成 */
    function lfMakeSelect($product_id, $arrClassName, $arrClassCatName) {

        $classcat_find1 = false;
        $classcat_find2 = false;
        // 在庫ありの商品の有無
        $stock_find = false;

        // 商品規格情報の取得
        $arrProductsClass = $this->lfGetProductsClass($product_id);

        // 規格1クラス名の取得
        $this->tpl_class_name1[$product_id] =
            isset($arrClassName[$arrProductsClass[0]['class_id1']])
            ? $arrClassName[$arrProductsClass[0]['class_id1']]
            : "";

        // 規格2クラス名の取得
        $this->tpl_class_name2[$product_id] =
            isset($arrClassName[$arrProductsClass[0]['class_id2']])
            ? $arrClassName[$arrProductsClass[0]['class_id2']]
            : "";

        // すべての組み合わせ数
        $count = count($arrProductsClass);

        $classcat_id1 = "";

        $arrSele = array();
        $arrList = array();

        $list_id = 0;
        $arrList[0] = "\tlist". $product_id. "_0 = new Array('選択してください'";
        $arrVal[0] = "\tval". $product_id. "_0 = new Array(''";

        for ($i = 0; $i < $count; $i++) {
            // 在庫のチェック
            if($arrProductsClass[$i]['stock'] <= 0 && $arrProductsClass[$i]['stock_unlimited'] != '1') {
                continue;
            }

            $stock_find = true;

            // 規格1のセレクトボックス用
            if($classcat_id1 != $arrProductsClass[$i]['classcategory_id1']){
                $arrList[$list_id].=");\n";
                $arrVal[$list_id].=");\n";
                $classcat_id1 = $arrProductsClass[$i]['classcategory_id1'];
                $arrSele[$classcat_id1] = $arrClassCatName[$classcat_id1];
                $list_id++;

                $arrList[$list_id] = "";
                $arrVal[$list_id] = "";
            }

            // 規格2のセレクトボックス用
            $classcat_id2 = $arrProductsClass[$i]['classcategory_id2'];

            // セレクトボックス表示値
            if($arrList[$list_id] == "") {
                $arrList[$list_id] = "\tlist". $product_id. "_". $list_id. " = new Array('選択してください', '". $arrClassCatName[$classcat_id2]. "'";
            } else {
                $arrList[$list_id].= ", '".$arrClassCatName[$classcat_id2]."'";
            }

            // セレクトボックスPOST値
            if($arrVal[$list_id] == "") {
                $arrVal[$list_id] = "\tval". $product_id. "_". $list_id. " = new Array('', '". $classcat_id2. "'";
            } else {
                $arrVal[$list_id].= ", '".$classcat_id2."'";
            }
        }

        $arrList[$list_id].=");\n";
        $arrVal[$list_id].=");\n";

        // 規格1
        $this->arrClassCat1[$product_id] = $arrSele;

        $lists = "\tlists".$product_id. " = new Array(";
        $no = 0;
        foreach($arrList as $val) {
            $this->tpl_javascript.= $val;
            if ($no != 0) {
                $lists.= ",list". $product_id. "_". $no;
            } else {
                $lists.= "list". $product_id. "_". $no;
            }
            $no++;
        }
        $this->tpl_javascript.= $lists.");\n";

        $vals = "\tvals".$product_id. " = new Array(";
        $no = 0;
        foreach($arrVal as $val) {
            $this->tpl_javascript.= $val;
            if ($no != 0) {
                $vals.= ",val". $product_id. "_". $no;
            } else {
                $vals.= "val". $product_id. "_". $no;
            }
            $no++;
        }
        $this->tpl_javascript.= $vals.");\n";

        // 選択されている規格2ID
        $classcategory_id = "classcategory_id". $product_id;

        $classcategory_id_2 = $classcategory_id . "_2";
        if (!isset($classcategory_id_2)) $classcategory_id_2 = "";
        if (!isset($_POST[$classcategory_id_2])) $_POST[$classcategory_id_2] = "";

        $this->tpl_onload .= "lnSetSelect('" . $classcategory_id ."_1', "
            . "'" . $classcategory_id_2 . "',"
            . "'" . $product_id . "',"
            . "'" . $_POST[$classcategory_id_2] ."'); ";

        // 規格1が設定されている
        if($arrProductsClass[0]['classcategory_id1'] != '0') {
            $classcat_find1 = true;
        }

        // 規格2が設定されている
        if($arrProductsClass[0]['classcategory_id2'] != '0') {
            $classcat_find2 = true;
        }

        $this->tpl_classcat_find1[$product_id] = $classcat_find1;
        $this->tpl_classcat_find2[$product_id] = $classcat_find2;
        $this->tpl_stock_find[$product_id] = $stock_find;
    }

    /* 商品規格情報の取得 */
    function lfGetProductsClass($product_id) {
        $arrRet = array();
        if(SC_Utils_Ex::sfIsInt($product_id)) {
            // 商品規格取得
            $objQuery = new SC_Query();
            $col = "product_class_id, classcategory_id1, classcategory_id2, class_id1, class_id2, stock, stock_unlimited";
            $table = "vw_product_class AS prdcls";
            $where = "product_id = ?";
            $objQuery->setOrder("rank1 DESC, rank2 DESC");
            $arrRet = $objQuery->select($col, $table, $where, array($product_id));
        }
        return $arrRet;
    }
}
?>
