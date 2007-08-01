<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 画像詳細 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Products_DetailImage extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/detail_image.tpl';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView();
        $objCartSess = new SC_CartSession("", false);

        // 管理ページからの確認の場合は、非公開の商品も表示する。
        if($_GET['admin'] == 'on') {
            $where = "del_flg = 0";
        } else {
            $where = "del_flg = 0 AND status = 1";
        }

        // 値の正当性チェック
        if(!SC_Utils_Ex::sfIsInt($_GET['product_id']) || !SC_Utils_Ex::sfIsRecord("dtb_products", "product_id", $_GET['product_id'], $where)) {
            SC_Utils_Ex::sfDispSiteError(PRODUCT_NOT_FOUND);
        }


        $image_key = $_GET['image'];

        $objQuery = new SC_Query();
        $col = "name, $image_key";
        $arrRet = $objQuery->select($col, "dtb_products", "product_id = ?", array($_GET['product_id']));

        list($width, $height) = getimagesize(IMAGE_SAVE_DIR . $arrRet[0][$image_key]);
        $this->tpl_width = $width;
        $this->tpl_height = $height;

        $this->tpl_table_width = $this->tpl_width + 20;
        $this->tpl_table_height = $this->tpl_height + 20;

        $this->tpl_image = $arrRet[0][$image_key];
        $this->tpl_name = $arrRet[0]['name'];

        $objView->assignobj($this);
        $objView->display($this->tpl_mainpage);
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
