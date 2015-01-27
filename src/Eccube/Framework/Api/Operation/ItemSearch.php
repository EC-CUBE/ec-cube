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

namespace Eccube\Framework\Api\Operation;

use Eccube\Application;
use Eccube\Framework\PageNavi;
use Eccube\Framework\Product;
use Eccube\Framework\Query;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Util\Utils;

/**
 * APIの基本クラス
 *
 * @package Api
 * @author LOCKON CO.,LTD.
 */
class ItemSearch extends Base
{
    protected $operation_name = 'ItemSearch';
    protected $operation_description = '商品検索・商品一覧情報を取得します。';
    protected $default_auth_types = self::API_AUTH_TYPE_OPEN;
    protected $default_enable = '1';
    protected $default_is_log = '0';
    protected $default_sub_data = '';

    public function doAction($arrParam)
    {
        $arrRequest = $this->doInitParam($arrParam);
        if (!$this->isParamError()) {
            $masterData                 = Application::alias('eccube.db.master_data');
            $arrSTATUS            = $masterData->getMasterData('mtb_status');
            $arrSTATUS_IMAGE      = $masterData->getMasterData('mtb_status_image');

            /* @var $objProduct Product */
            $objProduct = Application::alias('eccube.product');
            $arrSearchData = array(
                'category_id' => $arrRequest['BrowseNode'],
                'maker_name' => $arrRequest['Manufacturer'],
                'name' => $arrRequest['Keywords'],
                'orderby' => $arrRequest['Sort'],
            );

            $arrSearchCondition = $this->getSearchCondition($arrSearchData);
            $disp_number = 10;

            $objQuery = Application::alias('eccube.query');
            $objQuery->setWhere($arrSearchCondition['where_for_count']);
            /* @var $objProduct Product */
            $objProduct = Application::alias('eccube.product');
            $linemax = $objProduct->findProductCount($objQuery, $arrSearchCondition['arrval']);
            /* @var $objNavi PageNavi */
            $objNavi = Application::alias('eccube.page_navi', $arrRequest['ItemPage'], $linemax, $disp_number);
            $arrProducts = $this->getProductsList($arrSearchCondition, $disp_number, $objNavi->start_row, $linemax, $objProduct);

            if (!Utils::isBlank($arrProducts)) {
                $arrProducts = $this->setStatusDataTo($arrProducts, $arrSTATUS, $arrSTATUS_IMAGE);
                Application::alias('eccube.product')->setPriceTaxTo($arrProducts);
                foreach ($arrProducts as $key=>$val) {
                    $arrProducts[$key]['main_list_image'] = Utils::sfNoImageMainList($val['main_list_image']);
                }

                $arrData = array();
                foreach ($arrProducts as $key => $val) {
                    $arrData[] = array(
                        'product_id' => $val['product_id'],
                        'DetailPageURL' => HTTP_URL . 'products/detail.php?product_id=' . $val['product_id'],
                        'ItemAttributes' => $val
                        );
                }
                $this->setResponse('Item', $arrData);

                return true;
            } else {
                $this->addError('ItemSearch.Error', '※ 要求された情報は見つかりませんでした。');
            }
        }

        return false;
    }

    protected function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('カテゴリID', 'BrowseNode', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('キーワード', 'Keywords', STEXT_LEN, 'a', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('メーカー名', 'Manufacturer', STEXT_LEN, 'a', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ページ番号', 'ItemPage', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ソート', 'Sort', STEXT_LEN, 'a', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
    }

    public function getResponseGroupName()
    {
        return 'Items';
    }

    /**
     * 商品一覧の取得
     *
     * @param integer $disp_number
     * @param integer $linemax
     * @param Product $objProduct
     * @return array
     * TODO: LC_Page_Products_List::lfGetProductsList() と共通化
     */
    protected function getProductsList($searchCondition, $disp_number, $startno, $linemax, &$objProduct)
    {
        $objQuery = Application::alias('eccube.query');

        $arrOrderVal = array();

        // 表示順序
        switch ($searchCondition['orderby']) {
            // 販売価格が安い順
            case 'price':
                $objProduct->setProductsOrder('price02', 'dtb_products_class', 'ASC');
                break;
            // 販売価格が高い順
            case '-price':
                $objProduct->setProductsOrder('price02', 'dtb_products_class', 'DESC');
                break;

            // 新着順
            case 'releasedate':
            case 'date':
                $objProduct->setProductsOrder('create_date', 'dtb_products', 'DESC');
                break;

            // 新着順
            case 'releasedate':
            case 'date':
                $objProduct->setProductsOrder('create_date', 'dtb_products', 'ASC');
                break;

            default:
                if (strlen($searchCondition['where_category']) >= 1) {
                    $dtb_product_categories = '(SELECT * FROM dtb_product_categories WHERE '.$searchCondition['where_category'].')';
                    $arrOrderVal           = $searchCondition['arrvalCategory'];
                } else {
                    $dtb_product_categories = 'dtb_product_categories';
                }
                $col = 'T3.rank * 2147483648 + T2.rank';
                $from = "$dtb_product_categories T2 JOIN dtb_category T3 ON T2.category_id = T3.category_id";
                $where = 'T2.product_id = alldtl.product_id';
                $objQuery->setOrder('T3.rank DESC, T2.rank DESC');
                $sub_sql = $objQuery->getSql($col, $from, $where);
                $sub_sql = $objQuery->dbFactory->addLimitOffset($sub_sql, 1);

                $objQuery->setOrder("($sub_sql) DESC ,product_id DESC");
                break;
        }
        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($disp_number, $startno);
        $objQuery->setWhere($searchCondition['where']);

        // 表示すべきIDとそのIDの並び順を一気に取得
        $arrProductId = $objProduct->findProductIdsOrder($objQuery, array_merge($searchCondition['arrval'], $arrOrderVal));

        $objQuery = Application::alias('eccube.query');
        $arrProducts = $objProduct->getListByProductIds($objQuery, $arrProductId);
        // 規格を設定
        $objProduct->setProductsClassByProductIds($arrProductId);
        $arrProducts['productStatus'] = $objProduct->getProductStatus($arrProductId);

        return $arrProducts;
    }

    /**
     * 検索条件のwhere文とかを取得
     *
     * @return array
     * TODO: LC_Page_Products_List:;lfGetSearchCondition() と共通化
     */
    protected function getSearchCondition($arrSearchData)
    {
        $searchCondition = array(
            'where'             => '',
            'arrval'            => array(),
            'where_category'    => '',
            'arrvalCategory'    => array(),
            'orderby'           => ''
        );

        // カテゴリからのWHERE文字列取得
        if (!Utils::isBlank($arrSearchData['category_id'])) {
            list($searchCondition['where_category'], $searchCondition['arrvalCategory']) = Application::alias('eccube.helper.db')->getCatWhere($arrSearchData['category_id']);
        }
        // ▼対象商品IDの抽出
        // 商品検索条件の作成（未削除、表示）
        $searchCondition['where'] = 'alldtl.del_flg = 0 AND alldtl.status = 1 ';

        if (strlen($searchCondition['where_category']) >= 1) {
            $searchCondition['where'] .= ' AND EXISTS (SELECT * FROM dtb_product_categories WHERE ' . $searchCondition['where_category'] . ' AND product_id = alldtl.product_id)';
            $searchCondition['arrval'] = array_merge($searchCondition['arrval'], $searchCondition['arrvalCategory']);
        }

        // 商品名をwhere文に
        $name = $arrSearchData['name'];
        $name = str_replace(',', '', $name);
        // 全角スペースを半角スペースに変換
        $name = str_replace('　', ' ', $name);
        // スペースでキーワードを分割
        $names = preg_split('/ +/', $name);
        // 分割したキーワードを一つずつwhere文に追加
        foreach ($names as $val) {
            if (strlen($val) > 0) {
                $searchCondition['where']    .= ' AND ( alldtl.name ILIKE ? OR alldtl.comment3 ILIKE ?) ';
                $searchCondition['arrval'][]  = "%$val%";
                $searchCondition['arrval'][]  = "%$val%";
            }
        }

        // メーカーらのWHERE文字列取得
        if ($arrSearchData['maker_id']) {
            $searchCondition['where']   .= ' AND alldtl.maker_id = ? ';
            $searchCondition['arrval'][] = $arrSearchData['maker_id'];
        }

        $searchCondition['where_for_count'] = $searchCondition['where'];

        // 在庫無し商品の非表示
        if (NOSTOCK_HIDDEN) {
            $searchCondition['where'] .= ' AND (stock >= 1 OR stock_unlimited = 1)';
            $searchCondition['where_for_count'] .= ' AND EXISTS(SELECT * FROM dtb_products_class WHERE product_id = alldtl.product_id AND del_flg = 0 AND (stock >= 1 OR stock_unlimited = 1))';
        }

        // ソート順
        if (!Utils::isBlank($arrSearchData['orderby'])) {
            $searchCondition['orderby'] = $arrSearchData['orderby'];
        }

        return $searchCondition;
    }

    /**
     * 商品情報配列に商品ステータス情報を追加する
     *
     * @param  Array $arrProducts    商品一覧情報
     * @param  Array $arrStatus      商品ステータス配列
     * @param  Array $arrStatusImage スタータス画像配列
     * @return Array $arrProducts 商品一覧情報
     */
    protected function setStatusDataTo($arrProducts, $arrStatus, $arrStatusImage)
    {
        foreach ($arrProducts['productStatus'] as $product_id => $arrValues) {
            for ($i = 0; $i < count($arrValues); $i++) {
                $product_status_id = $arrValues[$i];
                if (!empty($product_status_id)) {
                    $arrProductStatus = array(
                        'status_cd' => $product_status_id,
                        'status_name' => $arrStatus[$product_status_id],
                        'status_image' =>$arrStatusImage[$product_status_id],
                    );
                    $arrProducts['productStatus'][$product_id][$i] = $arrProductStatus;
                }
            }
        }

        return $arrProducts;
    }
}
