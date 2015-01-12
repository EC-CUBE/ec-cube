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
use Eccube\Framework\PageNavi;
use Eccube\Framework\Product;
use Eccube\Framework\Query;
use Eccube\Framework\Helper\CategoryHelper;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;

/**
 * 商品並べ替え のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class ProductRank extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'products/product_rank.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'product_rank';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = '商品並び替え';
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
        /* @var $objCategory CategoryHelper */
        $objCategory = Application::alias('eccube.helper.category');

        $this->tpl_pageno = isset($_POST['pageno']) ? $_POST['pageno'] : '';

        // 通常時は親カテゴリを0に設定する。
        $this->arrForm['parent_category_id'] =
            isset($_POST['parent_category_id']) ? $_POST['parent_category_id'] : 0;
        $this->arrForm['product_id'] =
            isset($_POST['product_id']) ? $_POST['product_id'] : '';

        switch ($this->getMode()) {
            case 'up':
                $this->lfRankUp($objDb, $this->arrForm['parent_category_id'], $this->arrForm['product_id']);
                break;
            case 'down':
                $this->lfRankDown($objDb, $this->arrForm['parent_category_id'], $this->arrForm['product_id']);
                break;
            case 'move':
                $this->lfRankMove($objDb, $this->arrForm['parent_category_id'], $this->arrForm['product_id']);
                break;
            case 'tree':
                // カテゴリの切替は、ページ番号をクリアする。
                $this->tpl_pageno = '';
                break;
            case 'renumber':
                $this->lfRenumber($this->arrForm['parent_category_id']);
                break;
            default:
                break;
        }

        $this->arrTree = $objCategory->getTree();
        $this->arrParentID = $objCategory->getTreeTrail($this->arrForm['parent_category_id']);
        $this->arrProductsList = $this->lfGetProduct($this->arrForm['parent_category_id']);
        $arrBread = $objCategory->getTreeTrail($this->arrForm['parent_category_id'], FALSE);
        $this->tpl_bread_crumbs = Utils::jsonEncode(array_reverse($arrBread));
    }

    /* 商品読み込み */
    public function lfGetProduct($category_id)
    {
        // FIXME Product クラスを使用した実装
        $objQuery = Application::alias('eccube.query');
        $col = 'alldtl.product_id, name, main_list_image, product_code_min, product_code_max, status';
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');
        $table = $objProduct->alldtlSQL();
        $table.= ' LEFT JOIN dtb_product_categories AS T5 ON alldtl.product_id = T5.product_id';
        $where = 'del_flg = 0 AND category_id = ?';

        // 行数の取得
        $linemax = $objQuery->count($table, $where, array($category_id));
        // 該当件数表示用
        $this->tpl_linemax = $linemax;

        /* @var $objNavi PageNavi */
        $objNavi = Application::alias('eccube.page_navi', $this->tpl_pageno, $linemax, SEARCH_PMAX, 'eccube.movePage', NAVI_PMAX);
        $startno = $objNavi->start_row;
        $this->tpl_start_row = $objNavi->start_row;
        $this->tpl_strnavi = $objNavi->strnavi;     // Navi表示文字列
        $this->tpl_pagemax = $objNavi->max_page;    // ページ最大数（「上へ下へ」表示判定用）
        $this->tpl_disppage = $objNavi->now_page;   // 表示ページ番号（「上へ下へ」表示判定用）

        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset(SEARCH_PMAX, $startno);

        $objQuery->setOrder('rank DESC, alldtl.product_id DESC');

        $arrRet = $objQuery->select($col, $table, $where, array($category_id));

        return $arrRet;
    }

    /*
     * 商品の数値指定での並び替え実行
     */
    public function lfRenumber($parent_category_id)
    {
        $objQuery = Application::alias('eccube.query');

        $sql = <<< __EOS__
            UPDATE dtb_product_categories
            SET
                rank =
                    (
                        SELECT COUNT(*)
                        FROM dtb_product_categories t_in
                        WHERE t_in.category_id = dtb_product_categories.category_id
                            AND (
                                t_in.rank < dtb_product_categories.rank
                                OR (
                                    t_in.rank = dtb_product_categories.rank
                                    AND t_in.product_id < dtb_product_categories.product_id
                                )
                            )
                    ) + 1
            WHERE dtb_product_categories.category_id = ?
__EOS__;
        $arrRet = $objQuery->query($sql, array($parent_category_id));

        return $arrRet;
    }

    /**
     * @param DbHelper $objDb
     */
    public function lfRankUp(&$objDb, $parent_category_id, $product_id)
    {
        $where = 'category_id = ' . Utils::sfQuoteSmart($parent_category_id);
        $objDb->rankUp('dtb_product_categories', 'product_id', $product_id, $where);
    }

    /**
     * @param DbHelper $objDb
     */
    public function lfRankDown(&$objDb, $parent_category_id, $product_id)
    {
        $where = 'category_id = ' . Utils::sfQuoteSmart($parent_category_id);
        $objDb->rankDown('dtb_product_categories', 'product_id', $product_id, $where);
    }

    /**
     * @param DbHelper $objDb
     */
    public function lfRankMove(&$objDb, $parent_category_id, $product_id)
    {
        $key = 'pos-'.$product_id;
        $input_pos = mb_convert_kana($_POST[$key], 'n');
        if (Utils::sfIsInt($input_pos)) {
            $where = 'category_id = ' . Utils::sfQuoteSmart($parent_category_id);
            $objDb->moveRank('dtb_product_categories', 'product_id', $product_id, $input_pos, $where);
        }
    }
}
