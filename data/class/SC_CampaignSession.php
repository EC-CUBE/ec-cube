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

/* キャンペーン管理クラス */
class SC_CampaignSession {
    var $key;
    var $campaign_id = 'campaign_id';
    var $is_campaign = 'is_campaign';
    var $campaign_dir = 'campaign_dir';

    /* コンストラクタ */
    function SC_CampaignSession($key = "campaign") {
        SC_Utils_Ex::sfDomainSessionStart();
        $this->key = $key;
    }

    /* キャンペーンIDをセット */
    function setCampaignId($campaign_id) {
        $_SESSION[$this->key][$this->campaign_id] = $campaign_id;
    }

    /* キャンペーンIDを取得 */
    function getCampaignId() {
        return $_SESSION[$this->key][$this->campaign_id];
    }

    /* キャンペーンページからの遷移情報を保持 */
    function setIsCampaign() {
        $_SESSION[$this->key][$this->is_campaign] = true;
    }

    /* キャンペーンページからの遷移情報を取得 */
    function getIsCampaign() {
        return isset($_SESSION[$this->key][$this->is_campaign]) ? $_SESSION[$this->key][$this->is_campaign] : false;
    }

    /* キャンペーン情報を削除 */
    function delCampaign() {
        unset($_SESSION[$this->key]);
    }

    /* キャンペーンディレクトリ名をセット */
    function setCampaignDir($campaign_dir) {
        $_SESSION[$this->key][$this->campaign_dir] = $campaign_dir;
    }

    /* キャンペーンディレクトリ名を取得 */
    function getCampaignDir() {
        return isset($_SESSION[$this->key][$this->campaign_dir])
                ? $_SESSION[$this->key][$this->campaign_dir] : "";
    }

    /* キャンペーンページならフレームを変更 */
    function pageView($objView, $site_frame = SITE_FRAME) {
        // XXX キャンペーン削除で不具合があったので、応急処置をしています。(テスト不十分)
        if ($this->getIsCampaign()) {
            $site_frame_campaign = CAMPAIGN_TEMPLATE_PATH . $this->getCampaignDir()  . "/active/site_frame.tpl";
            if (file_exists($site_frame_campaign)) {
                $site_frame = $site_frame_campaign;
            }
        }
        $objView->display($site_frame);
    }
}
?>
