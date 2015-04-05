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

namespace Eccube\Page\Admin\Contents;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\PageNavi;
use Eccube\Framework\Product;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;

/**
 * おすすめ商品管理 商品検索のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class RecommendSearch extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainno = 'contents';
        $this->tpl_subno = '';

        $this->tpl_subtitle = '商品検索';
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

        $rank = intval($_GET['rank']);

        switch ($this->getMode()) {
            case 'search':
                // POST値の引き継ぎ
                $this->arrErr = $this->lfCheckError($objFormParam);
                $arrPost = $objFormParam->getHashArray();
                // 入力された値にエラーがない場合、検索処理を行う。
                // 検索結果の数に応じてページャの処理も入れる。
                if (Utils::isBlank($this->arrErr)) {
                    /* @var $objProduct Product */
                    $objProduct = Application::alias('eccube.product');

                    $wheres = $this->createWhere($objFormParam, $objDb);
                    $this->tpl_linemax = $this->getLineCount($wheres, $objProduct);

                    $page_max = Utils::sfGetSearchPageMax($arrPost['search_page_max']);

                    // ページ送りの取得
                    /* @var $objNavi PageNavi */
                    $objNavi = Application::alias('eccube.page_navi', $arrPost['search_pageno'], $this->tpl_linemax, $page_max, 'eccube.moveSearchPage', NAVI_PMAX);
                    $this->tpl_strnavi = $objNavi->strnavi;      // 表示文字列
                    $startno = $objNavi->start_row;

                    $arrProduct_id = $this->getProducts($wheres, $objProduct, $page_max, $startno);
                    $this->arrProducts = $this->getProductList($arrProduct_id, $objProduct);
                    $this->arrForm = $arrPost;
                }
                break;
            default:
                break;
        }

        // カテゴリ取得
        $this->arrCatList = $objDb->getCategoryList();
        $this->rank       = $rank;
        $this->setTemplate('contents/recommend_search.tpl');
    }

    /**
     * パラメーターの初期化を行う
     * @param FormParam $objFormParam
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('商品ID', 'search_name', LTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品ID', 'search_category_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('商品コード', 'search_product_code', LTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品ステータス', 'search_status', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('ページ番号', 'search_pageno', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
    }

    /**
     * 入力されたパラメーターのエラーチェックを行う。
     * @param  FormParam $objFormParam
     * @return Array  エラー内容
     */
    public function lfCheckError(&$objFormParam)
    {
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $objFormParam->getHashArray());
        $objErr->arrErr = $objFormParam->checkError();

        return $objErr->arrErr;
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
                    $where .=    ' AND alldtl.product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code LIKE ? GROUP BY product_id)';
                    $bind[] = '%'.$val.'%';
                    break;
                case 'search_status':
                    $where .= ' AND alldtl.status = ?';
                    $bind[] = $val;
                    break;
                default:
                    break;
            }
        }

        return array(
            'where'=>$where,
            'bind' => $bind
        );
    }

    /**
     *
     * 検索結果対象となる商品の数を返す。
     * @param array      $whereAndBind
     * @param Product $objProduct
     */
    public function getLineCount($whereAndBind, Product &$objProduct)
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
        // 検索結果の取得
        return $objProduct->findProductIdsOrder($objQuery, $bind);
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
}
