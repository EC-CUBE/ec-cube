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
require_once(CLASS_FILE_PATH . "pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php");

/**
 * 検索ブロック のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_FrontParts_Bloc_SearchProducts.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_FrontParts_Bloc_SearchProducts extends LC_Page_FrontParts_Bloc {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->setTplMainpage('search_products.tpl');
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
        $arrSearch = array();	// 検索項目表示用
        $objDb = new SC_Helper_DB_Ex();
        // 選択中のカテゴリIDを判定する
        $this->category_id = $objDb->sfGetCategoryId($_GET['product_id'], $_GET['category_id']);
        // カテゴリ検索用選択リスト
        $arrRet = $objDb->sfGetCategoryList('', true, '　');

        if(is_array($arrRet)) {
            // 文字サイズを制限する
            foreach($arrRet as $key => $val) {
                $str = SC_Utils_Ex::sfCutString($val, SEARCH_CATEGORY_LEN, false);
                $arrRet[$key] = preg_replace('/　/', "&nbsp;&nbsp;", $str);
            }
        }
        $this->arrCatList = $arrRet;

        // 選択中のメーカーIDを判定する
        $this->maker_id = $objDb->sfGetMakerId($_GET['product_id'], $_GET['maker_id']);
        // メーカー検索用選択リスト
        $arrRet = $objDb->sfGetMakerList('', true);
        if(is_array($arrRet)) {
            // 文字サイズを制限する
            foreach($arrRet as $key => $val) {
                $arrRet[$key] = SC_Utils_Ex::sfCutString($val, SEARCH_CATEGORY_LEN);
            }
        }
        $this->arrMakerList = $arrRet;
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
}
?>
