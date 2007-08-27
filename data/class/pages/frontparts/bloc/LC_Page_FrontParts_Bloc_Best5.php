<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * Best5 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_FrontParts_Bloc_Best5 extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = BLOC_PATH . 'best5.tpl';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objSubView = new SC_SiteView();
        $objSiteInfo = $objView->objSiteInfo;

        // 基本情報を渡す
        $objSiteInfo = new SC_SiteInfo();
        $this->arrInfo = $objSiteInfo->data;

        //おすすめ商品表示
        $this->arrBestProducts = $this->lfGetRanking();

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

    //おすすめ商品検索
    function lfGetRanking(){
        $objQuery = new SC_Query();

        $col = "A.*, name, price02_min, price01_min, main_list_image ";
        $from = "dtb_best_products AS A INNER JOIN vw_products_allclass AS allcls using(product_id)";
        $where = "status = 1";
        $order = "rank";
        $objQuery->setorder($order);

        $arrBestProducts = $objQuery->select($col, $from, $where);

        return $arrBestProducts;
    }
}
?>
