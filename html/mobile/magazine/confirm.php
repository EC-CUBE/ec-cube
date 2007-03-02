<?php
/**
 * ���ޥ���ǧ
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = 'magazine/confirm.tpl';
		$this->tpl_title .= '���ޥ���ǧ';
	}
}

$objPage = new LC_Page();
$objConn = new SC_DbConn();
$objPage->arrForm = $_POST;

// ��Ͽ
if (isset($_REQUEST['btnRegist'])) {
	$objPage->arrErr = lfMailErrorCheck($objPage->arrForm, "regist");

	// ���顼���ʤ����
	if (count($objPage->arrErr) == 0) {
		// ��ǧ
		$objPage->arrForm['kind'] = '���ޥ���Ͽ';
		$objPage->arrForm['type'] = 'regist';
		$objPage->arrForm['mail'] = $objPage->arrForm['regist'];
	} else {
		$objPage->tpl_mainpage = 'magazine/index.tpl';
		$objPage->tpl_title = '���ޥ���Ͽ�����';
	}
// ���
} elseif (isset($_REQUEST['btnCancel'])) {
	$objPage->arrErr = lfMailErrorCheck($objPage->arrForm, "cancel");

	// ���顼���ʤ����
	if (count($objPage->arrErr) == 0) {
		// ��ǧ
		$objPage->arrForm['kind'] = '���ޥ����';
		$objPage->arrForm['type'] = 'cancel';
		$objPage->arrForm['mail'] = $objPage->arrForm['cancel'];
	} else {
		$objPage->tpl_mainpage = 'magazine/index.tpl';
		$objPage->tpl_title = '���ޥ���Ͽ�����';
	}
// ��λ
} elseif ($_REQUEST['mode'] == 'regist' or $_REQUEST['mode'] == 'cancel') {

	//����Ͽ
	if ($_REQUEST['mode'] == 'regist') {
		$uniqId = lfRegistData($_POST["email"]);
		$subject = sfMakesubject('���ޥ���Ͽ�Τ���ǧ');
	//�����
	} elseif ($_REQUEST['mode'] == 'cancel') {
		$uniqId = lfGetSecretKey($_POST["email"]);
		$subject = sfMakesubject('���ޥ�����Τ���ǧ');
	}
	$CONF = sf_getBasisData();
	$objPage->CONF = $CONF;
	$objPage->tpl_url = gfAddSessionId(SSL_URL . "magazine/" . $_REQUEST['mode'] . ".php?id=" . $uniqId);
	
	$objMailText = new SC_SiteView();
	$objMailText->assignobj($objPage);
	$toCustomerMail = $objMailText->fetch("mail_templates/mailmagazine_" . $_REQUEST['mode'] . ".tpl");
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
	$objMail->setTo($_POST["email"], $_POST["email"]);
	$objMail->sendMail();

	// ��λ�ڡ����˰�ư�����롣
	header("Location:" . gfAddSessionId("./complete.php"));
	exit;
} else {
	sfDispSiteError(CUSTOMER_ERROR);
}

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView = new SC_SiteView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------


//---- ���ϥ��顼�����å�
function lfMailErrorCheck($array, $dataName) {
	$objErr = new SC_CheckError($array);
	$objErr->doFunc(
				array('�᡼�륢�ɥ쥹', $dataName, MTEXT_LEN) ,
				array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", 
					"SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK", "MOBILE_EMAIL_CHECK"));

	// ���ϥ��顼���ʤ����
	if (count($objErr->arrErr) == 0) {
		// ���ޥ�����Ͽ̵ͭ
		$flg = lfIsRegistData($array[$dataName]);

		// ��Ͽ�λ�
		if ($dataName == 'regist' and $flg == true) {
			$objErr->arrErr[$dataName] = "������Ͽ����Ƥ��ޤ���<br>";
		// ����λ�
		} elseif ($dataName == 'cancel' and $flg == false) {
			$objErr->arrErr[$dataName] = "���ޥ���Ͽ������Ƥ��ޤ���<br>";
		}
	}

	return $objErr->arrErr;
}


//---- ���ޥ���Ͽ
function lfRegistData ($email) {
	global $objConn;

	$count = 1;
	while ($count != 0) {
		$uniqid = sfGetUniqRandomId("t");
		$count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer_mail WHERE secret_key = ?", array($uniqid));
	}
	
	$arrRegist["email"] = $email;			// �᡼�륢�ɥ쥹
	$arrRegist["mail_flag"] = 5;			// ��Ͽ����
	$arrRegist["secret_key"] = $uniqid;		// IDȯ��
	$arrRegist["create_date"] = "now()"; 	// ������
	$arrRegist["update_date"] = "now()"; 	// ������

	//-- ����Ͽ�¹�
	$objConn->query("BEGIN");

	$objQuery = new SC_Query();

	//--�����˥��ޥ���Ͽ���Ƥ��뤫��Ƚ��
	$sql = "SELECT count(*) FROM dtb_customer_mail WHERE email = ?";
	$mailResult = $objConn->getOne($sql, array($arrRegist["email"]));

	if ($mailResult == 1) {		
		$objQuery->update("dtb_customer_mail", $arrRegist, "email = '" .addslashes($arrRegist["email"]). "'");			
	} else {
		$objQuery->insert("dtb_customer_mail", $arrRegist);		
	}
	$objConn->query("COMMIT");

	return $uniqid;
}

// ��Ͽ����Ƥ��륭���μ���
function lfGetSecretKey ($email) {
	global $objConn;

	$sql = "SELECT secret_key FROM dtb_customer_mail WHERE email = ?";
	$uniqid = $objConn->getOne($sql, array($email));

	if ($uniqid == '') {
		$count = 1;
		while ($count != 0) {
			$uniqid = sfGetUniqRandomId("t");
			$count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer_mail WHERE secret_key = ?", array($uniqid));
		}

		$objQuery = new SC_Query();
		$objQuery->update("dtb_customer_mail", array('secret_key' => $uniqid), "email = '" .addslashes($email). "'");
	}

	return $uniqid;
}

// ������Ͽ����Ƥ��뤫�ɤ���
function lfIsRegistData ($email) {
	global $objConn;

	$sql = "SELECT email, mail_flag FROM dtb_customer_mail WHERE email = ?";
	$mailResult = $objConn->getRow($sql, array($email));

	// NULL����ɤȤߤʤ�
	if (count($mailResult) == 0 or ($mailResult[1] != null and $mailResult[1] != 2 )) {
		return false;
	} else {
		return true;
	}
}


?>
