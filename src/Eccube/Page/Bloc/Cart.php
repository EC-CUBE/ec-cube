<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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


namespace Eccube\Page\Bloc;

use Eccube\Application;
use Eccube\Framework\CartSession;
use Eccube\Framework\Db\MasterData;
use Eccube\Framework\Helper\DbHelper;

/**
 * カート のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Cart extends AbstractBloc
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrProductType = $masterData->getMasterData('mtb_product_type'); //商品種類を取得
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        /* @var $objCartSess CartSession */
        $objCart = Application::alias('eccube.cart_session');
        $this->isMultiple = $objCart->isMultiple();
        $this->hasDownload = $objCart->hasProductType(PRODUCT_TYPE_DOWNLOAD);
        // 旧仕様との互換のため、不自然なセットとなっている
        $this->arrCartList = array(0 => $this->lfGetCartData($objCart));
    }

    /**
     * カートの情報を取得する
     *
     * @param  Eccube\CartSession $objCart カートセッション管理クラス
     * @return array          カートデータ配列
     */
    public function lfGetCartData(CartSession &$objCart)
    {
        $arrCartKeys = $objCart->getKeys();
        foreach ($arrCartKeys as $cart_key) {
            // 購入金額合計
            $products_total += $objCart->getAllProductsTotal($cart_key);
            // 合計数量
            $total_quantity += $objCart->getTotalQuantity($cart_key);

            // 送料無料チェック
            if (!$this->isMultiple && !$this->hasDownload) {
                $is_deliv_free = $objCart->isDelivFree($cart_key);
            }
        }

        $arrCartList = array();

        $arrCartList['ProductsTotal'] = $products_total;
        $arrCartList['TotalQuantity'] = $total_quantity;

        // 店舗情報の取得
        $arrInfo = Application::alias('eccube.helper.db')->getBasisData();
        $arrCartList['free_rule'] = $arrInfo['free_rule'];

        // 送料無料までの金額
        if ($is_deliv_free) {
            $arrCartList['deliv_free'] = 0;
        } else {
            $deliv_free = $arrInfo['free_rule'] - $products_total;
            $arrCartList['deliv_free'] = $deliv_free;
        }

        return $arrCartList;
    }
}
