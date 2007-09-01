<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/admin/products/LC_Page_Admin_Products_ProductClass.php");

/**
 * 商品登録(規格) のページクラス(拡張).
 *
 * LC_Page_Admin_Products_ProductClass をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Admin_Products_Product_Ex.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Admin_Products_ProductClass_Ex extends LC_Page_Admin_Products_ProductClass {

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
