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

namespace Eccube\Page\Mypage;

use Eccube\Application;
use Eccube\Framework\Customer;
use Eccube\Framework\PageNavi;
use Eccube\Framework\Product;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\Util\Utils;

/**
 * MyPage のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Favorite extends AbstractMypage
{
    /** ページナンバー */
    public $tpl_pageno;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_subtitle = 'お気に入り一覧';
        $this->tpl_mypageno = 'favorite';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    public function action()
    {
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');

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
                Application::alias('eccube.product')->setPriceTaxTo($this->arrFavorite);


                // 一覧メイン画像の指定が無い商品のための処理
                foreach ($this->arrFavorite as $key => $val) {
                    $this->arrFavorite[$key]['main_list_image'] = Utils::sfNoImageMainList($val['main_list_image']);
                }

                echo Utils::jsonEncode($this->arrFavorite);
                Application::alias('eccube.response')->actionExit();
                break;

            default:
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
     * お気に入りを取得する
     *
     * @param mixed $customer_id
     * @param LC_Page_Mypage_Favorite $objPage
     * @access private
     * @return array お気に入り商品一覧
     */
    public function lfGetFavoriteProduct($customer_id, &$objPage)
    {
        $objQuery       = Application::alias('eccube.query');
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');

        $objQuery->setOrder('f.create_date DESC');
        $where = 'f.customer_id = ? and p.status = 1';
        if (NOSTOCK_HIDDEN) {
            $where .= ' AND EXISTS(SELECT * FROM dtb_products_class WHERE product_id = f.product_id AND del_flg = 0 AND (stock >= 1 OR stock_unlimited = 1))';
        }
        $arrProductId  = $objQuery->getCol('f.product_id', 'dtb_customer_favorite_products f inner join dtb_products p using (product_id)', $where, array($customer_id));

        $objQuery       = Application::alias('eccube.query');
        $objQuery->setWhere($this->lfMakeWhere('alldtl.', $arrProductId));
        $linemax        = $objProduct->findProductCount($objQuery);

        $objPage->tpl_linemax = $linemax;   // 何件が該当しました。表示用

        // ページ送りの取得
        /* @var $objNavi PageNavi */
        $objNavi = Application::alias('eccube.page_navi', $objPage->tpl_pageno, $linemax, SEARCH_PMAX, 'eccube.movePage', NAVI_PMAX);
        $this->tpl_strnavi = $objNavi->strnavi; // 表示文字列
        $startno        = $objNavi->start_row;

        $objQuery       = Application::alias('eccube.query');
        //$objQuery->setLimitOffset(SEARCH_PMAX, $startno);
        // 取得範囲の指定(開始行番号、行数のセット)
        $arrProductId  = array_slice($arrProductId, $startno, SEARCH_PMAX);

        $where = $this->lfMakeWhere('', $arrProductId);
        $where .= ' AND del_flg = 0';
        $objQuery->setWhere($where, $arrProductId);
        $arrProducts = $objProduct->lists($objQuery);

        //取得している並び順で並び替え
        $arrProducts2 = array();
        foreach ($arrProducts as $item) {
            $arrProducts2[$item['product_id']] = $item;
        }
        $arrProductsList = array();
        foreach ($arrProductId as $product_id) {
            $arrProductsList[] = $arrProducts2[$product_id];
        }

        // 税込金額を設定する
        Application::alias('eccube.product')->setIncTaxToProducts($arrProductsList);

        return $arrProductsList;
    }

    /* 仕方がない処理。。 */

    /**
     * @param string $tablename
     */
    public function lfMakeWhere($tablename, $arrProductId)
    {
        // 取得した表示すべきIDだけを指定して情報を取得。
        $where = '';
        if (is_array($arrProductId) && !empty($arrProductId)) {
            $where = $tablename . 'product_id IN (' . implode(',', $arrProductId) . ')';
        } else {
            // 一致させない
            $where = '0<>0';
        }

        return $where;
    }

    // お気に入り商品削除

    /**
     * @param integer $product_id
     */
    public function lfDeleteFavoriteProduct($customer_id, $product_id)
    {
        $objQuery = Application::alias('eccube.query');

        $exists = $objQuery->exists('dtb_customer_favorite_products', 'customer_id = ? AND product_id = ?', array($customer_id, $product_id));

        if ($exists) {
            $objQuery->delete('dtb_customer_favorite_products', 'customer_id = ? AND product_id = ?', array($customer_id, $product_id));
        }
    }
}
