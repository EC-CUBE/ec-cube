<?php

require_once("../require.php");
//require_once("./index_csv.php");

class LC_Page {
	var $arrForm;
	var $arrHidden;
	var $arrProducts;
	var $arrPageMax;
	function LC_Page() {
		$this->tpl_mainpage = 'products/index_test.tpl';
		$this->tpl_mainno = 'products';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_subno = 'index';
		$this->tpl_pager = ROOT_DIR . 'data/Smarty/templates/admin/pager.tpl';
		$this->tpl_subtitle = '���ʥޥ���';

		global $arrPageMax;
		$this->arrPageMax = $arrPageMax;
		global $arrDISP;
		$this->arrDISP = $arrDISP;
		global $arrSTATUS;
		$this->arrSTATUS = $arrSTATUS;
		global $arrPRODUCTSTATUS_COLOR;
		$this->arrPRODUCTSTATUS_COLOR = $arrPRODUCTSTATUS_COLOR;

	}
}

$objPage = new LC_Page();
$objView = new SC_View();

session_start();

$max = 10;
for($i = 0; $i < $max; $i++) {
	$objPage->arrProducts[$i]['product_id'] = $i;
}

// ���̤�ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

// ����ʸ������Ѵ� 
function lfConvertParam() {
	global $objPage;
	/*
	 *	ʸ������Ѵ�
	 *	K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
	 *	C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
	 *	V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ�	
	 *	n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
	 */
	$arrConvList['search_name'] = "KVa";
	$arrConvList['search_product_code'] = "KVa";
	
	// ʸ���Ѵ�
	foreach ($arrConvList as $key => $val) {
		// POST����Ƥ����ͤΤ��Ѵ����롣
		if(isset($objPage->arrForm[$key])) {
			$objPage->arrForm[$key] = mb_convert_kana($objPage->arrForm[$key] ,$val);
		}
	}
}

// ���顼�����å� 
// ���ϥ��顼�����å�
function lfCheckError() {
	$objErr = new SC_CheckError();
	$objErr->doFunc(array("������", "search_startyear", "search_startmonth", "search_startday"), array("CHECK_DATE"));
	$objErr->doFunc(array("��λ��", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_DATE"));
	$objErr->doFunc(array("������", "��λ��", "search_startyear", "search_startmonth", "search_startday", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_SET_TERM"));
	return $objErr->arrErr;
}

// �����å��ܥå�����WHEREʸ����
function lfGetCBWhere($key, $max) {
	$str = "";
	$find = false;
	for ($cnt = 1; $cnt <= $max; $cnt++) {
		if ($_POST[$key . $cnt] == "1") {
			$str.= "1";
			$find = true;
		} else {
			$str.= "_";
		}
	}
	if (!$find) {
		$str = "";
	}
	return $str;
}

// ���ƥ���ID�򥭡������ƥ���̾���ͤˤ���������֤���
function lfGetIDName($arrCatList) {
	$max = count($arrCatList);
	for ($cnt = 0; $cnt < $max; $cnt++ ) {
		$key = $arrCatList[$cnt]['category_id'];
		$val = $arrCatList[$cnt]['category_name'];
		$arrRet[$key] = $val;	
	}
	return $arrRet;
}

?>