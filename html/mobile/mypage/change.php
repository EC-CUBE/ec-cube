<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * �����ѹ�
 */

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'mypage/change.tpl';		// �ᥤ��ƥ�ץ졼��
		$this->tpl_title .= '��Ͽ�ѹ�(1/3)';			//���ڡ��������ȥ�
	}
}

//---- �ڡ����������
$CONF = sf_getBasisData();					// Ź�޴��ܾ���
$objConn = new SC_DbConn();
$objPage = new LC_Page();
$objView = new SC_MobileView();
$objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();
$objPage->arrPref = $arrPref;
$objPage->arrJob = $arrJob;
$objPage->arrReminder = $arrReminder;
$objPage->arrYear = $objDate->getYear('', 1950);	//�����եץ����������
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

//---- ��Ͽ�ѥ��������
$arrRegistColumn = array(
							 array(  "column" => "name01", "convert" => "aKV" ),
							 array(  "column" => "name02", "convert" => "aKV" ),
							 array(  "column" => "kana01", "convert" => "CKV" ),
							 array(  "column" => "kana02", "convert" => "CKV" ),
							 array(  "column" => "zip01", "convert" => "n" ),
							 array(  "column" => "zip02", "convert" => "n" ),
							 array(  "column" => "pref", "convert" => "n" ),
							 array(  "column" => "addr01", "convert" => "aKV" ),
							 array(  "column" => "addr02", "convert" => "aKV" ),
							 array(  "column" => "email", "convert" => "a" ),
							 array(  "column" => "email_mobile", "convert" => "a" ),
							 array(  "column" => "tel01", "convert" => "n" ),
							 array(  "column" => "tel02", "convert" => "n" ),
							 array(  "column" => "tel03", "convert" => "n" ),
							 array(  "column" => "fax01", "convert" => "n" ),
							 array(  "column" => "fax02", "convert" => "n" ),
							 array(  "column" => "fax03", "convert" => "n" ),
							 array(  "column" => "sex", "convert" => "n" ),
							 array(  "column" => "job", "convert" => "n" ),
							 array(  "column" => "birth", "convert" => "n" ),
							 array(  "column" => "reminder", "convert" => "n" ),
							 array(  "column" => "reminder_answer", "convert" => "aKV"),
							 array(  "column" => "password", "convert" => "a" ),
							 array(  "column" => "mailmaga_flg", "convert" => "n" )			 
						 );

//---- ��Ͽ�����ѥ��������
$arrRejectRegistColumn = array("year", "month", "day", "email02", "email_mobile02", "password02");

$objPage->arrForm = lfGetCustomerData();
$objPage->arrForm['password'] = DEFAULT_PASSWORD;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	//-- POST�ǡ����ΰ����Ѥ�
	$objPage->arrForm = array_merge($objPage->arrForm, $_POST);

	if($objPage->arrForm['year'] == '----') {
		$objPage->arrForm['year'] = '';
	}
	
	$objPage->arrForm['email'] = strtolower($objPage->arrForm['email']);		// email�Ϥ��٤ƾ�ʸ���ǽ���
	
	//-- ���ϥǡ������Ѵ�
	$objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);

	// ���ܥ����ѽ���
	if (!empty($_POST["return"])) {
		switch ($_POST["mode"]) {
		case "complete":
			$_POST["mode"] = "set3";
			break;
		case "confirm":
			$_POST["mode"] = "set2";
			break;
		default:
			$_POST["mode"] = "set1";
			break;
		}
	}

	//--�����ϥ��顼�����å�
	if ($_POST["mode"] == "set1") {
		$objPage->arrErr = lfErrorCheck1($objPage->arrForm);
		$objPage->tpl_mainpage = 'mypage/change.tpl';
		$objPage->tpl_title = '��Ͽ�ѹ�(1/3)';
	} elseif ($_POST["mode"] == "set2") {
		$objPage->arrErr = lfErrorCheck2($objPage->arrForm);
		$objPage->tpl_mainpage = 'mypage/set1.tpl';
		$objPage->tpl_title = '��Ͽ�ѹ�(2/3)';
	} else {
		$objPage->arrErr = lfErrorCheck3($objPage->arrForm);
		$objPage->tpl_mainpage = 'mypage/set2.tpl';
		$objPage->tpl_title = '��Ͽ�ѹ�(3/3)';
	}

	if ($objPage->arrErr || !empty($_POST["return"])) {		// ���ϥ��顼�Υ����å�
		foreach($objPage->arrForm as $key => $val) {
			$objPage->$key = $val;
		}

		//-- �ǡ���������
		if ($_POST["mode"] == "set1") {
			$checkVal = array("email", "password", "reminder", "reminder_answer", "name01", "name02", "kana01", "kana02");
		} elseif ($_POST["mode"] == "set2") {
			$checkVal = array("sex", "year", "month", "day", "zip01", "zip02");
		} else {
			$checkVal = array("pref", "addr01", "addr02", "tel01", "tel02", "tel03", "mail_flag");
		}

		foreach($objPage->arrForm as $key => $val) {
			if ($key != "return" && $key != "mode" && $key != "confirm" && $key != session_name() && !in_array($key, $checkVal)) {
				$objPage->list_data[ $key ] = $val;
			}
		}

	} else {

		//--���ƥ�ץ졼������
		if ($_POST["mode"] == "set1") {
			$objPage->tpl_mainpage = 'mypage/set1.tpl';
			$objPage->tpl_title = '��Ͽ�ѹ�(2/3)';
		} elseif ($_POST["mode"] == "set2") {
			$objPage->tpl_mainpage = 'mypage/set2.tpl';
			$objPage->tpl_title = '��Ͽ�ѹ�(3/3)';
		} elseif ($_POST["mode"] == "confirm") {
			//�ѥ����ɽ��
			$passlen = strlen($objPage->arrForm['password']);
			$objPage->passlen = lfPassLen($passlen);

			// �᡼��������
			if (strtolower($_POST['mailmaga_flg']) == "on") {
				$_POST['mailmaga_flg']  = "2";
			} else {
				$_POST['mailmaga_flg']  = "3";
			}

			$objPage->tpl_mainpage = 'mypage/change_confirm.tpl';
			$objPage->tpl_title = '��Ͽ�ѹ�(��ǧ�ڡ���)';

		}

		//-- �ǡ�������
		unset($objPage->list_data);
		if ($_POST["mode"] == "set1") {
			$checkVal = array("sex", "year", "month", "day", "zip01", "zip02");
		} elseif ($_POST["mode"] == "set2") {
			$checkVal = array("pref", "addr01", "addr02", "tel01", "tel02", "tel03", "mail_flag");
		} else {
			$checkVal = array();
		}

		foreach($_POST as $key => $val) {
			if ($key != "return" && $key != "mode" && $key != "confirm" && $key != session_name() && !in_array($key, $checkVal)) {
				$objPage->list_data[ $key ] = $val;
			}
		}


		//--������Ͽ�ȴ�λ����
		if ($_POST["mode"] == "complete") {

			//-- ���ϥǡ������Ѵ�
			$arrForm = lfConvertParam($_POST, $arrRegistColumn);
			$arrForm['email'] = strtolower($arrForm['email']);		// email�Ϥ��٤ƾ�ʸ���ǽ���
	
			//���顼�����å�
			$objPage->arrErr = lfErrorCheck($objPage->arrForm);
			$email_flag = true;

			if($objPage->arrForm['email'] != $objCustomer->getValue('email_mobile')) {
				//�᡼�륢�ɥ쥹�ν�ʣ�����å�
				$email_cnt = $objQuery->count("dtb_customer","del_flg=0 AND (email=? OR email_mobile=?)", array($objPage->arrForm['email'], $objPage->arrForm['email']));
				if ($email_cnt > 0){
					$email_flag = false;
				}
			}

			//���顼�ʤ��Ǥ��ĥ᡼�륢�ɥ쥹����ʣ���Ƥ��ʤ����
			if($objPage->arrErr == "" && $email_flag) {
				$arrForm['customer_id'] = $objCustomer->getValue('customer_id');
				//-- �Խ���Ͽ
				sfEditCustomerData($arrForm, $arrRegistColumn);
				//���å��������ǿ��ξ��֤˹�������
				$objCustomer->updateSession();
				//��λ�ڡ�����
				header("Location: " . gfAddSessionId("change_complete.php"));
				exit;
			} else {
				sfDispSiteError(CUSTOMER_ERROR, "", false, "", true);
			}

		}
	}
}

$arrPrivateVariables = array('secret_key', 'first_buy_date', 'last_buy_date', 'buy_times', 'buy_total', 'point', 'note', 'status', 'create_date', 'update_date', 'del_flg', 'cell01', 'cell02', 'cell03', 'mobile_phone_id');
foreach ($arrPrivateVariables as $key) {
	unset($objPage->list_data[$key]);
}

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//----------------------------------------------------------------------------------------------------------------------

//---- function��
function lfRegistData ($array, $arrRegistColumn, $arrRejectRegistColumn) {
	global $objConn;

	// ����Ͽ
	foreach ($arrRegistColumn as $data) {
		if (strlen($array[ $data["column"] ]) > 0 && ! in_array($data["column"], $arrRejectRegistColumn)) {
			$arrRegist[ $data["column"] ] = $array[ $data["column"] ];
		}
	}
		
	// �����������Ϥ���Ƥ�����
	if (strlen($array["year"]) > 0 ) {
		$arrRegist["birth"] = $array["year"] ."/". $array["month"] ."/". $array["day"] ." 00:00:00";
	}
	
	// �ѥ���ɤΰŹ沽
	$arrRegist["password"] = sha1($arrRegist["password"] . ":" . AUTH_MAGIC);
	
	$count = 1;
	while ($count != 0) {
		$uniqid = sfGetUniqRandomId("t");
		$count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer WHERE secret_key = ?", array($uniqid));
	}
	
	$arrRegist["secret_key"] = $uniqid;		// ����ϿIDȯ��
	$arrRegist["create_date"] = "now()"; 	// ������
	$arrRegist["update_date"] = "now()"; 	// ������
	$arrRegist["first_buy_date"] = "";	 	// �ǽ�ι�����
	
	// ���ӥ᡼�륢�ɥ쥹
	$arrRegist['email_mobile'] = $arrRegist['email'];

	//-- ����Ͽ�¹�
	$objConn->query("BEGIN");

	$objQuery = new SC_Query();
	$objQuery->insert("dtb_customer", $arrRegist);
	$objConn->query("COMMIT");

	return $uniqid;
}

//----������ʸ������Ѵ�
function lfConvertParam($array, $arrRegistColumn) {
	/*
	 *	ʸ������Ѵ�
	 *	K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
	 *	C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
	 *	V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ�	
	 *	n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
	 *  a :  ���ѱѿ�����Ⱦ�ѱѿ������Ѵ�����
	 */
	// �����̾�ȥ���С��Ⱦ���
	foreach ($arrRegistColumn as $data) {
		$arrConvList[ $data["column"] ] = $data["convert"];
	}
	// ʸ���Ѵ�
	foreach ($arrConvList as $key => $val) {
		// POST����Ƥ����ͤΤ��Ѵ����롣
		if(strlen(($array[$key])) > 0) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}


//���顼�����å�

function lfErrorCheck($array) {
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("��̾��������", 'name01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��̾����̾��", 'name02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��̾���ʥ���/����", 'kana01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("��̾���ʥ���/̾��", 'kana02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("͹���ֹ�1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("͹���ֹ�2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("͹���ֹ�", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("��ƻ�ܸ�", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("�Զ�Į¼", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("����", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹', "email", MTEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "NO_SPTAB" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�����ֹ�1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK"));
	$objErr->doFunc(array("�����ֹ�2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK"));
	$objErr->doFunc(array("�����ֹ�3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK"));
	$objErr->doFunc(array("�����ֹ�", "tel01", "tel02", "tel03", TEL_LEN) ,array("TEL_CHECK"));
	$objErr->doFunc(array("FAX�ֹ�", "fax01", "fax02", "fax03", TEL_LEN) ,array("TEL_CHECK"));
	$objErr->doFunc(array("����", "sex") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("������", "job") ,array("NUM_CHECK"));
	$objErr->doFunc(array("��ǯ����", "year", "month", "day"), array("CHECK_DATE"));
	$objErr->doFunc(array("�ѥ����", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "ALNUM_CHECK", "NUM_RANGE_CHECK"));
	$objErr->doFunc(array("�ѥ���ɳ�ǧ�Ѥμ���", "reminder") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("�ѥ���ɳ�ǧ�Ѥμ��������", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	return $objErr->arrErr;
	
}

//---- ���ϥ��顼�����å�
function lfErrorCheck1($array) {

	global $objConn;
	global $objCustomer;
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("��̾��������", 'name01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��̾����̾��", 'name02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��̾���ʥ���/����", 'kana01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("��̾���ʥ���/̾��", 'kana02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹', "email", MTEXT_LEN) ,array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK", "MOBILE_EMAIL_CHECK"));

	//�������Ƚ�� ����������⤷���ϲ���Ͽ��ϡ��ᥢ�ɰ�դ�����ˤʤäƤ�Τ�Ʊ���ᥢ�ɤ���Ͽ�Բ�
	$array["customer_id"] = $objCustomer->getValue('customer_id');
	if (strlen($array["email"]) > 0) {
		$objQuery = new SC_Query();
		$arrRet = $objQuery->select("email, update_date, del_flg", "dtb_customer","customer_id <> ? and (email ILIKE ? OR email_mobile ILIKE ?) ORDER BY del_flg", array($array["customer_id"], $array["email"], $array["email"]));

		if(count($arrRet) > 0) {
			if($arrRet[0]['del_flg'] != '1') {
				// ����Ǥ�����
				$objErr->arrErr["email"] .= "�� ���Ǥ˲����Ͽ�ǻ��Ѥ���Ƥ���᡼�륢�ɥ쥹�Ǥ���<br />";
			} else {
				// ��񤷤�����Ǥ�����
				$leave_time = sfDBDatetoTime($arrRet[0]['update_date']);
				$now_time = time();
				$pass_time = $now_time - $leave_time;
				// ��񤫤鲿����-�вᤷ�Ƥ��뤫Ƚ�ꤹ�롣
				$limit_time = ENTRY_LIMIT_HOUR * 3600;						
				if($pass_time < $limit_time) {
					$objErr->arrErr["email"] .= "�� ��񤫤������֤δ֤ϡ�Ʊ���᡼�륢�ɥ쥹����Ѥ��뤳�ȤϤǤ��ޤ���<br />";
				}
			}
		}
	}

	$objErr->doFunc(array("�ѥ����", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "SPTAB_CHECK" ,"ALNUM_CHECK", "NUM_RANGE_CHECK"));
	$objErr->doFunc(array("�ѥ���ɳ�ǧ�Ѥμ���", "reminder") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("�ѥ���ɳ�ǧ�Ѥμ��������", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK","SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
	
	return $objErr->arrErr;
}

//---- ���ϥ��顼�����å�
function lfErrorCheck2($array) {

	global $objConn, $objDate;
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("͹���ֹ�1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("͹���ֹ�2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("͹���ֹ�", "zip01", "zip02"), array("ALL_EXIST_CHECK"));

	$objErr->doFunc(array("����", "sex") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("��ǯ���� (ǯ)", "year", 4), array("EXIST_CHECK", "SPTAB_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	if (!isset($objErr->arrErr['year'])) {
		$objErr->doFunc(array("��ǯ���� (ǯ)", "year", $objDate->getStartYear()), array("MIN_CHECK"));
		$objErr->doFunc(array("��ǯ���� (ǯ)", "year", $objDate->getEndYear()), array("MAX_CHECK"));
	}
	$objErr->doFunc(array("��ǯ���� (����)", "month", "day"), array("SELECT_CHECK"));
	if (!isset($objErr->arrErr['year']) && !isset($objErr->arrErr['month']) && !isset($objErr->arrErr['day'])) {
		$objErr->doFunc(array("��ǯ����", "year", "month", "day"), array("CHECK_DATE"));
	}
	
	return $objErr->arrErr;
}

//---- ���ϥ��顼�����å�
function lfErrorCheck3($array) {

	global $objConn;
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("��ƻ�ܸ�", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("�Զ�Į¼", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("����", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�����ֹ�1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK" ));
	$objErr->doFunc(array("�����ֹ�2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK" ));
	$objErr->doFunc(array("�����ֹ�3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK" ));
	$objErr->doFunc(array("�����ֹ�", "tel01", "tel02", "tel03",TEL_ITEM_LEN) ,array("TEL_CHECK"));
	
	return $objErr->arrErr;
}

//��ǧ�ڡ����ѥѥ����ɽ����

function lfPassLen($passlen){
	$ret = "";
	for ($i=0;$i<$passlen;true){
	$ret.="*";
	$i++;
	}
	return $ret;
}


// ͹���ֹ椫�齻��μ���
function lfGetAddress($zipcode) {
	global $arrPref;

	$conn = new SC_DBconn(ZIP_DSN);

	// ͹���ֹ渡��ʸ����
	$zipcode = mb_convert_kana($zipcode ,"n");
	$sqlse = "SELECT state, city, town FROM mtb_zip WHERE zipcode = ?";

	$data_list = $conn->getAll($sqlse, array($zipcode));

	// ����ǥå������ͤ�ȿž�����롣
	$arrREV_PREF = array_flip($arrPref);

	/*
		��̳�ʤ����������ɤ����ǡ����򤽤Τޤޥ���ݡ��Ȥ����
		�ʲ��Τ褦��ʸ�������äƤ���Τ�	�к����롣
		���ʣ����������ܡ�
		���ʲ��˷Ǻܤ��ʤ����
	*/
	$town =  $data_list[0]['town'];
	$town = ereg_replace("��.*��$","",$town);
	$town = ereg_replace("�ʲ��˷Ǻܤ��ʤ����","",$town);
	$data_list[0]['town'] = $town;
	$data_list[0]['state'] = $arrREV_PREF[$data_list[0]['state']];

	return $data_list;
}

//�ܵҾ���μ���
function lfGetCustomerData(){
	global $objQuery;
	global $objCustomer;
	//�ܵҾ������
	$ret = $objQuery->select("*","dtb_customer","customer_id=?", array($objCustomer->getValue('customer_id')));
	$arrForm = $ret[0];
	$arrForm['email'] = $arrForm['email_mobile'];

	//���ޥ��ե饰����
	$arrForm['mailmaga_flg'] = $objQuery->get("dtb_customer","mailmaga_flg","email=?", array($objCustomer->getValue('email_mobile')));
	
	//��������ǯ��������
	if (isset($arrForm['birth'])){
		$birth = split(" ", $arrForm["birth"]);
		list($year, $month, $day) = split("-",$birth[0]);
		
		$arrForm['year'] = $year;
		$arrForm['month'] = $month;
		$arrForm['day'] = $day;
		
	}
	return $arrForm;
}


//-----------------------------------------------------------------------------------------------------------------------------------
?>
