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

namespace Eccube\Page\Rss;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\Product;
use Eccube\Framework\Query;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\View\SiteView;

/**
 * RSS(商品) のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Products extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        $this->skip_load_page_layout = true;
        parent::init();
        $this->tpl_mainpage = 'rss/products.tpl';
        $this->encode = 'UTF-8';
        $this->title = '商品一覧情報';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        $objView = new SiteView();

        //店舗情報をセット
        $this->arrSiteInfo = Application::alias('eccube.helper.db')->getBasisData();

        //商品IDを取得
        if (isset($_GET['product_id']) && $_GET['product_id'] != '' && is_numeric($_GET['product_id'])) {
            $product_id = $_GET['product_id'];
        } else {
            $product_id = '';
        }

        // モードによって分岐
        $mode = $this->getMode();
        switch ($mode) {
            case 'all':
                $arrProducts = $this->lfGetProductsDetailData($mode, $product_id);
                break;
            case 'list':
                if ($product_id != '' && is_numeric($product_id)) {
                    $arrProducts = $this->lfGetProductsDetailData($mode, $product_id);
                } else {
                    $arrProducts = $this->lfGetProductsListData();
                }
                break;
            default:
                if ($product_id != '' && is_numeric($product_id)) {
                    $arrProducts = $this->lfGetProductsDetailData($mode, $product_id);
                } else {
                    $arrProducts = $this->lfGetProductsAllData();
                }
                break;
        }

        // 商品情報をセット
        $this->arrProducts = $arrProducts;
        // 従来互換 (for 2.11)
        $this->arrProduct = &$this->arrProducts;

        //セットしたデータをテンプレートファイルに出力
        $objView->assignobj($this);

        //キャッシュしない(念のため)
        header('Pragma: no-cache');

        //XMLテキスト(これがないと正常にRSSとして認識してくれないツールがあるため)
        header('Content-type: application/xml');
        P_DETAIL_URLPATH;

        //画面表示
        $objView->display($this->tpl_mainpage, true);
    }

    /**
     * lfGetProductsDetailData.
     *
     * @param  str   $mode       モード
     * @param  str   $product_id 商品ID
     * @return array $arrProduct 商品情報の配列を返す
     */
    public function lfGetProductsDetailData($mode, $product_id)
    {
        $objQuery = Application::alias('eccube.query');
        //商品詳細を取得
        if ($mode == 'all') {
            $arrProduct = $this->lfGetProductsDetail($objQuery, $mode);
        } else {
            $arrProduct = $this->lfGetProductsDetail($objQuery, $product_id);
        }
        // 値の整形
        foreach (array_keys($arrProduct) as $key) {
            //販売価格を税込みに編集
            $arrProduct[$key]['price02'] = Application::alias('eccube.helper.db')->calcIncTax($arrProduct[$key]['price02']);
            // 画像ファイルのURLセット
            if (file_exists(IMAGE_SAVE_REALDIR . $arrProduct[$key]['main_list_image'])) {
                $dir = IMAGE_SAVE_RSS_URL;
            } else {
                $dir = IMAGE_TEMP_RSS_URL;
            }
            $arrProduct[$key]['main_list_image'] = $dir . $arrProduct[$key]['main_list_image'];
            if (file_exists(IMAGE_SAVE_REALDIR . $arrProduct[$key]['main_image'])) {
                $dir = IMAGE_SAVE_RSS_URL;
            } else {
                $dir = IMAGE_TEMP_RSS_URL;
            }
            $arrProduct[$key]['main_image'] = $dir . $arrProduct[$key]['main_image'];
            if (file_exists(IMAGE_SAVE_REALDIR . $arrProduct[$key]['main_large_image'])) {
                $dir = IMAGE_SAVE_RSS_URL;
            } else {
                $dir = IMAGE_TEMP_RSS_URL;
            }
            $arrProduct[$key]['main_large_image'] = $dir . $arrProduct[$key]['main_large_image'];
            // ポイント計算
            $arrProduct[$key]['point'] = Utils::sfPrePoint(
                $arrProduct[$key]['price02'],
                $arrProduct[$key]['point_rate']
            );
            // 在庫無制限
            if ($arrProduct[$key]['stock_unlimited'] == 1) {
                $arrProduct[$key]['stock_unlimited'] = '在庫無制限';
            } else {
                $arrProduct[$key]['stock_unlimited'] = NULL;
            }
        }

        return $arrProduct;
    }

    /**
     * lfGetProductsListData.
     *
     * @return array $arrProduct 商品情報の配列を返す
     */
    public function lfGetProductsListData()
    {
        $objQuery = Application::alias('eccube.query');
        //商品一覧を取得
        $arrProduct = $objQuery->getAll('SELECT product_id, name AS product_name FROM dtb_products');

        return $arrProduct;
    }

    /**
     * lfGetProductsAllData.
     *
     * @return array $arrProduct 商品情報の配列を返す
     */
    public function lfGetProductsAllData()
    {
        $objQuery = Application::alias('eccube.query');
        //商品情報を取得
        $arrProduct = $this->lfGetProductsAllclass($objQuery);
        // 値の整形
        foreach (array_keys($arrProduct) as $key) {
            // 画像ファイルのURLセット
            if (file_exists(IMAGE_SAVE_REALDIR . $arrProduct[$key]['main_list_image'])) {
                $dir = IMAGE_SAVE_RSS_URL;
            } else {
                $dir = IMAGE_TEMP_RSS_URL;
            }
            $arrProduct[$key]['main_list_image'] = $dir . $arrProduct[$key]['main_list_image'];
            if (file_exists(IMAGE_SAVE_REALDIR . $arrProduct[$key]['main_image'])) {
                $dir = IMAGE_SAVE_RSS_URL;
            } else {
                $dir = IMAGE_TEMP_RSS_URL;
            }
            $arrProduct[$key]['main_image'] = $dir . $arrProduct[$key]['main_image'];
            if (file_exists(IMAGE_SAVE_REALDIR . $arrProduct[$key]['main_large_image'])) {
                $dir = IMAGE_SAVE_RSS_URL;
            } else {
                $dir = IMAGE_TEMP_RSS_URL;
            }
            $arrProduct[$key]['main_large_image'] = $dir . $arrProduct[$key]['main_large_image'];
            // ポイント計算
            $arrProduct[$key]['point_max'] = Utils::sfPrePoint(
                $arrProduct[$key]['price02_max'],
                $arrProduct[$key]['point_rate']
            );
            $arrProduct[$key]['point_min'] = Utils::sfPrePoint(
                $arrProduct[$key]['price02_min'],
                $arrProduct[$key]['point_rate']
            );
        }

        return $arrProduct;
    }

    /**
     * 商品情報を取得する
     *
     * @param  Query $objQuery   DB操作クラス
     * @param  integer  $product_id 商品ID
     * @return array    $arrProduct 取得結果を配列で返す
     */
    public function lfGetProductsDetail(&$objQuery, $product_id = 'all')
    {
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');

        // --- 商品詳細の取得
        if ($product_id == 'all') {
            $objQuery->setOrder('product_id');
            $arrProductLsit = $objProduct->lists($objQuery);
        } else {
            $arrProductLsit = $objProduct->getListByProductIds($objQuery, array($product_id));
        }

        // 各商品のカテゴリIDとランクの取得
        $arrProduct = array();
        foreach ($arrProductLsit as $key => $val) {
            $sql = '';
            $sql .= ' SELECT';
            $sql .= '   T1.category_id,';
            $sql .= '   T1.rank AS product_rank,';
            $sql .= '   T2.rank AS category_rank';
            $sql .= ' FROM';
            $sql .= '   dtb_product_categories AS T1';
            $sql .= ' LEFT JOIN';
            $sql .= '   dtb_category AS T2';
            $sql .= ' ON';
            $sql .= '   T1.category_id = T2.category_id';
            $sql .= ' WHERE';
            $sql .= '   product_id = ?';
            $arrCategory = $objQuery->getAll($sql, array($val['product_id']));
            if (!empty($arrCategory)) {
                $arrProduct[$key] = array_merge($val, $arrCategory[0]);
            }
        }

        return $arrProduct;
    }

    /**
     * 商品情報を取得する(vw_products_allclass使用)
     *
     * @param  Query $objQuery DB操作クラス
     * @return array    $arrProduct 取得結果を配列で返す
     */
    public function lfGetProductsAllclass(&$objQuery)
    {
        // --- 商品一覧の取得
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');
        $objQuery->setWhere($objProduct->getProductDispConditions());
        $objQuery->setOrder('product_id');
        $arrProductLsit = $objProduct->lists($objQuery);
        // 各商品のカテゴリIDとランクの取得
        $arrProducts = array();
        foreach ($arrProductLsit as $key => $val) {
            $sql = '';
            $sql .= ' SELECT';
            $sql .= '   T1.category_id,';
            $sql .= '   T1.rank AS product_rank,';
            $sql .= '   T2.rank AS category_rank';
            $sql .= ' FROM';
            $sql .= '   dtb_product_categories AS T1';
            $sql .= ' LEFT JOIN';
            $sql .= '   dtb_category AS T2';
            $sql .= ' ON';
            $sql .= '   T1.category_id = T2.category_id';
            $sql .= ' WHERE';
            $sql .= '   product_id = ?';
            $arrCategory = $objQuery->getAll($sql, array($val['product_id']));
            if (!empty($arrCategory)) {
                $arrProducts[$key] = array_merge($val, $arrCategory[0]);
            }
        }

        // 税込金額を設定する
        Application::alias('eccube.product')->setIncTaxToProducts($arrProducts);

        return $arrProducts;
    }
}
