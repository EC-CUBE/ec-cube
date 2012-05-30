<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_EX_REALDIR . 'page_extends/frontparts/bloc/LC_Page_FrontParts_Bloc_Login_Ex.php';

/**
 * ナビ(ヘッダブロック) のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_FrontParts_Bloc_NaviHeader extends LC_Page_FrontParts_Bloc_Login_Ex {

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
        $this->arrProductType = $masterData->getMasterData('mtb_product_type'); //商品種類を取得
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
        parent::action();

        //ヘッダーナビのカート情報を取得
        $objCart = new SC_CartSession_Ex();
        $cartKeys = $objCart->getKeys();
        $arrInfo = SC_Helper_DB_Ex::sfGetBasisData();
        $this->freeRule = $arrInfo['free_rule'];
        $this->arrCartList = $this->lfGetCartData($objCart, $arrInfo, $cartKeys);
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
     * カートの情報を取得する
     *
     * @param SC_CartSession $objCart カートセッション管理クラス
     * @param Array $arrInfo 基本情報配列
     * @param Array $cartKeys 商品種類配列
     * @return array $arrCartList カートデータ配列
     */
    function lfGetCartData($objCart, $arrInfo, $cartKeys) {
        $cartList = array();
        foreach ($cartKeys as $key) {
            // カート集計処理
            $cartList[$key]['productTypeName'] = $this->arrProductType[$key]; //商品種類名
            $cartList[$key]['totalInctax'] = $objCart->getAllProductsTotal($key); //合計金額
            $cartList[$key]['delivFree'] = $arrInfo['free_rule'] - $cartList[$key]['totalInctax']; // 送料無料までの金額を計算
            $cartList[$key]['totalTax'] = $objCart->getAllProductsTax($key); //消費税合計
            $cartList[$key]['quantity'] = $objCart->getTotalQuantity($key); //商品数量合計
            $cartList[$key]['productTypeId'] = $key; // 商品種別ID
        }

        return $cartList;
    }
}
