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
 * キャンペーンプレビュー のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Contents_CampaignPreview extends LC_Page {

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
        $objView = new SC_SiteView(false);
        $objQuery = new SC_Query();

        // 正しく値が取得できない場合はキャンペーンTOPへ
        if($_GET['campaign_id'] == "" || $_GET['status'] == "") {
            $this->sendRedirect($this->getLocation(URL_CAMPAIGN_TOP));
            exit;
        }

        // statusの判別
        switch($_GET['status']) {
        case 'active':
            $status = CAMPAIGN_TEMPLATE_ACTIVE;
            break;
        case 'end':
            $status = CAMPAIGN_TEMPLATE_END;
            break;
        default:
            $status = CAMPAIGN_TEMPLATE_ACTIVE;
            break;
        }

        // ディレクトリ名を取得名		
        $directory_name = $objQuery->get("dtb_campaign", "directory_name", "campaign_id = ?", array($_GET['campaign_id']));

        $template_dir = CAMPAIGN_TEMPLATE_PATH . $directory_name  . "/" . $status . "preview.tpl";

        //----　ページ表示
        $objView->assignobj($this);
        $objView->display($template_dir);
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
