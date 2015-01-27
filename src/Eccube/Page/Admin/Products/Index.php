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

namespace Eccube\Page\Admin\Products;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\Date;
use Eccube\Framework\FormParam;
use Eccube\Framework\PageNavi;
use Eccube\Framework\Product;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\DB\DBFactory;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\BestProductsHelper;
use Eccube\Framework\Helper\CsvHelper;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;

/**
 * 商品管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Index extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'products/index.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'index';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = '商品マスター';

        $masterData = Application::alias('eccube.db.master_data');
        $this->arrPageMax = $masterData->getMasterData('mtb_page_max');
        $this->arrDISP = $masterData->getMasterData('mtb_disp');
        $this->arrSTATUS = $masterData->getMasterData('mtb_status');
        $this->arrPRODUCTSTATUS_COLOR = $masterData->getMasterData('mtb_product_status_color');

        /* @var $objDate Date */
        $objDate = Application::alias('eccube.date');
        // 登録・更新検索開始年
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE('Y'));
        $this->arrStartYear = $objDate->getYear();
        $this->arrStartMonth = $objDate->getMonth();
        $this->arrStartDay = $objDate->getDay();
        // 登録・更新検索終了年
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE('Y'));
        $this->arrEndYear = $objDate->getYear();
        $this->arrEndMonth = $objDate->getMonth();
        $this->arrEndDay = $objDate->getDay();
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
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');
        $objQuery = Application::alias('eccube.query');

        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $this->arrHidden = $objFormParam->getSearchArray();
        $this->arrForm = $objFormParam->getFormParamList();

        switch ($this->getMode()) {
            case 'delete':
                // 商品、子テーブル(商品規格)、会員お気に入り商品の削除
                $this->doDelete('product_id = ?', array($objFormParam->getValue('product_id')));
                // 件数カウントバッチ実行
                $objDb->countCategory($objQuery);
                $objDb->countMaker($objQuery);
                // 削除後に検索結果を表示するため breakしない

            // 検索パラメーター生成後に処理実行するため breakしない
            case 'csv':
            case 'delete_all':

            case 'search':
                $objFormParam->convParam();
                $objFormParam->trimParam();
                $this->arrErr = $this->lfCheckError($objFormParam);
                $arrParam = $objFormParam->getHashArray();

                if (count($this->arrErr) == 0) {
                    $where = 'del_flg = 0';
                    $arrWhereVal = array();
                    foreach ($arrParam as $key => $val) {
                        if ($val == '') {
                            continue;
                        }
                        $this->buildQuery($key, $where, $arrWhereVal, $objFormParam, $objDb);
                    }

                    $order = 'update_date DESC';

                    /* -----------------------------------------------
                     * 処理を実行
                     * ----------------------------------------------- */
                    switch ($this->getMode()) {
                        // CSVを送信する。
                        case 'csv':
                            /* @var $objCSV CsvHelper */
                            $objCSV = Application::alias('eccube.helper.csv');
                            // CSVを送信する。正常終了の場合、終了。
                            $objCSV->sfDownloadCsv(1, $where, $arrWhereVal, $order, true);
                            Application::alias('eccube.response')->actionExit();

                        // 全件削除(ADMIN_MODE)
                        case 'delete_all':
                            $this->doDelete($where, $arrWhereVal);
                            break;

                        // 検索実行
                        default:
                            // 行数の取得
                            $this->tpl_linemax = $this->getNumberOfLines($where, $arrWhereVal);
                            // ページ送りの処理
                            $page_max = Utils::sfGetSearchPageMax($objFormParam->getValue('search_page_max'));
                            // ページ送りの取得
                            /* @var $objNavi PageNavi */
                            $objNavi = Application::alias(
                                'eccube.page_navi',
                                $this->arrHidden['search_pageno'],
                                $this->tpl_linemax, $page_max,
                                'eccube.moveNaviPage', NAVI_PMAX
                            );
                            $this->arrPagenavi = $objNavi->arrPagenavi;

                            // 検索結果の取得
                            $this->arrProducts = $this->findProducts($where, $arrWhereVal, $page_max, $objNavi->start_row,
                                                                     $order, $objProduct);

                            // 各商品ごとのカテゴリIDを取得
                            if (count($this->arrProducts) > 0) {
                                foreach ($this->arrProducts as $key => $val) {
                                    $this->arrProducts[$key]['categories'] = $objProduct->getCategoryIds($val['product_id'], true);
                                    $objDb->g_category_on = false;
                                }
                            }
                    }
                }
                break;
        }

        // カテゴリの読込
        list($this->arrCatKey, $this->arrCatVal) = $objDb->getLevelCatList(false);
        $this->arrCatList = $this->lfGetIDName($this->arrCatKey, $this->arrCatVal);
    }

    /**
     * パラメーター情報の初期化を行う.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        // POSTされる値
        $objFormParam->addParam('商品ID', 'product_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('カテゴリID', 'category_id', STEXT_LEN, 'n', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ページ送り番号', 'search_pageno', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('表示件数', 'search_page_max', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));

        // 検索条件
        $objFormParam->addParam('商品ID', 'search_product_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品コード', 'search_product_code', STEXT_LEN, 'KVna', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品名', 'search_name', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('カテゴリ', 'search_category_id', STEXT_LEN, 'n', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('種別', 'search_status', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
        // 登録・更新日
        $objFormParam->addParam('開始年', 'search_startyear', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('開始月', 'search_startmonth', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('開始日', 'search_startday', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了年', 'search_endyear', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了月', 'search_endmonth', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了日', 'search_endday', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));

        $objFormParam->addParam('商品ステータス', 'search_product_statuses', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
    }

    /**
     * 入力内容のチェックを行う.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function lfCheckError(&$objFormParam)
    {
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $objFormParam->getHashArray());
        $objErr->arrErr = $objFormParam->checkError();

        $objErr->doFunc(array('開始日', '終了日', 'search_startyear', 'search_startmonth', 'search_startday', 'search_endyear', 'search_endmonth', 'search_endday'), array('CHECK_SET_TERM'));

        return $objErr->arrErr;
    }

    // カテゴリIDをキー、カテゴリ名を値にする配列を返す。
    public function lfGetIDName($arrCatKey, $arrCatVal)
    {
        $max = count($arrCatKey);
        for ($cnt = 0; $cnt < $max; $cnt++) {
            $key = isset($arrCatKey[$cnt]) ? $arrCatKey[$cnt] : '';
            $val = isset($arrCatVal[$cnt]) ? $arrCatVal[$cnt] : '';
            $arrRet[$key] = $val;
        }

        return $arrRet;
    }

    /**
     * 商品、子テーブル(商品規格)、お気に入り商品の削除
     *
     * @param  string $where    削除対象の WHERE 句
     * @param  array  $arrParam 削除対象の値
     * @return void
     */
    public function doDelete($where, $arrParam = array())
    {
        $objQuery = Application::alias('eccube.query');
        $product_ids = $objQuery->getCol('product_id', "dtb_products", $where, $arrParam);

        $sqlval['del_flg']     = 1;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery->begin();
        $objQuery->update('dtb_products_class', $sqlval, "product_id IN (SELECT product_id FROM dtb_products WHERE $where)", $arrParam);
        $objQuery->delete('dtb_customer_favorite_products', "product_id IN (SELECT product_id FROM dtb_products WHERE $where)", $arrParam);

        /* @var $objRecommend BestProductsHelper */
        $objRecommend = Application::alias('eccube.helper.best_products');
        $objRecommend->deleteByProductIDs($product_ids);

        $objQuery->update('dtb_products', $sqlval, $where, $arrParam);
        $objQuery->commit();
    }

    /**
     * クエリを構築する.
     *
     * 検索条件のキーに応じた WHERE 句と, クエリパラメーターを構築する.
     * クエリパラメーターは, FormParam の入力値から取得する.
     *
     * 構築内容は, 引数の $where 及び $arrValues にそれぞれ追加される.
     *
     * @param  string       $key          検索条件のキー
     * @param  string       $where        構築する WHERE 句
     * @param  array        $arrValues    構築するクエリパラメーター
     * @param  FormParam $objFormParam FormParam インスタンス
     * @param  FormParam $objDb        DbHelper インスタンス
     * @return void
     */
    public function buildQuery($key, &$where, &$arrValues, &$objFormParam, &$objDb)
    {
        /* @var $dbFactory DBFactory */
        $dbFactory = Application::alias('eccube.db.factory');
        switch ($key) {
            // 商品ID
            case 'search_product_id':
                $where .= ' AND product_id = ?';
                $arrValues[] = sprintf('%d', $objFormParam->getValue($key));
                break;
            // 商品コード
            case 'search_product_code':
                $where .= ' AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code ILIKE ? AND del_flg = 0)';
                $arrValues[] = sprintf('%%%s%%', $objFormParam->getValue($key));
                break;
            // 商品名
            case 'search_name':
                $where .= ' AND name LIKE ?';
                $arrValues[] = sprintf('%%%s%%', $objFormParam->getValue($key));
                break;
            // カテゴリ
            case 'search_category_id':
                list($tmp_where, $tmp_Values) = $objDb->getCatWhere($objFormParam->getValue($key));
                if ($tmp_where != '') {
                    $where.= ' AND product_id IN (SELECT product_id FROM dtb_product_categories WHERE ' . $tmp_where . ')';
                    $arrValues = array_merge((array) $arrValues, (array) $tmp_Values);
                }
                break;
            // 種別
            case 'search_status':
                $tmp_where = '';
                foreach ($objFormParam->getValue($key) as $element) {
                    if ($element != '') {
                        if (Utils::isBlank($tmp_where)) {
                            $tmp_where .= ' AND (status = ?';
                        } else {
                            $tmp_where .= ' OR status = ?';
                        }
                        $arrValues[] = $element;
                    }
                }

                if (!Utils::isBlank($tmp_where)) {
                    $tmp_where .= ')';
                    $where .= " $tmp_where ";
                }
                break;
            // 登録・更新日(開始)
            case 'search_startyear':
                $date = Utils::sfGetTimestamp($objFormParam->getValue('search_startyear'),
                                                    $objFormParam->getValue('search_startmonth'),
                                                    $objFormParam->getValue('search_startday'));
                $where.= ' AND update_date >= ?';
                $arrValues[] = $date;
                break;
            // 登録・更新日(終了)
            case 'search_endyear':
                $date = Utils::sfGetTimestamp($objFormParam->getValue('search_endyear'),
                                                    $objFormParam->getValue('search_endmonth'),
                                                    $objFormParam->getValue('search_endday'), true);
                $where.= ' AND update_date <= ?';
                $arrValues[] = $date;
                break;
            // 商品ステータス
            case 'search_product_statuses':
                $arrPartVal = $objFormParam->getValue($key);
                $count = count($arrPartVal);
                if ($count >= 1) {
                    $where .= ' '
                        . 'AND product_id IN ('
                        . '    SELECT product_id FROM dtb_product_status WHERE product_status_id IN (' . Utils::repeatStrWithSeparator('?', $count) . ')'
                        . ')';
                    $arrValues = array_merge($arrValues, $arrPartVal);
                }
                break;
            default:
                break;
        }
    }

    /**
     * 検索結果の行数を取得する.
     *
     * @param  string  $where     検索条件の WHERE 句
     * @param  array   $arrValues 検索条件のパラメーター
     * @return integer 検索結果の行数
     */
    public function getNumberOfLines($where, $arrValues)
    {
        $objQuery = Application::alias('eccube.query');

        return $objQuery->count('dtb_products', $where, $arrValues);
    }

    /**
     * 商品を検索する.
     *
     * @param  string     $where      検索条件の WHERE 句
     * @param  array      $arrValues  検索条件のパラメーター
     * @param  integer    $limit      表示件数
     * @param  integer    $offset     開始件数
     * @param  string     $order      検索結果の並び順
     * @param  Product $objProduct Product インスタンス
     * @return array      商品の検索結果
     */
    public function findProducts($where, $arrValues, $limit, $offset, $order, Product &$objProduct)
    {
        $objQuery = Application::alias('eccube.query');

        // 読み込む列とテーブルの指定
        $col = 'product_id, name, main_list_image, status, product_code_min, product_code_max, price02_min, price02_max, stock_min, stock_max, stock_unlimited_min, stock_unlimited_max, update_date';
        $from = $objProduct->alldtlSQL();

        $objQuery->setLimitOffset($limit, $offset);
        $objQuery->setOrder($order);

        return $objQuery->select($col, $from, $where, $arrValues);
    }
}
