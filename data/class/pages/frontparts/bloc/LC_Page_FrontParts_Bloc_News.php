<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 新着情報 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_FrontParts_Bloc_News.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_FrontParts_Bloc_News extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = BLOC_PATH . 'news.tpl';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        if (defined("MOBILE_SITE") && MOBILE_SITE) {
            $objSubView = new SC_SiteView();
        } else {
            $objSubView = new SC_MobileView();
        }

        //新着情報取得
        $this->arrNews = $this->lfGetNews();

        $objSubView->assignobj($this);
        $objSubView->display($this->tpl_mainpage);
    }


    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->tpl_mainpage = MOBILE_TEMPLATE_DIR . "frontparts/"
            . BLOC_DIR . 'news.tpl';
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        $this->process();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    function lfGetNews(){
        $conn = new SC_DBConn();
        $sql = "SELECT *, cast(substring(news_date,1,10) as date) as news_date_disp FROM dtb_news WHERE del_flg = '0' ORDER BY rank DESC";
        $list_data = $conn->getAll($sql);
        return $list_data;
    }
}
?>
