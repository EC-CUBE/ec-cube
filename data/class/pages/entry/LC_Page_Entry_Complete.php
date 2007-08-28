<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 会員登録(完了) のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Entry_Complete extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_css = URL_DIR.'css/layout/entry/complete.css';

        // メインテンプレートを設定
        if(CUSTOMER_CONFIRM_MAIL == true) {
            // 仮会員登録完了
            $this->tpl_mainpage = 'entry/complete.tpl';
        } else {
            // 本会員登録完了
            $this->tpl_mainpage = 'regist/complete.tpl';
        }

        $this->tpl_title .= '会員登録(完了ページ)';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView();
        $objCampaignSess = new SC_CampaignSession();

        // transaction check
        if (!$this->isValidToken()) {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
        }

        // レイアウトデザインを取得
        $layout = new SC_Helper_PageLayout_Ex();
        $objPage = $layout->sfGetPageLayout($this, false, DEF_LAYOUT);

        // キャンペーンからの遷移がチェック
        $this->is_campaign = $objCampaignSess->getIsCampaign();
        $this->campaign_dir = $objCampaignSess->getCampaignDir();

        $objView->assignobj($objPage);
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
