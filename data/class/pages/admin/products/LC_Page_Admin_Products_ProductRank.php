<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 商品並べ替え のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products_ProductRank extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/product_rank.tpl';
        $this->tpl_subnavi = 'products/subnavi.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'product_rank';
        $this->tpl_subtitle = '商品並び替え';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objQuery = new SC_Query();
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objDb = new SC_Helper_DB_Ex();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        $this->tpl_pageno = isset($_POST['pageno']) ? $_POST['pageno'] : "";

        // 通常時は親カテゴリを0に設定する。
        $this->arrForm['parent_category_id'] =
            isset($_POST['parent_category_id']) ? $_POST['parent_category_id'] : 0;

        if (!isset($_POST['mode'])) $_POST['mode'] = "";
        switch($_POST['mode']) {
        case 'up':
            $where = "category_id = " . SC_Utils_Ex::sfQuoteSmart($_POST['parent_category_id']);
            $objDb->sfRankUp("dtb_product_categories", "product_id", $_POST['product_id'], $where);
            break;
        case 'down':
            $where = "category_id = " . SC_Utils_Ex::sfQuoteSmart($_POST['parent_category_id']);
            $objDb->sfRankDown("dtb_product_categories", "product_id", $_POST['product_id'], $where);
            break;
        case 'move':
            $key = "pos-".$_POST['product_id'];
            $input_pos = mb_convert_kana($_POST[$key], "n");
            if(SC_Utils_Ex::sfIsInt($input_pos)) {
                $where = "category_id = " . SC_Utils_Ex::sfQuoteSmart($_POST['parent_category_id']);
                $objDb->sfMoveRank("dtb_product_categories", "product_id", $_POST['product_id'], $input_pos, $where);
            }
            break;
        case 'tree':
            // カテゴリの切替は、ページ番号をクリアする。
            $this->tpl_pageno = "";
            break;
            
        case 'renumber':
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
            $objQuery->query($sql, array($_POST['parent_category_id']));
            break;
        
        default:
            break;
        }

        $this->arrTree = $objDb->sfGetCatTree($this->arrForm['parent_category_id']);
        $this->arrProductsList =
            $this->lfGetProduct($this->arrForm['parent_category_id']);

        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* 商品読み込み */
    function lfGetProduct($category_id) {
        $objQuery = new SC_Query();
        $col = "product_id, name, main_list_image, product_code_min, product_code_max, status";
        $table = "vw_products_allclass AS allcls";
        $where = "del_flg = 0 AND category_id = ?";

        // 行数の取得
        $linemax = $objQuery->count($table, $where, array($category_id));
        // 該当件数表示用
        $this->tpl_linemax = $linemax;

        $objNavi = new SC_PageNavi($this->tpl_pageno, $linemax, SEARCH_PMAX, "fnNaviPage", NAVI_PMAX);
        $startno = $objNavi->start_row;
        $this->tpl_start_row = $objNavi->start_row;
        $this->tpl_strnavi = $objNavi->strnavi;     // Navi表示文字列
        $this->tpl_pagemax = $objNavi->max_page;    // ページ最大数（「上へ下へ」表示判定用）
        $this->tpl_disppage = $objNavi->now_page;   // 表示ページ番号（「上へ下へ」表示判定用）

        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset(SEARCH_PMAX, $startno);

        $objQuery->setOrder("product_rank DESC, product_id DESC");

        $arrRet = $objQuery->select($col, $table, $where, array($category_id));
        return $arrRet;
    }
}
?>
