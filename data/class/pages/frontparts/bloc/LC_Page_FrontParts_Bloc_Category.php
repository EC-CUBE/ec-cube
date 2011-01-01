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
require_once(CLASS_REALDIR . "pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php");

/**
 * カテゴリ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_FrontParts_Bloc_Category.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_FrontParts_Bloc_Category extends LC_Page_FrontParts_Bloc {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->setTplMainpage('category.tpl');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objDb = new SC_Helper_DB_Ex();

        // 選択中のカテゴリIDを判定する
        $arrCategory_id = $objDb->sfGetCategoryId($_GET['product_id'], $_GET['category_id']);

        // 選択中のカテゴリID
        $this->tpl_category_id = empty($arrCategory_id) ? array(0) : $arrCategory_id;;
        $this->lfGetCatTree($this->tpl_category_id, true, $this);
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->tpl_mainpage = MOBILE_TEMPLATE_REALDIR . "frontparts/"
            . BLOC_DIR . 'category.tpl';
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        $objSubView = new SC_MobileView();

       $this->lfGetMainCat(true, $this);

        $objSubView->assignobj($this);
        $objSubView->display($this->tpl_mainpage);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    // カテゴリツリーの取得
    function lfGetCatTree($arrParent_category_id, $count_check = false) {
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();
        $col = "*";
        $from = "dtb_category left join dtb_category_total_count using (category_id)";
        // 登録商品数のチェック
        if($count_check) {
            $where = "del_flg = 0 AND product_count > 0";
        } else {
            $where = "del_flg = 0";
        }
        $objQuery->setOption("ORDER BY rank DESC");
        $arrRet = $objQuery->select($col, $from, $where);

        foreach ($arrParent_category_id as $category_id) {
            $arrParentID = $objDb->sfGetParents($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $category_id);
            $arrBrothersID = SC_Utils_Ex::sfGetBrothersArray($arrRet, 'parent_category_id', 'category_id', $arrParentID);
            $arrChildrenID = SC_Utils_Ex::sfGetUnderChildrenArray($arrRet, 'parent_category_id', 'category_id', $category_id);

            $this->root_parent_id[] = $arrParentID[0];

            $arrDispID = array_merge($arrBrothersID, $arrChildrenID);

            foreach($arrRet as $key => $array) {
                foreach($arrDispID as $val) {
                    if($array['category_id'] == $val) {
                        $arrRet[$key]['display'] = 1;
                        break;
                    }
                }
            }
        }

        $this->arrTree = $arrRet;
    }

    // メインカテゴリーの取得
    function lfGetMainCat($count_check = false, &$objSubPage) {
        $objQuery = new SC_Query();
        $col = "*";
        $from = "dtb_category left join dtb_category_total_count using (category_id)";
        // メインカテゴリーとその直下のカテゴリーを取得する。
        $where = 'level <= 2 AND del_flg = 0';
        // 登録商品数のチェック
        if($count_check) {
            $where .= " AND product_count > 0";
        }
        $objQuery->setOption("ORDER BY rank DESC");
        $arrRet = $objQuery->select($col, $from, $where);

        // メインカテゴリーを抽出する。
        $arrMainCat = array();
        foreach ($arrRet as $cat) {
            if ($cat['level'] != 1) {
                continue;
            }

            // 子カテゴリーを持つかどうかを調べる。
            $arrChildrenID = SC_Utils_Ex::sfGetUnderChildrenArray($arrRet, 'parent_category_id', 'category_id', $cat['category_id']);
            $cat['has_children'] = count($arrChildrenID) > 0;
            $arrMainCat[] = $cat;
        }

        $objSubPage->arrCat = $arrMainCat;
        return $objSubPage;
    }
}
?>
