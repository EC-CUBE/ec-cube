<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'entry/index.tpl';		// �ᥤ��ƥ�ץ졼��
		$this->tpl_title .= '�����Ͽ(1/3)';			//���ڡ��������ȥ�
	}
}

//---- �ڡ����������
$CONF = sf_getBasisData();					// Ź�޴��ܾ���
$objConn = new SC_DbConn();
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
$objPage->arrPref = $arrPref;
$objPage->arrJob = $arrJob;
$objPage->arrReminder = $arrReminder;
$objPage->arrYear = $objDate->getYear('', 1950);	//�����եץ����������
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

// ���᡼��
if (isset($_SESSION['mobile']['kara_mail_from'])) {
	$objPage->tpl_kara_mail_from = $_POST['email'] = $_SESSION['mobile']['kara_mail_from'];
} elseif (MOBILE_USE_KARA_MAIL) {
	$token = gfPrepareKaraMail('entry/index.php');
	if ($token !== false) {
		$objPage->tpl_mainpage = 'entry/mail.tpl';
		$objPage->tpl_title = '�����Ͽ(���᡼��)';
		$objPage->tpl_kara_mail_to = MOBILE_KARA_MAIL_ADDRESS_USER . MOBILE_KARA_MAIL_ADDRESS_DELIMITER . 'entry_' . $token . '@' . MOBILE_KARA_MAIL_ADDRESS_DOMAIN;
		$objPage->tpl_from_address = $CONF['email03'];
	}
}

//SSLURLȽ��
if (SSLURL_CHECK == 1){
	$ssl_url= sfRmDupSlash(SSL_URL.$_SERVER['REQUEST_URI']);
	if (!ereg("^https://", $non_ssl_url)){
		sfDispSiteError(URL_ERROR);
	}
}

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
							 array(  "column" => "email2", "convert" => "a" ),
							 array(  "column" => "email_mobile", "convert" => "a" ),
							 array(  "column" => "email_mobile2", "convert" => "a" ),
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
							 array(  "column" => "password02", "convert" => "a" )
						 );

//---- ��Ͽ�����ѥ��������
$arrRejectRegistColumn = array("year", "month", "day", "email02", "email_mobile02", "password02");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	//-- POST�ǡ����ΰ����Ѥ�
	$objPage->arrForm = $_POST;
	
	if($objPage->arrForm['year'] == '----') {
		$objPage->arrForm['year'] = '';
	}
	
	$objPage->arrForm['email'] = strtolower($objPage->arrForm['email']);		// email�Ϥ��٤ƾ�ʸ���ǽ���
	
	//-- ���ϥǡ������Ѵ�
	$objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);

	//--�����ϥ��顼�����å�
	if ($_POST["mode"] == "set1") {
		$objPage->arrErr = lfErrorCheck1($objPage->arrForm);
		$objPage->tpl_mainpage = 'entry/index.tpl';
		$objPage->tpl_title = '�����Ͽ(1/3)';
	} elseif ($_POST["mode"] == "set2") {
		$objPage->arrErr = lfErrorCheck2($objPage->arrForm);
		$objPage->tpl_mainpage = 'entry/set1.tpl';
		$objPage->tpl_title = '�����Ͽ(2/3)';
	} else {
		$objPage->arrErr = lfErrorCheck3($objPage->arrForm);
		$objPage->tpl_mainpage = 'entry/set2.tpl';
		$objPage->tpl_title = '�����Ͽ(3/3)';
	}

	if ($objPage->arrErr || $_POST["mode"] == "return") {		// ���ϥ��顼�Υ����å�
		foreach($objPage->arrForm as $key => $val) {
			$objPage->$key = $val;
		}

		//-- �ǡ���������
		if ($_POST["mode"] == "set1") {
			$checkVal = array("email", "password", "reminder", "reminder_answer", "name01", "name02", "kana01", "kana02");
			foreach($objPage->arrForm as $key => $val) {
				if ($key != "mode" && $key != "subm" & !in_array($key, $checkVal)) $objPage->list_data[ $key ] = $val;
			}

		} elseif ($_POST["mode"] == "set2") {
			$checkVal = array("sex", "year", "month", "day", "zip01", "zip02");
			foreach($objPage->arrForm as $key => $val) {
				if ($key != "mode" && $key != "subm" & !in_array($key, $checkVal)) $objPage->list_data[ $key ] = $val;
			}
		} else {
			$checkVal = array("pref", "addr01", "addr02", "tel01", "tel02", "tel03");
			foreach($objPage->arrForm as $key => $val) {
				if ($key != "mode" && $key != "subm" & !in_array($key, $checkVal)) $objPage->list_data[ $key ] = $val;
			}
		}


	} else {

		//--���ƥ�ץ졼������
		if ($_POST["mode"] == "set1") {
			$objPage->tpl_mainpage = 'entry/set1.tpl';
			$objPage->tpl_title = '�����Ͽ(2/3)';
		} elseif ($_POST["mode"] == "set2") {
			$objPage->tpl_mainpage = 'entry/set2.tpl';
			$objPage->tpl_title = '�����Ͽ(3/3)';

			$address = lfGetAddress($_REQUEST['zip01'].$_REQUEST['zip02']);
			$objView->assign("pref", @$address[0]['state']);
			$objView->assign("addr01", @$address[0]['city'] . @$address[0]['town']);
		} elseif ($_POST["mode"] == "confirm") {
			//�ѥ����ɽ��
			$passlen = strlen($objPage->arrForm['password']);
			$objPage->passlen = lfPassLen($passlen);
			
			//�᡼��������
			if ($objPage->arrForm['mail_flag'] = "ON") {
				$objPage->arrForm['mail_flag']  = "2";
			}

			$objPage->tpl_mainpage = 'entry/confirm.tpl';
			$objPage->tpl_title = '�����Ͽ(��ǧ�ڡ���)';

		}

		//-- �ǡ�������
		unset($objPage->list_data);
		foreach($objPage->arrForm as $key => $val) {
			if ($key != "mode" && $key != "subm") $objPage->list_data[ $key ] = $val;
		}


		//--������Ͽ�ȴ�λ����
		if ($_POST["mode"] == "complete") {
			$objPage->uniqid = lfRegistData ($objPage->arrForm, $arrRegistColumn, $arrRejectRegistColumn);

			// ���᡼�������Ѥߤξ��Ϥ���������Ͽ��λ�ˤ��롣
			if (isset($_SESSION['mobile']['kara_mail_from'])) {
				header("Location:" . gfAddSessionId(URL_DIR . "regist/index.php?mode=regist&id=" . $objPage->uniqid));
				exit;
			}

			$objPage->tpl_mainpage = 'entry/complete.tpl';
			$objPage->tpl_title = '�����Ͽ(��λ�ڡ���)';

			sfMobileSetExtSessionId('id', $objPage->uniqid, 'regist/index.php');

			//������Ͽ��λ�᡼������
			$objPage->CONF = $CONF;
			$objPage->to_name01 = $_POST['name01'];
			$objPage->to_name02 = $_POST['name02'];
			$objMailText = new SC_SiteView();
			$objMailText->assignobj($objPage);
			$subject = sfMakesubject('�����Ͽ�Τ���ǧ');
			$toCustomerMail = $objMailText->fetch("mail_templates/customer_mail.tpl");
			$objMail = new GC_SendMail();
			$objMail->setItem(
								''									//������
								, $subject							//�����֥�������
								, $toCustomerMail					//����ʸ
								, $CONF["email03"]					//�����������ɥ쥹
								, $CONF["shop_name"]				//����������̾��
								, $CONF["email03"]					//��reply_to
								, $CONF["email04"]					//��return_path
								, $CONF["email04"]					//  Errors_to
								, $CONF["email01"]					//  Bcc
																);
			// ���������
			$name = $_POST["name01"] . $_POST["name02"] ." ��";
			$objMail->setTo($_POST["email"], $name);
			$objMail->sendMail();

			// ��λ�ڡ����˰�ư�����롣
			header("Location:" . gfAddSessionId("./complete.php"));
			exit;
		}
	}
}

if($objPage->year == '') {
	$objPage->year = '----';
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

	//--�������ǥ��ޥ���Ͽ���Ƥ��뤫��Ƚ��
	$sql = "SELECT count(*) FROM dtb_customer_mail WHERE email = ?";
	$mailResult = $objConn->getOne($sql, array($arrRegist["email"]));

	//--�����ޥ�����Ͽ�¹�
	$arrRegistMail["email"] = $arrRegist["email"];	
	if ($array["mail_flag"] == 1) {
		$arrRegistMail["mail_flag"] = 4; 
	} elseif ($array["mail_flag"] == 2) {
		$arrRegistMail["mail_flag"] = 5; 
	} else {
		$arrRegistMail["mail_flag"] = 6; 
	}
	$arrRegistMail["update_date"] = "now()";
	
	// �����ǥ��ޥ���Ͽ���Ƥ�����
	if ($mailResult == 1) {		
		$objQuery->update("dtb_customer_mail", $arrRegistMail, "email = '" .addslashes($arrRegistMail["email"]). "'");			
	} else {				//��������Ͽ�ξ��
		$arrRegistMail["create_date"] = "now()";
		$objQuery->insert("dtb_customer_mail", $arrRegistMail);		
	}
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

//---- ���ϥ��顼�����å�
function lfErrorCheck1($array) {

	global $objConn;
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("��̾��������", 'name01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��̾����̾��", 'name02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�եꥬ�ʡʥ�����", 'kana01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("�եꥬ�ʡʥᥤ��", 'kana02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹', "email", MTEXT_LEN) ,array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK", "MOBILE_EMAIL_CHECK"));

	//�������Ƚ�� ����������⤷���ϲ���Ͽ��ϡ��ᥢ�ɰ�դ�����ˤʤäƤ�Τ�Ʊ���ᥢ�ɤ���Ͽ�Բ�
	if (strlen($array["email"]) > 0) {
		$objQuery = new SC_Query();
		$arrRet = $objQuery->select("email, update_date, del_flg", "dtb_customer","email ILIKE ? OR email_mobile ILIKE ? ORDER BY del_flg", array($array["email"], $array["email"]));
				
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
	$objErr->doFunc(array("�ѥ���ɤ�˺�줿�Ȥ��Υҥ�� ����", "reminder") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("�ѥ���ɤ�˺�줿�Ȥ��Υҥ�� ����", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK","SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
	
	return $objErr->arrErr;
}

//---- ���ϥ��顼�����å�
function lfErrorCheck2($array) {

	global $objConn;
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("͹���ֹ�1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("͹���ֹ�2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("͹���ֹ�", "zip01", "zip02"), array("ALL_EXIST_CHECK"));

	$objErr->doFunc(array("������", "sex") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("��ǯ����", "year", "month", "day"), array("SELECT_CHECK", "CHECK_DATE"));
	
	return $objErr->arrErr;
}

//---- ���ϥ��顼�����å�
function lfErrorCheck3($array) {

	global $objConn;
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("��ƻ�ܸ�", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("������1", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("������2", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������ֹ�1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK" ));
	$objErr->doFunc(array("�������ֹ�2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK" ));
	$objErr->doFunc(array("�������ֹ�3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK" ));
	$objErr->doFunc(array("�������ֹ�", "tel01", "tel02", "tel03",TEL_ITEM_LEN) ,array("TEL_CHECK"));
	
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

//-----------------------------------------------------------------------------------------------------------------------------------
?>
