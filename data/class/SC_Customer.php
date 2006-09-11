<?php
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
	
	function getCustomerDataFromEmailPass( $pass, $email ) {
		// 本登録された会員のみ
		$sql = "SELECT * FROM dtb_customer WHERE email ILIKE ? AND del_flg = 0 AND status = 2";
		$result = $this->conn->getAll($sql, array($email));
		$data = $result[0];
		
		// パスワードが合っていれば顧客情報をcustomer_dataにセットしてtrueを返す
		if ( crypt($pass,$data['password'] ) == $data['password'] ){
			$this->customer_data = $data;
			$this->startSession();
			return true;
		}
		return false;
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
	function isLoginSuccess() {
		// ログイン時のメールアドレスとDBのメールアドレスが一致している場合
		if(sfIsInt($_SESSION['customer']['customer_id'])) {
			$objQuery = new SC_Query();
			$email = $objQuery->get("dtb_customer", "email", "customer_id = ?", array($_SESSION['customer']['customer_id']));
			if($email == $_SESSION['customer']['email']) {
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