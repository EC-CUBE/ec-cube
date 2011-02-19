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
require_once(CLASS_REALDIR . "pages/LC_Page.php");

/**
 * 商品一覧 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
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
     * Page のAction.
     *
     * @return void
     */
    function action() {
        $objQuery =& SC_Query::getSingletonInstance();
        $objProduct = new SC_Product();

        $this->arrForm = $_REQUEST;
        //modeの取得
        $this->mode = $this->getMode();
        
        //表示条件の取得
        $this->arrSearchData = array(
            'category_id' => $this->lfGetCategoryId(intval($this->arrForm['category_id'])),
            'maker_id'=>intval($this->arrForm['maker_id']),
            'name'=>$this->arrForm['name']
        );
        $this->orderby = $this->arrForm['orderby'];
        
        //ページング設定
        $this->tpl_pageno = $this->arrForm['pageno'];
        $this->disp_number = $this->lfGetDisplayNum($this->arrForm['disp_number']);

        // 画面に表示するサブタイトルの設定
        $this->tpl_subtitle = $this->lfGetPageTitle($this->mode,$this->arrSearchData['category_id']);

        // 画面に表示する検索条件を設定
        $this->arrSearch = $this->lfGetSearchConditionDisp($this->arrSearchData);

        // 商品一覧データの取得
        $arrSearchCondition = $this->lfGetSearchCondition($this->arrSearchData);
        $this->tpl_linemax = $this->lfGetProductAllNum($arrSearchCondition);
        $urlParam = "category_id={$this->arrSearchData['category_id']}&pageno=#page#";
        $this->objNavi = new SC_PageNavi($this->tpl_pageno, $this->tpl_linemax, $this->disp_number, "fnNaviPage", NAVI_PMAX, $urlParam,SC_Display::detectDevice() !== DEVICE_TYPE_MOBILE);
        $this->arrProducts = $this->lfGetProductsList($arrSearchCondition,$this->disp_number,$this->objNavi->start_row,$this->tpl_linemax,&$objProduct);
        //商品一覧の表示処理
        $strnavi = $this->objNavi->strnavi;
        // 表示文字列
        $this->tpl_strnavi = empty($strnavi) ? "&nbsp;" : $strnavi;

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
        $this->productStatus = $this->arrProducts["productStatus"];
        unset($this->arrProducts["productStatus"]);

        $objJson = new Services_JSON();
        $this->tpl_javascript .= 'var productsClassCategories = ' . $objJson->encode($objProduct->classCategories) . ';\n';

        //onloadスクリプトを設定
        foreach ($this->arrProducts as $arrProduct) {
            $js_fnOnLoad .= "fnSetClassCategories(document.product_form{$arrProduct['product_id']});\n";
        }

        //カート処理
        $target_product_id = intval($this->arrForm['product_id']);
        if ( $target_product_id > 0) {
            // 商品IDの正当性チェック
            if (!SC_Utils_Ex::sfIsInt($this->arrForm['product_id']) || !SC_Helper_DB_Ex::sfIsRecord("dtb_products", "product_id", $this->arrForm['product_id'], "del_flg = 0 AND status = 1")) {
                SC_Utils_Ex::sfDispSiteError(PRODUCT_NOT_FOUND);
            }

            // 入力内容のチェック
            $arrErr = $this->lfCheckError($target_product_id,&$this->arrForm,$this->tpl_classcat_find1,$this->tpl_classcat_find2);
            if (count($arrErr) == 0) {
                $this->lfAddCart($this->arrForm,$this->tpl_classcat_find1,$this->tpl_classcat_find2,$target_product_id);
                exit;
            }
            $js_fnOnLoad .= $this->lfSetSelectedData(&$this->arrProducts,$this->arrForm,$arrErr,$target_product_id);
        }

        // ページャ用データ設定(モバイル)
        if (Net_UserAgent_Mobile::isMobile() === true) {
            $this->tpl_previous_page = $this->objNavi->arrPagenavi['before'];
            $this->tpl_next_page =  $this->objNavi->arrPagenavi['next'];
        }

        $this->tpl_javascript .= 'function fnOnLoad(){' . $js_fnOnLoad . '}'."\n";
        $this->tpl_onload .= 'fnOnLoad(); ';

        $this->tpl_rnd = SC_Utils_Ex::sfGetRandomString(3);
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
            || !SC_Helper_DB_Ex::sfIsRecord('dtb_category', 'category_id', (array)$category_id, 'del_flg = 0')
        ) {
            SC_Utils_Ex::sfDispSiteError(CATEGORY_NOT_FOUND);
    }

        // 指定されたカテゴリIDを元に正しいカテゴリIDを取得する。
        $arrCategory_id = SC_Helper_DB_Ex::sfGetCategoryId('', $category_id);

        if (empty($arrCategory_id)) {
            SC_Utils_Ex::sfDispSiteError(CATEGORY_NOT_FOUND);
        }

        return $arrCategory_id[0];
    }

    /* 商品一覧の表示 */
    function lfGetProductsList($searchCondition,$disp_number,$startno,$linemax,&$objProduct) {

        $arrval_order = array();

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
                if (strlen($searchCondition["where_category"]) >= 1) {
                    $dtb_product_categories = "(SELECT * FROM dtb_product_categories WHERE ".$searchCondition["where_category"].")";
                    $arrval_order = $searchCondition["arrvalCategory"];
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
                    //$objQuery->setOrder($order);
                break;
        }
        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($disp_number, $startno);
        $objQuery->setWhere($searchCondition["where"]);

         // 表示すべきIDとそのIDの並び順を一気に取得
        $arrProduct_id = $objProduct->findProductIdsOrder($objQuery, array_merge($searchCondition["arrval"], $arrval_order));

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
        $arrProducts = array();
        foreach($arrProduct_id as $product_id) {
            $arrProducts[] = $arrProducts2[$product_id];
        }
        
        // 規格を設定
        $objProduct->setProductsClassByProductIds($arrProduct_id);
        $arrProducts += array("productStatus"=>$objProduct->getProductStatus($arrProduct_id));     
        return $arrProducts;
    }

    /* 入力内容のチェック */
    function lfCheckError($product_id,&$arrForm,$tpl_classcat_find1,$tpl_classcat_find2) {

        // 入力データを渡す。
        $objErr = new SC_CheckError($arrForm);

        // 複数項目チェック
        if ($tpl_classcat_find1[$product_id]) {
            $objErr->doFunc(array("規格1", 'classcategory_id1', INT_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        }
        if ($tpl_classcat_find2[$product_id]) {
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
    function lfGetDisplayNum($display_number) {
        // 表示件数
        if (!isset($display_number)
            OR !SC_Utils_Ex::sfIsInt($display_number)
        ) {
            //最小表示件数を選択
            return current(array_keys($this->arrPRODUCTLISTMAX));
        }
        return $display_number;
    }

    /**
     * ページタイトルの設定
     *
     * @return str
     */    
    function lfGetPageTitle($mode,$category_id = 0){
        if ($mode == 'search') {
            return "検索結果";
        } elseif ($category_id == 0) {
            return "全商品";
        } else {
            $arrCat = SC_Helper_DB_Ex::sfGetCat($category_id);
            return $arrCat['name'];
        }
        return "";       
    }

    /**
     * 表示用検索条件の設定
     *
     * @return array
     */    
    function lfGetSearchConditionDisp($arrSearchData){
        $objQuery =& SC_Query::getSingletonInstance();
        $arrSearch = array('category'=>"指定なし",'maker'=>"指定なし",'name'=>"指定なし");
        // カテゴリー検索条件
        if ($arrSearchData['category_id'] > 0) {
            $arrSearch['category'] = $objQuery->getOne("SELECT category_name FROM dtb_category WHERE category_id = ?", array($arrSearchData['category_id']));
        }

        // メーカー検索条件
        if (strlen($arrSearchData['maker_id']) > 0) {
            $arrSearch['maker'] = $objQuery->getOne("SELECT name FROM dtb_maker WHERE maker_id = ?", array($arrSearchData['maker_id']));
        }

        // 商品名検索条件
        if (strlen($arrSearchData['name']) > 0) {
            $arrSearch['name'] = $arrSearchData['name'];
        }
        return $arrSearch;
    }
    
    /**
     * 該当件数の取得
     *
     * @return int
     */    
    function lfGetProductAllNum($searchCondition){
        // 検索結果対象となる商品の数を取得
        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->setWhere($searchCondition["where"]);
        $objProduct = new SC_Product();
        return $objProduct->findProductCount($objQuery, $searchCondition["arrval"]);
    }
    
    /**
     * 検索条件のwhere文とかを取得
     *
     * @return array
     */    
    function lfGetSearchCondition($arrSearchData){
        $searchCondition = array(
            "where"=>"",
            "arrval"=>array(),
            "where_category"=>"",
            "arrvalCategory"=>array()
        );
        
        // カテゴリからのWHERE文字列取得
        if ($arrSearchData["category_id"] != 0) {
            list($searchCondition["where_category"], $searchCondition["arrvalCategory"]) = SC_Helper_DB_Ex::sfGetCatWhere($arrSearchData["category_id"]);
        }
        // ▼対象商品IDの抽出
        // 商品検索条件の作成（未削除、表示）
        $searchCondition["where"] = "alldtl.del_flg = 0 AND alldtl.status = 1 ";

        // 在庫無し商品の非表示
        if (NOSTOCK_HIDDEN === true) {
            $searchCondition["where"] .= ' AND (stock >= 1 OR stock_unlimited = 1)';
        }

        if (strlen($searchCondition["where_category"]) >= 1) {
            $searchCondition["where"] .= " AND T2.".$searchCondition["where_category"];
            $searchCondition["arrval"] = array_merge($searchCondition["arrval"], $searchCondition["arrvalCategory"]);
        }

        // 商品名をwhere文に
        $name = $arrSearchData['name'];
        $name = ereg_replace(",", "", $name);// XXX
        // 全角スペースを半角スペースに変換
        $name = str_replace('　', ' ', $name);
        // スペースでキーワードを分割
        $names = preg_split("/ +/", $name);
        // 分割したキーワードを一つずつwhere文に追加
        foreach ($names as $val) {
            if ( strlen($val) > 0 ) {
                $searchCondition["where"] .= " AND ( alldtl.name ILIKE ? OR alldtl.comment3 ILIKE ?) ";
                $searchCondition["arrval"][] = "%$val%";
                $searchCondition["arrval"][] = "%$val%";
            }
        }

        // メーカーらのWHERE文字列取得
        if ($arrSearchData['maker_id']) {
            $searchCondition["where"] .= " AND alldtl.maker_id = ? ";
            $searchCondition["arrval"][] = $arrSearchData['maker_id'];
        }
        return $searchCondition;
    }

    /**
     * カートに入れる商品情報にエラーがあったら戻す
     *
     * @return str
     */   
    function lfSetSelectedData(&$arrProducts,$arrForm,$arrErr,$product_id){
        $js_fnOnLoad = "";
        foreach (array_keys($arrProducts) as $key) {
            if ($arrProducts[$key]['product_id'] == $product_id) {
                $arrProducts[$key]['product_class_id'] = $arrForm['product_class_id'];
                $arrProducts[$key]['classcategory_id1'] = $arrForm['classcategory_id1'];
                $arrProducts[$key]['classcategory_id2'] = $arrForm['classcategory_id2'];
                $arrProducts[$key]['quantity'] = $arrForm['quantity'];
                $arrProducts[$key]['arrErr'] = $arrErr;
                $js_fnOnLoad .= "fnSetClassCategories(document.product_form{$arrProducts[$key]['product_id']}, '{$arrForm['classcategory_id2']}');\n";
            }
        }
        return $js_fnOnLoad;
    }
    
    /**
     * カートに商品を追加
     *
     * @return void
     */   
    function lfAddCart($arrForm,$tpl_classcat_find1,$tpl_classcat_find2,$target_product_id){
        $classcategory_id1 = $arrForm['classcategory_id1'];
        $classcategory_id2 = $arrForm['classcategory_id2'];
        // 規格1が設定されていない場合
        if (!$tpl_classcat_find1[$target_product_id]) {
            $classcategory_id1 = '0';
        }
        // 規格2が設定されていない場合
        if (!$tpl_classcat_find2[$target_product_id]) {
            $classcategory_id2 = '0';
        }

        // 規格IDを取得
        $product_class_id = $arrForm['product_class_id'];
        $product_type = $arrForm['product_type'];
        $objCartSess = new SC_CartSession();
        $objCartSess->addProduct($product_class_id, $arrForm['quantity'], $product_type);

        // カート「戻るボタン」用に保持
        if (SC_Utils_Ex::sfIsInternalDomain($_SERVER['HTTP_REFERER'])) {
            $_SESSION['cart_referer_url'] = $_SERVER['HTTP_REFERER'];
        }

        SC_Response_Ex::sendRedirect(CART_URLPATH);
    }
}
?>
