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
    var $tpl_class_name1;

    /** テンプレートクラス名2 */
    var $tpl_class_name2;

    /** JavaScript テンプレート */
    var $tpl_javascript;

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

        $this->tpl_class_name1 = array();
        $this->tpl_class_name2 = array();
        $this->allowClientCache();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView();
        $conn = new SC_DBConn();
        $objDb = new SC_Helper_DB_Ex();

        //表示件数の選択
        if(isset($_POST['disp_number'])
           && SC_Utils_Ex::sfIsInt($_POST['disp_number'])) {
            $this->disp_number = $_POST['disp_number'];
        } else {
            //最小表示件数を選択
            $this->disp_number = current(array_keys($this->arrPRODUCTLISTMAX));
        }

        //表示順序の保存
        $this->orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "";

        // GETのカテゴリIDを元に正しいカテゴリIDを取得する。
        $arrCategory_id = $objDb->sfGetCategoryId("", $_GET['category_id']);

        if (!isset($_GET['mode'])) $_GET['mode'] = "";
        if (!isset($_GET['name'])) $_GET['name'] = "";
        if (!isset($_POST['orderby'])) $_POST['orderby'] = "";
        if (empty($arrCategory_id)) $arrCategory_id = array("0");

        // タイトル編集
        $tpl_subtitle = "";
        if ($_GET['mode'] == 'search') {
            $tpl_subtitle = "検索結果";
        } elseif (empty($arrCategory_id[0])) {
            $tpl_subtitle = "全商品";
        } else {
            $arrFirstCat = $objDb->sfGetFirstCat($arrCategory_id[0]);
            $tpl_subtitle = $arrFirstCat['name'];
        }

        $objQuery = new SC_Query();
        $count = $objQuery->count("dtb_best_products", "category_id = ?", $arrCategory_id);

        // 以下の条件でBEST商品を表示する
        // ・BEST最大数の商品が登録されている。
        // ・カテゴリIDがルートIDである。
        // ・検索モードでない。
        if(($count >= BEST_MIN) && $this->lfIsRootCategory($arrCategory_id[0]) && ($_GET['mode'] != 'search') ) {
            // 商品TOPの表示処理
            $this->arrBestItems = SC_Utils_Ex::sfGetBestProducts($conn, $arrCategory_id[0]);
            $this->BEST_ROOP_MAX = ceil((BEST_MAX-1)/2);
        } else {
            if ($_GET['mode'] == 'search' && strlen($_GET['category_id']) == 0 ){
                // 検索時にcategory_idがGETに存在しない場合は、仮に埋めたIDを空白に戻す
                $arrCategory_id = array(0);
            }

            // 商品一覧の表示処理
            $this->lfDispProductsList($arrCategory_id[0], $_GET['name'], $this->disp_number, $_POST['orderby']);

            // 検索条件を画面に表示
            // カテゴリー検索条件
            if (strlen($_GET['category_id']) == 0) {
                $arrSearch['category'] = "指定なし";
            }else{
                $arrCat = $conn->getOne("SELECT category_name FROM dtb_category WHERE category_id = ?", $arrCategory_id);
                $arrSearch['category'] = $arrCat;
            }

            // 商品名検索条件
            if ($_GET['name'] === "") {
                $arrSearch['name'] = "指定なし";
            }else{
                $arrSearch['name'] = $_GET['name'];
            }
        }

        // レイアウトデザインを取得
        $layout = new SC_Helper_PageLayout_Ex();
        $layout->sfGetPageLayout($this, false, "products/list.php");

        if(isset($_POST['mode']) && $_POST['mode'] == "cart"
           && $_POST['product_id'] != "") {

            // 値の正当性チェック
            if(!SC_Utils_Ex::sfIsInt($_POST['product_id']) || !$objDb->sfIsRecord("dtb_products", "product_id", $_POST['product_id'], "del_flg = 0 AND status = 1")) {
                SC_Utils_Ex::sfDispSiteError(PRODUCT_NOT_FOUND);
            } else {
                // 入力値の変換
                $this->arrErr = $this->lfCheckError($_POST['product_id']);
                if(count($this->arrErr) == 0) {
                    $objCartSess = new SC_CartSession();
                    $classcategory_id = "classcategory_id". $_POST['product_id'];
                    $classcategory_id1 = $_POST[$classcategory_id. '_1'];
                    $classcategory_id2 = $_POST[$classcategory_id. '_2'];
                    $quantity = "quantity". $_POST['product_id'];
                    // 規格1が設定されていない場合
                    if(!$this->tpl_classcat_find1[$_POST['product_id']]) {
                        $classcategory_id1 = '0';
                    }
                    // 規格2が設定されていない場合
                    if(!$this->tpl_classcat_find2[$_POST['product_id']]) {
                        $classcategory_id2 = '0';
                    }
                    $objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
                    $objCartSess->addProduct(array($_POST['product_id'], $classcategory_id1, $classcategory_id2), $_POST[$quantity]);
                    $this->sendRedirect($this->getLocation(URL_CART_TOP));
                    exit;
                }
            }
        }

        $this->tpl_subtitle = $tpl_subtitle;

        // 支払方法の取得
        $this->arrPayment = $this->lfGetPayment();
        // 入力情報を渡す
        $this->arrForm = $_POST;

        $this->lfConvertParam();

        $this->category_id = $arrCategory_id[0];
        $this->arrSearch = $arrSearch;

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
        $objView = new SC_MobileView();
        $conn = new SC_DBConn();
        $objDb = new SC_Helper_DB_Ex();

        //表示件数の選択
        if(isset($_REQUEST['disp_number'])
           && SC_Utils_Ex::sfIsInt($_REQUEST['disp_number'])) {
            $this->disp_number = $_REQUEST['disp_number'];
        } else {
            //最小表示件数を選択
            $this->disp_number = current(array_keys($this->arrPRODUCTLISTMAX));
        }

        //表示順序の保存
        $this->orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : "";

        // GETのカテゴリIDを元に正しいカテゴリIDを取得する。
        $arrCategory_id = $objDb->sfGetCategoryId("", $_GET['category_id']);


        // タイトル編集
        $tpl_subtitle = "";
        $tpl_search_mode = false;

        if (!isset($_GET['mode'])) $_GET['mode'] = "";
        if (!isset($_POST['mode'])) $_POST['mode'] = "";
        if (!isset($_GET['name'])) $_GET['name'] = "";
        if (!isset($_REQUEST['orderby'])) $_REQUEST['orderby'] = "";
        if (empty($arrCategory_id)) $arrCategory_id = array("0");

        if($_GET['mode'] == 'search'){
            $tpl_subtitle = "検索結果";
            $tpl_search_mode = true;
        }elseif (empty($arrCategory_id)) {
            $tpl_subtitle = "全商品";
        }else{
            $arrFirstCat = $objDb->sfGetFirstCat($arrCategory_id[0]);
            $tpl_subtitle = $arrFirstCat['name'];
        }

        $objQuery = new SC_Query();
        $count = $objQuery->count("dtb_best_products", "category_id = ?", $arrCategory_id);

        // 以下の条件でBEST商品を表示する
        // ・BEST最大数の商品が登録されている。
        // ・カテゴリIDがルートIDである。
        // ・検索モードでない。
        if(($count >= BEST_MIN) && $this->lfIsRootCategory($arrCategory_id[0]) && ($_GET['mode'] != 'search') ) {
            // 商品TOPの表示処理

            $this->arrBestItems = SC_Utils_Ex::sfGetBestProducts($conn, $arrCategory_id[0]);
            $this->BEST_ROOP_MAX = ceil((BEST_MAX-1)/2);
        } else {
            if ($_GET['mode'] == 'search' && strlen($_GET['category_id']) == 0 ){
                // 検索時にcategory_idがGETに存在しない場合は、仮に埋めたIDを空白に戻す
                $arrCategory_id = array("");
            }

            // 商品一覧の表示処理
            $this->lfDispProductsList($arrCategory_id[0], $_GET['name'], $this->disp_number, $_REQUEST['orderby']);

            // 検索条件を画面に表示
            // カテゴリー検索条件
            if (strlen($_GET['category_id']) == 0) {
                $arrSearch['category'] = "指定なし";
            }else{
                $arrCat = $conn->getOne("SELECT category_name FROM dtb_category WHERE category_id = ?",array($category_id));
                $arrSearch['category'] = $arrCat;
            }

            // 商品名検索条件
            if ($_GET['name'] === "") {
                $arrSearch['name'] = "指定なし";
            }else{
                $arrSearch['name'] = $_GET['name'];
            }
        }

        if($_POST['mode'] == "cart" && $_POST['product_id'] != "") {
            // 値の正当性チェック
            if(!SC_Utils_Ex::sfIsInt($_POST['product_id']) || !SC_Utils_Ex::sfIsRecord("dtb_products", "product_id", $_POST['product_id'], "del_flg = 0 AND status = 1")) {
                SC_Utils_Ex::sfDispSiteError(PRODUCT_NOT_FOUND, "", false, "", true);
            } else {
                // 入力値の変換
                $this->arrErr = $this->lfCheckError($_POST['product_id']);
                if(count($this->arrErr) == 0) {
                    $objCartSess = new SC_CartSession();
                    $classcategory_id = "classcategory_id". $_POST['product_id'];
                    $classcategory_id1 = $_POST[$classcategory_id. '_1'];
                    $classcategory_id2 = $_POST[$classcategory_id. '_2'];
                    $quantity = "quantity". $_POST['product_id'];
                    // 規格1が設定されていない場合
                    if(!$this->tpl_classcat_find1[$_POST['product_id']]) {
                        $classcategory_id1 = '0';
                    }
                    // 規格2が設定されていない場合
                    if(!$this->tpl_classcat_find2[$_POST['product_id']]) {
                        $classcategory_id2 = '0';
                    }
                    $objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
                    $objCartSess->addProduct(array($_POST['product_id'], $classcategory_id1, $classcategory_id2), $_POST[$quantity]);
                    $this->sendRedirect(MOBILE_URL_CART_TOP, array(session_name() => session_id()));
                    exit;
                }
            }
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

        $this->tpl_subtitle = $tpl_subtitle;
        $this->tpl_search_mode = $tpl_search_mode;

        // 支払方法の取得
        $this->arrPayment = $this->lfGetPayment();
        // 入力情報を渡す
        $this->arrForm = $_POST;

        $this->category_id = $arrCategory_id[0];
        $this->arrSearch = $arrSearch;
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

    /* カテゴリIDがルートかどうかの判定 */
    function lfIsRootCategory($category_id) {
        $objQuery = new SC_Query();
        $level = $objQuery->get("dtb_category", "level", "category_id = ?", array($category_id));
        if($level == 1) {
            return true;
        }
        return false;
    }

    /* 商品一覧の表示 */
    function lfDispProductsList($category_id, $name, $disp_num, $orderby) {

        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();
        $this->tpl_pageno = defined("MOBILE_SITE") ? @$_GET['pageno'] : @$_POST['pageno'];

        //表示順序
        switch($orderby) {

        //価格順
        case 'price':
            $col = "DISTINCT price02_min, product_id, product_code_min, product_code_max,"
                . " name, comment1, comment2, comment3,"
                . " main_list_comment, main_image, main_list_image,"
                . " price01_min, price01_max, price02_max,"
                . " stock_min, stock_max, stock_unlimited_min, stock_unlimited_max,"
                . " point_rate, sale_limit, sale_unlimited, deliv_date_id, deliv_fee,"
                . " status, product_flag, create_date, del_flg";
            $from = "vw_products_allclass AS T1";
            $order = "price02_min, product_id";
            break;

        //新着順
        case 'date':
            $col = "DISTINCT create_date, product_id, product_code_min, product_code_max,"
                . " name, comment1, comment2, comment3,"
                . " main_list_comment, main_image, main_list_image,"
                . " price01_min, price01_max, price02_min, price02_max,"
                . " stock_min, stock_max, stock_unlimited_min, stock_unlimited_max,"
                . " point_rate, sale_limit, sale_unlimited, deliv_date_id, deliv_fee,"
                . " status, product_flag, del_flg";
            $from = "vw_products_allclass AS T1";
            $order = "create_date DESC, product_id";
            break;

        default:
            $col = "DISTINCT T1.product_id, product_code_min, product_code_max,"
                . " price01_min, price01_max, price02_min, price02_max,"
                . " stock_min, stock_max, stock_unlimited_min,"
                . " stock_unlimited_max, del_flg, status, name, comment1,"
                . " comment2, comment3, main_list_comment, main_image,"
                . " main_list_image, product_flag, deliv_date_id, sale_limit,"
                . " point_rate, sale_unlimited, create_date, deliv_fee, "
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
            break;
        }

        // 商品検索条件の作成（未削除、表示）
        $where = "del_flg = 0 AND status = 1 ";
        // カテゴリからのWHERE文字列取得
        if ( $category_id ) {
            list($tmp_where, $arrval) = $objDb->sfGetCatWhere($category_id);
            if($tmp_where != "") {
                $where.= " AND $tmp_where";
            }
        }

        // 商品名をwhere文に
        $name = ereg_replace(",", "", $name);// XXX
        // 全角スペースを半角スペースに変換
        $name = str_replace('　', ' ', $name);
        // スペースでキーワードを分割
        $names = preg_split("/ +/", $name);
        // 分割したキーワードを一つずつwhere文に追加
        foreach ($names as $val) {
            if ( strlen($val) > 0 ){
                $where .= " AND ( name ILIKE ? OR comment3 ILIKE ?) ";
                $ret = SC_Utils_Ex::sfManualEscape($val);
                $arrval[] = "%$ret%";
                $arrval[] = "%$ret%";
            }
        }

        if (empty($arrval)) {
            $arrval = array();
        }

        // 行数の取得
        $linemax = count($objQuery->getAll("SELECT DISTINCT product_id "
                                         . "FROM vw_products_allclass AS allcls "
                                         . (!empty($where) ? " WHERE " . $where
                                                           : ""), $arrval));

        $this->tpl_linemax = $linemax;   // 何件が該当しました。表示用

        // ページ送りの取得
        $this->objNavi = new SC_PageNavi($this->tpl_pageno, $linemax, $disp_num, "fnNaviPage", NAVI_PMAX);

        $strnavi = $this->objNavi->strnavi;
        $strnavi = str_replace('onclick="fnNaviPage', 'onclick="form1.mode.value=\''.'\'; fnNaviPage', $strnavi);
        // 表示文字列
        $this->tpl_strnavi = empty($strnavi) ? "&nbsp;" : $strnavi;
        $startno = $this->objNavi->start_row;                 // 開始行

        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setlimitoffset($disp_num, $startno);
        // 表示順序
        $objQuery->setorder($order);

        // 検索結果の取得
        $this->arrProducts = $objQuery->select($col, $from, $where, $arrval);

        // 規格名一覧
        $arrClassName = $objDb->sfGetIDValueList("dtb_class", "class_id", "name");
        // 規格分類名一覧
        $arrClassCatName = $objDb->sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
        // 規格セレクトボックス設定
        if($disp_num == 15) {
            for($i = 0; $i < count($this->arrProducts); $i++) {
                $this->lfMakeSelect($this->arrProducts[$i]['product_id'], $arrClassName, $arrClassCatName);
                // 購入制限数を取得
                $this->lfGetSaleLimit($this->arrProducts[$i]);
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
        if (!isset($_POST[$classcategory_id_2]) || !is_numeric($_POST[$classcategory_id_2])) $_POST[$classcategory_id_2] = "";

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
            $objQuery->setorder("rank1 DESC, rank2 DESC");
            $arrRet = $objQuery->select($col, $table, $where, array($product_id));
        }
        return $arrRet;
    }

    /* 入力内容のチェック */
    function lfCheckError($id) {

        // 入力データを渡す。
        $objErr = new SC_CheckError();

        $classcategory_id1 = "classcategory_id". $id. "_1";
        $classcategory_id2 = "classcategory_id". $id. "_2";
        $quantity = "quantity". $id;
        // 複数項目チェック
        if ($this->tpl_classcat_find1[$id]) {
            $objErr->doFunc(array("規格1", $classcategory_id1, INT_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        }
        if ($this->tpl_classcat_find2[$id]) {
            $objErr->doFunc(array("規格2", $classcategory_id2, INT_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        }
        $objErr->doFunc(array("個数", $quantity, INT_LEN), array("EXIST_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));

        return $objErr->arrErr;
    }

    // 購入制限数の設定
    function lfGetSaleLimit($product) {
        //在庫が無限または購入制限値が設定値より大きい場合
        if($product['sale_unlimited'] == 1 || $product['sale_limit'] > SALE_LIMIT_MAX) {
            $this->tpl_sale_limit[$product['product_id']] = SALE_LIMIT_MAX;
        } else {
            $this->tpl_sale_limit[$product['product_id']] = $product['sale_limit'];
        }
    }

    //支払方法の取得
    //payment_id    1:代金引換　2:銀行振り込み　3:現金書留
    function lfGetPayment() {
        $objQuery = new SC_Query;
        $col = "payment_id, rule, payment_method";
        $from = "dtb_payment";
        $where = "del_flg = 0";
        $order = "payment_id";
        $objQuery->setorder($order);
        $arrRet = $objQuery->select($col, $from, $where);
        return $arrRet;
    }

    function lfconvertParam () {
        foreach ($this->arrForm as $key => $value) {
            if (preg_match('/^quantity[0-9]+/', $key)) {
                 $this->arrForm[$key]
                    = htmlspecialchars($this->arrForm[$key], ENT_QUOTES, CHAR_CODE);
            }
        }
    }
}
?>
