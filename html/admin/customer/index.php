<?php

require_once("../require.php");
require_once(ROOT_DIR."data/include/csv_output.inc");

//---- �ڡ���ɽ���ѥ��饹
class LC_Page {
	var $arrSession;
	var $tpl_mode;
	var $list_data;
	var $search_data;
	var $arrErr;
	var $arrYear;
	var $arrMonth;
	var $arrDay;
	var $arrJob;
	var $arrSex;
	var $arrPageMax;
	var $count;
	var $search_SQL;
	
	var $tpl_strnavi;
	
	var $arrHtmlmail;

	function LC_Page() {
		$this->tpl_mainpage = 'customer/index.tpl';
		$this->tpl_mainno = 'customer';
		$this->tpl_subnavi = 'customer/subnavi.tpl';
		$this->tpl_subno = 'index';
		$this->tpl_pager = ROOT_DIR . 'data/Smarty/templates/admin/pager.tpl';
		$this->tpl_subtitle = '�ܵҥޥ���';
		
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrJob;
		$arrJob["����"] = "����";
		$this->arrJob = $arrJob;
		global $arrSex;		
		$this->arrSex = $arrSex;
		global $arrPageRows;
		$this->arrPageRows = $arrPageRows;
		
		global $arrMAILMAGATYPE;
		$this->arrMAILMAGATYPE = $arrMAILMAGATYPE;
		$this->arrHtmlmail[''] = "���٤�";
		$this->arrHtmlmail[1] = $arrMAILMAGATYPE[1];
		$this->arrHtmlmail[2] = $arrMAILMAGATYPE[2];		
	}
}

//----��CSV�����������
$arrColumnCSV= array(
						0  => array("sql" => "customer_id", "csv" => "customer_id", "header" => "�ܵ�ID"),
						1  => array("sql" => "name01", "csv" => "name01", "header" => "̾��1"),
						2  => array("sql" => "name02", "csv" => "name02", "header" => "̾��2"),
						3  => array("sql" => "kana01", "csv" => "kana01", "header" => "�եꥬ��1"),
						4  => array("sql" => "kana02", "csv" => "kana02", "header" => "�եꥬ��2"),
						5  => array("sql" => "zip01", "csv" => "zip01", "header" => "͹���ֹ�1"),
						6  => array("sql" => "zip02", "csv" => "zip02", "header" => "͹���ֹ�2"),
						7  => array("sql" => "pref", "csv" => "pref", "header" => "��ƻ�ܸ�"),
						8  => array("sql" => "addr01", "csv" => "addr01", "header" => "����1"),
						9  => array("sql" => "addr02", "csv" => "addr02", "header" => "����2"),
						10 => array("sql" => "email", "csv" => "email", "header" => "E-MAIL"),
						11 => array("sql" => "tel01", "csv" => "tel01", "header" => "TEL1"),
						12 => array("sql" => "tel02", "csv" => "tel02", "header" => "TEL2"),
						13 => array("sql" => "tel03", "csv" => "tel03", "header" => "TEL3"),
						14 => array("sql" => "fax01", "csv" => "fax01", "header" => "FAX1"),
						15 => array("sql" => "fax02", "csv" => "fax02", "header" => "FAX2"),
						16 => array("sql" => "fax03", "csv" => "fax03", "header" => "FAX3"),
						17 => array("sql" => "CASE WHEN sex = 1 THEN '����' ELSE '����' END AS sex", "csv" => "sex", "header" => "����"),
						18 => array("sql" => "job", "csv" => "job", "header" => "����"),
						19 => array("sql" => "to_char(birth, 'YYYYǯMM��DD��') AS birth", "csv" => "birth", "header" => "������"),
						20 => array("sql" => "to_char(first_buy_date, 'YYYYǯMM��DD��HH24:MI') AS first_buy_date", "csv" => "first_buy_date", "header" => "��������"),
						21 => array("sql" => "to_char(last_buy_date, 'YYYYǯMM��DD��HH24:MI') AS last_buy_date", "csv" => "last_buy_date", "header" => "�ǽ�������"),
						22 => array("sql" => "buy_times", "csv" => "buy_times", "header" => "�������"),
						23 => array("sql" => "point", "csv" => "point", "header" => "�ݥ���ȻĹ�"),
						24 => array("sql" => "note", "csv" => "note", "header" => "����"),
						25 => array("sql" => "to_char(create_date, 'YYYYǯMM��DD��HH24:MI') AS create_date", "csv" => "create_date", "header" => "��Ͽ��"),
						26 => array("sql" => "to_char(update_date, 'YYYYǯMM��DD��HH24:MI') AS update_date", "csv" => "update_date", "header" => "������")
					);

//---- �ڡ����������
$objConn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objDate = new SC_Date(1901);
$objPage->arrYear = $objDate->getYear();	//�����եץ����������
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();
$objPage->objDate = $objDate;

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

// POST�ͤΰ����Ѥ�
$objPage->arrForm = $_POST;

// �ڡ���������
$objPage->arrHidden['search_pageno'] = $_POST['search_pageno'];

// ������ɤΰ����Ѥ�
foreach ($_POST as $key => $val) {

	if (ereg("^search_", $key)) {
		switch($key) {
			case 'search_sex':
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

// �ܵҺ��
if ($_POST['mode'] == "delete") {
	$sql = "SELECT status,email FROM dtb_customer WHERE customer_id = ? AND delete = 0";
	$result_customer = $objConn->getAll($sql, array($_POST["edit_customer_id"]));

	if ($result_customer[0]["status"] == 2) {			//�ܲ�����
		$arrDel = array("delete" => 1, "update_date" => "NOW()"); 
		$objConn->autoExecute("dtb_customer", $arrDel, "customer_id = " .addslashes($_POST["edit_customer_id"]) );
	} elseif ($result_customer[0]["status"] == 1) {		//��������
		$sql = "DELETE FROM dtb_customer WHERE customer_id = ?";
		$objConn->query($sql, array($_POST["edit_customer_id"]));
	}
	$sql = "DELETE FROM dtb_customer_mail WHERE email = ?";
	$objConn->query($sql, array($result_customer[0]["email"]));
}

if ($_POST['mode'] == "search" || $_POST['mode'] == "csv"  || $_POST['mode'] == "delete" || $_POST['mode'] == "delete_all") {
	// ����ʸ���ζ����Ѵ�
	lfConvertParam();
	// ���顼�����å�
	$objPage->arrErr = lfCheckError($objPage->arrForm);

	$where = "delete = 0";

	/* ���ϥ��顼�ʤ� */
	if (count($objPage->arrErr) == 0) {
		
		//-- �����ǡ�������
		$objSelect = new SC_CustomerList($objPage->arrForm, "customer");
		if ($_POST["mode"] == 'csv') {
			$searchSql = $objSelect->getListCSV($arrColumnCSV);
		}else{
			$searchSql = $objSelect->getList();
		}
		
		$objPage->search_data = $objConn->getAll($searchSql, $objSelect->arrVal);

		switch($_POST['mode']) {
		case 'csv':
			$i = 0;
			$header = "";
			
			// CSV��������
			$arrCsvOutput = (sfgetCsvOutput(2, " WHERE csv_id = 2 AND status = 1"));

			if (count($arrCsvOutput) <= 0) break;

			foreach($arrCsvOutput as $data) {
				$arrColumn[] = $data["col"];
				if ($i != 0) $header .= ", ";
				$header .= $data["disp_name"];
				$i ++;
			}
			$header .= "\n";

			//-����ƻ�ܸ�/���Ȥ��Ѵ�
			for($i = 0; $i < count($objPage->search_data); $i ++) {
				$objPage->search_data[$i]["pref"] = $arrPref[ $objPage->search_data[$i]["pref"] ];
				$objPage->search_data[$i]["job"]  = $arrJob[ $objPage->search_data[$i]["job"] ];
			}

			//-��CSV����
			$data = lfGetCSVData($objPage->search_data, $arrColumn);
			sfCSVDownload($header.$data);
			exit;
			break;
		case 'delete_all':
			// ������̤򤹤٤ƺ��
			$where = "product_id IN (SELECT product_id FROM vw_products_nonclass WHERE $where)";
			$sqlval['delete'] = 1;
			$objQuery->update("dtb_products", $sqlval, $where, $arrval);

			$sql = "SELECT status,email FROM dtb_customer WHERE customer_id = ? AND delete = 0";
			$result_customer = $objConn->getAll($sql, array($_POST["del_customer_id"]));

			if ($result_customer[0]["status"] == 2) {			//�ܲ�����
				$arrDel = array("delete" => 1, "update_date" => "NOW()");
				$objConn->autoExecute("dtb_customer", $arrDel, "customer_id = " .addslashes($_POST["del_customer_id"]) );
			} elseif ($result_customer[0]["status"] == 1) {		//��������
				$sql = "DELETE FROM dtb_customer WHERE customer_id = ?";
				$objConn->query($sql, array($_POST["del_customer_id"]));
			}
			$sql = "DELETE FROM dtb_customer_mail WHERE email = ?";
			$objConn->query($sql, array($result_customer[0]["email"]));	
			
			break;
		default:

			// �Կ��μ���
			$linemax = $objConn->getOne( $objSelect->getListCount(), $objSelect->arrVal);
			$objPage->tpl_linemax = $linemax;				// ���郎�������ޤ�����ɽ����
	
			// �ڡ�������ν���
			if(is_numeric($_POST['search_page_max'])) {	
				$page_max = $_POST['search_page_max'];
			} else {
				$page_max = SEARCH_PMAX;
			}
			// �ڡ�������μ���
			$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnCustomerPage", NAVI_PMAX);
			$startno = $objNavi->start_row;
			$objPage->arrPagenavi = $objNavi->arrPagenavi;		
		}
	}
}

$objPage->arrCatList = sfGetCategoryList();

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);


//--------------------------------------------------------------------------------------------------------------------------------------

//----������ʸ������Ѵ�
function lfConvertParam() {
	global $objPage;
	
	/*
	 *	ʸ������Ѵ�
	 *	K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
	 *	C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
	 *	V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ�	
	 *	n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
	 *  a :  ���ѱѿ�����Ⱦ�ѱѿ������Ѵ�����
	 */
	// �����̾�ȥ���С��Ⱦ���
	$arrConvList['search_customer_id'] = "n" ;
	$arrConvList['search_name'] = "aKV" ;
	$arrConvList['search_pref'] = "n" ;
	$arrConvList['search_kana'] = "CKV" ;
	$arrConvList['search_b_start_year'] = "n" ;
	$arrConvList['search_b_start_month'] = "n" ;
	$arrConvList['search_b_start_day'] = "n" ;
	$arrConvList['search_b_end_year'] = "n" ;
	$arrConvList['search_b_end_month'] = "n" ;
	$arrConvList['search_b_end_day'] = "n" ;
	$arrConvList['search_tel'] = "n" ;
	$arrConvList['search_birth_month'] = "n" ;
	$arrConvList['search_email'] = "a" ;
	$arrConvList['search_buy_total_from'] = "n" ;
	$arrConvList['search_buy_total_to'] = "n" ;
	$arrConvList['search_buy_times_from'] = "n" ;
	$arrConvList['search_buy_times_to'] = "n" ;
	$arrConvList['search_start_year'] = "n" ;
	$arrConvList['search_start_month'] = "n" ;
	$arrConvList['search_start_day'] = "n" ;
	$arrConvList['search_end_year'] = "n" ;
	$arrConvList['search_end_month'] = "n" ;
	$arrConvList['search_end_day'] = "n" ;
	$arrConvList['search_page_rows'] = "n" ;
	$arrConvList['search_buy_start_year'] = "n" ;		//���ǽ������� START ǯ
	$arrConvList['search_buy_start_month'] = "n" ;		//���ǽ������� START ��
	$arrConvList['search_buy_start_day'] = "n" ;		//���ǽ������� START ��
	$arrConvList['search_buy_end_year'] = "n" ;			//���ǽ������� END ǯ
	$arrConvList['search_buy_end_month'] = "n" ;		//���ǽ������� END ��
	$arrConvList['search_buy_end_day'] = "n" ;			//���ǽ������� END ��
	$arrConvList['search_buy_product_name'] = "aKV" ;	//����������̾
	$arrConvList['search_buy_product_code'] = "aKV" ;	//���������ʥ�����
	$arrConvList['search_category_id'] = "" ;			//�����ƥ���
		
	// ʸ���Ѵ�
	foreach ($arrConvList as $key => $val) {
		// POST����Ƥ����ͤΤ��Ѵ����롣
		if(isset($objPage->arrForm[$key])) {
			$objPage->arrForm[$key] = mb_convert_kana($objPage->arrForm[$key] ,$val);
		}
	}
}


//---- ���ϥ��顼�����å�
function lfCheckError($array) {

	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("�ܵҥ�����", "search_customer_id", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��ƻ�ܸ�", "search_pref", 2), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�ܵ�̾", "search_name", STEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�ܵ�̾(����)", "search_kana", STEXT_LEN), array("KANA_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("������(������)", "search_b_start_year", "search_b_start_month", "search_b_start_day"), array("CHECK_DATE"));
	$objErr->doFunc(array("������(��λ��)", "search_b_end_year", "search_b_end_month", "search_b_end_day"), array("CHECK_DATE"));
	$objErr->doFunc(array("������(������)","������(��λ��)", "search_b_start_year", "search_b_start_month", "search_b_start_day", "search_b_end_year", "search_b_end_month", "search_b_end_day"), array("CHECK_SET_TERM"));
	$objErr->doFunc(array("������", "search_birth_month", 2), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹', "search_email", STEXT_LEN) ,array("EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�����ֹ�", "search_tel", TEL_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������(����)", "search_buy_total_from", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������(��λ)", "search_buy_total_to", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	if ( (is_numeric($array["search_buy_total_from"]) && is_numeric($array["search_buy_total_to"]) ) && ($array["search_buy_total_from"] > $array["search_buy_total_to"]) ) $objErr->arrErr["search_buy_total_from"] .= "�� ������ۤλ����ϰϤ������Ǥ���";
	$objErr->doFunc(array("�������(����)", "search_buy_times_from", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������(��λ)", "search_buy_times_to", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	if ( (is_numeric($array["search_buy_times_from"]) && is_numeric($array["search_buy_times_to"]) ) && ($array["search_buy_times_from"] > $array["search_buy_times_to"]) ) $objErr->arrErr["search_buy_times_from"] .= "�� ��������λ����ϰϤ������Ǥ���";
	$objErr->doFunc(array("��Ͽ��������(������)", "search_start_year", "search_start_month", "search_start_day",), array("CHECK_DATE"));
	$objErr->doFunc(array("��Ͽ��������(��λ��)", "search_end_year", "search_end_month", "search_end_day"), array("CHECK_DATE"));	
	$objErr->doFunc(array("��Ͽ��������(������)","��Ͽ��������(��λ��)", "search_start_year", "search_start_month", "search_start_day", "search_end_year", "search_end_month", "search_end_day"), array("CHECK_SET_TERM"));
	$objErr->doFunc(array("ɽ�����", "search_page_rows", 3), array("NUM_CHECK","MAX_LENGTH_CHECK"));	
	$objErr->doFunc(array("�ǽ�������(������)", "search_buy_start_year", "search_buy_start_month", "search_buy_start_day",), array("CHECK_DATE"));	//�ǽ�������(������)
	$objErr->doFunc(array("�ǽ�����(��λ��)", "search_buy_end_year", "search_buy_end_month", "search_buy_end_day"), array("CHECK_DATE"));			//�ǽ�������(��λ��)
	//�������(from) �� �������(to) �ξ��ϥ��顼�Ȥ���
	$objErr->doFunc(array("�ǽ�������(������)","��Ͽ��������(��λ��)", "search_buy_start_year", "search_buy_start_month", "search_buy_start_day", "search_buy_end_year", "search_buy_end_month", "search_buy_end_day"), array("CHECK_SET_TERM"));	
	$objErr->doFunc(array("�������ʥ�����", "search_buy_product_code", STEXT_LEN), array("MAX_LENGTH_CHECK"));						//�������ʥ�����
	$objErr->doFunc(array("��������̾", "search_buy_product_name", STEXT_LEN), array("MAX_LENGTH_CHECK"));							//��������̾��
	
	return $objErr->arrErr;
}

function lfSetWhere($arrForm){
	foreach ($arrForm as $key => $val) {
		
		$val = sfManualEscape($val);
		
		if($val == "") continue;
		
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
}

//---- CSV�����ѥǡ�������
function lfGetCSVData( $array, $arrayIndex){	
	
	for ($i=0; $i<count($array); $i++){
		
		for ($j=0; $j<count($array[$i]); $j++ ){
			if ( $j > 0 ) $return .= ",";
			$return .= "\"";			
			if ( $arrayIndex ){
				$return .= mb_ereg_replace("<","��",mb_ereg_replace( "\"","\"\"",$array[$i][$arrayIndex[$j]] )) ."\"";	
			} else {
				$return .= mb_ereg_replace("<","��",mb_ereg_replace( "\"","\"\"",$array[$i][$j] )) ."\"";
			}
		}
		$return .= "\n";			
	}
	
	return $return;
}


?>