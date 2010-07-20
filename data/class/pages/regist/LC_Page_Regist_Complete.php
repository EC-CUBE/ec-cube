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
 * 会員登録完了のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Regist_Complete.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Regist_Complete extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'regist/complete.tpl';
        $this->tpl_title = '会員登録(完了ページ)';
        $this->tpl_conv_page = AFF_ENTRY_COMPLETE;
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

        // キャンペーンからの登録の場合の処理
        if($_GET["cp"] != "") {
            $arrCampaign= $objQuery->select("directory_name", "dtb_campaign", "campaign_id = ?", array($_GET["cp"]));
            // キャンペーンディレクトリ名を保持
            $dir_name = $arrCampaign[0]['directory_name'];
        } else {
            $dir_name = "";
        }

        // レイアウトデザインを取得
        $helper = new SC_Helper_PageLayout_Ex();
        $helper->sfGetPageLayout($this, false, DEF_LAYOUT);

        $objView->assignobj($this);
        // フレームを選択(キャンペーンページから遷移なら変更)
        if($this->dir_name != "") {
            $objView->display(CAMPAIGN_TEMPLATE_PATH . $dir_name  . "/active/site_frame.tpl");
            $objCampaignSess->delCampaign();
        } else {
            $objView->display(SITE_FRAME);
        }
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

        // カートが空かどうかを確認する。
        $objCartSess = new SC_CartSession();
        $this->tpl_cart_empty = count($objCartSess->getCartList()) < 1;

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
