<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * MyPage のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_MyPage_Favorite extends LC_Page_AbstractMypage_Ex {

    // {{{ properties

    /** ページナンバー */
    var $tpl_pageno;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_subtitle = 'お気に入り一覧';
        $this->tpl_mypageno = 'favorite';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {
        $objProduct  = new SC_Product_Ex();
        $objCustomer = new SC_Customer_Ex();
        $customer_id = $objCustomer->getValue('customer_id');

        switch ($this->getMode()) {
            case 'delete_favorite':
                // お気に入り削除
                $this->lfDeleteFavoriteProduct($customer_id, intval($_POST['product_id']));
                break;
            case 'getList':
                // スマートフォン版のもっと見るボタン用
                // ページ送り用
                if (isset($_POST['pageno'])) {
                    $this->tpl_pageno = intval($_POST['pageno']);
                }
                $this->arrFavorite = $this->lfGetFavoriteProduct($customer_id, $this);
                $this->arrFavorite = $objProduct->setPriceTaxTo($this->arrFavorite);
                echo SC_Utils_Ex::jsonEncode($this->arrFavorite);
                exit;
                break;
        }

        // ページ送り用
        if (isset($_POST['pageno'])) {
            $this->tpl_pageno = intval($_POST['pageno']);
        }
        $this->arrFavorite = $this->lfGetFavoriteProduct($customer_id, $this);
        // 1ページあたりの件数
        $this->dispNumber = SEARCH_PMAX;
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
     * お気に入りを取得する
     *
     * @param mixed $customer_id
     * @param mixed $objPage
     * @access private
     * @return array お気に入り商品一覧
     */
    function lfGetFavoriteProduct($customer_id, &$objPage) {
        $objQuery       = SC_Query_Ex::getSingletonInstance();
        $objProduct     = new SC_Product_Ex();

        $objQuery->setOrder('create_date DESC');
        $arrProduct_id  = $objQuery->getCol('product_id', 'dtb_customer_favorite_products', 'customer_id = ?', array($customer_id));

        $objQuery       =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setWhere($this->lfMakeWhere('alldtl.', $arrProduct_id));
        $linemax        = $objProduct->findProductCount($objQuery);

        $objPage->tpl_linemax = $linemax;   // 何件が該当しました。表示用

        // ページ送りの取得
        $objNavi        = new SC_PageNavi_Ex($objPage->tpl_pageno, $linemax, SEARCH_PMAX, 'fnNaviPage', NAVI_PMAX);
        $this->tpl_strnavi = $objNavi->strnavi; // 表示文字列
        $startno        = $objNavi->start_row;

        $objQuery       =& SC_Query_Ex::getSingletonInstance();
        //$objQuery->setLimitOffset(SEARCH_PMAX, $startno);
        // 取得範囲の指定(開始行番号、行数のセット)
        $arrProduct_id  = array_slice($arrProduct_id, $startno, SEARCH_PMAX);

        $objQuery->setWhere($this->lfMakeWhere('', $arrProduct_id));
        $objProduct->setProductsOrder('create_date', 'dtb_customer_favorite_products', 'DESC');
        $arrProducts    = $objProduct->lists($objQuery, $arrProduct_id);

        //取得している並び順で並び替え
        $arrProducts2 = array();
        foreach($arrProducts as $item) {
            $arrProducts2[ $item['product_id'] ] = $item;
        }
        $arrProductsList = array();
        foreach($arrProduct_id as $product_id) {
            $arrProductsList[] = $arrProducts2[$product_id];
        }

        return $arrProductsList;
    }

    /* 仕方がない処理。。 */
    function lfMakeWhere ($tablename, $arrProduct_id) {

        // 取得した表示すべきIDだけを指定して情報を取得。
        $where = "";
        if (is_array($arrProduct_id) && !empty($arrProduct_id)) {
            $where = $tablename . 'product_id IN (' . implode(',', $arrProduct_id) . ')';
        } else {
            // 一致させない
            $where = '0<>0';
        }
        // 在庫無し商品の非表示
        if (NOSTOCK_HIDDEN === true) {
            $where .= ' AND (stock_max >= 1 OR stock_unlimited_max = 1)';
        }
        return $where;
    }

    // お気に入り商品削除
    function lfDeleteFavoriteProduct($customer_id, $product_id) {
        $objQuery   = new SC_Query_Ex();
        $count      = $objQuery->count("dtb_customer_favorite_products", "customer_id = ? AND product_id = ?", array($customer_id, $product_id));

        if ($count > 0) {
            $objQuery->begin();
            $objQuery->delete('dtb_customer_favorite_products', "customer_id = ? AND product_id = ?", array($customer_id, $product_id));
            $objQuery->commit();
        }
    }
}
