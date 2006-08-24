<?php

require_once("../require.php");
require_once(ROOT_DIR."data/include/csv_output.inc");

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

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
		$this->tpl_subnavi = '';
		$this->tpl_subno = "index";
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



//---- �����ѹ�������
$arrSearchColumn = array(
							array(  "column" => "customer_id",		"convert" => "n" ),
							array(  "column" => "name",				"convert" => "aKV" ),
							array(  "column" => "pref",				"convert" => "n" ),
							array(  "column" => "kana",				"convert" => "CKV" ),
							array(  "column" => "sex",				"convert" => "" ),
							array(  "column" => "b_start_year",		"convert" => "n" ),
							array(  "column" => "b_start_month",	"convert" => "n" ),
							array(  "column" => "b_start_day",		"convert" => "n" ),
							array(  "column" => "b_end_year",		"convert" => "n" ),
							array(  "column" => "b_end_month",		"convert" => "n" ),
							array(  "column" => "b_end_day",		"convert" => "n" ),
							array(  "column" => "tel",				"convert" => "n" ),
							array(  "column" => "job",				"convert" => "" ),
							array(  "column" => "birth_month",		"convert" => "n" ),
							array(  "column" => "email",			"convert" => "a" ),
							array(  "column" => "buy_total_from",	"convert" => "n" ),
							array(  "column" => "buy_total_to",		"convert" => "n" ),
							array(  "column" => "buy_times_from",	"convert" => "n" ),
							array(  "column" => "buy_times_to",		"convert" => "n" ),
							array(  "column" => "start_year",		"convert" => "n" ),
							array(  "column" => "start_month",		"convert" => "n" ),
							array(  "column" => "start_day",		"convert" => "n" ),
							array(  "column" => "end_year",			"convert" => "n" ),
							array(  "column" => "end_month",		"convert" => "n" ),
							array(  "column" => "end_day",			"convert" => "n" ),
							array(  "column" => "page_rows",		"convert" => "n" )

							// 2006/04/20 KAKINAKA-ADD:�ǽ����������������ʥ����ɡ���������̾�Ρ����ƥ���򸡺����ܤ��ɲä��� START
							,array(  "column" => "buy_start_year",		"convert" => "n" )		//���ǽ������� START ǯ
							,array(  "column" => "buy_start_month",		"convert" => "n" )		//���ǽ������� START ��
							,array(  "column" => "buy_start_day",		"convert" => "n" )		//���ǽ������� START ��
							,array(  "column" => "buy_end_year",		"convert" => "n" )		//���ǽ������� END ǯ
							,array(  "column" => "buy_end_month",		"convert" => "n" )		//���ǽ������� END ��
							,array(  "column" => "buy_end_day",			"convert" => "n" )		//���ǽ������� END ��
							,array(  "column" => "buy_product_name",	"convert" => "aKV" )	//����������̾
							,array(  "column" => "buy_product_code",	"convert" => "aKV" )	//���������ʥ�����
							,array(  "column" => "category_id",			"convert" => "" )		//�����ƥ���
							// 2006/04/20 KAKINAKA-ADD:�ǽ����������������ʥ����ɡ���������̾�Ρ����ƥ���򸡺����ܤ��ɲä��� END
							
							,array(  "column" => "cell",				"convert" => "n" )		// 2006/05/10 KAKINAKA-ADD:�������ä򸡺����ܤ��ɲä��� END

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

						// 2006/05/12 KAKINAKA ADD:���������ֹ��csv���Ϥ��� START
						14 => array("sql" => "cell01", "csv" => "cell01", "header" => "���������ֹ�1"),
						15 => array("sql" => "cell02", "csv" => "cell02", "header" => "���������ֹ�2"),
						16 => array("sql" => "cell03", "csv" => "cell03", "header" => "���������ֹ�3"),
						// 2006/05/12 KAKINAKA ADD:���������ֹ��csv���Ϥ��� END

						17 => array("sql" => "fax01", "csv" => "fax01", "header" => "FAX1"),
						18 => array("sql" => "fax02", "csv" => "fax02", "header" => "FAX2"),
						19 => array("sql" => "fax03", "csv" => "fax03", "header" => "FAX3"),
						20 => array("sql" => "CASE WHEN sex = 1 THEN '����' ELSE '����' END AS sex", "csv" => "sex", "header" => "����"),

						// 2006/05/12 KAKINAKA DEL:���ȤϽ��Ϥ��ʤ� START
						//21 => array("sql" => "job", "csv" => "job", "header" => "����"),
						// 2006/05/12 KAKINAKA DEL:���ȤϽ��Ϥ��ʤ� END

						21 => array("sql" => "to_char(birth, 'YYYYǯMM��DD��') AS birth", "csv" => "birth", "header" => "������"),
						22 => array("sql" => "to_char(first_buy_date, 'YYYYǯMM��DD��HH24:MI') AS first_buy_date", "csv" => "first_buy_date", "header" => "��������"),
						23 => array("sql" => "to_char(last_buy_date, 'YYYYǯMM��DD��HH24:MI') AS last_buy_date", "csv" => "last_buy_date", "header" => "�ǽ�������"),
						24 => array("sql" => "buy_times", "csv" => "buy_times", "header" => "�������"),
						25 => array("sql" => "point", "csv" => "point", "header" => "�ݥ���ȻĹ�"),
						26 => array("sql" => "note", "csv" => "note", "header" => "����"),
						27 => array("sql" => "to_char(create_date, 'YYYYǯMM��DD��HH24:MI') AS create_date", "csv" => "create_date", "header" => "��Ͽ��"),
						28 => array("sql" => "to_char(update_date, 'YYYYǯMM��DD��HH24:MI') AS update_date", "csv" => "update_date", "header" => "������")
					);

//----���ܵҾ��󸡺�
if($_POST['mode'] == "search") {

	//-- �����ͥ���С���
	$objPage->list_data = lfConvertParam($_POST, $arrSearchColumn);

	//-- ���ϥ��顼�Υ����å�
	$objPage->arrErr = lfErrorCheck($objPage->list_data);
	//-- �������ϤȲ��������
	if (! is_array($objPage->arrErr)) {

		//-- �ܵҺ����		
		if ($_POST["del_mode"] == "delete" && is_numeric($_POST["del_customer_id"])) {
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
		}
			
		$objSelect = new SC_CustomerList($objPage->list_data, "customer");
	
		//-- �ڡ�������ν���
		if(is_numeric($_POST['page_rows'])) {	
			$page_max = $_POST['page_rows'];
		} else {
			$page_max = SEARCH_PMAX;
		}
				
		$objPage->count = $objConn->getOne( $objSelect->getListCount(), $objSelect->arrVal);
		$objNavi = new SC_PageNavi($_POST['pageno'], $objPage->count, $page_max, "fnCustomerPage", NAVI_PMAX);

		$objPage->tpl_strnavi = $objNavi->strnavi;
		$startno = $objNavi->start_row;

		//-- �����ǡ�������
		if ($_POST["csv_mode"] == 'csv') {
			$searchSql = $objSelect->getListCSV($arrColumnCSV);
		} else {
			$objSelect->setLimitOffset($_POST["page_rows"], $startno);
			$searchSql = $objSelect->getList();
		}
	
		$objPage->search_data = $objConn->getAll($searchSql, $objSelect->arrVal);

		//--��CSV��������ɻ�
		if ($_POST["csv_mode"] == "csv") {
			$i = 0;
			foreach($arrColumnCSV as $data) {
				$arrColumn[] = $data["csv"];
				if ($i != 0) $header .= ", ";
				$header .= $data["header"];
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
			exit();
		}
	}

}

// 2006/04/18 KAKINAKA-ADD:���ƥ�����ɹ����ɲ�
$objPage->arrCatList = sfGetCategoryList();

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);


//--------------------------------------------------------------------------------------------------------------------------------------

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


//----������ʸ������Ѵ�
function lfConvertParam($array, $arrSearchColumn) {
	/*
	 *	ʸ������Ѵ�
	 *	K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
	 *	C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
	 *	V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ�	
	 *	n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
	 *  a :  ���ѱѿ�����Ⱦ�ѱѿ������Ѵ�����
	 */
	// �����̾�ȥ���С��Ⱦ���
	foreach ($arrSearchColumn as $data) {
		$arrConvList[ $data["column"] ] = $data["convert"];
	}
	// ʸ���Ѵ�
	foreach ($arrConvList as $key => $val) {
		// POST����Ƥ����ͤΤ��Ѵ����롣
		if (! is_array($array[$key]) && strlen($array[$key]) > 0) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}


//---- ���ϥ��顼�����å�
function lfErrorCheck($array) {

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

	// 2006/04/20 KAKINAKA-ADD:�ǽ����������������ʥ����ɡ���������̾�Τ򸡺����ܤ��ɲä��� START
	$objErr->doFunc(array("�ǽ�������(������)", "buy_start_year", "buy_start_month", "buy_start_day",), array("CHECK_DATE"));	//�ǽ�������(������)
	$objErr->doFunc(array("�ǽ�����(��λ��)", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_DATE"));			//�ǽ�������(��λ��)
	//�������(from) �� �������(to) �ξ��ϥ��顼�Ȥ���
	$objErr->doFunc(array("�ǽ�������(������)","��Ͽ��������(��λ��)", "buy_start_year", "buy_start_month", "buy_start_day", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_SET_TERM"));	
	
	$objErr->doFunc(array("�������ʥ�����", "buy_product_code", STEXT_LEN), array("MAX_LENGTH_CHECK"));						//�������ʥ�����
	$objErr->doFunc(array("��������̾", "buy_product_name", STEXT_LEN), array("MAX_LENGTH_CHECK"));							//��������̾��
	// 2006/04/20 KAKINAKA-ADD:�ǽ����������������ʥ����ɡ���������̾�Τ򸡺����ܤ��ɲä��� END

	$objErr->doFunc(array("���������ֹ�", "cell", TEL_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));	// 2006/05/10 KAKINAKA ADD:�������ø������ɲ�
	
	return $objErr->arrErr;
}

?>