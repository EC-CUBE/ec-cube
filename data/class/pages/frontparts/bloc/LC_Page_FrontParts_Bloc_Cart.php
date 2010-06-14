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
require_once(CLASS_PATH . "pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php");

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
        $bloc_file = 'cart.tpl';
        $this->setTplMainpage($bloc_file);
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objSubView = new SC_SiteView(false);
        $objCart = new SC_CartSession();
        $objSiteInfo = new SC_SiteInfo;

        if (count($_SESSION[$objCart->key]) > 0){
            // カート情報を取得
            $arrCartList = $objCart->getCartList();

            // カート内の商品ＩＤ一覧を取得
            $arrAllProductID = $objCart->getAllProductID();
            // 商品が1つ以上入っている場合には商品名称を取得
            if (count($arrAllProductID) > 0){
                $objQuery = new SC_Query();
                $arrVal = array();
                $sql = "";
                $sql = "SELECT name FROM dtb_products WHERE product_id IN ( ?";
                $arrVal = array($arrAllProductID[0]);
                for($i = 1 ; $i < count($arrAllProductID) ; $i++){
                    $sql.= " ,? ";
                    array_push($arrVal, $arrAllProductID[$i]);
                }
                $sql.= " )";

                $arrProduct_name = $objQuery->getAll($sql, $arrVal);

                foreach($arrProduct_name as $key => $val){
                    $arrCartList[$key]['product_name'] = $val['name'];
                }
            }
            // 店舗情報の取得
            $arrInfo = $objSiteInfo->data;
            // 購入金額合計
            $ProductsTotal = $objCart->getAllProductsTotal();

            // 合計数量
            $TotalQuantity = $objCart->getTotalQuantity();

            // 送料無料までの金額
            $arrCartList[0]['ProductsTotal'] = $ProductsTotal;
            $arrCartList[0]['TotalQuantity'] = $TotalQuantity;
            $deliv_free = $arrInfo['free_rule'] - $ProductsTotal;
            $arrCartList[0]['free_rule'] = $arrInfo['free_rule'];
            $arrCartList[0]['deliv_free'] = $deliv_free;

            $this->arrCartList = $arrCartList;
        }

        $objSubView->assignobj($this);
        $objSubView->display($this->tpl_mainpage);
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
