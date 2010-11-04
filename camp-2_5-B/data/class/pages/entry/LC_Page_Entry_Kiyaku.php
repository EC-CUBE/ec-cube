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
 * ご利用規約 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Entry_Kiyaku extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'entry/kiyaku.tpl';
        $this->tpl_title = "ご利用規約";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        global $objCampaignSess;

        $objView = new SC_SiteView();
        $objCustomer = new SC_Customer();
        $objCampaignSess = new SC_CampaignSession();

        // レイアウトデザインを取得
        $layout = new SC_Helper_PageLayout_Ex();
        $layout->sfGetPageLayout($this, false, DEF_LAYOUT);

        // 規約内容の取得
        $objQuery = new SC_Query();
        $objQuery->setOrder("rank DESC");
        $arrRet = $objQuery->select("kiyaku_title, kiyaku_text", "dtb_kiyaku", "del_flg <> 1");

        $max = count($arrRet);
        $this->tpl_kiyaku_text = "";
        for ($i = 0; $i < $max; $i++) {
            $this->tpl_kiyaku_text.=$arrRet[$i]['kiyaku_title'] . "\n\n";
            $this->tpl_kiyaku_text.=$arrRet[$i]['kiyaku_text'] . "\n\n";
        }

        // キャンペーンからの遷移がチェック
        $this->is_campaign = $objCampaignSess->getIsCampaign();
        $this->campaign_dir = $objCampaignSess->getCampaignDir();

        $objView->assignobj($this);
        // フレームを選択(キャンペーンページから遷移なら変更)
        $objCampaignSess->pageView($objView);
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->init();
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        $objView = new SC_MobileView();
        $objCustomer = new SC_Customer();

        $offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
        $next = $offset;

        // レイアウトデザインを取得
        $objLayout = new SC_Helper_PageLayout_Ex();
        $objLayout->sfGetPageLayout($this, false, DEF_LAYOUT);

        // 規約内容の取得
        $objQuery = new SC_Query();
        $count = $objQuery->count("dtb_kiyaku", "del_flg <> 1");
        $objQuery->setOrder("rank DESC");
        $objQuery->setLimitOffset(1, $offset);
        $arrRet = $objQuery->select("kiyaku_title, kiyaku_text", "dtb_kiyaku", "del_flg <> 1");

        if($count > $offset + 1){
            $next++;
        } else {
            $next = -1;
        }

        $max = count($arrRet);
        $this->tpl_kiyaku_text = "";
        for ($i = 0; $i < $max; $i++) {
            $this->tpl_kiyaku_text.=$arrRet[$i]['kiyaku_title'] . "\n\n";
            $this->tpl_kiyaku_text.=$arrRet[$i]['kiyaku_text'] . "\n\n";
        }

        $objView->assign("offset", $next);
        $objView->assignobj($this);
        $objView->display(SITE_FRAME);
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
