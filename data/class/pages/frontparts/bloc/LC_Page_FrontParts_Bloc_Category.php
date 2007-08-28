<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * カテゴリ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_FrontParts_Bloc_Category extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = BLOC_PATH . 'category.tpl';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objSubView = new SC_SiteView();
        $objDb = new SC_Helper_DB_Ex();

        // 選択中のカテゴリIDを判定する
        $category_id = $objDb->sfGetCategoryId($_GET['product_id'], $_GET['category_id']);

        // 選択中のカテゴリID
        $this->tpl_category_id = $category_id;
        $objPage = $this->lfGetCatTree($category_id, true, $this);

        $objSubView->assignobj($objPage);
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
    function lfGetCatTree($parent_category_id, $count_check = false, $objSubPage) {
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
        $objQuery->setoption("ORDER BY rank DESC");
        $arrRet = $objQuery->select($col, $from, $where);

        $arrParentID = $objDb->sfGetParents($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $parent_category_id);
        $arrBrothersID = SC_Utils_Ex::sfGetBrothersArray($arrRet, 'parent_category_id', 'category_id', $arrParentID);
        $arrChildrenID = SC_Utils_Ex::sfGetUnderChildrenArray($arrRet, 'parent_category_id', 'category_id', $parent_category_id);

        $objSubPage->root_parent_id = $arrParentID[0];

        $arrDispID = array_merge($arrBrothersID, $arrChildrenID);

        foreach($arrRet as $key => $array) {
            foreach($arrDispID as $val) {
                if($array['category_id'] == $val) {
                    $arrRet[$key]['display'] = 1;
                    break;
                }
            }
        }

        $objSubPage->arrTree = $arrRet;
        return $objSubPage;
    }
}
?>
