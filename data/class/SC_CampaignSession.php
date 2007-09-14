<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/* �����ڡ���������饹 */
class SC_CampaignSession {
	var $key;
	var $campaign_id = 'campaign_id';
	var $is_campaign = 'is_campaign';
	var $campaign_dir = 'campaign_dir';
	
	/* ���󥹥ȥ饯�� */
	function SC_CampaignSession($key = "campaign") {
		sfDomainSessionStart();
		$this->key = $key;
	}

	/* �����ڡ���ID�򥻥å� */
	function setCampaignId($campaign_id) {
		$_SESSION[$this->key][$this->campaign_id] = $campaign_id;
	}
	
	/* �����ڡ���ID����� */
	function getCampaignId() {
		return $_SESSION[$this->key][$this->campaign_id];
	}
			
	/* �����ڡ���ڡ�����������ܾ�����ݻ� */
	function setIsCampaign() {
		$_SESSION[$this->key][$this->is_campaign] = true;
	}

	/* �����ڡ���ڡ�����������ܾ������� */
	function getIsCampaign() {
		return $_SESSION[$this->key][$this->is_campaign];
	}

	/* �����ڡ��������� */
	function delCampaign() {
		unset($_SESSION[$this->key]);
	}

	/* �����ڡ���ǥ��쥯�ȥ�̾�򥻥å� */
	function setCampaignDir($campaign_dir) {
		$_SESSION[$this->key][$this->campaign_dir] = $campaign_dir;
	}
	
	/* �����ڡ���ǥ��쥯�ȥ�̾����� */
	function getCampaignDir() {
		return $_SESSION[$this->key][$this->campaign_dir];
	}
	
	/* �����ڡ���ڡ����ʤ�ե졼����ѹ� */
	function pageView($objView, $site_frame = SITE_FRAME) {
		if($this->getIsCampaign()) {
			$objView->display(CAMPAIGN_TEMPLATE_PATH . $this->getCampaignDir()  . "/active/site_frame.tpl");
		} else {
			$objView->display($site_frame);
		}
	}
}
?>