<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once("./index_csv.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'order/index.tpl';
		$this->tpl_subnavi = 'order/subnavi.tpl';
		$this->tpl_mainno = 'order';		
		$this->tpl_subno = 'index';
		$this->tpl_pager = DATA_PATH . 'Smarty/templates/admin/pager.tpl';
		$this->tpl_subtitle = '�������';
		global $arrORDERSTATUS;
		$this->arrORDERSTATUS = $arrORDERSTATUS;
		global $arrORDERSTATUS_COLOR;
		$this->arrORDERSTATUS_COLOR = $arrORDERSTATUS_COLOR;
		global $arrSex;
		$this->arrSex = $arrSex;
		global $arrPageMax;
		$this->arrPageMax = $arrPageMax;
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
		$sqlval['del_flg'] = '1';
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
		$where = "del_flg = 0";
		foreach ($arrRet as $key => $val) {
			if($val == "") {
				continue;
			}
			$val = sfManualEscape($val);
			
			switch ($key) {
				case 'search_order_name':
					if(DB_TYPE == "pgsql"){
						$where .= " AND order_name01||order_name02 ILIKE ?";
					}elseif(DB_TYPE == "mysql"){
						$where .= " AND concat(order_name01,order_name02) ILIKE ?";
					}
					$nonsp_val = ereg_replace("[ ��]+","",$val);
					$arrval[] = "%$nonsp_val%";
					break;
				case 'search_order_kana':
					if(DB_TYPE == "pgsql"){
						$where .= " AND order_kana01||order_kana02 ILIKE ?";
					}elseif(DB_TYPE == "mysql"){
						$where .= " AND concat(order_kana01,order_kana02) ILIKE ?";
					}
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
					if(DB_TYPE == "pgsql"){
						$where .= " AND (order_tel01||order_tel02||order_tel03) ILIKE ?";
					}elseif(DB_TYPE == "mysql"){
						$where .= " AND concat(order_tel01,order_tel02,order_tel03) ILIKE ?";
					}
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
				case 'search_startyear_c':
					$date = sfGetTimestamp($_POST['search_startyear_c'], $_POST['search_startmonth_c'], $_POST['search_startday_c']);
					$where.= " AND create_date >= ?";
					$arrval[] = $date;
					break;
				case 'search_endyear_c':
					$date = sfGetTimestamp($_POST['search_endyear_c'], $_POST['search_endmonth_c'], $_POST['search_endday_c'], true);
					$where.= " AND create_date <= ?";
					$arrval[] = $date;
					break;
                case 'search_startyear_u':
					$date = sfGetTimestamp($_POST['search_startyear_u'], $_POST['search_startmonth_u'], $_POST['search_startday_u']);
					$where.= " AND update_date >= ?";
					$arrval[] = $date;
					break;
				case 'search_endyear_u':
					$date = sfGetTimestamp($_POST['search_endyear_u'], $_POST['search_endmonth_u'], $_POST['search_endday_u'], true);
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
			$sqlval['del_flg'] = 1;
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
	$objFormParam->addParam("������", "search_startyear_c", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("������", "search_startmonth_c", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("������", "search_startday_c", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("������", "search_endyear_c", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("������", "search_endmonth_c", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("������", "search_endday_c", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("�ѹ���", "search_startyear_u", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�ѹ���", "search_startmonth_u", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�ѹ���", "search_startday_u", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�ѹ���", "search_endyear_u", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�ѹ���", "search_endmonth_u", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�ѹ���", "search_endday_u", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
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