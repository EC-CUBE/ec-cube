<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once("./index_csv.php");

class LC_Page {
	var $arrForm;
	var $arrHidden;
	var $arrProducts;
	var $arrPageMax;
	function LC_Page() {
		$this->tpl_mainpage = 'products/index.tpl';
		$this->tpl_mainno = 'products';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_subno = 'index';
		$this->tpl_pager = DATA_PATH . 'Smarty/templates/admin/pager.tpl';
		$this->tpl_subtitle = '���ʥޥ���';

		global $arrPageMax;
		$this->arrPageMax = $arrPageMax;
		global $arrDISP;
		$this->arrDISP = $arrDISP;
		global $arrSTATUS;
		$this->arrSTATUS = $arrSTATUS;
		global $arrPRODUCTSTATUS_COLOR;
		$this->arrPRODUCTSTATUS_COLOR = $arrPRODUCTSTATUS_COLOR;
		$this->tpl_movilink_flg = sfIsMoviLink();
		/*
		 session_start����no-cache�إå������������뤳�Ȥ�
		 �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
		 private-no-expire:���饤����ȤΥ���å������Ĥ��롣
		*/
		session_cache_limiter('private-no-expire');		
	}
}

//$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();

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


if(isset($_POST['mode'])) {
	// ����ʸ���ζ����Ѵ�
	lfConvertParam();
	// ���顼�����å�
	$objPage->arrErr = lfCheckError();

	$where = "del_flg = 0";
	$view_where = "del_flg = 0";
	
	// ���ϥ��顼�ʤ�
	if (count($objPage->arrErr) == 0) {

		$arrval = array();
		foreach ($objPage->arrForm as $key => $val) {
			$val = sfManualEscape($val);
			
			if($val == "") {
				continue;
			}
			
			switch ($key) {
				case 'search_product_id':	// ����ID
					$where .= " AND product_id = ?";
					$view_where .= " AND product_id = ?";
					$arrval[] = $val;
					break;
				case 'search_product_class_name': //����̾��
					$where_in = " (SELECT classcategory_id FROM dtb_classcategory WHERE class_id IN (SELECT class_id FROM dtb_class WHERE name LIKE ?)) ";
					$where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE classcategory_id1 IN " . $where_in;
					$where .= " OR classcategory_id2 IN" . $where_in . ")";
					$view_where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE classcategory_id1 IN " . $where_in;
					$view_where .= " OR classcategory_id2 IN" . $where_in . ")";
					$arrval[] = "%$val%";
					$arrval[] = "%$val%";
					$view_where = $where;
					break;
				case 'search_name':			// ����̾
					$where .= " AND name ILIKE ?";
					$view_where .= " AND name ILIKE ?";
					$arrval[] = "%$val%";
					break;
				case 'search_category_id':	// ���ƥ��꡼
					list($tmp_where, $tmp_arrval) = sfGetCatWhere($val);
					if($tmp_where != "") {
						$where.= " AND $tmp_where";
						$view_where.= " AND $tmp_where";
						$arrval = array_merge((array)$arrval, (array)$tmp_arrval);
					}
					break;
				case 'search_product_code':	// ���ʥ�����
					$where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code ILIKE ? GROUP BY product_id)";
					$view_where .= " AND EXISTS (SELECT product_id FROM dtb_products_class as cls WHERE cls.product_code ILIKE ? AND dtb_products.product_id = cls.product_id GROUP BY cls.product_id )";
					$arrval[] = "%$val%";
					break;
				case 'search_startyear':	// ��Ͽ��������FROM��
					$date = sfGetTimestamp($_POST['search_startyear'], $_POST['search_startmonth'], $_POST['search_startday']);
					$where.= " AND update_date >= '" . $_POST['search_startyear'] . "/" . $_POST['search_startmonth']. "/" .$_POST['search_startday'] . "'";
					$view_where.= " AND update_date >= '" . $_POST['search_startyear'] . "/" . $_POST['search_startmonth']. "/" .$_POST['search_startday'] . "'";
					break;
				case 'search_endyear':		// ��Ͽ��������TO��
					$date = sfGetTimestamp($_POST['search_endyear'], $_POST['search_endmonth'], $_POST['search_endday']);
					$date = date('Y/m/d', strtotime($date) + 86400);
					$where.= " AND update_date < date('" . $date . "')";
					$view_where.= " AND update_date < date('" . $date . "')";
					break;
				case 'search_product_flag':	//����
					global $arrSTATUS;
					$search_product_flag = sfSearchCheckBoxes($val);
					if($search_product_flag != "") {
						$where.= " AND product_flag LIKE ?";
						$view_where.= " AND product_flag LIKE ?";
						$arrval[] = $search_product_flag;					
					}
					break;
				case 'search_status':		// ���ơ�����
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
						$where.= " $tmp_where";
						$view_where.= " $tmp_where";
					}
					break;
				default:
					break;
			}
		}

		$order = "update_date DESC, product_id DESC";
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
		case 'csv_movilink':
			// ���ץ����λ���
			$option = "ORDER BY $order";
			// CSV���ϥ����ȥ�Ԥκ���
			$arrOutput = sfSwapArray(sfgetCsvOutput(CSV_ID_MOVI, " WHERE csv_id = ? AND status = 1", array(CSV_ID_MOVI)));
						
			if (count($arrOutput) <= 0) break;
			
			$arrOutputCols = $arrOutput['col'];
			$arrOutputTitle = $arrOutput['disp_name'];
			
			$head = sfGetMovilinkCSVList($arrOutputTitle);
			
			$data = sfGetMovilinkCSV($where, $option, $arrval, $arrOutputCols);

			// CSV���������롣
			sfCSVDownload($head.$data);
			exit;
			break;
		case 'delete_all':
			// ������̤򤹤٤ƺ��
			$where = "product_id IN (SELECT product_id FROM vw_products_nonclass AS noncls  WHERE $where)";
			$sqlval['del_flg'] = 1;
			$objQuery->update("dtb_products", $sqlval, $where, $arrval);
			break;
		default:
			// �ɤ߹�����ȥơ��֥�λ���
			$col = "product_id, name, category_id, main_list_image, status, product_code, price01, price02, stock, stock_unlimited";
			$from = "vw_products_nonclass AS noncls ";

			// �Կ��μ���
			$linemax = $objQuery->count("dtb_products", $view_where, $arrval);
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
			if(DB_TYPE != "mysql") $objQuery->setlimitoffset($page_max, $startno);
			// ɽ�����
			$objQuery->setorder($order);
			
			// view��ʹ��ߤ򤫤���(mysql��)
			sfViewWhere("&&noncls_where&&", $view_where, $arrval, $objQuery->order . " " .  $objQuery->setlimitoffset($page_max, $startno, true));

			// ������̤μ���
			$objPage->arrProducts = $objQuery->select($col, $from, $where, $arrval);
			
			break;
		}
	}
}
	
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
	$objErr->doFunc(array("����ID", "search_product_id"), array("NUM_CHECK"));
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