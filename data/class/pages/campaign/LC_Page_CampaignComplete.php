<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * キャンペーン終了 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_CampaignComplete extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = TEMPLATE_DIR . '/campaign/complete.tpl';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        global $objCampaignSess;

        $objView = new SC_SiteView();
        $objQuery = new SC_Query();
        $objCampaignSess = new SC_CampaignSession();

        // キャンペーンページからの遷移で無い場合はTOPページへ
        if(!$objCampaignSess->getIsCampaign()) {
            $this->sendRedirect($this->getLocation(URL_DIR));
        }

        // 入力情報を渡す
        $this->arrForm = $_POST;
        $this->campaign_name = $objQuery->get("dtb_campaign", "campaign_name", "campaign_id = ?", array($objCampaignSess->getCampaignId()));
        $site_frame = CAMPAIGN_TEMPLATE_PATH . $objCampaignSess->getCampaignDir()  . "/active/site_frame.tpl";

        //----　ページ表示
        $objView->assignobj($this);
        $objView->display($site_frame);
        // セッションの開放
        $objCampaignSess->delCampaign();
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
