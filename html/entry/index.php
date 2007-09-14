<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css = URL_DIR.'css/layout/entry/index.css';	// �ᥤ��CSS�ѥ�
		$this->tpl_mainpage = 'entry/index.tpl';		// �ᥤ��ƥ�ץ졼��
		$this->tpl_title .= '�����Ͽ(���ϥڡ���)';			//���ڡ��������ȥ�
	}
}

//---- �ڡ����������
$CONF = sf_getBasisData();					// Ź�޴��ܾ���
$objConn = new SC_DbConn();
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;
$objCustomer = new SC_Customer();
$objCampaignSess = new SC_CampaignSession();
$objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
$objPage->arrPref = $arrPref;
$objPage->arrJob = $arrJob;
$objPage->arrReminder = $arrReminder;
$objPage->arrYear = $objDate->getYear('', 1950);	//�����եץ����������
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

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
							 array(  "column" => "password02", "convert" => "a" ),
							 array(  "column" => "mailmaga_flg", "convert" => "n" ),
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
	$objPage->arrForm['email02'] = strtolower($objPage->arrForm['email02']);	// email�Ϥ��٤ƾ�ʸ���ǽ���
	
	//-- ���ϥǡ������Ѵ�
	$objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);
		
	//--�����ϥ��顼�����å�
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);

	if ($objPage->arrErr || $_POST["mode"] == "return") {		// ���ϥ��顼�Υ����å�
		foreach($objPage->arrForm as $key => $val) {
			$objPage->$key = $val;
		}

	} else {

		//--����ǧ
		if ($_POST["mode"] == "confirm") {
			foreach($objPage->arrForm as $key => $val) {
				if ($key != "mode" && $key != "subm") $objPage->list_data[ $key ] = $val;
			}
			//�ѥ����ɽ��
			$passlen = strlen($objPage->arrForm['password']);
			$objPage->passlen = lfPassLen($passlen);
			
			$objPage->tpl_css = '/css/layout/entry/confirm.css';
			$objPage->tpl_mainpage = 'entry/confirm.tpl';
			$objPage->tpl_title = '�����Ͽ(��ǧ�ڡ���)';

		}

		//--�������Ͽ�ȴ�λ����
		if ($_POST["mode"] == "complete") {
			// �����ڡ��󤫤�����ܤλ��Ѥ���
			if($objCampaignSess->getIsCampaign()) {
				$objPage->etc_value = "&cp=".$objCampaignSess->getCampaignId();
			}
			
			// ����������Ͽ
			$objPage->uniqid = lfRegistData ($objPage->arrForm, $arrRegistColumn, $arrRejectRegistColumn, CUSTOMER_CONFIRM_MAIL);
			
			$objPage->tpl_css = '/css/layout/entry/complete.css';
			$objPage->tpl_mainpage = 'entry/complete.tpl';
			$objPage->tpl_title = '�����Ͽ(��λ�ڡ���)';

			//����λ�᡼������
			$objPage->CONF = $CONF;
			$objPage->name01 = $_POST['name01'];
			$objPage->name02 = $_POST['name02'];
			$objMailText = new SC_SiteView();
			$objMailText->assignobj($objPage);
			
			// �������ͭ���ξ��
			if(CUSTOMER_CONFIRM_MAIL == true) {
				$subject = sfMakesubject('�����Ͽ�Τ���ǧ');
				$toCustomerMail = $objMailText->fetch("mail_templates/customer_mail.tpl");
			} else {
				$subject = sfMakesubject('�����Ͽ�Τ���λ');
				$toCustomerMail = $objMailText->fetch("mail_templates/customer_regist_mail.tpl");
				// ��������֤ˤ���
				$objCustomer->setLogin($_POST["email"]);
			}
			
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
							);
			// ���������
			$name = $_POST["name01"] . $_POST["name02"] ." ��";
			$objMail->setTo($_POST["email"], $name);
			$objMail->sendMail();

			// ��λ�ڡ����˰�ư�����롣
			header("Location: ./complete.php");
			exit;
		}
	}
}

if($objPage->year == '') {
	$objPage->year = '----';
}

//----���ڡ���ɽ��
$objView->assignobj($objPage);
// �ե졼�������(�����ڡ���ڡ����������ܤʤ��ѹ�)
$objCampaignSess->pageView($objView);

//----------------------------------------------------------------------------------------------------------------------
// ����������Ͽ
function lfRegistData ($array, $arrRegistColumn, $arrRejectRegistColumn, $confirm_flg) {
	global $objConn;
	global $arrInfo;

	// ��Ͽ�ǡ���������
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
	
	// �������Ͽ�ξ��
	if($confirm_flg == true) {
		// ��ʣ���ʤ������Ͽ������ȯ�Ԥ��롣
		$count = 1;
		while ($count != 0) {
			$uniqid = sfGetUniqRandomId("t");
			$count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer WHERE secret_key = ?", array($uniqid));
		}
		switch($array["mailmaga_flg"]) {
			case 1:
				$arrRegist["mailmaga_flg"] = 4; 
				break;
			case 2:
				$arrRegist["mailmaga_flg"] = 5; 
				break;
			default:
				$arrRegist["mailmaga_flg"] = 6;
				break;
		}
		
		$arrRegist["status"] = "1";				// �����
	} else {
		// ��ʣ���ʤ������Ͽ������ȯ�Ԥ��롣
		$count = 1;
		while ($count != 0) {
			$uniqid = sfGetUniqRandomId("r");
			$count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer WHERE secret_key = ?", array($uniqid));
		}
		$arrRegist["status"] = "2";				// �ܲ��
	}
	
	/*
	  secret_key�ϡ��ơ��֥�ǽ�ʣ���Ĥ���Ƥ��ʤ���礬����Τǡ�
      �ܲ����Ͽ�Ǥ����Ѥ���ʤ������åȤ��Ƥ�����
    */
	$arrRegist["secret_key"] = $uniqid;		// �����Ͽ����
	$arrRegist["create_date"] = "now()"; 	// ������
	$arrRegist["update_date"] = "now()"; 	// ������
	$arrRegist["first_buy_date"] = "";	 	// �ǽ�ι�����
	
	//-- ����Ͽ�¹�
	$objConn->query("BEGIN");

	$objQuery = new SC_Query();
	$objQuery->insert("dtb_customer", $arrRegist);


/* ���ޥ������ǽ�ϸ�������桡2007/03/07


	//--�������ǥ��ޥ���Ͽ���Ƥ��뤫��Ƚ��
	$sql = "SELECT count(*) FROM dtb_customer_mail WHERE email = ?";
	$mailResult = $objConn->getOne($sql, array($arrRegist["email"]));

	//--�����ޥ�����Ͽ�¹�
	$arrRegistMail["email"] = $arrRegist["email"];	
	if ($array["mailmaga_flg"] == 1) {
		$arrRegistMail["mailmaga_flg"] = 4; 
	} elseif ($array["mailmaga_flg"] == 2) {
		$arrRegistMail["mailmaga_flg"] = 5; 
	} else {
		$arrRegistMail["mailmaga_flg"] = 6; 
	}
	$arrRegistMail["update_date"] = "now()";
	
	// �����ǥ��ޥ���Ͽ���Ƥ�����
	if ($mailResult == 1) {		
		$objQuery->update("dtb_customer_mail", $arrRegistMail, "email = '" .addslashes($arrRegistMail["email"]). "'");			
	} else {				//��������Ͽ�ξ��
		$arrRegistMail["create_date"] = "now()";
		$objQuery->insert("dtb_customer_mail", $arrRegistMail);		
	}
*/
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
function lfErrorCheck($array) {

	global $objConn;
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("��̾��������", 'name01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��̾����̾��", 'name02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�եꥬ�ʡʥ�����", 'kana01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("�եꥬ�ʡʥᥤ��", 'kana02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("͹���ֹ�1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("͹���ֹ�2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("͹���ֹ�", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("��ƻ�ܸ�", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("������1", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("������2", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹', "email", MTEXT_LEN) ,array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹(��ǧ)', "email02", MTEXT_LEN) ,array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK","SPTAB_CHECK" , "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹', '�᡼�륢�ɥ쥹(��ǧ)', "email", "email02") ,array("EQUAL_CHECK"));

	//�������Ƚ�� ����������⤷���ϲ���Ͽ��ϡ��ᥢ�ɰ�դ�����ˤʤäƤ�Τ�Ʊ���ᥢ�ɤ���Ͽ�Բ�
	if (strlen($array["email"]) > 0) {
		$objQuery = new SC_Query();
		$arrRet = $objQuery->select("email, update_date, del_flg", "dtb_customer","email ILIKE ? ORDER BY del_flg", array($array["email"]));
				
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

	$objErr->doFunc(array("�������ֹ�1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK" ));
	$objErr->doFunc(array("�������ֹ�2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK" ));
	$objErr->doFunc(array("�������ֹ�3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK" ));
	$objErr->doFunc(array("�������ֹ�", "tel01", "tel02", "tel03",TEL_ITEM_LEN) ,array("TEL_CHECK"));
	$objErr->doFunc(array("FAX�ֹ�1", 'fax01'), array("SPTAB_CHECK"));
	$objErr->doFunc(array("FAX�ֹ�2", 'fax02'), array("SPTAB_CHECK"));
	$objErr->doFunc(array("FAX�ֹ�3", 'fax03'), array("SPTAB_CHECK"));
	$objErr->doFunc(array("FAX�ֹ�", "fax01", "fax02", "fax03", TEL_ITEM_LEN) ,array("TEL_CHECK"));
	$objErr->doFunc(array("������", "sex") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("�ѥ����", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "SPTAB_CHECK" ,"ALNUM_CHECK", "NUM_RANGE_CHECK"));
	$objErr->doFunc(array("�ѥ����(��ǧ)", 'password02', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "SPTAB_CHECK" ,"ALNUM_CHECK", "NUM_RANGE_CHECK"));
	$objErr->doFunc(array('�ѥ����', '�ѥ����(��ǧ)', "password", "password02") ,array("EQUAL_CHECK"));
	$objErr->doFunc(array("�ѥ���ɤ�˺�줿�Ȥ��Υҥ�� ����", "reminder") ,array("SELECT_CHECK", "NUM_CHECK")); 
	$objErr->doFunc(array("�ѥ���ɤ�˺�줿�Ȥ��Υҥ�� ����", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK","SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�᡼��ޥ�����", "mailmaga_flg") ,array("SELECT_CHECK", "NUM_CHECK"));
	
	$objErr->doFunc(array("��ǯ����", "year", "month", "day"), array("CHECK_DATE"));
	$objErr->doFunc(array("�᡼��ޥ�����", 'mailmaga_flg'), array("SELECT_CHECK"));
	
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

//-----------------------------------------------------------------------------------------------------------------------------------
?>
