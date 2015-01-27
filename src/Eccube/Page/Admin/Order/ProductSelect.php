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

namespace Eccube\Page\Admin\Order;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\PageNavi;
use Eccube\Framework\Product;
use Eccube\Framework\Query;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;

/**
 * 商品選択 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class ProductSelect extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'order/product_select.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = '';
        $this->tpl_maintitle = '受注管理';
        $this->tpl_subtitle = '商品選択';

        $masterData = Application::alias('eccube.db.master_data');
        $this->arrPRODUCTSTATUS_COLOR = $masterData->getMasterData('mtb_product_status_color');
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
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $objFormParam = Application::alias('eccube.form_param');
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        $this->tpl_no = $this->getNo(array($_GET, $_POST));
        $this->shipping_id = $this->getShippingId(array($_GET, $_POST));

        switch ($this->getMode()) {
            case 'search':
                /* @var $objProduct Product */
                $objProduct = Application::alias('eccube.product');
                $this->arrForm = $objFormParam->getHashArray();
                $wheres = $this->createWhere($objFormParam, $objDb);
                $this->tpl_linemax = $this->getLineCount($wheres, $objProduct);

                //ぶった斬りポイント==================================================================
                // ページ送りの処理
                $page_max = Utils::sfGetSearchPageMax($_POST['search_page_max']);

                // ページ送りの取得
                /* @var $objNavi PageNavi */
                $objNavi = Application::alias('eccube.page_navi', $_POST['search_pageno'], $this->tpl_linemax, $page_max, 'eccube.moveSearchPage', NAVI_PMAX);
                $this->tpl_strnavi = $objNavi->strnavi;     // 表示文字列
                $startno = $objNavi->start_row;
                $arrProduct_id = $this->getProducts($wheres, $objProduct, $page_max, $startno);
                $productList = $this->getProductList($arrProduct_id, $objProduct);
                //取得している並び順で並び替え
                $this->arrProducts = $this->sortProducts($arrProduct_id, $productList);
                $objProduct->setProductsClassByProductIds($arrProduct_id);
                $this->tpl_javascript .= $this->getTplJavascript($objProduct);
                $js_fnOnLoad = $this->getFnOnload($this->arrProducts);
                $this->tpl_javascript .= 'function fnOnLoad()
                {' . $js_fnOnLoad . '}';
                $this->tpl_onload .= 'fnOnLoad();';
                // 規格1クラス名
                $this->tpl_class_name1 = $objProduct->className1;
                // 規格2クラス名
                $this->tpl_class_name2 = $objProduct->className2;
                // 規格1
                $this->arrClassCat1 = $objProduct->classCats1;
                // 規格1が設定されている
                $this->tpl_classcat_find1 = $objProduct->classCat1_find;
                // 規格2が設定されている
                $this->tpl_classcat_find2 = $objProduct->classCat2_find;
                $this->tpl_product_class_id = $objProduct->product_class_id;
                $this->tpl_stock_find = $objProduct->stock_find;
                break;
            default:
                break;
        }

        // カテゴリ取得
        $this->arrCatList = $objDb->getCategoryList();
        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     * 商品取得
     *
     * @param array      $arrProductId
     * @param Product $objProduct
     */
    public function getProductList($arrProductId, Product &$objProduct)
    {
        $objQuery = Application::alias('eccube.query');

        // 表示順序
        $order = 'update_date DESC, product_id DESC';
        $objQuery->setOrder($order);

        return $objProduct->getListByProductIds($objQuery, $arrProductId);
    }

    /**
     * ロード時に実行するJavascriptを生成
     * @param array $arrProducts
     */
    public function getFnOnload($arrProducts)
    {
        foreach ($arrProducts as $arrProduct) {
            $js_fnOnLoad .= "fnSetClassCategories(document.product_form{$arrProduct['product_id']});";
        }

        return $js_fnOnLoad;
    }

    /**
     * 規格クラス用JavaScript生成
     * @param Product $objProduct
     */
    public function getTplJavascript(Product &$objProduct)
    {
        return 'eccube.productsClassCategories = ' . Utils::jsonEncode($objProduct->classCategories) . '; ';
    }

    /**
     * 検索結果の取得
     * @param array      $whereAndBind string whereと array bindの連想配列
     * @param Product $objProduct
     * @param integer $page_max
     */
    public function getProducts($whereAndBind, Product &$objProduct, $page_max, $startno)
    {
        $where = $whereAndBind['where'];
        $bind = $whereAndBind['bind'];
        $objQuery = Application::alias('eccube.query');
        $objQuery->setWhere($where);
        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($page_max, $startno);
        // 表示順序
        $objQuery->setOrder($order);

        // 検索結果の取得
        return $objProduct->findProductIdsOrder($objQuery, $bind);
    }

    /**
     *
     * 検索結果対象となる商品の数を返す。
     * @param array      $whereAndBind
     * @param Product $objProduct
     */
    public function getLineCount($whereAndBind, &$objProduct)
    {
        $where = $whereAndBind['where'];
        $bind = $whereAndBind['bind'];
        // 検索結果対象となる商品の数を取得
        $objQuery = Application::alias('eccube.query');
        $objQuery->setWhere($where);
        $linemax = $objProduct->findProductCount($objQuery, $bind);

        return $linemax;   // 何件が該当しました。表示用
    }

    /**
     *
     * POSTされた値からSQLのWHEREとBINDを配列で返す。
     * @return array        ('where' => where string, 'bind' => databind array)
     * @param  FormParam $objFormParam
     * @param DbHelper $objDb
     */
    public function createWhere(&$objFormParam, &$objDb)
    {
        $arrForm = $objFormParam->getHashArray();
        $where = 'alldtl.del_flg = 0';
        $bind = array();
        foreach ($arrForm as $key => $val) {
            if ($val == '') {
                continue;
            }

            switch ($key) {
                case 'search_name':
                    $where .= ' AND name ILIKE ?';
                    $bind[] = '%'.$val.'%';
                    break;
                case 'search_category_id':
                    list($tmp_where, $tmp_bind) = $objDb->getCatWhere($val);
                    if ($tmp_where != '') {
                        $where.= ' AND alldtl.product_id IN (SELECT product_id FROM dtb_product_categories WHERE ' . $tmp_where . ')';
                        $bind = array_merge((array) $bind, (array) $tmp_bind);
                    }
                    break;
                case 'search_product_code':
                    $where .=    ' AND alldtl.product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code LIKE ? AND del_flg = 0 GROUP BY product_id)';
                    $bind[] = '%'.$val.'%';
                    break;

                default:
                    break;
            }
        }

        return array(
            'where' => $where,
            'bind'  => $bind,
        );
    }

    /**
     * リクエストパラメーターnoを取ってくる。
     * @param unknown_type $globalParams
     */
    public function getNo($globalParams)
    {
        foreach ($globalParams as $params) {
            if (isset($params['no']) && $params['no']!= '') {
                return intval($params['no']);
            }
        }

        return null;
    }

    /**
     * リクエストパラメーター shipping_id を取ってくる。
     * @param unknown_type $globalParams
     */
    public function getShippingId($globalParams)
    {
        foreach ($globalParams as $params) {
            if (isset($params['shipping_id']) && $params['shipping_id']!= '') {
                return intval($params['shipping_id']);
            }
        }

        return null;
    }

    /**
     * 取得している並び順で並び替え
     * @param $arrProduct_id
     * @param $productList
     */
    public function sortProducts($arrProduct_id, $productList)
    {
        $products  = array();
        foreach ($productList as $item) {
            $products[ $item['product_id'] ] = $item;
        }
        $arrProducts = array();
        foreach ($arrProduct_id as $product_id) {
            $arrProducts[] = $products[$product_id];
        }

        return $arrProducts;
    }

    /**
     * パラメーター情報の初期化
     * @param FormParam $objFormParam
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('オーダーID', 'order_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('商品名', 'search_name', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('カテゴリID', 'search_category_id', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK', 'SPTAB_CHECK'));
        $objFormParam->addParam('商品コード', 'search_product_code', LTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK', 'SPTAB_CHECK'));
        $objFormParam->addParam('フッター', 'footer', LTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK', 'SPTAB_CHECK'));
        $objFormParam->addParam('届け先ID', 'shipping_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
    }
}
