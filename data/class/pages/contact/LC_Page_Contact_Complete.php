<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 問い合わせ(完了ページ) のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Contact_Complete extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'contact/complete.tpl';
        $this->tpl_title = 'お問い合わせ(完了ページ)';
        $this->tpl_mainno = 'contact';
        $this->tpl_css = array();
        $this->tpl_css[1] = URL_DIR.'css/layout/contact/index.css';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView();
        $objCampaignSess = new SC_CampaignSession();

        // レイアウトデザインを取得
        $layout = new SC_Helper_PageLayout_Ex();
        $this = $layout->sfGetPageLayout($this, false, DEF_LAYOUT);

        // キャンペーンからの遷移かチェック
        $this->is_campaign = $objCampaignSess->getIsCampaign();
        $this->campaign_dir = $objCampaignSess->getCampaignDir();

        $objView->assignobj($this);
        // フレームを選択(キャンペーンページから遷移なら変更)
        $objCampaignSess->pageView($objView);
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
