<?php
/**
 * 
 * @copyright	2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: affiliate.php 8813 2006-12-04 05:24:35Z kakinaka $
 * @link		http://www.lockon.co.jp/
 *
 */

 
 
require_once("./require.php");

$arrConversionPage = array(
	1 => '���ʹ�����λ����',
	2 => '�����Ͽ��λ����'
);

//�ڡ����������饹
class LC_Page {
	//���󥹥ȥ饯��
	function LC_Page() {
		//�ᥤ��ƥ�ץ졼�Ȥλ���
		$this->tpl_mainpage = MODULE_PATH . 'affiliate.tpl';
		$this->tpl_subtitle = '���ե��ꥨ���ȥ���������';
		global $arrConversionPage;
		$this->arrConversionPage = $arrConversionPage;
	}
}
$objPage = new LC_Page();
$objView = new SC_AdminView();

// ǧ�ڳ�ǧ
$objSess = new SC_Session();
sfIsSuccess($objSess);

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
$objFormParam = lfInitParam($objFormParam);
// POST�ͤμ���
$objFormParam->setParam($_POST);

switch($_POST['mode']) {
case 'edit':
	// ���ϥ��顼Ƚ��
	$objPage->arrErr = $objFormParam->checkError();
	if(count($objPage->arrErr) == 0) {
		$arrRet = $objQuery->select("sub_data", "dtb_module", "module_id = ?", array(AFF_TAG_MID));
		$arrSubData = unserialize($arrRet[0]['sub_data']);
		$arrRet = $objFormParam->getHashArray();		
		$arrSubData[$arrRet['conv_page']] = $arrRet['aff_tag'];
		$sqlval['sub_data'] = serialize($arrSubData);
		$objQuery = new SC_Query();
		$objQuery->update("dtb_module", $sqlval, "module_id = ?", array(AFF_TAG_MID));
		// javascript�¹�
		$objPage->tpl_onload = "window.close();";
	}
	break;
// ����С������ڡ���������
case 'select':
	if(is_numeric($_POST['conv_page'])) {
		// sub_data��꥿��������ɤ߹���
		$conv_page = $_POST['conv_page'];
		$arrRet = $objQuery->select("sub_data", "dtb_module", "module_id = ?", array(AFF_TAG_MID));
		$arrSubData = unserialize($arrRet[0]['sub_data']);
		$aff_tag = $arrSubData[$conv_page];
		$objFormParam->setValue('conv_page', $conv_page);
		$objFormParam->setValue('aff_tag', $aff_tag);		
	}
	break;
default:
	break;
}

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);					//�ѿ���ƥ�ץ졼�Ȥ˥������󤹤�
$objView->display($objPage->tpl_mainpage);		//�ƥ�ץ졼�Ȥν���
//-------------------------------------------------------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam($objFormParam) {
	$objFormParam->addParam("����С������ڡ���", "conv_page", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("���ե��ꥨ���ȥ���", "aff_tag", MTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));	
	return $objFormParam;
}
?>