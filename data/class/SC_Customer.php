<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
			$this->getCustomerDataFromEmailPass( $pass, $email);
		}
	}
	
	function getCustomerDataFromEmailPass( $pass, $email, $mobile = false ) {
		$sql_mobile = $mobile ? ' OR email_mobile ILIKE ?' : '';
		$arrValues = array($email);
		if ($mobile) {
			$arrValues[] = $email;
		}
		// ����Ͽ���줿����Τ�
		$sql = "SELECT * FROM dtb_customer WHERE (email ILIKE ?" . $sql_mobile . ") AND del_flg = 0 AND status = 2";
		$result = $this->conn->getAll($sql, $arrValues);
		$data = $result[0];
		
		// �ѥ���ɤ���äƤ���иܵҾ����customer_data�˥��åȤ���true���֤�
		if ( sha1($pass . ":" . AUTH_MAGIC) == $data['password'] ){
			$this->customer_data = $data;
			$this->startSession();
			return true;
		}
		return false;
	}

	/**
	 * ����ü��ID�����פ�������¸�ߤ��뤫�ɤ���������å����롣
	 *
	 * @return boolean ������������¸�ߤ������ true������ʳ��ξ��
	 *                 �� false ���֤���
	 */
	function checkMobilePhoneId() {
		if (!isset($_SESSION['mobile']['phone_id']) || $_SESSION['mobile']['phone_id'] === false) {
			return false;
		}

		// ����ü��ID�����פ�������Ͽ���줿����򸡺����롣
		$sql = 'SELECT count(*) FROM dtb_customer WHERE mobile_phone_id = ? AND del_flg = 0 AND status = 2';
		$result = $this->conn->getOne($sql, array($_SESSION['mobile']['phone_id']));
		return $result > 0;
	}

	/**
	 * ����ü��ID����Ѥ��Ʋ���򸡺������ѥ���ɤξȹ��Ԥ���
	 * �ѥ���ɤ���äƤ�����ϸܵҾ����������롣
	 *
	 * @param string $pass �ѥ����
	 * @return boolean ������������¸�ߤ����ѥ���ɤ���äƤ������ true��
	 *                 ����ʳ��ξ��� false ���֤���
	 */
	function getCustomerDataFromMobilePhoneIdPass($pass) {
		if (!isset($_SESSION['mobile']['phone_id']) || $_SESSION['mobile']['phone_id'] === false) {
			return false;
		}

		// ����ü��ID�����פ�������Ͽ���줿����򸡺����롣
		$sql = 'SELECT * FROM dtb_customer WHERE mobile_phone_id = ? AND del_flg = 0 AND status = 2';
		@list($data) = $this->conn->getAll($sql, array($_SESSION['mobile']['phone_id']));

		// �ѥ���ɤ���äƤ�����ϡ��ܵҾ����customer_data�˳�Ǽ����true���֤���
		if (sha1($pass . ':' . AUTH_MAGIC) == @$data['password']) {
			$this->customer_data = $data;
			$this->startSession();
			return true;
		}
		return false;
	}

	/**
	 * ����ü��ID����Ͽ���롣
	 *
	 * @return void
	 */
	function updateMobilePhoneId() {
		if (!isset($_SESSION['mobile']['phone_id']) || $_SESSION['mobile']['phone_id'] === false) {
			return;
		}

		if ($this->customer_data['mobile_phone_id'] == $_SESSION['mobile']['phone_id']) {
			return;
		}

		$objQuery = new SC_Query;
		$sqlval = array('mobile_phone_id' => $_SESSION['mobile']['phone_id']);
		$where = 'customer_id = ? AND del_flg = 0 AND status = 2';
		$objQuery->update('dtb_customer', $sqlval, $where, array($this->customer_data['customer_id']));

		$this->customer_data['mobile_phone_id'] = $_SESSION['mobile']['phone_id'];
	}

	/**
	 * email ���� email_mobile �ط��ӤΥ᡼�륢�ɥ쥹�򥳥ԡ����롣
	 *
	 * @return void
	 */
	function updateEmailMobile() {
		// ���Ǥ� email_mobile ���ͤ����äƤ�����ϲ��⤷�ʤ���
		if ($this->customer_data['email_mobile'] != '') {
			return;
		}

		// email �����ӤΥ᡼�륢�ɥ쥹�ǤϤʤ����ϲ��⤷�ʤ���
		if (!gfIsMobileMailAddress($this->customer_data['email'])) {
			return;
		}

		// email ���� email_mobile �إ��ԡ����롣
		$objQuery = new SC_Query;
		$sqlval = array('email_mobile' => $this->customer_data['email']);
		$where = 'customer_id = ? AND del_flg = 0 AND status = 2';
		$objQuery->update('dtb_customer', $sqlval, $where, array($this->customer_data['customer_id']));

		$this->customer_data['email_mobile'] = $this->customer_data['email'];
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
	function isLoginSuccess($dont_check_email_mobile = false) {
		// ��������Υ᡼�륢�ɥ쥹��DB�Υ᡼�륢�ɥ쥹�����פ��Ƥ�����
		if(sfIsInt($_SESSION['customer']['customer_id'])) {
			$objQuery = new SC_Query();
			$email = $objQuery->get("dtb_customer", "email", "customer_id = ?", array($_SESSION['customer']['customer_id']));
			if($email == $_SESSION['customer']['email']) {
				// ��Х��륵���Ȥξ��Ϸ��ӤΥ᡼�륢�ɥ쥹����Ͽ����Ƥ��뤳�Ȥ�����å����롣
				// ������ $dont_check_email_mobile �� true �ξ��ϥ����å����ʤ���
				if (defined('MOBILE_SITE') && !$dont_check_email_mobile) {
					$email_mobile = $objQuery->get("dtb_customer", "email_mobile", "customer_id = ?", array($_SESSION['customer']['customer_id']));
					return isset($email_mobile);
				}
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

	// �ѥ�᡼����NULL���ɤ�����Ƚ��
	function hasValue($keyname) {
		return isset($_SESSION['customer'][$keyname]);
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
