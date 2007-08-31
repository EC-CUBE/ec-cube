<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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
        $conn = new SC_DBConn();
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objDb = new SC_Helper_DB_Ex();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        $this->tpl_pageno = isset($_POST['pageno']) ? $_POST['pageno'] : "";

        // 通常時は親カテゴリを0に設定する。
        $this->arrForm['parent_category_id'] =
            isset($_POST['parent_category_id']) ? $_POST['parent_category_id'] : "";

        if (!isset($_POST['mode'])) $_POST['mode'] = "";
        switch($_POST['mode']) {
        case 'up':
            $where = "category_id = " . addslashes($_POST['parent_category_id']);
            $objDb->sfRankUp("dtb_products", "product_id", $_POST['product_id'], $where);
            break;
        case 'down':
            $where = "category_id = " . addslashes($_POST['parent_category_id']);
            $objDb->sfRankDown("dtb_products", "product_id", $_POST['product_id'], $where);
            break;
        case 'move':
            $key = "pos-".$_POST['product_id'];
            $input_pos = mb_convert_kana($_POST[$key], "n");
            if(SC_Utils_Ex::sfIsInt($input_pos)) {
                $where = "category_id = " . addslashes($_POST['parent_category_id']);
                $objDb->sfMoveRank("dtb_products", "product_id", $_POST['product_id'], $input_pos, $where);
            }
            break;
        case 'tree':
            // カテゴリの切替は、ページ番号をクリアする。
            $this->tpl_pageno = "";
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
        $col = "product_id, name, main_list_image, rank, product_code";
        $table = "vw_products_nonclass AS noncls ";
        $where = "del_flg = 0 AND category_id = ?";

        // 行数の取得
        $linemax = $objQuery->count("dtb_products", $where, array($category_id));
        // 順位、該当件数表示用
        $this->tpl_linemax = $linemax;

        $objNavi = new SC_PageNavi($this->tpl_pageno, $linemax, SEARCH_PMAX, "fnNaviPage", NAVI_PMAX);
        $startno = $objNavi->start_row;
        $this->tpl_strnavi = $objNavi->strnavi;		// Navi表示文字列
        $this->tpl_pagemax = $objNavi->max_page;		// ページ最大数（「上へ下へ」表示判定用）
        $this->tpl_disppage = $objNavi->now_page;	// 表示ページ番号（「上へ下へ」表示判定用）

        // 取得範囲の指定(開始行番号、行数のセット)
        if(DB_TYPE != "mysql") $objQuery->setlimitoffset(SEARCH_PMAX, $startno);

        $objQuery->setorder("rank DESC");

        // viewも絞込みをかける(mysql用)
        //sfViewWhere("&&noncls_where&&", $where, array($category_id), $objQuery->order . " " .  $objQuery->setlimitoffset(SEARCH_PMAX, $startno, true)); TODO

        $arrRet = $objQuery->select($col, $table, $where, array($category_id));
        return $arrRet;
    }
}
?>
