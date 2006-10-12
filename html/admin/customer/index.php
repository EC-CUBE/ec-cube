<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once(DATA_PATH . "include/csv_output.inc");

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
		$this->tpl_pager = DATA_PATH . 'Smarty/templates/admin/pager.tpl';
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
						19 => array("sql" => "cast(birth as date) AS birth", "csv" => "birth", "header" => "������"),
						20 => array("sql" => "cast(first_buy_date as date) AS first_buy_date", "csv" => "first_buy_date", "header" => "��������"),
						21 => array("sql" => "cast(last_buy_date as date) AS last_buy_date", "csv" => "last_buy_date", "header" => "�ǽ�������"),
						22 => array("sql" => "buy_times", "csv" => "buy_times", "header" => "�������"),
						23 => array("sql" => "point", "csv" => "point", "header" => "�ݥ���ȻĹ�"),
						24 => array("sql" => "note", "csv" => "note", "header" => "����"),
						25 => array("sql" => "cast(create_date as date) AS create_date", "csv" => "create_date", "header" => "��Ͽ��"),
						26 => array("sql" => "cast(update_date as date) AS update_date", "csv" => "update_date", "header" => "������")
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
	switch($key) {
		case 'sex':
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

// �ܵҺ��
if ($_POST['mode'] == "delete") {
	$sql = "SELECT status,email FROM dtb_customer WHERE customer_id = ? AND del_flg = 0";
	$result_customer = $objConn->getAll($sql, array($_POST["edit_customer_id"]));

	if ($result_customer[0]["status"] == 2) {			//�ܲ�����
		$arrDel = array("del_flg" => 1, "update_date" => "NOW()"); 
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

	$where = "del_flg = 0";

	/* ���ϥ��顼�ʤ� */
	if (count($objPage->arrErr) == 0) {
		
		//-- �����ǡ�������
		$objSelect = new SC_CustomerList($objPage->arrForm, "customer");
		
		// ɽ���������
		$page_rows = $objPage->arrForm['page_rows'];
		if(is_numeric($page_rows)) {	
			$page_max = $page_rows;
		} else {
			$page_max = SEARCH_PMAX;
		}
		
		if ($objPage->arrForm['search_pageno'] == 0){
			$objPage->arrForm['search_pageno'] = 1;
		}
		
		$offset = $page_max * ($objPage->arrForm['search_pageno'] - 1);
		$objSelect->setLimitOffset($page_max, $offset);		
		
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
			$where = "product_id IN (SELECT product_id FROM vw_products_nonclass AS noncls WHERE $where)";
			$sqlval['del_flg'] = 1;
			$objQuery->update("dtb_products", $sqlval, $where, $arrval);

			$sql = "SELECT status,email FROM dtb_customer WHERE customer_id = ? AND del_flg = 0";
			$result_customer = $objConn->getAll($sql, array($_POST["del_customer_id"]));

			if ($result_customer[0]["status"] == 2) {			//�ܲ�����
				$arrDel = array("del_flg" => 1, "update_date" => "NOW()");
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
	$arrConvList['customer_id'] = "n" ;
	$arrConvList['name'] = "aKV" ;
	$arrConvList['pref'] = "n" ;
	$arrConvList['kana'] = "CKV" ;
	$arrConvList['b_start_year'] = "n" ;
	$arrConvList['b_start_month'] = "n" ;
	$arrConvList['b_start_day'] = "n" ;
	$arrConvList['b_end_year'] = "n" ;
	$arrConvList['b_end_month'] = "n" ;
	$arrConvList['b_end_day'] = "n" ;
	$arrConvList['tel'] = "n" ;
	$arrConvList['birth_month'] = "n" ;
	$arrConvList['email'] = "a" ;
	$arrConvList['buy_total_from'] = "n" ;
	$arrConvList['buy_total_to'] = "n" ;
	$arrConvList['buy_times_from'] = "n" ;
	$arrConvList['buy_times_to'] = "n" ;
	$arrConvList['start_year'] = "n" ;
	$arrConvList['start_month'] = "n" ;
	$arrConvList['start_day'] = "n" ;
	$arrConvList['end_year'] = "n" ;
	$arrConvList['end_month'] = "n" ;
	$arrConvList['end_day'] = "n" ;
	$arrConvList['page_rows'] = "n" ;
	$arrConvList['buy_start_year'] = "n" ;		//���ǽ������� START ǯ
	$arrConvList['buy_start_month'] = "n" ;		//���ǽ������� START ��
	$arrConvList['buy_start_day'] = "n" ;		//���ǽ������� START ��
	$arrConvList['buy_end_year'] = "n" ;			//���ǽ������� END ǯ
	$arrConvList['buy_end_month'] = "n" ;		//���ǽ������� END ��
	$arrConvList['buy_end_day'] = "n" ;			//���ǽ������� END ��
	$arrConvList['buy_product_name'] = "aKV" ;	//����������̾
	$arrConvList['buy_product_code'] = "aKV" ;	//���������ʥ�����
	$arrConvList['category_id'] = "" ;			//�����ƥ���
		
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
	
	$objErr->doFunc(array("�ܵҥ�����", "customer_id", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��ƻ�ܸ�", "pref", 2), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�ܵ�̾", "name", STEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�ܵ�̾(����)", "kana", STEXT_LEN), array("KANA_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("������(������)", "b_start_year", "b_start_month", "b_start_day"), array("CHECK_DATE"));
	$objErr->doFunc(array("������(��λ��)", "b_end_year", "b_end_month", "b_end_day"), array("CHECK_DATE"));
	$objErr->doFunc(array("������(������)","������(��λ��)", "b_start_year", "b_start_month", "b_start_day", "b_end_year", "b_end_month", "b_end_day"), array("CHECK_SET_TERM"));
	$objErr->doFunc(array("������", "birth_month", 2), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹', "email", STEXT_LEN) ,array("EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�����ֹ�", "tel", TEL_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������(����)", "buy_total_from", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������(��λ)", "buy_total_to", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	if ( (is_numeric($array["buy_total_from"]) && is_numeric($array["buy_total_to"]) ) && ($array["buy_total_from"] > $array["buy_total_to"]) ) $objErr->arrErr["buy_total_from"] .= "�� ������ۤλ����ϰϤ������Ǥ���";
	$objErr->doFunc(array("�������(����)", "buy_times_from", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������(��λ)", "buy_times_to", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	if ( (is_numeric($array["buy_times_from"]) && is_numeric($array["buy_times_to"]) ) && ($array["buy_times_from"] > $array["buy_times_to"]) ) $objErr->arrErr["buy_times_from"] .= "�� ��������λ����ϰϤ������Ǥ���";
	$objErr->doFunc(array("��Ͽ��������(������)", "start_year", "start_month", "start_day",), array("CHECK_DATE"));
	$objErr->doFunc(array("��Ͽ��������(��λ��)", "end_year", "end_month", "end_day"), array("CHECK_DATE"));	
	$objErr->doFunc(array("��Ͽ��������(������)","��Ͽ��������(��λ��)", "start_year", "start_month", "start_day", "end_year", "end_month", "end_day"), array("CHECK_SET_TERM"));
	$objErr->doFunc(array("ɽ�����", "page_rows", 3), array("NUM_CHECK","MAX_LENGTH_CHECK"));	
	$objErr->doFunc(array("�ǽ�������(������)", "buy_start_year", "buy_start_month", "buy_start_day",), array("CHECK_DATE"));	//�ǽ�������(������)
	$objErr->doFunc(array("�ǽ�����(��λ��)", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_DATE"));			//�ǽ�������(��λ��)
	//�������(from) �� �������(to) �ξ��ϥ��顼�Ȥ���
	$objErr->doFunc(array("�ǽ�������(������)","��Ͽ��������(��λ��)", "buy_start_year", "buy_start_month", "buy_start_day", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_SET_TERM"));	
	$objErr->doFunc(array("�������ʥ�����", "buy_product_code", STEXT_LEN), array("MAX_LENGTH_CHECK"));						//�������ʥ�����
	$objErr->doFunc(array("��������̾", "buy_product_name", STEXT_LEN), array("MAX_LENGTH_CHECK"));							//��������̾��

	return $objErr->arrErr;
}

function lfSetWhere($arrForm){
	foreach ($arrForm as $key => $val) {
		
		$val = sfManualEscape($val);
		
		if($val == "") continue;
		
		switch ($key) {
			case 'product_id':
				$where .= " AND product_id = ?";
				$arrval[] = $val;
				break;
			case 'product_class_id':
				$where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_class_id = ?)";
				$arrval[] = $val;
				break;
			case 'name':
				$where .= " AND name ILIKE ?";
				$arrval[] = "%$val%";
				break;
			case 'category_id':
				list($tmp_where, $tmp_arrval) = sfGetCatWhere($val);
				if($tmp_where != "") {
					$where.= " AND $tmp_where";
					$arrval = array_merge($arrval, $tmp_arrval);
				}
				break;
			case 'product_code':
				$where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code ILIKE ? GROUP BY product_id)";
				$arrval[] = "%$val%";
				break;
			case 'startyear':
				$date = sfGetTimestamp($_POST['startyear'], $_POST['startmonth'], $_POST['startday']);
				$where.= " AND update_date >= ?";
				$arrval[] = $date;
				break;
			case 'endyear':
				$date = sfGetTimestamp($_POST['endyear'], $_POST['endmonth'], $_POST['endday']);
				$where.= " AND update_date <= ?";
				$arrval[] = $date;
				break;
			case 'product_flag':
				global $arrSTATUS;
				$product_flag = sfSearchCheckBoxes($val);
				if($product_flag != "") {
					$where.= " AND product_flag LIKE ?";
					$arrval[] = $product_flag;					
				}
				break;
			case 'status':
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