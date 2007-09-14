<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'entry/kiyaku.tpl';
		$this->tpl_title="�����ѵ���";
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCustomer = new SC_Customer();
$objCampaignSess = new SC_CampaignSession();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

// �������Ƥμ���
$objQuery = new SC_Query();
$objQuery->setorder("rank DESC");
$arrRet = $objQuery->select("kiyaku_title, kiyaku_text", "dtb_kiyaku", "del_flg <> 1");

$max = count($arrRet);
$objPage->tpl_kiyaku_text = "";
for ($i = 0; $i < $max; $i++) {
	$objPage->tpl_kiyaku_text.=$arrRet[$i]['kiyaku_title'] . "\n\n"; 
	$objPage->tpl_kiyaku_text.=$arrRet[$i]['kiyaku_text'] . "\n\n"; 
}

// �����ڡ��󤫤�����ܤ������å�
$objPage->is_campaign = $objCampaignSess->getIsCampaign();
$objPage->campaign_dir = $objCampaignSess->getCampaignDir();

$objView->assignobj($objPage);
// �ե졼�������(�����ڡ���ڡ����������ܤʤ��ѹ�)
$objCampaignSess->pageView($objView);
//--------------------------------------------------------------------------------------------------------------------------
?>
