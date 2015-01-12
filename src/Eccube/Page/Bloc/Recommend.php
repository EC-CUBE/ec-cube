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
use Eccube\Framework\Query;
use Eccube\Framework\Product;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\BestProductsHelper;

/**
 * Recommend のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Recommend extends AbstractBloc
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
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
        // 基本情報を渡す
        $objSiteInfo = Application::alias('eccube.helper.db')->getBasisData();
        $this->arrInfo = $objSiteInfo->data;

        //おすすめ商品表示
        $this->arrBestProductsHelper = $this->lfGetRanking();
    }

    /**
     * おすすめ商品検索.
     *
     * @return array $arrBestProductsHelper 検索結果配列
     */
    public function lfGetRanking()
    {
        /* @var $objRecommend BestProductsHelper */
        $objRecommend = Application::alias('eccube.helper.best_products');

        // おすすめ商品取得
        $arrRecommends = $objRecommend->getList(RECOMMEND_NUM);

        $response = array();
        if (count($arrRecommends) > 0) {
            // 商品一覧を取得
            $objQuery = Application::alias('eccube.query');
            /* @var $objProduct Product */
            $objProduct = Application::alias('eccube.product');
            // where条件生成&セット
            $arrProductId = array();
            foreach ($arrRecommends as $key => $val) {
                $arrProductId[] = $val['product_id'];
            }
            $arrProducts = $objProduct->getListByProductIds($objQuery, $arrProductId);

            // 税込金額を設定する
            Application::alias('eccube.product')->setIncTaxToProducts($arrProducts);

            // おすすめ商品情報にマージ
            foreach ($arrRecommends as $key => $value) {
                if (isset($arrProducts[$value['product_id']])) {
                    $product = $arrProducts[$value['product_id']];
                    if ($product['status'] == 1 && (!NOSTOCK_HIDDEN || ($product['stock_max'] >= 1 || $product['stock_unlimited_max'] == 1))) {
                        $response[] = array_merge($value, $arrProducts[$value['product_id']]);
                    }
                } else {
                    // 削除済み商品は除外
                    unset($arrRecommends[$key]);
                }
            }
        }

        return $response;
    }
}
