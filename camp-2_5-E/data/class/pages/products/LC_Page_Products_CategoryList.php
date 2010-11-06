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
 * カテゴリ一覧 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Products_CategoryList extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のAction
     * @return void
     */
    function action() {}

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->init();
        $this->tpl_mainpage = MOBILE_TEMPLATE_DIR . 'products/category_list.tpl';
        $this->tpl_title = 'カテゴリ一覧ページ';
    }

    /**
     * Page のプロセス（モバイル）.
     *
     * @return void
     */
    function mobileProcess(){
        parent::mobileProcess();
        $this->mobileAction();
        $this->sendResponse();
    }

    /**
     * Page のAction(モバイル).
     *
     * @return void
     */
    function mobileAction() {
        // カテゴリIDの正当性チェック
        $this->lfCheckCategoryId();
        
        //$objView = new SC_MobileView();

        // レイアウトデザインを取得
        $objLayout = new SC_Helper_PageLayout_Ex();
        $objLayout->sfGetPageLayout($this, false, DEF_LAYOUT);

        // カテゴリー情報を取得する。
        $this->lfGetCategories(@$_GET['category_id'], true, $this);

        //$objView->assignobj($this);
        //$objView->display(SITE_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* カテゴリIDの正当性チェック */
    function lfCheckCategoryId() {
        $objDb = new SC_Helper_DB_Ex();
        $category_id = $_POST['category_id'] ? $_POST['category_id'] : $_GET['category_id'];
        if (!defined('MOBILE_SITE') && !isset($_REQUEST['category_id']))
            SC_Utils_Ex::sfDispSiteError(CATEGORY_NOT_FOUND);
        if ($category_id
                && (!SC_Utils_Ex::sfIsInt($category_id)
                || SC_Utils_Ex::sfIsZeroFilling($category_id)
                || !$objDb->sfIsRecord('dtb_category', 'category_id', (array)$category_id, 'del_flg = 0')))
            SC_Utils_Ex::sfDispSiteError(CATEGORY_NOT_FOUND);
    }

    /**
     * 選択されたカテゴリーとその子カテゴリーの情報を取得し、
     * ページオブジェクトに格納する。
     *
     * @param string $category_id カテゴリーID
     * @param boolean $count_check 有効な商品がないカテゴリーを除くかどうか
     * @param object &$objPage ページオブジェクト
     * @return void
     */
    function lfGetCategories($category_id, $count_check = false, &$objPage) {
        $objDb = new SC_Helper_DB_Ex();
        // カテゴリーの正しいIDを取得する。
        $arrCategory_id = $objDb->sfGetCategoryId('', $category_id);
        $category_id = $arrCategory_id[0];
        if ($category_id == 0) {
            SC_Utils_Ex::sfDispSiteError(CATEGORY_NOT_FOUND);
        }

        $arrCategory = null;	// 選択されたカテゴリー
        $arrChildren = array();	// 子カテゴリー

        $arrAll = $objDb->sfGetCatTree($category_id, $count_check);
        foreach ($arrAll as $category) {
            // 選択されたカテゴリーの場合
            if ($category['category_id'] == $category_id) {
                $arrCategory = $category;
                continue;
            }

            // 関係のないカテゴリーはスキップする。
            if ($category['parent_category_id'] != $category_id) {
                continue;
            }

            // 子カテゴリーの場合は、孫カテゴリーが存在するかどうかを調べる。
            $arrGrandchildrenID = SC_Utils_Ex::sfGetUnderChildrenArray($arrAll, 'parent_category_id', 'category_id', $category['category_id']);
            $category['has_children'] = count($arrGrandchildrenID) > 0;
            $arrChildren[] = $category;
        }

        if (!isset($arrCategory)) {
            SC_Utils_Ex::sfDispSiteError(CATEGORY_NOT_FOUND);
        }

        // 子カテゴリーの商品数を合計する。
        $children_product_count = 0;
        foreach ($arrChildren as $category) {
            $children_product_count += $category['product_count'];
        }

        // 選択されたカテゴリーに直属の商品がある場合は、子カテゴリーの先頭に追加する。
        if ($arrCategory['product_count'] > $children_product_count) {
            $arrCategory['product_count'] -= $children_product_count;	// 子カテゴリーの商品数を除く。
            $arrCategory['has_children'] = false;	// 商品一覧ページに遷移させるため。
            array_unshift($arrChildren, $arrCategory);
        }

        // 結果を格納する。
        $objPage->arrCategory = $arrCategory;
        $objPage->arrChildren = $arrChildren;
    }
}
?>
