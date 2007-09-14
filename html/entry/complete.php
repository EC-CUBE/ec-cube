<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css = URL_DIR.'css/layout/entry/complete.css';	// �ᥤ��CSS�ѥ�
		
		if(CUSTOMER_CONFIRM_MAIL == true) {
			// �������Ͽ��λ
			$this->tpl_mainpage = 'entry/complete.tpl';			// �ᥤ��ƥ�ץ졼��
		} else {
			// �ܲ����Ͽ��λ
			$this->tpl_mainpage = 'regist/complete.tpl';		// �ᥤ��ƥ�ץ졼��			
		}
		
		$this->tpl_title .= '�����Ͽ(��λ�ڡ���)';			//���ڡ��������ȥ�
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCampaignSess = new SC_CampaignSession();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

// �����ڡ��󤫤�����ܤ������å�
$objPage->is_campaign = $objCampaignSess->getIsCampaign();
$objPage->campaign_dir = $objCampaignSess->getCampaignDir();

$objView->assignobj($objPage);
// �ե졼�������(�����ڡ���ڡ����������ܤʤ��ѹ�)
$objCampaignSess->pageView($objView);
//----------------------------------------------------------------------------------------------------------------------
?>