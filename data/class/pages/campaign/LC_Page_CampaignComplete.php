<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
            exit;
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
