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
 * 商品一覧 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Products_List.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Products_List extends LC_Page {

    // {{{ properties

    /** テンプレートクラス名1 */
    var $tpl_class_name1 = array();

    /** テンプレートクラス名2 */
    var $tpl_class_name2 = array();

    /** JavaScript テンプレート */
    var $tpl_javascript;

    var $orderby;

    var $mode;

    /** 検索条件(内部データ) */
    var $arrSearchData = array();

    /** 検索条件(表示用) */
    var $arrSearch = array();

    var $tpl_subtitle = '';

    /** ランダム文字列 **/
    var $tpl_rnd = '';

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrSTATUS = $masterData->getMasterData("mtb_status");
        $this->arrSTATUS_IMAGE = $masterData->getMasterData("mtb_status_image");
        $this->arrDELIVERYDATE = $masterData->getMasterData("mtb_delivery_date");
        $this->arrPRODUCTLISTMAX = $masterData->getMasterData("mtb_product_list_max");
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->lfLoadParam();

        $objView = new SC_SiteView(!$this->inCart);
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();

        // タイトル編集
        if ($this->mode == 'search') {
            $this->tpl_subtitle = "検索結果";
        } elseif ($this->arrSearchData['category_id'] == 0) {
            $this->tpl_subtitle = "全商品";
        } else {
            $arrCat = $objDb->sfGetCat($this->arrSearchData['category_id']);
            $this->tpl_subtitle = $arrCat['name'];
        }

        $count = $objQuery->count("dtb_best_products", "category_id = ?", array($this->arrSearchData['category_id']));

        // 商品一覧の表示処理
        $this->lfDispProductsList();

        // 検索条件を画面に表示
        // カテゴリー検索条件
        if ($this->arrSearchData['category_id'] == 0) {
            $this->arrSearch['category'] = "指定なし";
        } else {
            $arrCat = $objQuery->getOne("SELECT category_name FROM dtb_category WHERE category_id = ?", array($this->arrSearchData['category_id']));
            $this->arrSearch['category'] = $arrCat;
        }

        // メーカー検索条件
        if (strlen($this->arrSearchData['maker_id']) == 0) {
            $this->arrSearch['maker'] = "指定なし";
        } else {
            $this->arrSearch['maker'] = $objQuery->getOne("SELECT name FROM dtb_maker WHERE maker_id = ?", $this->arrSearchData['maker_id']);
        }

        // 商品名検索条件
        if (strlen($this->arrSearchData['name']) == 0) {
            $this->arrSearch['name'] = "指定なし";
        } else {
            $this->arrSearch['name'] = $this->arrSearchData['name'];
        }

        // レイアウトデザインを取得
        $layout = new SC_Helper_PageLayout_Ex();
        $layout->sfGetPageLayout($this, false, "products/list.php");

        foreach ($this->arrProducts as $arrProduct) {
            $js_fnOnLoad .= "fnSetClassCategories(document.product_form{$arrProduct['product_id']});\n";
        }
        
        if ($this->inCart) {
            // 商品IDの正当性チェック
            if (!SC_Utils_Ex::sfIsInt($this->arrForm['product_id']) || !$objDb->sfIsRecord("dtb_products", "product_id", $this->arrForm['product_id'], "del_flg = 0 AND status = 1")) {
                SC_Utils_Ex::sfDispSiteError(PRODUCT_NOT_FOUND);
            }
            $product_id = $this->arrForm['product_id'];
            // 入力内容のチェック
            $arrErr = $this->lfCheckError($product_id);
            if (count($arrErr) == 0) {
                $classcategory_id1 = $this->arrForm['classcategory_id1'];
                $classcategory_id2 = $this->arrForm['classcategory_id2'];
                    // 規格1が設定されていない場合
                if (!$this->tpl_classcat_find1[$product_id]) {
                        $classcategory_id1 = '0';
                    }
                    // 規格2が設定されていない場合
                if (!$this->tpl_classcat_find2[$product_id]) {
                        $classcategory_id2 = '0';
                    }
                $objCartSess = new SC_CartSession();
                $objCartSess->addProduct(array($product_id, $classcategory_id1, $classcategory_id2), $this->arrForm['quantity']);
                    $this->sendRedirect($this->getLocation(URL_CART_TOP));
                    exit;
            }
            foreach (array_keys($this->arrProducts) as $key) {
                $arrProduct =& $this->arrProducts[$key];
                if ($arrProduct['product_id'] == $product_id) {
                    $arrProduct['classcategory_id1'] = $this->arrForm['classcategory_id1'];
                    $arrProduct['classcategory_id2'] = $this->arrForm['classcategory_id2'];
                    $arrProduct['quantity'] = $this->arrForm['quantity'];
                    $arrProduct['arrErr'] = $arrErr;
                    $js_fnOnLoad .= "fnSetClassCategories(document.product_form{$arrProduct['product_id']}, '{$this->arrForm['classcategory_id2']}');\n";
                }
            }
        }
        $this->tpl_javascript .= 'function fnOnLoad(){' . $js_fnOnLoad . '}';
        $this->tpl_onload .= 'fnOnLoad(); ';

        $this->tpl_rnd = SC_Utils_Ex::sfGetRandomString(3);

        $objView->assignobj($this);
        $objView->display(SITE_FRAME);
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->init();
    }

    /**
     * Page のプロセス(モバイル).
     *
     * FIXME スパゲッティ...
     *
     * @return void
     */
    function mobileProcess() {
        $this->lfLoadParam();

        $objView = new SC_MobileView();
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();

        // タイトル編集
        $tpl_search_mode = false;

        if ($this->mode == 'search') {
            $this->tpl_subtitle = "検索結果";
            $tpl_search_mode = true;
        } elseif ($this->arrSearchData['category_id'] == 0) {
            $this->tpl_subtitle = "全商品";
        } else {
            $arrCat = $objDb->sfGetCat($this->arrSearchData['category_id']);
            $this->tpl_subtitle = $arrCat['name'];
        }

        $count = $objQuery->count("dtb_best_products", "category_id = ?", array($this->arrSearchData['category_id']));

            // 商品一覧の表示処理
        $this->lfDispProductsList();

            // 検索条件を画面に表示
            // カテゴリー検索条件
        if ($this->arrSearchData['category_id'] == 0) {
            $this->arrSearch['category'] = "指定なし";
        } else {
                $arrCat = $objQuery->getOne("SELECT category_name FROM dtb_category WHERE category_id = ?",array($category_id));
            $this->arrSearch['category'] = $arrCat;
            }

            // 商品名検索条件
        if ($this->arrForm['name'] === "") {
            $this->arrSearch['name'] = "指定なし";
            } else {
            $this->arrSearch['name'] = $this->arrForm['name'];
                    }

        // ページ送り機能用のURLを作成する。
        $objURL = new Net_URL($_SERVER['PHP_SELF']);
        foreach ($_REQUEST as $key => $value) {
            if ($key == session_name() || $key == 'pageno') {
                continue;
            }
            $objURL->addQueryString($key, mb_convert_encoding($value, 'SJIS', CHAR_CODE));
        }

        if ($this->objNavi->now_page > 1) {
            $objURL->addQueryString('pageno', $this->objNavi->now_page - 1);
            $this->tpl_previous_page = $objURL->path . '?' . $objURL->getQueryString();
        }
        if ($this->objNavi->now_page < $this->objNavi->max_page) {
            $objURL->addQueryString('pageno', $this->objNavi->now_page + 1);
            $this->tpl_next_page = $objURL->path . '?' . $objURL->getQueryString();
        }

        $this->tpl_search_mode = $tpl_search_mode;

        $this->tpl_mainpage = MOBILE_TEMPLATE_DIR . "products/list.tpl";

        $objView->assignobj($this);
        $objView->display(SITE_FRAME);
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
     * カテゴリIDの取得
     *
     * @return integer カテゴリID
     */
    function lfGetCategoryId($category_id) {
        $objDb = new SC_Helper_DB_Ex();
        
        // 指定なしの場合、0 を返す
        if (
            strlen($category_id) == 0
            || (String) $category_id == '0'
        ) {
            return 0;
        }
        
        // 正当性チェック
        if (
            !SC_Utils_Ex::sfIsInt($category_id)
                || SC_Utils_Ex::sfIsZeroFilling($category_id)
            || !$objDb->sfIsRecord('dtb_category', 'category_id', (array)$category_id, 'del_flg = 0')
        ) {
            SC_Utils_Ex::sfDispSiteError(CATEGORY_NOT_FOUND);
    }
        
        // 指定されたカテゴリIDを元に正しいカテゴリIDを取得する。
        $arrCategory_id = $objDb->sfGetCategoryId('', $category_id);
        
        if (empty($arrCategory_id)) {
            SC_Utils_Ex::sfDispSiteError(CATEGORY_NOT_FOUND);
        }
        
        return $arrCategory_id[0];
    }

    /* 商品一覧の表示 */
    function lfDispProductsList() {

        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();
        $arrval = array();
        $arrval_order = array();
        $arrval_category = array();
        
        // カテゴリからのWHERE文字列取得
        if ($this->arrSearchData['category_id'] != 0) {
            list($where_category, $arrval_category) = $objDb->sfGetCatWhere($this->arrSearchData['category_id']);
        }
        
        // ▼対象商品IDの抽出
        // 商品検索条件の作成（未削除、表示）
        $where = "del_flg = 0 AND status = 1 ";
        
        // 在庫無し商品の非表示
        if (NOSTOCK_HIDDEN === true) {
            $where .= ' AND (stock_max >= 1 OR stock_unlimited_max = 1)';
        }
        
        if (strlen($where_category) >= 1) {
            $where.= " AND $where_category";
            $arrval = array_merge($arrval, $arrval_category);
        }

        // 商品名をwhere文に
        $name = $this->arrSearchData['name'];
        $name = ereg_replace(",", "", $name);// XXX
        // 全角スペースを半角スペースに変換
        $name = str_replace('　', ' ', $name);
        // スペースでキーワードを分割
        $names = preg_split("/ +/", $name);
        // 分割したキーワードを一つずつwhere文に追加
        foreach ($names as $val) {
            if ( strlen($val) > 0 ) {
                $where .= " AND ( name ILIKE ? OR comment3 ILIKE ?) ";
                $arrval[] = "%$val%";
                $arrval[] = "%$val%";
            }
        }

        // メーカーらのWHERE文字列取得
        if ($this->arrSearchData['maker_id']) {
            $where .= " AND maker_id = ? ";
            $arrval[] = $this->arrSearchData['maker_id'];
        }
        
        // 対象商品IDの抽出
        $arrProduct_id = array_unique($objQuery->getCol('vw_products_allclass AS allcls', 'product_id', $where, $arrval));
        // 行数の取得
        $linemax = count($arrProduct_id);

        $this->tpl_linemax = $linemax;   // 何件が該当しました。表示用

        // ページ送りの取得
        $urlParam = "category_id={$this->arrSearchData['category_id']}&pageno=#page#";
        $this->objNavi = new SC_PageNavi($this->tpl_pageno, $linemax, $this->disp_number, "fnNaviPage", NAVI_PMAX, $urlParam);
        $strnavi = $this->objNavi->strnavi;

        // 表示文字列
        $this->tpl_strnavi = empty($strnavi) ? "&nbsp;" : $strnavi;
        $startno = $this->objNavi->start_row;                 // 開始行
        
        // ▼商品詳細取得
        $col = <<< __EOS__
             product_id
            ,product_code_min
            ,product_code_max
            ,name
            ,comment1
            ,comment2
            ,comment3
            ,main_list_comment
            ,main_image
            ,main_list_image
            ,price01_min
            ,price01_max
            ,price02_min
            ,price02_max
            ,stock_min
            ,stock_max
            ,stock_unlimited_min
            ,stock_unlimited_max
            ,point_rate
            ,sale_limit
            ,deliv_date_id
            ,deliv_fee
            ,status
            ,product_flag
            ,del_flg
__EOS__;
        
        $from = "vw_products_allclass_detail AS alldtl";
        
        // WHERE 句
        $where = '0=0';
        if (is_array($arrProduct_id) && !empty($arrProduct_id)) {
            $where .= ' AND product_id IN (' . implode(',', $arrProduct_id) . ')';
        } else {
            // 一致させない
            $where .= ' AND 0<>0';
        }
        
        // 表示順序
        switch ($this->orderby) {

            // 販売価格順
            case 'price':
                $order = "price02_min, product_id";
                break;

            // 新着順
            case 'date':
                $order = "create_date DESC, product_id";
                break;

            default:
                if (strlen($where_category) >= 1) {
                    $dtb_product_categories = "(SELECT * FROM dtb_product_categories WHERE $where_category)";
                    $arrval_order = array_merge($arrval_category, $arrval_category);
                } else {
                    $dtb_product_categories = 'dtb_product_categories';
                }
                $order = <<< __EOS__
                    (
                        SELECT
                             T3.rank
                        FROM
                            $dtb_product_categories T2
                            JOIN dtb_category T3
                                USING (category_id)
                        WHERE T2.product_id = alldtl.product_id
                        ORDER BY T3.rank DESC, T2.rank DESC
                        LIMIT 1
                    ) DESC
                    ,(
                        SELECT
                            T2.rank
                        FROM
                            $dtb_product_categories T2
                            JOIN dtb_category T3
                                USING (category_id)
                        WHERE T2.product_id = alldtl.product_id
                        ORDER BY T3.rank DESC, T2.rank DESC
                        LIMIT 1
                    ) DESC
                    ,product_id
__EOS__;
                break;
        }
        
        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($this->disp_number, $startno);
        // 表示順序
        $objQuery->setOrder($order);
        
        // 検索結果の取得
        $this->arrProducts = $objQuery->select($col, $from, $where, $arrval_order);
        // ▲商品詳細取得
        
        $arrProductId = array();
        // 規格セレクトボックス設定
        foreach ($this->arrProducts as $product) {
            $arrProductId[] = $product['product_id'];
        }
        
        require_once CLASS_PATH . 'SC_Product.php';
        $objProduct = new SC_Product($arrProductId);
        
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

        $this->tpl_stock_find = $objProduct->stock_find;

        $productsClassCategories = $objProduct->classCategories;
        
        require_once DATA_PATH . 'module/Services/JSON.php';
        $objJson = new Services_JSON();
        $this->tpl_javascript .= 'productsClassCategories = ' . $objJson->encode($productsClassCategories) . '; ';
    }

    /* 入力内容のチェック */
    function lfCheckError($id) {

        // 入力データを渡す。
        $objErr = new SC_CheckError($this->arrForm);

        // 複数項目チェック
        if ($this->tpl_classcat_find1[$id]) {
            $objErr->doFunc(array("規格1", 'classcategory_id1', INT_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        }
        if ($this->tpl_classcat_find2[$id]) {
            $objErr->doFunc(array("規格2", 'classcategory_id2', INT_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        }
        $objErr->doFunc(array("数量", 'quantity', INT_LEN), array("EXIST_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));

        return $objErr->arrErr;
    }

    /**
     * パラメータの読み込み
     *
     * @return void
     */
    function lfLoadParam() {
        $this->arrForm = $_GET;
        
        $this->mode = $this->arrForm['mode'];
        $this->arrSearchData['category_id'] = $this->lfGetCategoryId($this->arrForm['category_id']);
        $this->arrSearchData['maker_id'] = $this->arrForm['maker_id'];
        $this->arrSearchData['name'] = $this->arrForm['name'];
        $this->orderby = $this->arrForm['orderby'];
        // 表示件数
        if (
            isset($this->arrForm['disp_number'])
            && SC_Utils_Ex::sfIsInt($this->arrForm['disp_number'])
        ) {
            $this->disp_number = $this->arrForm['disp_number'];
        } else {
            //最小表示件数を選択
            $this->disp_number = current(array_keys($this->arrPRODUCTLISTMAX));
        }
        $this->tpl_pageno = $this->arrForm['pageno'];
        $this->inCart = strlen($this->arrForm['product_id']) >= 1;
    }
}
?>
