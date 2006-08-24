<?php

require_once("../../require.php");
require_once("./index_csv.php");
//require_once("../../require2.php");

class LC_Page {
	var $arrForm;
	var $arrHidden;
	var $arrProducts;
	var $arrPageMax;
	function LC_Page() {
		$this->tpl_mainpage = 'products/index.tpl';
//		$this->tpl_mainpage="products/test.tpl";

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

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();
$objFormParam->setParam($_POST);

$objFormParam->splitParamCheckBoxes('search_order_sex');
$objFormParam->splitParamCheckBoxes('search_payment_id');

// ������ɤΰ����Ѥ�
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key)) {
		switch($key) {
			case 'search_order_sex':
			case 'search_payment_id':
				$objPage->arrHidden[$key] = sfMergeParamCheckBoxes($val);
				break;
			default:
				$objPage->arrHidden[$key] = $val;
				break;
		}		
	}
}

// �ڡ���������
$objPage->arrHidden['search_pageno'] = $_POST['search_pageno'];

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

if($_POST['mode'] == 'delete') {
	if(sfIsInt($_POST['order_id'])) {
		$objQuery = new SC_Query();
		$where = "order_id = ?";
		$sqlval['delete'] = '1';
		$objQuery->update("dtb_order", $sqlval, $where, array($_POST['order_id']));
	}	
}

switch($_POST['mode']) {
case 'delete':
case 'csv':
case 'delete_all':
case 'search':
	// �����ͤ��Ѵ�
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($arrRet);
	$arrRet = $objFormParam->getHashArray();
	// ���Ϥʤ�
	if (count($objPage->arrErr) == 0) {
		$where = "delete = 0";
		foreach ($arrRet as $key => $val) {
			if($val == "") {
				continue;
			}
			$val = sfManualEscape($val);
			
			switch ($key) {
				case 'search_order_name':
					$where .= " AND order_name01||order_name02 ILIKE ?";
					$nonsp_val = ereg_replace("[ ��]+","",$val);
					$arrval[] = "%$nonsp_val%";
					break;
				case 'search_order_kana':
					$where .= " AND order_kana01||order_kana02 ILIKE ?";
					$nonsp_val = ereg_replace("[ ��]+","",$val);
					$arrval[] = "%$nonsp_val%";
					break;
				case 'search_order_id1':
					$where .= " AND order_id >= ?";
					$arrval[] = $val;
					break;
				case 'search_order_id2':
					$where .= " AND order_id <= ?";
					$arrval[] = $val;
					break;
				case 'search_order_sex':
					$tmp_where = "";
					foreach($val as $element) {
						if($element != "") {
							if($tmp_where == "") {
								$tmp_where .= " AND (order_sex = ?";
							} else {
								$tmp_where .= " OR order_sex = ?";
							}
							$arrval[] = $element;
						}
					}
					
					if($tmp_where != "") {
						$tmp_where .= ")";
						$where .= " $tmp_where ";
					}					
					break;
				case 'search_order_tel':
					$where .= " AND (order_tel01||order_tel02||order_tel03) ILIKE ?";
					$nonmark_val = ereg_replace("[()-]+","",$val);
					$arrval[] = "$nonmark_val%";
					break;
				case 'search_order_email':
					$where .= " AND order_email ILIKE ?";
					$arrval[] = "%$val%";
					break;
				case 'search_payment_id':
					$tmp_where = "";
					foreach($val as $element) {
						if($element != "") {
							if($tmp_where == "") {
								$tmp_where .= " AND (payment_id = ?";
							} else {
								$tmp_where .= " OR payment_id = ?";
							}
							$arrval[] = $element;
						}
					}
					
					if($tmp_where != "") {
						$tmp_where .= ")";
						$where .= " $tmp_where ";
					}
					break;
				case 'search_total1':
					$where .= " AND total >= ?";
					$arrval[] = $val;
					break;
				case 'search_total2':
					$where .= " AND total <= ?";
					$arrval[] = $val;
					break;
				case 'search_startyear':
					$date = sfGetTimestamp($_POST['search_startyear'], $_POST['search_startmonth'], $_POST['search_startday']);
					$where.= " AND update_date >= ?";
					$arrval[] = $date;
					break;
				case 'search_endyear':
					$date = sfGetTimestamp($_POST['search_endyear'], $_POST['search_endmonth'], $_POST['search_endday'], true);
					$where.= " AND update_date <= ?";
					$arrval[] = $date;
					break;
				case 'search_sbirthyear':
					$date = sfGetTimestamp($_POST['search_sbirthyear'], $_POST['search_sbirthmonth'], $_POST['search_sbirthday']);
					$where.= " AND order_birth >= ?";
					$arrval[] = $date;
					break;
				case 'search_ebirthyear':
					$date = sfGetTimestamp($_POST['search_ebirthyear'], $_POST['search_ebirthmonth'], $_POST['search_ebirthday'], true);
					$where.= " AND order_birth <= ?";
					$arrval[] = $date;
					break;
				case 'search_order_status':
					$where.= " AND status = ?";
					$arrval[] = $val;
					break;
				default:
					break;
			}
		}
		
		$order = "update_date DESC";
		
		switch($_POST['mode']) {
		case 'csv':
			// ���ץ����λ���
			$option = "ORDER BY $order";
			
			// CSV���ϥ����ȥ�Ԥκ���
			$arrCsvOutput = sfSwapArray(sfgetCsvOutput(3, " WHERE csv_id = 3 AND status = 1"));
			
			if (count($arrCsvOutput) <= 0) break;
			
			$arrCsvOutputCols = $arrCsvOutput['col'];
			$arrCsvOutputTitle = $arrCsvOutput['disp_name'];
			$head = sfGetCSVList($arrCsvOutputTitle);
			$data = lfGetCSV("dtb_order", $where, $option, $arrval, $arrCsvOutputCols);
			
			// CSV���������롣
			sfCSVDownload($head.$data);
			exit;
			break;
		case 'delete_all':
			// ������̤򤹤٤ƺ��
			$sqlval['delete'] = 1;
			$objQuery = new SC_Query();
			$objQuery->update("dtb_order", $sqlval, $where, $arrval);
			break;
		default:
			// �ɤ߹�����ȥơ��֥�λ���
			$col = "*";
			$from = "dtb_order";
			
			$objQuery = new SC_Query();
			// �Կ��μ���
			$linemax = $objQuery->count($from, $where, $arrval);
			$objPage->tpl_linemax = $linemax;				// ���郎�������ޤ�����ɽ����
			
			// �ڡ�������ν���
			if(is_numeric($_POST['search_page_max'])) {	
				$page_max = $_POST['search_page_max'];
			} else {
				$page_max = SEARCH_PMAX;
			}
			
			// �ڡ�������μ���
			$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnNaviSearchPage", NAVI_PMAX);
			$startno = $objNavi->start_row;
			$objPage->arrPagenavi = $objNavi->arrPagenavi;		
			
			// �����ϰϤλ���(���Ϲ��ֹ桢�Կ��Υ��å�)
			$objQuery->setlimitoffset($page_max, $startno);
			// ɽ�����
			$objQuery->setorder($order);
			// ������̤μ���
			$objPage->arrResults = $objQuery->select($col, $from, $where, $arrval);
		}
	}
	break;
	
default:
	break;
}

$objDate = new SC_Date();
// ��Ͽ��������������
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrRegistYear = $objDate->getYear();
// ��ǯ����������
$objDate->setStartYear(BIRTH_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrBirthYear = $objDate->getYear();
// ����������
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

// �����ͤμ���
$objPage->arrForm = $objFormParam->getFormParamList();
// ��ʧ����ˡ�μ���
$arrRet = sfGetPayment();
$objPage->arrPayment = sfArrKeyValue($arrRet, 'payment_id', 'payment_method');

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("�����ֹ�1", "search_order_id1", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�����ֹ�2", "search_order_id2", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�б�����", "search_order_status", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�ܵ�̾", "search_order_name", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܵ�̾(����)", "search_order_kana", STEXT_LEN, "KVCa", array("KANA_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("����", "search_order_sex", INT_LEN, "n", array("MAX_LENGTH_CHECK"));
	$objFormParam->addParam("ǯ��1", "search_age1", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("ǯ��2", "search_age2", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�᡼�륢�ɥ쥹", "search_order_email", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
	$objFormParam->addParam("TEL", "search_order_tel", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
	$objFormParam->addParam("��ʧ����ˡ", "search_payment_id", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�������1", "search_total1", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�������2", "search_total2", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("ɽ�����", "search_page_max", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("������", "search_startyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("������", "search_startmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("������", "search_startday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��λ��", "search_endyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��λ��", "search_endmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��λ��", "search_endday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("������", "search_sbirthyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("������", "search_sbirthmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("������", "search_sbirthday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��λ��", "search_ebirthyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��λ��", "search_ebirthmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��λ��", "search_ebirthday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
}

/* �������ƤΥ����å� */
function lfCheckError() {
	global $objFormParam;
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	// �ü���ܥ����å�
	$objErr->doFunc(array("�����ֹ�1", "�����ֹ�2", "search_order_id1", "search_order_id2"), array("GREATER_CHECK"));
	$objErr->doFunc(array("ǯ��1", "ǯ��2", "search_age1", "search_age2"), array("GREATER_CHECK"));
	$objErr->doFunc(array("�������1", "�������2", "search_total1", "search_total2"), array("GREATER_CHECK"));
	$objErr->doFunc(array("������", "search_startyear", "search_startmonth", "search_startday"), array("CHECK_DATE"));
	$objErr->doFunc(array("��λ��", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_DATE"));
	$objErr->doFunc(array("������", "��λ��", "search_startyear", "search_startmonth", "search_startday", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_SET_TERM"));
	
	$objErr->doFunc(array("������", "search_sbirthyear", "search_sbirthmonth", "search_sbirthday"), array("CHECK_DATE"));
	$objErr->doFunc(array("��λ��", "search_ebirthyear", "search_ebirthmonth", "search_ebirthday"), array("CHECK_DATE"));
	$objErr->doFunc(array("������", "��λ��", "search_sbirthyear", "search_sbirthmonth", "search_sbirthday", "search_ebirthyear", "search_ebirthmonth", "search_ebirthday"), array("CHECK_SET_TERM"));

	return $objErr->arrErr;
}


?>

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objDate = new SC_Date();


// ��Ͽ��������������ǯ
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrStartYear = $objDate->getYear();
$objPage->arrStartMonth = $objDate->getMonth();
$objPage->arrStartDay = $objDate->getDay();
// ��Ͽ������������λǯ
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrEndYear = $objDate->getYear();
$objPage->arrEndMonth = $objDate->getMonth();
$objPage->arrEndDay = $objDate->getDay();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);
//�����ڡ�����Խ���
if(sfIsInt($_POST['campaign_id']) && $_POST['mode'] == "camp_search") {
	$objQuery = new SC_Query();
	$search_data = $objQuery->get("dtb_campaign", "search_condition", "campaign_id = ? ", array($_POST['campaign_id']));
	$arrSearch = unserialize($search_data);
	foreach ($arrSearch as $key => $val) {
		$_POST[$key] = $val;
	}
}

// POST�ͤΰ����Ѥ�
$objPage->arrForm = $_POST;

// ������ɤΰ����Ѥ�
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key) || ereg("^campaign_", $key)) {
		switch($key) {
			case 'search_product_flag':
			case 'search_status':
				$objPage->arrHidden[$key] = sfMergeParamCheckBoxes($val);
				if(!is_array($val)) {
					$objPage->arrForm[$key] = split("-", $val);
				}
				break;
			default:
				$objPage->arrHidden[$key] = $val;
				break;
		}
	}
}

// �ڡ���������
$objPage->arrHidden['search_pageno'] = $_POST['search_pageno'];

// ���ʺ��
if ($_POST['mode'] == "delete") {
	if($_POST['category_id'] != "") {
		// ����դ��쥳���ɤκ��
		$where = "category_id = " . addslashes($_POST['category_id']);
		sfDeleteRankRecord("dtb_products", "product_id", $_POST['product_id'], $where);
	} else {
		sfDeleteRankRecord("dtb_products", "product_id", $_POST['product_id']);
	}
	// �ҥơ��֥�(���ʵ���)�κ��
	$objQuery = new SC_Query();
	$objQuery->delete("dtb_products_class", "product_id = ?", array($_POST['product_id']));
	
	// ���������ȥХå��¹�
	sfCategory_Count($objQuery);	
}


if ($_POST['mode'] == "search" || $_POST['mode'] == "csv"  || $_POST['mode'] == "delete" || $_POST['mode'] == "delete_all" || $_POST['mode'] == "camp_search") {
	// ����ʸ���ζ����Ѵ�
	lfConvertParam();
	// ���顼�����å�
	$objPage->arrErr = lfCheckError();

	$where = "delete = 0";

	// ���ϥ��顼�ʤ�
	if (count($objPage->arrErr) == 0) {

		foreach ($objPage->arrForm as $key => $val) {
				
			$val = sfManualEscape($val);
			
			if($val == "") {
				continue;
			}
			
			switch ($key) {
				case 'search_product_id':
					$where .= " AND product_id = ?";
					$arrval[] = $val;
					break;
				case 'search_product_class_id':
					$where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_class_id = ?)";
					$arrval[] = $val;
					break;
				case 'search_name':
					$where .= " AND name ILIKE ?";
					$arrval[] = "%$val%";
					break;
				case 'search_category_id':
					list($tmp_where, $tmp_arrval) = sfGetCatWhere($val);
					if($tmp_where != "") {
						$where.= " AND $tmp_where";
						$arrval = array_merge($arrval, $tmp_arrval);
					}
					break;
				case 'search_product_code':
					$where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code ILIKE ? GROUP BY product_id)";
					$arrval[] = "%$val%";
					break;
				case 'search_startyear':
					$date = sfGetTimestamp($_POST['search_startyear'], $_POST['search_startmonth'], $_POST['search_startday']);
					$where.= " AND update_date >= ?";
					$arrval[] = $date;
					break;
				case 'search_endyear':
					$date = sfGetTimestamp($_POST['search_endyear'], $_POST['search_endmonth'], $_POST['search_endday']);
					$where.= " AND update_date <= ?";
					$arrval[] = $date;
					break;
				case 'search_product_flag':
					global $arrSTATUS;
					$search_product_flag = sfSearchCheckBoxes($val);
					if($search_product_flag != "") {
						$where.= " AND product_flag LIKE ?";
						$arrval[] = $search_product_flag;					
					}
					break;
				case 'search_status':
					$tmp_where = "";
					foreach ($val as $element){
						if ($element != ""){
							if ($tmp_where == ""){
								$tmp_where.="AND (status LIKE ? ";
							}else{
								$tmp_where.="OR status LIKE ? ";
							}
							$arrval[]=$element;
						}
					}
					if ($tmp_where != ""){
						$tmp_where.=")";
						$where.= "$tmp_where";
					}
					break;
				default:
					break;
			}
		}

		$order = "update_date DESC";
		$objQuery = new SC_Query();
		
		switch($_POST['mode']) {
		case 'csv':
			// ���ץ����λ���
			$option = "ORDER BY $order";
			// CSV���ϥ����ȥ�Ԥκ���
			$arrOutput = sfSwapArray(sfgetCsvOutput(1, " WHERE csv_id = 1 AND status = 1"));
			
			if (count($arrOutput) <= 0) break;
			
			$arrOutputCols = $arrOutput['col'];
			$arrOutputTitle = $arrOutput['disp_name'];
			
			$head = sfGetCSVList($arrOutputTitle);
			
			$data = lfGetProductsCSV($where, $option, $arrval, $arrOutputCols);

			// CSV���������롣
			sfCSVDownload($head.$data);
			exit;
			break;
		case 'delete_all':
			// ������̤򤹤٤ƺ��
			$where = "product_id IN (SELECT product_id FROM vw_products_nonclass WHERE $where)";
			$sqlval['delete'] = 1;
			$objQuery->update("dtb_products", $sqlval, $where, $arrval);
			break;
		default:
			// �ɤ߹�����ȥơ��֥�λ���
			$col = "product_id, name, category_id, main_list_image, status, product_code, price01, price02, stock, stock_unlimited";
			$from = "vw_products_nonclass";

			// �Կ��μ���
			$linemax = $objQuery->count($from, $where, $arrval);
			$objPage->tpl_linemax = $linemax;				// ���郎�������ޤ�����ɽ����

			// �ڡ�������ν���
			if(is_numeric($_POST['search_page_max'])) {	
				$page_max = $_POST['search_page_max'];
			} else {
				$page_max = SEARCH_PMAX;
			}

			// �ڡ�������μ���
			$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnNaviSearchPage", NAVI_PMAX);
			$startno = $objNavi->start_row;
			$objPage->arrPagenavi = $objNavi->arrPagenavi;
			
			//�����ڡ����ʸ������ϡ�����̤ξ���ID���ѿ��˳�Ǽ����
			if($_POST['search_mode'] == 'campaign') {
				$arrRet = $objQuery->select($col, $from, $where, $arrval);
				if(count($arrRet) > 0) {
					$arrRet = sfSwapArray($arrRet);
					$pid = implode("-", $arrRet['product_id']);
					$objPage->arrHidden['campaign_product_id'] = $pid;
				}
			}

			// �����ϰϤλ���(���Ϲ��ֹ桢�Կ��Υ��å�)
			$objQuery->setlimitoffset($page_max, $startno);
			// ɽ�����
			$objQuery->setorder($order);
			// ������̤μ���
			$objPage->arrProducts = $objQuery->select($col, $from, $where, $arrval);
//			$arrProducts = $objQuery->select($col, $from, $where, $arrval);
			
//			$objPage->arrTest = $arrProducts;
			
			$objPage->tpl_mainpage="products/test.tpl";

			break;
		}
	}
}
/*
$arrProducts = Array
(
    '0' => Array
        (
            'product_id' => '18',
            'name' => 'test',
            'category_id' => '11',
            'main_list_image' => '08172054_44e458f942afc.gif',
            'status' => '1',
            'product_code' => 'cd 01',
            'price01' => '500',
            'price02' => '500',
            'stock' => '43',
            'stock_unlimited' => ""
        ),

    '1' => Array
        (
            'product_id' => '14',
            'name' => 'LPO���ӥ�',
            'category_id' => '10',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '14999',
            'stock_unlimited' => ""
        ),

    '2' => Array
        (
            'product_id' => '16',
            'name' => 'LPO���ӥ�',
            'category_id' => '10',
            'main_list_image' => '08181941_44e59975c535d.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '14927',
            'stock_unlimited' => ""
        ),

    '3' => Array
        (
            'product_id' => '15',
            'name' => 'LPO���ӥ�',
            'category_id' => '10',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '14998',
            'stock_unlimited' => ""
        ),
    '4' => Array
        (
            'product_id' => '17',
            'name' => 'LPO���ӥ�',
            'category_id' => '15',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '0',
            'stock_unlimited' => ""
        ),

    '5' => Array
        (
            'product_id' => '13',
            'name' => 'LPO���ӥ�',
            'category_id' => '10',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '15000',
            'stock_unlimited' => ""
        ),

    '6' => Array
        (
            'product_id' => '12',
            'name' => 'LPO���ӥ�',
            'category_id' => '10',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '15000',
            'stock_unlimited' => ""
        ),

    '7' => Array
        (
            'product_id' => '11',
            'name' => 'LPO���ӥ�',
            'category_id' => '10',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '15000',
            'stock_unlimited' => ""
        ),
    '8' => Array
        (
            'product_id' => '10',
            'name' => 'LPO���ӥ�',
            'category_id' => '10',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '15000',
            'stock_unlimited' => ""
        ),

    '9' => Array
        (
            'product_id' => '9',
            'name' => 'LPO���ӥ�',
            'category_id' => '10',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '15000',
            'stock_unlimited' => ""
        )

);


$objPage->arrProducts = $arrProducts;
*/

// ���ƥ�����ɹ�
$objPage->arrCatList = sfGetCategoryList();
$objPage->arrCatIDName = lfGetIDName($objPage->arrCatList);

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