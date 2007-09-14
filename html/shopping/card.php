<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

$arrJPO_INFO['10'] = "���ʧ��";
$arrJPO_INFO['21'] = "�ܡ��ʥ����ʧ��";
$arrJPO_INFO['80'] = "���ʧ��";
$arrJPO_INFO['61C02'] = "ʬ��2��ʧ��";
$arrJPO_INFO['61C03'] = "ʬ��3��ʧ��";
$arrJPO_INFO['61C05'] = "ʬ��5��ʧ��";
$arrJPO_INFO['61C06'] = "ʬ��6��ʧ��";
$arrJPO_INFO['61C10'] = "ʬ��10��ʧ��";
$arrJPO_INFO['61C12'] = "ʬ��12��ʧ��";
$arrJPO_INFO['61C15'] = "ʬ��15��ʧ��";

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_css = URL_DIR.'css/layout/shopping/card.css';	// �ᥤ��CSS�ѥ�
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = 'shopping/card.tpl';			// �ᥤ��ƥ�ץ졼��
		global $arrJPO_INFO;
		$this->arrJPO_INFO = $arrJPO_INFO;
		/*
		 session_start����no-cache�إå������������뤳�Ȥ�
		 �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
		 private-no-expire:���饤����ȤΥ���å������Ĥ��롣
		*/
		session_cache_limiter('private-no-expire');		
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objCampaignSess = new SC_CampaignSession();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();
// POST�ͤμ���
$objFormParam->setParam($_POST);

// ������������������Ƚ��
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

switch($_POST['mode']) {
// ��Ͽ
case 'regist':
	// �����ͤ��Ѵ�
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($arrRet);
	// ���ϥ��顼�ʤ��ξ��
	if(count($objPage->arrErr) == 0) {
		// �����Ƚ��׽���
		$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
		// �������ơ��֥���ɹ�
		$arrData = sfGetOrderTemp($uniqid);
		// �����Ƚ��פ򸵤˺ǽ��׻�
		$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

		// �����ɤ�ǧ�ڤ�Ԥ�
		$arrVal = $objFormParam->getHashArray();
		$card_no = $arrVal['card_no01'].$arrVal['card_no02'].$arrVal['card_no03'].$arrVal['card_no04'];
		$card_exp = $arrVal['card_month']. "/" . $arrVal['card_year']; // MM/DD
		$result = sfGetAuthonlyResult(CGI_DIR, CGI_FILE, $arrVal['name01'], $arrVal['name02'], $card_no, $card_exp, $arrData['payment_total'], $uniqid, $arrVal['jpo_info']);

		// �������Ƥε�Ͽ
		$sqlval['credit_result'] = $result['action-code'];
		$sqlval['credit_msg'] = $result['aux-msg'].$result['MErrMsg'];
		$objQuery = new SC_Query();
		$objQuery->update("dtb_order_temp", $sqlval, "order_temp_id = ?", array($uniqid));
				
		// Ϳ�����������ξ��
		if($result['action-code'] == '000') {
			// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
			$objSiteSess->setRegistFlag();
			// ������λ�ڡ�����
			header("Location: " . URL_SHOP_COMPLETE);
		} else {
			switch($result['action-code']) {
			case '115':
				$objPage->tpl_error = "�� �����ɤ�ͭ�����¤��ڤ�Ƥ��ޤ���";
				break;
			case '212':
				$objPage->tpl_error = "�� �������ֹ�˸�꤬����ޤ���";
				break;
			case '100':
				$objPage->tpl_error = "�� �����ɲ�ҤǤ��������ǧ����ޤ���Ǥ�����";
				break;
			default:
				$objPage->tpl_error = "�� ���쥸�åȥ����ɤξȹ�˼��Ԥ��ޤ�����";
				break;
			}
		}
	}
	break;
// ���Υڡ��������
case 'return':
	// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
	$objSiteSess->setRegistFlag();
	// ��ǧ�ڡ����ذ�ư
	header("Location: " . URL_SHOP_CONFIRM);
	exit;
	break;
}

$objDate = new SC_Date();
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(RELEASE_YEAR + CREDIT_ADD_YEAR);
$objPage->arrYear = $objDate->getZeroYear();
$objPage->arrMonth = $objDate->getZeroMonth();

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);
// �ե졼�������(�����ڡ���ڡ����������ܤʤ��ѹ�)
$objCampaignSess->pageView($objView);
//-----------------------------------------------------------------------------------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("�������ֹ�1", "card_no01", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�������ֹ�2", "card_no02", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�������ֹ�3", "card_no03", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�������ֹ�4", "card_no04", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�����ɴ���ǯ", "card_year", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�����ɴ��·�", "card_month", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��", "card_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
	$objFormParam->addParam("̾", "card_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
	$objFormParam->addParam("����ʧ����ˡ", "jpo_info", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALNUM_CHECK"));
}

/* �������ƤΥ����å� */
function lfCheckError() {
	global $objFormParam;
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	return $objErr->arrErr;
}

?>
