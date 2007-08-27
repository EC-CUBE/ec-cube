<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 検索ブロック のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_FrontParts_Bloc_SearchProducts extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = BLOC_PATH . 'search_products.tpl';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $arrSearch = array();	// 検索項目表示用
        $objDb = new SC_Helper_DB_Ex();
        // 選択中のカテゴリIDを判定する
        $this->category_id = $objDb->sfGetCategoryId($_GET['product_id'], $_GET['category_id']);
        // カテゴリ検索用選択リスト
        $arrRet = $objDb->sfGetCategoryList('', true, '　');

        if(is_array($arrRet)) {
            // 文字サイズを制限する
            foreach($arrRet as $key => $val) {
                $arrRet[$key] = SC_Utils_Ex::sfCutString($val, SEARCH_CATEGORY_LEN);
            }
        }
        $this->arrCatList = $arrRet;

        $objSubView = new SC_SiteView();
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
}
?>
