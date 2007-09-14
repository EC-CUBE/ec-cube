<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * CSV�ۿ���ǽ�����Τ��ᡢ������ʬ�����ե����벽���ޤ�����<br>
 * @author hirokazu_fukuda
 * @version 2005/12/27
 */


//---- �����ѹ�������
$objPage->arrHtmlmail = array( "" => "ξ��",  1 => "HTML", 2 => "TEXT" );


//---- �����������ѹ��ܤ�����
$arrRegistColumn = array(
							 array(  "column" => "template_id",		"convert" => "n" )
							,array(  "column" => "mail_method",		"convert" => "n" )
							,array(  "column" => "send_year",		"convert" => "n" )
							,array(  "column" => "send_month", 		"convert" => "n" )
							,array(  "column" => "send_day",		"convert" => "n" )
							,array(  "column" => "send_hour",		"convert" => "n" )
							,array(  "column" => "send_minutes",	"convert" => "n" )
							,array(  "column" => "subject",			"convert" => "aKV" )
							,array(  "column" => "body",			"convert" => "KV" )
						);

//---- ���ޥ��������
$arrCustomerType = array(
						1 => "���",
						2 => "����",
						//3 => "CSV��Ͽ"
						);

//---- ��������
$arrSearchColumn = array(
							array(  "column" => "name",				"convert" => "aKV"),
							array(  "column" => "pref",				"convert" => "n" ),
							array(  "column" => "kana",				"convert" => "CKV"),
							array(  "column" => "sex",				"convert" => "" ),
							array(  "column" => "tel",				"convert" => "n" ),
							array(  "column" => "job",				"convert" => "" ),
							array(  "column" => "email",			"convert" => "a" ),
							array(  "column" => "email_mobile",		"convert" => "a" ),							
							array(  "column" => "htmlmail",			"convert" => "n" ),
							array(  "column" => "customer",			"convert" => "" ),
							array(  "column" => "buy_total_from",	"convert" => "n" ),
							array(  "column" => "buy_total_to",		"convert" => "n" ),
							array(  "column" => "buy_times_from",	"convert" => "n" ),
							array(  "column" => "buy_times_to",		"convert" => "n" ),
							array(  "column" => "birth_month",		"convert" => "n" ),
							array(  "column" => "b_start_year",		"convert" => "n" ),
							array(  "column" => "b_start_month",	"convert" => "n" ),
							array(  "column" => "b_start_day",		"convert" => "n" ),
							array(  "column" => "b_end_year",		"convert" => "n" ),
							array(  "column" => "b_end_month",		"convert" => "n" ),
							array(  "column" => "b_end_day",		"convert" => "n" ),
							array(  "column" => "start_year",		"convert" => "n" ),
							array(  "column" => "start_month",		"convert" => "n" ),
							array(  "column" => "start_day",		"convert" => "n" ),
							array(  "column" => "end_year",			"convert" => "n" ),
							array(  "column" => "end_month",		"convert" => "n" ),
							array(  "column" => "end_day",			"convert" => "n" ),
							array(  "column" => "buy_start_year",	"convert" => "n" ),
							array(  "column" => "buy_start_month",	"convert" => "n" ),
							array(  "column" => "buy_start_day",	"convert" => "n" ),
							array(  "column" => "buy_end_year",		"convert" => "n" ),
							array(  "column" => "buy_end_month",	"convert" => "n" ),
							array(  "column" => "buy_end_day",		"convert" => "n" ),
							array(  "column" => "buy_product_code",	"convert" => "aKV" ),
							array(  "column" => "buy_product_name", "convert" => "aKV" ),
							array(  "column" => "category_id",	    "convert" => ""  ),		
							array(  "column" => "buy_total_from",	"convert" => "n" ),	
							array(  "column" => "buy_total_to",	    "convert" => "n" ),
							array(  "column" => "campaign_id",	    "convert" => ""  ),
							array(  "column" => "mail_type",		"convert" => ""  ),
							array(  "column" => "domain",           "convert" => "" )
					);

//--------------------------------------------------------------------------------------------------------------------------------------

//---- HTML�ƥ�ץ졼�Ȥ���Ѥ����硢�ǡ�����������롣	
function lfGetHtmlTemplateData($id) {
	
	global $conn;
	$sql = "SELECT * FROM dtb_mailmaga_template WHERE template_id = ?";
	$result = $conn->getAll($sql, array($id));
	$list_data = $result[0];

	// �ᥤ���ʤξ������
	$sql = "SELECT name, main_image, point_rate, deliv_fee, price01_min, price01_max, price02_min, price02_max FROM vw_products_allclass AS allcls WHERE product_id = ?";
	$main = $conn->getAll($sql, array($list_data["main_product_id"]));
	$list_data["main"] = $main[0];

	// ���־��ʤξ������
	$sql = "SELECT product_id, name, main_list_image, price01_min, price01_max, price02_min, price02_max FROM vw_products_allclass AS allcls WHERE product_id = ?";
	$k = 0;
	$l = 0;
	for ($i = 1; $i <= 12; $i ++) {
		if ($l == 4) {
			$l = 0;
			$k ++;
		}
		$result = "";
		$j = sprintf("%02d", $i);
		if ($i > 0 && $i < 5 ) $k = 0;
		if ($i > 4 && $i < 9 ) $k = 1;
		if ($i > 8 && $i < 13 ) $k = 2;	
		
		if (is_numeric($list_data["sub_product_id" .$j])) {
			$result = $conn->getAll($sql, array($list_data["sub_product_id" .$j]));
			$list_data["sub"][$k][$l] = $result[0];
			$list_data["sub"][$k]["data_exists"] = "OK";	//�����ʤ˥ǡ��������İʾ�¸�ߤ���ե饰
		}
		$l ++;
	}
	return $list_data;
}

//---   �ƥ�ץ졼�Ȥμ�����֤�
function lfGetTemplateMethod($conn, $templata_id){
	
	if ( sfCheckNumLength($template_id) ){
		$sql = "SELECT mail_method FROM dtb_mailmaga_template WEHRE template_id = ?";
	}	
}

//---   hidden���ǽ���������κ���
function lfGetHidden( $array ){
	if ( is_array($array) ){
		foreach( $array as $key => $val ){
			if ( is_array( $val )){
				for ( $i=0; $i<count($val); $i++){
					$return[ $key.'['.$i.']'] = $val[$i];
				}				
			} else {
				$return[$key] = $val;			
			}
		}
	}
	return $return;
}

//----������ʸ������Ѵ�
function lfConvertParam($array, $arrSearchColumn) {
	
	// ʸ���Ѵ�
	foreach ($arrSearchColumn as $data) {
		$arrConvList[ $data["column"] ] = $data["convert"];
	}

	$new_array = array();
	foreach ($arrConvList as $key => $val) {
		if ( strlen($array[$key]) > 0 ){						// �ǡ����Τ����Τ����֤�
			$new_array[$key] = $array[$key];
			if( strlen($val) > 0) {
				$new_array[$key] = mb_convert_kana($new_array[$key] ,$val);
			}
		}
	}
	return $new_array;
	
}


//---- ���ϥ��顼�����å�
function lfErrorCheck($array, $flag = '') {

	// flag ����Ͽ����
	
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("�ܵҥ�����", "customer_id", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��ƻ�ܸ�", "pref", 2), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�ܵ�̾", "name", STEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�ܵ�̾(����)", "kana", STEXT_LEN), array("KANA_CHECK", "MAX_LENGTH_CHECK"));

	$objErr->doFunc(array('�᡼�륢�ɥ쥹', "email", STEXT_LEN) ,array("EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�����ֹ�", "tel", TEL_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
		
	$objErr->doFunc(array("�������(����)", "buy_times_from", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������(��λ)", "buy_times_to", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	if ((is_numeric($array["buy_total_from"]) && is_numeric($array["buy_total_to"]) ) && ($array["buy_times_from"] > $array["buy_times_to"]) ) $objErr->arrErr["buy_times_from"] .= "�� ��������λ����ϰϤ������Ǥ���";
	
	$objErr->doFunc(array("������", "birth_month", 2), array("NUM_CHECK","MAX_LENGTH_CHECK"));
	
	$objErr->doFunc(array("������(������)", "b_start_year", "b_start_month", "b_start_day",), array("CHECK_DATE"));
	$objErr->doFunc(array("������(��λ��)", "b_end_year", "b_end_month", "b_end_day"), array("CHECK_DATE"));	
	$objErr->doFunc(array("������(������)","������(��λ��)", "b_start_year", "b_start_month", "b_start_day", "b_end_year", "b_end_month", "b_end_day"), array("CHECK_SET_TERM"));
	
	$objErr->doFunc(array("��Ͽ��������(������)", "start_year", "start_month", "start_day",), array("CHECK_DATE"));
	$objErr->doFunc(array("��Ͽ��������(��λ��)", "end_year", "end_month", "end_day"), array("CHECK_DATE"));	
	$objErr->doFunc(array("��Ͽ��������(������)","��Ͽ��������(��λ��)", "start_year", "start_month", "start_day", "end_year", "end_month", "end_day"), array("CHECK_SET_TERM"));
	
	$objErr->doFunc(array("�ǽ�������(������)", "buy_start_year", "buy_start_month", "buy_start_day",), array("CHECK_DATE"));
	$objErr->doFunc(array("�ǽ�����(��λ��)", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_DATE"));	
	$objErr->doFunc(array("�ǽ�������(������)","��Ͽ��������(��λ��)", "buy_start_year", "buy_start_month", "buy_start_day", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_SET_TERM"));
	
	$objErr->doFunc(array("�������ʥ�����", "buy_product_code", STEXT_LEN), array("MAX_LENGTH_CHECK"));

	$objErr->doFunc(array("��������̾", "buy_product_name", STEXT_LEN), array("MAX_LENGTH_CHECK"));
	
	$objErr->doFunc(array("�������(����)", "buy_total_from", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));	
	$objErr->doFunc(array("�������(��λ)", "buy_total_to", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));	

	$objErr->doFunc(array("�����ڡ���", "campaign_id", INT_LEN), array("NUM_CHECK"));	
		
	//�������(from) �� �������(to) �ξ��ϥ��顼�Ȥ���
	if ( (is_numeric($array["buy_total_from"]) && is_numeric($array["buy_total_to"]) ) && 
		 ($array["buy_total_from"] > $array["buy_total_to"]) ) {
		 $objErr->arrErr["buy_total_from"] .= "�� ������ۤλ����ϰϤ������Ǥ���";
	 }

	if ( $flag ){
		$objErr->doFunc(array("�ƥ�ץ졼��", "template_id"), array("EXIST_CHECK", "NUM_CHECK"));
		$objErr->doFunc(array("�᡼��������ˡ", "mail_method"), array("EXIST_CHECK", "NUM_CHECK"));
		
		if(MELMAGA_BATCH_MODE) {
			$objErr->doFunc(array("�ۿ�����ǯ��","send_year"), array("EXIST_CHECK", "NUM_CHECK"));
			$objErr->doFunc(array("�ۿ����ʷ��","send_month"), array("EXIST_CHECK", "NUM_CHECK"));
			$objErr->doFunc(array("�ۿ���������","send_day"), array("EXIST_CHECK", "NUM_CHECK"));
			$objErr->doFunc(array("�ۿ����ʻ���","send_hour"), array("EXIST_CHECK", "NUM_CHECK"));
			$objErr->doFunc(array("�ۿ�����ʬ��","send_minutes"), array("EXIST_CHECK", "NUM_CHECK"));
			$objErr->doFunc(array("�ۿ���", "send_year", "send_month", "send_day"), array("CHECK_DATE"));
			$objErr->doFunc(array("�ۿ���", "send_year", "send_month", "send_day","send_hour", "send_minutes"), array("ALL_EXIST_CHECK"));
		}
		$objErr->doFunc(array("Subject", "subject", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
		$objErr->doFunc(array("��ʸ", 'body', LLTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));	// HTML�ƥ�ץ졼�Ȥ���Ѥ��ʤ����
	}

	return $objErr->arrErr;
}

/* �ƥ�ץ졼��ID��subject��������֤� */ 
function getTemplateList($conn){
	global $arrMagazineTypeAll;
	
	$sql = "SELECT template_id, subject, mail_method FROM dtb_mailmaga_template WHERE del_flg = 0 ";
	if ($_POST["htmlmail"] == 2 || $_POST['mail_type'] == 2) {
		$sql .= " AND mail_method = 2 ";	//TEXT��˾�Ԥؤ�TEST�᡼��ƥ�ץ졼�ȥꥹ��
	}
	$sql .= " ORDER BY template_id DESC";
	$result = $conn->getAll($sql);
	
	if ( is_array($result) ){ 
		foreach( $result as $line ){
			$return[$line['template_id']] = "��" . $arrMagazineTypeAll[$line['mail_method']] . "��" . $line['subject'];  
		}
	}
	
	return $return;
}

/* �ƥ�ץ졼��ID����ƥ�ץ졼�ȥǡ�������� */ 
function getTemplateData($conn, $id){
	
	if ( sfCheckNumLength($id) ){
		$sql = "SELECT * FROM dtb_mailmaga_template WHERE template_id = ? ORDER BY template_id DESC";
		$result = $conn->getAll( $sql, array($id) );
		if ( is_array($result) ) {
			$return = $result[0];
		}
	}
	return $return;
}



?>