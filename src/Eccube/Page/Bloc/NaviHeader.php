<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Page\Bloc;

use Eccube\Application;
use Eccube\Framework\CartSession;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Db\MasterData;

/**
 * ナビ(ヘッダブロック) のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class NaviHeader extends Login
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
        parent::action();

        //ヘッダーナビのカート情報を取得
        /* @var $objCart CartSession */
        $objCart = Application::alias('eccube.cart_session');
        $cartKeys = $objCart->getKeys();
        $arrInfo = Application::alias('eccube.helper.db')->getBasisData();
        $this->freeRule = $arrInfo['free_rule'];
        $this->arrCartList = $this->lfGetCartData($objCart, $arrInfo, $cartKeys);
    }

    /**
     * カートの情報を取得する
     *
     * @param  CartSession $objCart  カートセッション管理クラス
     * @param  Array          $arrInfo  基本情報配列
     * @param  Array          $cartKeys 商品種類配列
     * @return array          $arrCartList カートデータ配列
     */
    public function lfGetCartData(CartSession $objCart, $arrInfo, $cartKeys)
    {
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
