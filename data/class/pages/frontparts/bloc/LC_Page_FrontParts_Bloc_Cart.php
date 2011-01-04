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
require_once(CLASS_REALDIR . "pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php");

/**
 * カート のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_FrontParts_Bloc_Cart.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_FrontParts_Bloc_Cart extends LC_Page_FrontParts_Bloc {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->setTplMainpage('cart.tpl');
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
        $objCart = new SC_CartSession();
        $objSiteInfo = new SC_SiteInfo;

        $cartKeys = $objCart->getKeys();
        foreach ($cartKeys as $cartKey) {

            // カート情報を取得
            $arrCartList = $objCart->getCartList($cartKey);

            // カート内の商品ＩＤ一覧を取得
            $arrAllProductID = $objCart->getAllProductID($cartKey);
            // 商品が1つ以上入っている場合には商品名称を取得
            if (count($arrCartList) > 0){

                foreach($arrCartList['productsClass'] as $key => $val){
                    $arrCartList[$key]['product_name'] = $val['name'];
                }
            }
            // 購入金額合計
            $ProductsTotal += $objCart->getAllProductsTotal($cartKey);
            // 合計数量
            $TotalQuantity += $objCart->getTotalQuantity($cartKey);

        }

        // 店舗情報の取得
        $arrInfo = $objSiteInfo->data;

        // 送料無料までの金額
        $arrCartList[0]['ProductsTotal'] = $ProductsTotal;
        $arrCartList[0]['TotalQuantity'] = $TotalQuantity;
        /*
         * FIXME
         * 商品種別ごとに送料無料までの金額を計算するよう要修正
         */
        $deliv_free = $arrInfo['free_rule'] - $ProductsTotal;
        $arrCartList[0]['free_rule'] = $arrInfo['free_rule'];
        $arrCartList[0]['deliv_free'] = $deliv_free;

        $this->arrCartList = $arrCartList;
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
}
?>
