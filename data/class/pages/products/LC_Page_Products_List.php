<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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

    /* 検索条件(内部データ) */
    var $arrSearchData = array();

    /* 検索条件(表示用) */
    var $arrSearch = array();

    var $tpl_subtitle = '';

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
        $conn = new SC_DBConn();
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

        $objQuery = new SC_Query();
        $count = $objQuery->count("dtb_best_products", "category_id = ?", array($this->arrSearchData['category_id']));

        // 商品一覧の表示処理
        $this->lfDispProductsList();

        // 検索条件を画面に表示
        // カテゴリー検索条件
        if ($this->arrSearchData['category_id'] == 0) {
            $this->arrSearch['category'] = "指定なし";
        } else {
            $arrCat = $conn->getOne("SELECT category_name FROM dtb_category WHERE category_id = ?", array($this->arrSearchData['category_id']));
            $this->arrSearch['category'] = $arrCat;
        }

        // メーカー検索条件
        if (strlen($this->arrSearchData['maker_id']) == 0) {
            $this->arrSearch['maker'] = "指定なし";
        } else {
            $this->arrSearch['maker'] = $name = $conn->getOne("SELECT name FROM dtb_maker WHERE maker_id = ?", $this->arrSearchData['maker_id']);
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
            $this->tpl_onload .= "fnSetSelect(document.product_form{$product_id}, '{$this->arrForm['classcategory_id2']}');";
            foreach (array_keys($this->arrProducts) as $key) {
                $arrProduct =& $this->arrProducts[$key];
                if ($arrProduct['product_id'] == $product_id) {
                    $arrProduct['classcategory_id1'] = $this->arrForm['classcategory_id1'];
                    $arrProduct['classcategory_id2'] = $this->arrForm['classcategory_id2'];
                    $arrProduct['quantity'] = $this->arrForm['quantity'];
                    $arrProduct['arrErr'] = $arrErr;
                }
            }
        }

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
        $conn = new SC_DBConn();
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

        $objQuery = new SC_Query();
        $count = $objQuery->count("dtb_best_products", "category_id = ?", array($this->arrSearchData['category_id']));

        // 商品一覧の表示処理
        $this->lfDispProductsList();

        // 検索条件を画面に表示
        // カテゴリー検索条件
        if ($this->arrSearchData['category_id'] == 0) {
            $this->arrSearch['category'] = "指定なし";
        } else {
            $arrCat = $conn->getOne("SELECT category_name FROM dtb_category WHERE category_id = ?",array($category_id));
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
                $ret = SC_Utils_Ex::sfManualEscape($val);
                $arrval[] = "%$ret%";
                $arrval[] = "%$ret%";
            }
        }

        // メーカーらのWHERE文字列取得
        if ($this->arrSearchData['maker_id']) {
            $where .= " AND maker_id = ? ";
            $arrval[] = $this->arrSearchData['maker_id'];
        }
        
        // 対象商品IDの抽出
        $arrProduct_id = $objQuery->getCol('vw_products_allclass AS allcls', 'DISTINCT product_id', $where, $arrval);
        
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
            ,sale_unlimited
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
        $objQuery->setlimitoffset($this->disp_number, $startno);
        // 表示順序
        $objQuery->setorder($order);
        
        // 検索結果の取得
        $this->arrProducts = $objQuery->select($col, $from, $where, $arrval_order);
        // ▲商品詳細取得
        
        // 規格名一覧
        $arrClassName = $objDb->sfGetIDValueList("dtb_class", "class_id", "name");
        // 規格分類名一覧
        $arrClassCatName = $objDb->sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
        // 規格セレクトボックス設定
        for ($i = 0; $i < count($this->arrProducts); $i++) {
            $this->lfMakeSelect($this->arrProducts[$i]['product_id'], $arrClassName, $arrClassCatName);
            // 購入制限数を取得
            $this->lfGetSaleLimit($this->arrProducts[$i]);
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
            if ($arrProductsClass[$i]['stock'] <= 0 && $arrProductsClass[$i]['stock_unlimited'] != '1') {
                continue;
            }

            $stock_find = true;

            // 規格1のセレクトボックス用
            if ($classcat_id1 != $arrProductsClass[$i]['classcategory_id1']) {
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
            if ($arrList[$list_id] == "") {
                $arrList[$list_id] = "\tlist". $product_id. "_". $list_id. " = new Array('選択してください', '". $arrClassCatName[$classcat_id2]. "'";
            } else {
                $arrList[$list_id].= ", '".$arrClassCatName[$classcat_id2]."'";
            }

            // セレクトボックスPOST値
            if ($arrVal[$list_id] == "") {
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
        if (!isset($this->arrForm['classcategory_id2']) || !is_numeric($this->arrForm['classcategory_id2'])) {
            $this->arrForm['classcategory_id2'] = '';
        }
        
        // 規格1が設定されている
        if ($arrProductsClass[0]['classcategory_id1'] != '0') {
            $classcat_find1 = true;
        }

        // 規格2が設定されている
        if ($arrProductsClass[0]['classcategory_id2'] != '0') {
            $classcat_find2 = true;
        }

        $this->tpl_classcat_find1[$product_id] = $classcat_find1;
        $this->tpl_classcat_find2[$product_id] = $classcat_find2;
        $this->tpl_stock_find[$product_id] = $stock_find;
    }

    /* 商品規格情報の取得 */
    function lfGetProductsClass($product_id) {
        $arrRet = array();
        if (SC_Utils_Ex::sfIsInt($product_id)) {
            // 商品規格取得
            $objQuery = new SC_Query();
            $col = "product_class_id, classcategory_id1, classcategory_id2, class_id1, class_id2, stock, stock_unlimited";
            $table = "vw_product_class AS prdcls";
            $where = "product_id = ?";
            $objQuery->setorder("rank1 DESC, rank2 DESC");
            $arrRet = $objQuery->select($col, $table, $where, array($product_id));
        }
        return $arrRet;
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

    // 購入制限数の設定
    function lfGetSaleLimit($product) {
        //在庫が無限または購入制限値が設定値より大きい場合
        if ($product['sale_unlimited'] == 1 || $product['sale_limit'] > SALE_LIMIT_MAX) {
            $this->tpl_sale_limit[$product['product_id']] = SALE_LIMIT_MAX;
        } else {
            $this->tpl_sale_limit[$product['product_id']] = $product['sale_limit'];
        }
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