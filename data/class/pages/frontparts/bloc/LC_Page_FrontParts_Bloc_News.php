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
 * @version $Id$
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
        $objSubView = new SC_SiteView();

        //新着情報取得
        $this->arrNews = $this->lfGetNews();

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

    function lfGetNews(){
        $conn = new SC_DBConn();
        $sql = "SELECT *, cast(substring(news_date,1,10) as date) as news_date_disp FROM dtb_news WHERE del_flg = '0' ORDER BY rank DESC";
        $list_data = $conn->getAll($sql);
        return $list_data;
    }
}
?>
