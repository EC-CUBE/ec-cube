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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/frontparts/bloc/LC_Page_FrontParts_Bloc_Ex.php';

/**
 * カテゴリ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_FrontParts_Bloc_Category extends LC_Page_FrontParts_Bloc_Ex {

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
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {

        // モバイル判定
        switch (SC_Display_Ex::detectDevice()) {
            case DEVICE_TYPE_MOBILE:
                // メインカテゴリの取得
                $this->arrCat = $this->lfGetMainCat(true);
                break;
            default:
                // 選択中のカテゴリID
                $this->tpl_category_id = $this->lfGetSelectedCategoryId($_GET);
                // カテゴリツリーの取得
                $this->arrTree = $this->lfGetCatTree($this->tpl_category_id, true);
                break;
        }


    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * 選択中のカテゴリIDを取得する.
     *
     * @param array $arrRequest リクエスト配列
     * @return array $arrCategoryId 選択中のカテゴリID
     */
    function lfGetSelectedCategoryId($arrRequest) {
            // 商品ID取得
        $product_id = '';
        if (isset($arrRequest['product_id']) && $arrRequest['product_id'] != '' && is_numeric($arrRequest['product_id'])) {
            $product_id = $arrRequest['product_id'];
        }
        // カテゴリID取得
        $category_id = '';
        if (isset($arrRequest['category_id']) && $arrRequest['category_id'] != '' && is_numeric($arrRequest['category_id'])) {
            $category_id = $arrRequest['category_id'];
        }
        // 選択中のカテゴリIDを判定する
        $objDb = new SC_Helper_DB_Ex();
        $arrCategoryId = $objDb->sfGetCategoryId($product_id, $category_id);
        if (empty($arrCategoryId)) {
            $arrCategoryId = array(0);
        }
        return $arrCategoryId;
    }

    /**
     * カテゴリツリーの取得.
     *
     * @param array $arrParentCategoryId 親カテゴリの配列
     * @param boolean $count_check 登録商品数をチェックする場合はtrue
     * @return array $arrRet カテゴリツリーの配列を返す
     */
    function lfGetCatTree($arrParentCategoryId, $count_check = false) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objDb = new SC_Helper_DB_Ex();
        $col = '*';
        $from = 'dtb_category left join dtb_category_total_count ON dtb_category.category_id = dtb_category_total_count.category_id';
        // 登録商品数のチェック
        if ($count_check) {
            $where = 'del_flg = 0 AND product_count > 0';
        } else {
            $where = 'del_flg = 0';
        }
        $objQuery->setOption('ORDER BY rank DESC');
        $arrRet = $objQuery->select($col, $from, $where);
        foreach ($arrParentCategoryId as $category_id) {
            $arrParentID = $objDb->sfGetParents(
                'dtb_category',
                'parent_category_id',
                'category_id',
                $category_id
            );
            $arrBrothersID = SC_Utils_Ex::sfGetBrothersArray(
                $arrRet,
                'parent_category_id',
                'category_id',
                $arrParentID
            );
            $arrChildrenID = SC_Utils_Ex::sfGetUnderChildrenArray(
                $arrRet,
                'parent_category_id',
                'category_id',
                $category_id
            );
            $this->root_parent_id[] = $arrParentID[0];
            $arrDispID = array_merge($arrBrothersID, $arrChildrenID);
            foreach ($arrRet as &$arrCategory) {
                if (in_array($arrCategory['category_id'], $arrDispID)) {
                    $arrCategory['display'] = 1;
                }
            }
        }
        return $arrRet;
    }

    /**
     * メインカテゴリの取得.
     *
     * @param boolean $count_check 登録商品数をチェックする場合はtrue
     * @return array $arrMainCat メインカテゴリの配列を返す
     */
    function lfGetMainCat($count_check = false) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = '*';
        $from = 'dtb_category left join dtb_category_total_count ON dtb_category.category_id = dtb_category_total_count.category_id';
        // メインカテゴリとその直下のカテゴリを取得する。
        $where = 'level <= 2 AND del_flg = 0';
        // 登録商品数のチェック
        if ($count_check) {
            $where .= ' AND product_count > 0';
        }
        $objQuery->setOption('ORDER BY rank DESC');
        $arrRet = $objQuery->select($col, $from, $where);
        // メインカテゴリを抽出する。
        $arrMainCat = array();
        foreach ($arrRet as $cat) {
            if ($cat['level'] != 1) {
                continue;
            }
            // 子カテゴリを持つかどうかを調べる。
            $arrChildrenID = SC_Utils_Ex::sfGetUnderChildrenArray(
                $arrRet,
                'parent_category_id',
                'category_id',
                $cat['category_id']
            );
            $cat['has_children'] = count($arrChildrenID) > 0;
            $arrMainCat[] = $cat;
        }
        return $arrMainCat;
    }
}
