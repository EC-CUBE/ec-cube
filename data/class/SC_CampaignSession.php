<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/* キャンペーン管理クラス */
class SC_CampaignSession {
	var $key;
	var $campaign_id = 'campaign_id';
	var $is_campaign = 'is_campaign';
	var $campaign_dir = 'campaign_dir';
	
	/* コンストラクタ */
	function SC_CampaignSession($key = "campaign") {
		sfDomainSessionStart();
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
		return $_SESSION[$this->key][$this->is_campaign];
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
		return $_SESSION[$this->key][$this->campaign_dir];
	}
	
	/* キャンペーンページならフレームを変更 */
	function pageView($objView, $site_frame = SITE_FRAME) {
		if($this->getIsCampaign()) {
			$objView->display(CAMPAIGN_TEMPLATE_PATH . $this->getCampaignDir()  . "/active/site_frame.tpl");
		} else {
			$objView->display($site_frame);
		}
	}
}
?>