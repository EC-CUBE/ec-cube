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
 * 検索ブロック のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_FrontParts_Bloc_SearchProducts.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_FrontParts_Bloc_SearchProducts extends LC_Page_FrontParts_Bloc_Ex {

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

        // 商品ID取得
        $product_id = $this -> lfGetProductId();
        // カテゴリID取得
        $category_id = $this -> lfGetCategoryId();
        // メーカーID取得
        $maker_id = $this -> lfGetMakerId();
        // 選択中のカテゴリIDを判定する
        $this->category_id = $this->lfGetSelectedCategoryId($product_id, $category_id);
        // カテゴリ検索用選択リスト
        $this->arrCatList = $this->lfGetCategoryList();
        // 選択中のメーカーIDを判定する
        $this->maker_id = $this->lfGetSelectedMakerId($product_id, $maker_id);
        // メーカー検索用選択リスト
        $this->arrMakerList = $this->lfGetMakerList();


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
     * 商品IDを取得する.
     *
     * @return string $product_id 商品ID
     */
    function lfGetProductId() {
        $product_id = '';
        if (isset($_GET['product_id']) && $_GET['product_id'] != '' && is_numeric($_GET['product_id'])) {
            $product_id = $_GET['product_id'];
        }
        return $product_id;
    }

    /**
     * カテゴリIDを取得する.
     *
     * @return string $category_id カテゴリID
     */
    function lfGetCategoryId() {
        $category_id = '';
        if (isset($_GET['category_id']) && $_GET['category_id'] != '' && is_numeric($_GET['category_id'])) {
            $category_id = $_GET['category_id'];
        }
        return $category_id;
    }

    /**
     * メーカーIDを取得する.
     *
     * @return string $maker_id メーカーID
     */
    function lfGetMakerId() {
        $maker_id = '';
        if (isset($_GET['maker_id']) && $_GET['maker_id'] != '' && is_numeric($_GET['maker_id'])) {
            $maker_id = $_GET['maker_id'];
        }
        return $maker_id;
    }

    /**
     * 選択中のカテゴリIDを取得する
     *
     * @return array $arrCategoryId 選択中のカテゴリID
     */
    function lfGetSelectedCategoryId($product_id, $category_id) {
        // 選択中のカテゴリIDを判定する
        $objDb = new SC_Helper_DB_Ex();
        $arrCategoryId = $objDb->sfGetCategoryId($product_id, $category_id);
        return $arrCategoryId;
    }

    /**
     * 選択中のメーカーIDを取得する
     *
     * @return array $arrMakerId 選択中のメーカーID
     */
    function lfGetSelectedMakerId($product_id, $maker_id) {
        // 選択中のメーカーIDを判定する
        $objDb = new SC_Helper_DB_Ex();
        $arrMakerId = $objDb->sfGetMakerId($product_id, $maker_id);
        return $arrMakerId;
    }

    /**
     * カテゴリ検索用選択リストを取得する
     *
     * @return array $arrCategoryList カテゴリ検索用選択リスト
     */
    function lfGetCategoryList() {
        $objDb = new SC_Helper_DB_Ex();
        // カテゴリ検索用選択リスト
        $arrCategoryList = $objDb->sfGetCategoryList('', true, '　');
        if (is_array($arrCategoryList)) {
            // 文字サイズを制限する
            foreach ($arrCategoryList as $key => $val) {
                $truncate_str = SC_Utils_Ex::sfCutString($val, SEARCH_CATEGORY_LEN, false);
                $arrCategoryList[$key] = preg_replace('/　/u', '&nbsp;&nbsp;', $truncate_str);
            }
        }
        return $arrCategoryList;
    }

    /**
     * メーカー検索用選択リストを取得する
     *
     * @return array $arrMakerList メーカー検索用選択リスト
     */
    function lfGetMakerList() {
        $objDb = new SC_Helper_DB_Ex();
        // メーカー検索用選択リスト
        $arrMakerList = $objDb->sfGetMakerList('', true);
        if (is_array($arrMakerList)) {
            // 文字サイズを制限する
            foreach ($arrMakerList as $key => $val) {
                $arrMakerList[$key] = SC_Utils_Ex::sfCutString($val, SEARCH_CATEGORY_LEN, false);
            }
        }
        return $arrMakerList;
    }

}
