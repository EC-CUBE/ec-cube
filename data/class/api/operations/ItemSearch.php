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

/**
 * APIの基本クラス
 *
 * @package Api
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
require_once CLASS_EX_REALDIR . 'api_extends/SC_Api_Abstract_Ex.php';

class API_ItemSearch extends SC_Api_Abstract_Ex {

    protected $operation_name = 'ItemSearch';
    protected $operation_description = '';
    protected $default_auth_types = self::API_AUTH_TYPE_OPEN;
    protected $default_enable = '1';
    protected $default_is_log = '0';
    protected $default_sub_data = '';

    public function __construct() {
        parent::__construct();
        $this->operation_description = t('c_Product search and product list information are retrieved._01');
    }

    public function doAction($arrParam) {
        $arrRequest = $this->doInitParam($arrParam);
        if (!$this->isParamError()) {

            $masterData                 = new SC_DB_MasterData_Ex();
            $arrSTATUS            = $masterData->getMasterData('mtb_status');
            $arrSTATUS_IMAGE      = $masterData->getMasterData('mtb_status_image');

            $objProduct = new SC_Product_Ex();
            $arrSearchData = array(
                'category_id' => $arrRequest['BrowseNode'],
                'maker_name' => $arrRequest['Manufacturer'],
                'name' => $arrRequest['Keywords'],
                'orderby' => $arrRequest['Sort'],
            );

            $arrSearchCondition = $this->getSearchCondition($arrSearchData);
            $disp_number = 10;

            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $objQuery->setWhere($arrSearchCondition['where_for_count']);
            $objProduct = new SC_Product_Ex();
            $linemax = $objProduct->findProductCount($objQuery, $arrSearchCondition['arrval']);
            $objNavi = new SC_PageNavi_Ex($arrRequest['ItemPage'], $tpl_linemax, $disp_number);
            $arrProducts = $this->getProductsList($arrSearchCondition, $disp_number, $objNavi->start_row, $linemax, $objProduct);

            if (!SC_Utils_Ex::isBlank($arrProducts)) {
                $arrProducts = $this->setStatusDataTo($arrProducts, $arrSTATUS, $arrSTATUS_IMAGE);
                $arrProducts = $objProduct->setPriceTaxTo($arrProducts);
                foreach ($arrProducts as $key=>$val) {
                    $arrProducts[$key]['main_list_image'] = SC_Utils_Ex::sfNoImageMainList($val['main_list_image']);
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
                $this->addError('ItemSearch.Error', t('c_* The requested information was not found._01'));
            }
        }

        return false;
    }

    protected function lfInitParam(&$objFormParam) {
        $objFormParam->addParam(t('c_Category ID_01'), 'BrowseNode', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Keyword_01'), 'Keywords', STEXT_LEN, 'a', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Manufacturer name_01'), 'Manufacturer', STEXT_LEN, 'a', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Page number_01'), 'ItemPage', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Sort_01'), 'Sort', STEXT_LEN, 'a', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
    }

    public function getResponseGroupName() {
        return 'Items';
    }


    /**
     * 商品一覧の取得
     *
     * @return array
     * TODO: LC_Page_Products_List::lfGetProductsList() と共通化
     */
    protected function getProductsList($searchCondition, $disp_number, $startno, $linemax, &$objProduct) {

        $arrOrderVal = array();

        $objQuery =& SC_Query_Ex::getSingletonInstance();
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
                $order = <<< __EOS__
                    (
                        SELECT
                            T3.rank * 2147483648 + T2.rank
                        FROM
                            $dtb_product_categories T2
                            JOIN dtb_category T3
                              ON T2.category_id = T3.category_id
                        WHERE T2.product_id = alldtl.product_id
                        ORDER BY T3.rank DESC, T2.rank DESC
                        LIMIT 1
                    ) DESC
                    ,product_id DESC
__EOS__;
                    $objQuery->setOrder($order);
                break;
        }
        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($disp_number, $startno);
        $objQuery->setWhere($searchCondition['where']);

        // 表示すべきIDとそのIDの並び順を一気に取得
        $arrProductId = $objProduct->findProductIdsOrder($objQuery, array_merge($searchCondition['arrval'], $arrOrderVal));

        $objQuery =& SC_Query_Ex::getSingletonInstance();
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
    protected function getSearchCondition($arrSearchData) {
        $searchCondition = array(
            'where'             => '',
            'arrval'            => array(),
            'where_category'    => '',
            'arrvalCategory'    => array(),
            'orderby'           => ''
        );

        // カテゴリからのWHERE文字列取得
        if (!SC_Utils_Ex::isBlank($arrSearchData['category_id'])) {
            list($searchCondition['where_category'], $searchCondition['arrvalCategory']) = SC_Helper_DB_Ex::sfGetCatWhere($arrSearchData['category_id']);
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
        if (!SC_Utils_Ex::isBlank($arrSearchData['orderby'])) {
            $searchCondition['orderby'] = $arrSearchData['orderby'];
        }

        return $searchCondition;
    }


    /**
     * 商品情報配列に商品ステータス情報を追加する
     *
     * @param Array $arrProducts 商品一覧情報
     * @param Array $arrStatus 商品ステータス配列
     * @param Array $arrStatusImage スタータス画像配列
     * @return Array $arrProducts 商品一覧情報
     */
    protected function setStatusDataTo($arrProducts, $arrStatus, $arrStatusImage) {

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
