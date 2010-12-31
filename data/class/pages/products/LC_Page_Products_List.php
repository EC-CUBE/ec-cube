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
require_once(CLASS_FILE_PATH . "pages/LC_Page.php");

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
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     *  ページのアクション（旧process)
     * @return void
     */
    function action() {
        $this->lfLoadParam();
        //$objView = new SC_SiteView(!$this->inCart);
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
                // 規格IDを取得
                $objProduct = new SC_Product();
                $product_class_id = $this->arrForm['product_class_id'];
                $product_type = $this->arrForm['product_type'];
                $objCartSess = new SC_CartSession();
                $objCartSess->addProduct($product_class_id, $this->arrForm['quantity'], $product_type);
                $this->objDisplay->redirect($this->getLocation(URL_CART_TOP));
                exit;
            }
            foreach (array_keys($this->arrProducts) as $key) {
                $arrProduct =& $this->arrProducts[$key];
                if ($arrProduct['product_id'] == $product_id) {
                    $arrProduct['product_class_id'] = $this->arrForm['product_class_id'];
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
     * @return void
     */
    function mobileProcess() {
        parent::mobileProcess();
        $this->mobieAction();
        $this->sendResponse();
    }

    /**
     * Page のAction(モバイル).
     *
     * FIXME スパゲッティ...
     *
     * @return void
     */
    function mobieAction(){
        $this->lfLoadParam();
        //$objView = new SC_MobileView();
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
        $where = "alldtl.del_flg = 0 AND alldtl.status = 1 ";

        // 在庫無し商品の非表示
        if (NOSTOCK_HIDDEN === true) {
            $where .= ' AND (stock >= 1 OR stock_unlimited = 1)';
        }

        if (strlen($where_category) >= 1) {
            $where .= " AND T2.$where_category";
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
                $where .= " AND ( alldtl.name ILIKE ? OR alldtl.comment3 ILIKE ?) ";
                $arrval[] = "%$val%";
                $arrval[] = "%$val%";
            }
        }

        // メーカーらのWHERE文字列取得
        if ($this->arrSearchData['maker_id']) {
            $where .= " AND alldtl.maker_id = ? ";
            $arrval[] = $this->arrSearchData['maker_id'];
        }
 
        // 検索結果対象となる商品の数を取得
        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->setWhere($where);
        $objProduct = new SC_Product();
        $linemax = $objProduct->findProductCount($objQuery, $arrval);
        $this->tpl_linemax = $linemax;   // 何件が該当しました。表示用

        // ページ送りの取得
        $urlParam = "category_id={$this->arrSearchData['category_id']}&pageno=#page#";
        $this->objNavi = new SC_PageNavi($this->tpl_pageno, $linemax, $this->disp_number, "fnNaviPage", NAVI_PMAX, $urlParam);
        $strnavi = $this->objNavi->strnavi;

        // 表示文字列
        $this->tpl_strnavi = empty($strnavi) ? "&nbsp;" : $strnavi;
        $startno = $this->objNavi->start_row;                 // 開始行

        $objProduct = new SC_Product();
        $objQuery =& SC_Query::getSingletonInstance();
        // 表示順序
        switch ($this->orderby) {
            // 販売価格が安い順
            case 'price':
                $objProduct->setProductsOrder('price02', 'dtb_products_class', 'ASC');
                break;

            // 新着順
            case 'date':
                $objProduct->setProductsOrder('create_date', 'dtb_products', 'DESC');
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
                    $objQuery->setOrder($order);
                break;
        }
        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($this->disp_number, $startno);
        $objQuery->setWhere($where);

         // 表示すべきIDとそのIDの並び順を一気に取得
        $arrProduct_id = $objProduct->findProductIdsOrder($objQuery, array_merge($arrval, $arrval_order));

        // 取得した表示すべきIDだけを指定して情報を取得。
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

        // 規格を設定
        $objProduct->setProductsClassByProductIds($arrProduct_id);

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
        $this->tpl_product_class_id = $objProduct->product_class_id;
        $this->tpl_product_type = $objProduct->product_type;

        // 商品ステータスを取得
        $this->productStatus = $objProduct->getProductStatus($arrProduct_id);

        $productsClassCategories = $objProduct->classCategories;

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
