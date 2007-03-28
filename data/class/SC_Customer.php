<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/*  [名称] SC_Customer
 *  [概要] 会員管理クラス
 */
class SC_Customer {
	
	var $conn;
	var $email;
	var $customer_data;		// 会員情報   
		
	function SC_Customer( $conn = '', $email = '', $pass = '' ) {
		// セッション開始
		/* startSessionから移動 2005/11/04 中川 */
		sfDomainSessionStart();
		
		// DB接続オブジェクト生成
		$DB_class_name = "SC_DbConn";
		if ( is_object($conn)){
			if ( is_a($conn, $DB_class_name)){
				// $connが$DB_class_nameのインスタンスである
				$this->conn = $conn;
			}
		} else {
			if (class_exists($DB_class_name)){
				//$DB_class_nameのインスタンスを作成する
				$this->conn = new SC_DbConn();			
			}
		}
			
		if ( is_object($this->conn) ) { 
			// 正常にDBに接続できる
			if ( $email ){
				// emailから顧客情報を取得する
				// $this->setCustomerDataFromEmail( $email );
			}
		} else {
			echo "DB接続オブジェクトの生成に失敗しています";
			exit;
		}
		
		if ( strlen($email) > 0 && strlen($pass) > 0 ){
			$this->getCustomerDataFromEmailPass( $email, $pass );
		}
	}
	
	function getCustomerDataFromEmailPass( $pass, $email, $mobile = false ) {
		$sql_mobile = $mobile ? ' OR email_mobile ILIKE ?' : '';
		$arrValues = array($email);
		if ($mobile) {
			$arrValues[] = $email;
		}
		// 本登録された会員のみ
		$sql = "SELECT * FROM dtb_customer WHERE (email ILIKE ?" . $sql_mobile . ") AND del_flg = 0 AND status = 2";
		$result = $this->conn->getAll($sql, $arrValues);
		$data = $result[0];
		
		// パスワードが合っていれば顧客情報をcustomer_dataにセットしてtrueを返す
		if ( sha1($pass . ":" . AUTH_MAGIC) == $data['password'] ){
			$this->customer_data = $data;
			$this->startSession();
			return true;
		}
		return false;
	}

	/**
	 * 携帯端末IDが一致する会員が存在するかどうかをチェックする。
	 *
	 * @return boolean 該当する会員が存在する場合は true、それ以外の場合
	 *                 は false を返す。
	 */
	function checkMobilePhoneId() {
		if (!isset($_SESSION['mobile']['phone_id']) || $_SESSION['mobile']['phone_id'] === false) {
			return false;
		}

		// 携帯端末IDが一致し、本登録された会員を検索する。
		$sql = 'SELECT count(*) FROM dtb_customer WHERE mobile_phone_id = ? AND del_flg = 0 AND status = 2';
		$result = $this->conn->getOne($sql, array($_SESSION['mobile']['phone_id']));
		return $result > 0;
	}

	/**
	 * 携帯端末IDを使用して会員を検索し、パスワードの照合を行う。
	 * パスワードが合っている場合は顧客情報を取得する。
	 *
	 * @param string $pass パスワード
	 * @return boolean 該当する会員が存在し、パスワードが合っている場合は true、
	 *                 それ以外の場合は false を返す。
	 */
	function getCustomerDataFromMobilePhoneIdPass($pass) {
		if (!isset($_SESSION['mobile']['phone_id']) || $_SESSION['mobile']['phone_id'] === false) {
			return false;
		}

		// 携帯端末IDが一致し、本登録された会員を検索する。
		$sql = 'SELECT * FROM dtb_customer WHERE mobile_phone_id = ? AND del_flg = 0 AND status = 2';
		@list($data) = $this->conn->getAll($sql, array($_SESSION['mobile']['phone_id']));

		// パスワードが合っている場合は、顧客情報をcustomer_dataに格納してtrueを返す。
		if (sha1($pass . ':' . AUTH_MAGIC) == @$data['password']) {
			$this->customer_data = $data;
			$this->startSession();
			return true;
		}
		return false;
	}

	/**
	 * 携帯端末IDを登録する。
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
	 * email から email_mobile へ携帯のメールアドレスをコピーする。
	 *
	 * @return void
	 */
	function updateEmailMobile() {
		// すでに email_mobile に値が入っている場合は何もしない。
		if ($this->customer_data['email_mobile'] != '') {
			return;
		}

		// email が携帯のメールアドレスではない場合は何もしない。
		if (!gfIsMobileMailAddress($this->customer_data['email'])) {
			return;
		}

		// email から email_mobile へコピーする。
		$objQuery = new SC_Query;
		$sqlval = array('email_mobile' => $this->customer_data['email']);
		$where = 'customer_id = ? AND del_flg = 0 AND status = 2';
		$objQuery->update('dtb_customer', $sqlval, $where, array($this->customer_data['customer_id']));

		$this->customer_data['email_mobile'] = $this->customer_data['email'];
	}
	
	// パスワードを確認せずにログイン
	function setLogin($email) {
		// 本登録された会員のみ
		$sql = "SELECT * FROM dtb_customer WHERE email ILIKE ? AND del_flg = 0 AND status = 2";
		$result = $this->conn->getAll($sql, array($email));
		$data = $result[0];
		$this->customer_data = $data;
		$this->startSession();
	}
	
	// セッション情報を最新の情報に更新する
	function updateSession() {
		$sql = "SELECT * FROM dtb_customer WHERE customer_id = ? AND del_flg = 0";
		$customer_id = $this->getValue('customer_id');
		$arrRet = $this->conn->getAll($sql, array($customer_id));
		$this->customer_data = $arrRet[0];
		$_SESSION['customer'] = $this->customer_data;
	}
		
	// ログイン情報をセッションに登録し、ログに書き込む
	function startSession() {
		sfDomainSessionStart();
		$_SESSION['customer'] = $this->customer_data;
		// セッション情報の保存
		gfPrintLog("access : user=".$this->customer_data['customer_id'] ."\t"."ip=". $_SERVER['REMOTE_HOST'], CUSTOMER_LOG_PATH );
	}

	// ログアウト　$_SESSION['customer']を解放し、ログに書き込む
	function EndSession() {
		// $_SESSION['customer']の解放
		unset($_SESSION['customer']);
		// ログに記録する
		gfPrintLog("logout : user=".$this->customer_data['customer_id'] ."\t"."ip=". $_SERVER['REMOTE_HOST'], CUSTOMER_LOG_PATH );
	}
	
	// ログインに成功しているか判定する。
	function isLoginSuccess($dont_check_email_mobile = false) {
		// ログイン時のメールアドレスとDBのメールアドレスが一致している場合
		if(sfIsInt($_SESSION['customer']['customer_id'])) {
			$objQuery = new SC_Query();
			$email = $objQuery->get("dtb_customer", "email", "customer_id = ?", array($_SESSION['customer']['customer_id']));
			if($email == $_SESSION['customer']['email']) {
				// モバイルサイトの場合は携帯のメールアドレスが登録されていることもチェックする。
				// ただし $dont_check_email_mobile が true の場合はチェックしない。
				if (defined('MOBILE_SITE') && !$dont_check_email_mobile) {
					$email_mobile = $objQuery->get("dtb_customer", "email_mobile", "customer_id = ?", array($_SESSION['customer']['customer_id']));
					return isset($email_mobile);
				}
				return true;
			}
		}
		return false;
	}
		
	// パラメータの取得
	function getValue($keyname) {
		return $_SESSION['customer'][$keyname];
	}
	
	// パラメータのセット
	function setValue($keyname, $val) {
		$_SESSION['customer'][$keyname] = $val;
	}

	// パラメータがNULLかどうかの判定
	function hasValue($keyname) {
		return isset($_SESSION['customer'][$keyname]);
	}
	
	// 誕生日月であるかどうかの判定
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
