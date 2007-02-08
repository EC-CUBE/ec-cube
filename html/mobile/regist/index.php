<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

//---- �ڡ���ɽ�����饹
class LC_Page {
	
	var $arrSession;
	var $tpl_mainpage;
	var $arrPref;

	function LC_Page() {
		$this->tpl_css = '/css/layout/regist/index.css';	// �ᥤ��CSS�ѥ�
	}
}

$objConn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteInfo = $objView->objSiteInfo;
$objCustomer = new SC_Customer();
$CONF = sf_getBasisData();
$arrInfo = $objSiteInfo->data;

//--������Ͽ��λ�Τ���˥᡼�뤫����³�������
if ($_GET["mode"] == "regist") {
	
	//-- ���ϥ����å�
	$objPage->arrErr = lfErrorCheck($_GET);
	if ($objPage->arrErr) {
		$objPage->tpl_mainpage = 'regist/error.tpl';
		$objPage->tpl_css = "/css/layout/regist/error.css";
		$objPage->tpl_title = '���顼';

	} else {
		//$objPage->tpl_mainpage = 'regist/complete.tpl';
		//$objPage->tpl_title = ' �����Ͽ(��λ�ڡ���)';
		$registSecretKey = lfRegistData($_GET);			//�ܲ����Ͽ�ʥե饰�ѹ���
		lfSendRegistMail($registSecretKey);				//�ܲ����Ͽ��λ�᡼������

		// ������Ѥߤξ��֤ˤ��롣
		$objQuery = new SC_Query();
		$email = $objQuery->get("dtb_customer", "email", "secret_key = ?", array($registSecretKey));
		$objCustomer->setLogin($email);
		header("Location: " . gfAddSessionId("./complete.php"));
		exit;
	}

//--������ʳ��Υ���������̵���Ȥ���
} else {
	$objPage->arrErr["id"] = "̵���ʥ��������Ǥ���";
	$objPage->tpl_mainpage = 'regist/error.tpl';
	$objPage->tpl_css = "/css/layout/regist/error.css";
	$objPage->tpl_title = '���顼';

}

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//---- ��Ͽ
function lfRegistData($array) {
	global $objConn;
	global $arrInfo;
	
	do {
		$secret = sfGetUniqRandomId("r");
	} while( ($result = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer WHERE secret_key = ?", array($secret)) ) != 0);

	$sql = "SELECT email FROM dtb_customer WHERE secret_key = ? AND status = 1";
	$email = $objConn->getOne($sql, array($array["id"]));

	$objConn->query("BEGIN");
	$arrRegist["secret_key"] = $secret;	//������ϿIDȯ��
	$arrRegist["status"] = 2;
	$arrRegist["update_date"] = "NOW()";
	
	$objQuery = new SC_Query();
	$where = "secret_key = ? AND status = 1";
	
	$arrRet = $objQuery->select("point", "dtb_customer", $where, array($array["id"]));
	// �����Ͽ���βû��ݥ����(�����������Ͽ�ξ��ϡ��ݥ���Ȳû���
	$arrRegist['point'] = $arrRet[0]['point'] + addslashes($arrInfo['welcome_point']);
	
	$objQuery->update("dtb_customer", $arrRegist, $where, array($array["id"]));

	/* �������μ�ư�����Ͽ�ϹԤ�ʤ�����DEL
	// ��������Ͽ�ξ�硢���β�ι������������Ȥߤʤ���
	// ���������ɤ߹���
	$where1 = "secret_key = ? AND status = 2";
	$customer = $objQuery->select("*", "dtb_customer", $where1, array($secret));
	// ������������ɤ߹���
	$order_temp_id = $objQuery->get("dtb_order_temp", "order_temp_id");
	// ��������ι���
	if ($order_temp_id != null) {
		$arrCustomer['customer_id'] = $customer[0]['customer_id'];
		$where3 = "order_temp_id = ?";
		$objQuery->update("dtb_order_temp", $arrCustomer, $where3, array($order_temp_id));
		$objQuery->update("dtb_order", $arrCustomer, $where3, array($order_temp_id));
	}
	*/

	$sql = "SELECT mail_flag FROM dtb_customer_mail WHERE email = ?";
	$result = $objConn->getOne($sql, array($email));
	
	switch($result) {
	// ��HTML
	case '4':
		$arrRegistMail["mail_flag"] = 1;
		break;
	// ��TEXT
	case '5':
		$arrRegistMail["mail_flag"] = 2;
		break;
	// ���ʤ�
	case '6':
		$arrRegistMail["mail_flag"] = 3;
		break;
	default:
		$arrRegistMail["mail_flag"] = $result;
		break;
	}

	$objConn->autoExecute("dtb_customer_mail", $arrRegistMail, "email = '" .addslashes($email). "'");
	$objConn->query("COMMIT");
		
	return $secret;		// ����ϿID���֤�
}

//---- ���ϥ��顼�����å�
function lfErrorCheck($array) {

	global $objConn;
	$objErr = new SC_CheckError($array);

	$objErr->doFunc(array("����ϿID", 'id'), array("EXIST_CHECK"));
	if (! EregI("^[[:alnum:]]+$",$array["id"] )) {
		$objErr->arrErr["id"] = "̵����URL�Ǥ����᡼��˵��ܤ���Ƥ����ܲ����Ͽ��URL����٤���ǧ����������";
	}
	if (! $objErr->arrErr["id"]) {

		$sql = "SELECT customer_id FROM dtb_customer WHERE secret_key = ? AND status = 1 AND del_flg = 0";
		$result = $objConn->getOne($sql, array($array["id"]));

		if (! is_numeric($result)) {
			$objErr->arrErr["id"] .= "�� ���˲����Ͽ����λ���Ƥ��뤫��̵����URL�Ǥ���<br>";
			return $objErr->arrErr;

		}
	}

	return $objErr->arrErr;
}

//---- �������Ͽ��λ�᡼������
function lfSendRegistMail($registSecretKey) {
	global $objConn;
	global $CONF;

	//-- ��̾�����
	$sql = "SELECT email, name01, name02 FROM dtb_customer WHERE secret_key = ?";
	$result = $objConn->getAll($sql, array($registSecretKey));
	$data = $result[0];
	
	//--���᡼������
	$objMailText = new SC_SiteView();
	$objMailText->assign("CONF", $CONF);
	$objMailText->assign("name01", $data["name01"]);
	$objMailText->assign("name02", $data["name02"]);
	$toCustomerMail = $objMailText->fetch("mail_templates/customer_regist_mail.tpl");
	$subject = sfMakeSubject('�ܲ����Ͽ����λ���ޤ�����');
	$objMail = new GC_SendMail();

	$objMail->setItem(
						  ''								//������
						, $subject//"��" .$CONF["shop_name"]. "��".ENTRY_CUSTOMER_REGIST_SUBJECT 		//�����֥�������
						, $toCustomerMail					//����ʸ
						, $CONF["email03"]					//�����������ɥ쥹
						, $CONF["shop_name"]				//����������̾��
						, $CONF["email03"]					//��reply_to
						, $CONF["email04"]					//��return_path
						, $CONF["email04"]					//  Errors_to
					);
	// ���������
	$name = $data["name01"] . $data["name02"] ." ��";
	$objMail->setTo($data["email"], $name);
	$objMail->sendMail();
}

?>
