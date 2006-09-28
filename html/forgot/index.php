<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	
	var $errmsg;
	var $arrReminder;
	var $temp_password;
		
	function LC_Page() {
		$this->tpl_mainpage = 'forgot/index.tpl';
		$this->tpl_mainno = '';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSess = new SC_Session();
$CONF = sf_getBasisData();					// Ź�޴��ܾ���
// ���å����������饹
$objCookie = new SC_Cookie(COOKIE_EXPIRE);

if ( $_POST['mode'] == 'mail_check' ){
	//�ᥢ�����ϻ�
	$_POST['email'] = strtolower($_POST['email']);
	$sql = "SELECT * FROM dtb_customer WHERE email ILIKE ? AND status = 2 AND del_flg = 0";
	$result = $conn->getAll($sql, array($_POST['email']) );
	
	if ( $result[0]['reminder'] ){		// �ܲ����Ͽ�Ѥߤξ��
		// ����email��¸�ߤ���		
		$_SESSION['forgot']['email'] = $_POST['email'];
		$_SESSION['forgot']['reminder'] = $result[0]['reminder'];
		// �ҥߥĤ��������ϲ���
		$objPage->Reminder = $arrReminder[$_SESSION['forgot']['reminder']];
		$objPage->tpl_mainpage = 'forgot/secret.tpl';
	} else {
		$sql = "SELECT customer_id FROM dtb_customer WHERE email ILIKE ? AND status = 1 AND del_flg = 0";	//����Ͽ��γ�ǧ
		$result = $conn->getAll($sql, array($_POST['email']) );
		if ($result) {
			$objPage->errmsg = "�����Ϥ�email���ɥ쥹�ϸ��߲���Ͽ��Ǥ���<br>��Ͽ�κݤˤ����ꤷ���᡼���URL�˥�����������<br>�ܲ����Ͽ�򤪴ꤤ���ޤ���";
		} else {		//����Ͽ���Ƥ��ʤ����
			$objPage->errmsg = "�����Ϥ�email���ɥ쥹����Ͽ����Ƥ��ޤ���";
		}
	}
	
} elseif( $_POST['mode'] == 'secret_check' ){
	//�ҥߥĤ��������ϻ�
	
	if ( $_SESSION['forgot']['email'] ) {
		// �ҥߥĤ������β������������������å�
		
		$sql = "SELECT * FROM dtb_customer WHERE email ILIKE ? AND del_flg = 0";
		$result = $conn->getAll($sql, array($_SESSION['forgot']['email']) );
		$data = $result[0];
		
		if ( $data['reminder_answer'] === $_POST['input_reminder'] ){
			// �ҥߥĤ�������������
						
			// �������ѥ���ɤ����ꤹ��
			$objPage->temp_password = gfMakePassword(8);
						
			if(FORGOT_MAIL == 1) {
				// �᡼����ѹ����Τ򤹤�
				lfSendMail($CONF, $_SESSION['forgot']['email'], $data['name01'], $objPage->temp_password);
			}
			
			// DB��񤭴�����
			$sql = "UPDATE dtb_customer SET password = ?, update_date = now() WHERE customer_id = ?";
			$conn->query( $sql, array( sha1($objPage->temp_password . ":" . AUTH_MAGIC) ,$data['customer_id']) );
			
			// ��λ���̤�ɽ��
			$objPage->tpl_mainpage = 'forgot/complete.tpl';
			
			// ���å�����ѿ��β���
			$_SESSION['forgot'] = array();
			unset($_SESSION['forgot']);
			
		} else {
			// �ҥߥĤ��������������ʤ�
			
			$objPage->Reminder = $arrReminder[$_SESSION['forgot']['reminder']];
			$objPage->errmsg = "�ѥ���ɤ�˺�줿�Ȥ��μ�����Ф������������������ޤ���";
			$objPage->tpl_mainpage = 'forgot/secret.tpl';

		}
	
		
	} else {
		// �����������������ޤ��ϡ����å�����ݻ����֤��ڤ�Ƥ���
		$objPage->errmsg = "email���ɥ쥹�������Ͽ���Ƥ���������<br />��������Ϥ�����֤��ФäƤ��ޤ��ȡ��ܥ�å�������ɽ��������ǽ��������ޤ���";
	}
}

// �ǥե��������
if($_POST['email'] != "") {
	// POST�ͤ�����
	$objPage->tpl_login_email = $_POST['email'];
} else {
	// ���å����ͤ�����
	$objPage->tpl_login_email = $objCookie->getCookie('login_email');
}

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);

// ---------------------------------------------------------------------------------------------------------------


function lfSendMail($CONF, $email, $customer_name, $temp_password){
	//���ѥ�����ѹ����Τ餻�᡼������
	
	$objPage = new LC_Page();
	$objPage->customer_name = $customer_name;
	$objPage->temp_password = $temp_password;
	$objMailText = new SC_SiteView();
	$objMailText->assignobj($objPage);
	
	$toCustomerMail = $objMailText->fetch("mail_templates/forgot_mail.tpl");
	$objMail = new GC_SendMail();
	
	$objMail->setItem(
						  ''								//������
						, "�ѥ���ɤ��ѹ�����ޤ���" ."��" .$CONF["shop_name"]. "��"		//�����֥�������
						, $toCustomerMail					//����ʸ
						, $CONF["email03"]					//�����������ɥ쥹
						, $CONF["shop_name"]				//����������̾��
						, $CONF["email03"]					//��reply_to
						, $CONF["email04"]					//��return_path
						, $CONF["email04"]					//  Errors_to

														);
	$objMail->setTo($email, $customer_name ." ��");
	$objMail->sendMail();	
	
}


?>

