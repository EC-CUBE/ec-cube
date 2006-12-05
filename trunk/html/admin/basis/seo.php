<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");
require_once(DATA_PATH . "include/page_layout.inc");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_mainpage = 'basis/seo.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_subno = 'seo';
		$this->tpl_mainno = 'basis';
		$this->tpl_subtitle = 'SEO����';
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrTAXRULE;
		$this->arrTAXRULE = $arrTAXRULE;
		
	}
}


$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

// �ǡ����μ���
$arrPageData = lfgetPageData(" edit_flg = 2 ");
$objPage->arrPageData = $arrPageData;

$page_id = $_POST['page_id'];

if($_POST['mode'] == "confirm") {
	// ���顼�����å�
	$objPage->arrErr[$page_id] = lfErrorCheck($arrPOST['meta'][$page_id]);
	
	// ���顼���ʤ���Хǡ����򹹿�
	if(count($objPage->arrErr[$page_id]) == 0) {

		// �����ǡ������Ѵ�
		$arrMETA = lfConvertParam($_POST['meta'][$page_id]);

		// �����ǡ�����������
		$arrUpdData = array($arrMETA['author'], $arrMETA['description'], $arrMETA['keyword'], $page_id);
		// �ǡ�������
		lfUpdPageData($arrUpdData);
	}else{	
		// POST�Υǡ������ɽ��
		$arrPageData = lfSetData($arrPageData, $arrPOST['meta']);
		$objPage->arrPageData = $arrPageData;
	}
}

// ���顼���ʤ���Хǡ����μ���
if(count($objPage->arrErr[$page_id]) == 0) {
	// �ǡ����μ���
	$arrPageData = lfgetPageData(" edit_flg = 2 ");
	$objPage->arrPageData = $arrPageData;
}

// ɽ������ɽ���ڤ��ؤ�
$arrDisp_flg = array();
foreach($arrPageData as $key => $val){
	$arrDisp_flg[$val['page_id']] = $_POST['disp_flg'.$val['page_id']];
}

$objPage->disp_flg = $arrDisp_flg;

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//--------------------------------------------------------------------------------------------------------------------------------------
/**************************************************************************************************************
 * �ؿ�̾	��lfUpdPageData
 * ��������	���ڡ����쥤�����ȥơ��֥�˥ǡ���������Ԥ�
 * ����		�������ǡ���
 * �����	���������
 **************************************************************************************************************/
function lfUpdPageData($arrUpdData = array()){
	$objQuery = new SC_Query();
	$sql = "";

	// SQL����
	$sql .= " UPDATE ";
	$sql .= "     dtb_pagelayout ";
	$sql .= " SET ";
	$sql .= "     author = ? , ";
	$sql .= "     description = ? , ";
	$sql .= "     keyword = ? ";
	$sql .= " WHERE ";
	$sql .= "     page_id = ? ";
	$sql .= " ";

	// SQL�¹�
	$ret = $objQuery->query($sql, $arrUpdData);
	
	return $ret;	
}

/**************************************************************************************************************
 * �ؿ�̾	��lfErrorCheck
 * ��������	�����Ϲ��ܤΥ��顼�����å���Ԥ�
 * ����		�����顼�����å��оݥǡ���
 * �����	�����顼����
 **************************************************************************************************************/
function lfErrorCheck($array) {
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("�᥿����:Author", "author", STEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�᥿����:Description", "description", STEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�᥿����:Keywords", "keyword", STEXT_LEN), array("MAX_LENGTH_CHECK"));

	return $objErr->arrErr;
}

/**************************************************************************************************************
 * �ؿ�̾	��lfSetData
 * ��������	���ƥ�ץ졼��ɽ���ǡ������ͤ򥻥åȤ���
 * ����1	��ɽ�����ǡ���
 * ����2	��ɽ���ǡ���
 * �����	��ɽ���ǡ���
 **************************************************************************************************************/
function lfSetData($arrPageData, $arrDispData){
	
	foreach($arrPageData as $key => $val){
		$page_id = $val['page_id'];
		$arrPageData[$key]['author'] = $arrDispData[$page_id]['author'];
		$arrPageData[$key]['description'] = $arrDispData[$page_id]['description'];
		$arrPageData[$key]['keyword'] = $arrDispData[$page_id]['keyword'];
	}
	
	return $arrPageData;
}

/* ����ʸ������Ѵ� */
function lfConvertParam($array) {
	/*
	 *	ʸ������Ѵ�
	 *	K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
	 *	C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
	 *	V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ�	
	 *	n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
	 *  a :  ���ѱѿ�����Ⱦ�ѱѿ������Ѵ�����
	 */
	// ��ʪ���ܾ���
	
	// ���ݥåȾ���
	$arrConvList['author'] = "KVa";
	$arrConvList['description'] = "KVa";
	$arrConvList['keyword'] = "KVa";

	// ʸ���Ѵ�
	foreach ($arrConvList as $key => $val) {
		// POST����Ƥ����ͤΤ��Ѵ����롣
		if(isset($array[$key])) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}


?>