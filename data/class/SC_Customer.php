<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/*  [̾��] SC_Customer
 *  [����] ����������饹
 */
class SC_Customer {
	
	var $conn;
	var $email;
	var $customer_data;		// �������   
		
	function SC_Customer( $conn = '', $email = '', $pass = '' ) {
		// ���å���󳫻�
		/* startSession�����ư 2005/11/04 ���� */
		sfDomainSessionStart();
		
		// DB��³���֥�����������
		$DB_class_name = "SC_DbConn";
		if ( is_object($conn)){
			if ( is_a($conn, $DB_class_name)){
				// $conn��$DB_class_name�Υ��󥹥��󥹤Ǥ���
				$this->conn = $conn;
			}
		} else {
			if (class_exists($DB_class_name)){
				//$DB_class_name�Υ��󥹥��󥹤��������
				$this->conn = new SC_DbConn();			
			}
		}
			
		if ( is_object($this->conn) ) { 
			// �����DB����³�Ǥ���
			if ( $email ){
				// email����ܵҾ�����������
				// $this->setCustomerDataFromEmail( $email );
			}
		} else {
			echo "DB��³���֥������Ȥ������˼��Ԥ��Ƥ��ޤ�";
			exit;
		}
		
		if ( strlen($email) > 0 && strlen($pass) > 0 ){
			$this->getCustomerDataFromEmailPass( $email, $pass );
		}
	}
	
	function getCustomerDataFromEmailPass( $pass, $email ) {
		// ����Ͽ���줿����Τ�
		$sql = "SELECT * FROM dtb_customer WHERE email ILIKE ? AND del_flg = 0 AND status = 2";
		$result = $this->conn->getAll($sql, array($email));
		$data = $result[0];
		
		// �ѥ���ɤ���äƤ���иܵҾ����customer_data�˥��åȤ���true���֤�
		if ( sha1($pass . ":" . AUTH_MAGIC) == $data['password'] ){
			$this->customer_data = $data;
			$this->startSession();
			return true;
		}
		return false;
	}
	
	// �ѥ���ɤ��ǧ�����˥�����
	function setLogin($email) {
		// ����Ͽ���줿����Τ�
		$sql = "SELECT * FROM dtb_customer WHERE email ILIKE ? AND del_flg = 0 AND status = 2";
		$result = $this->conn->getAll($sql, array($email));
		$data = $result[0];
		$this->customer_data = $data;
		$this->startSession();
	}
	
	// ���å��������ǿ��ξ���˹�������
	function updateSession() {
		$sql = "SELECT * FROM dtb_customer WHERE customer_id = ? AND del_flg = 0";
		$customer_id = $this->getValue('customer_id');
		$arrRet = $this->conn->getAll($sql, array($customer_id));
		$this->customer_data = $arrRet[0];
		$_SESSION['customer'] = $this->customer_data;
	}
		
	// ���������򥻥å�������Ͽ�������˽񤭹���
	function startSession() {
		sfDomainSessionStart();
		$_SESSION['customer'] = $this->customer_data;
		// ���å����������¸
		gfPrintLog("access : user=".$this->customer_data['customer_id'] ."\t"."ip=". $_SERVER['REMOTE_HOST'], CUSTOMER_LOG_PATH );
	}

	// �������ȡ�$_SESSION['customer']������������˽񤭹���
	function EndSession() {
		// $_SESSION['customer']�β���
		unset($_SESSION['customer']);
		// ���˵�Ͽ����
		gfPrintLog("logout : user=".$this->customer_data['customer_id'] ."\t"."ip=". $_SERVER['REMOTE_HOST'], CUSTOMER_LOG_PATH );
	}
	
	// ��������������Ƥ��뤫Ƚ�ꤹ�롣
	function isLoginSuccess() {
		// ��������Υ᡼�륢�ɥ쥹��DB�Υ᡼�륢�ɥ쥹�����פ��Ƥ�����
		if(sfIsInt($_SESSION['customer']['customer_id'])) {
			$objQuery = new SC_Query();
			$email = $objQuery->get("dtb_customer", "email", "customer_id = ?", array($_SESSION['customer']['customer_id']));
			if($email == $_SESSION['customer']['email']) {
				return true;
			}
		}
		return false;
	}
		
	// �ѥ�᡼���μ���
	function getValue($keyname) {
		return $_SESSION['customer'][$keyname];
	}
	
	// �ѥ�᡼���Υ��å�
	function setValue($keyname, $val) {
		$_SESSION['customer'][$keyname] = $val;
	}
	
	// ��������Ǥ��뤫�ɤ�����Ƚ��
	function isBirthMonth() {
		$arrRet = split("[- :/]", $_SESSION['customer']['birth']);
		$birth_month = intval($arrRet[1]);
		$now_month = intval(date("m"));
		
		if($birth_month == $now_month) {
			return true;
		}
		return false;
	}
}
?>