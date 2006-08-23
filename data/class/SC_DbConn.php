<?php
$current_dir = realpath(dirname(__FILE__));
require_once($current_dir . "/../module/DB.php");

$objDbConn = "";

class SC_DbConn{

	var $conn;
	var $result; 
	var $includePath;
	var $error_mail_to;
	var $error_mail_title;
	var $dsn;
	
	// コンストラクタ
	function SC_DbConn($dsn = ""){
		global $objDbConn;
		// 既に接続されている場合には接続しない
		if(!isset($objDbConn->connection)) {
			if($dsn != "") {
				$objDbConn = DB::connect($dsn);
				$this->dsn = $dsn;
			} else {
				$objDbConn = DB::connect(DEFAULT_DSN);
				$this->dsn = DEFAULT_DSN;
			}
		}
		$this->conn = $objDbConn;
		$this->error_mail_to = DB_ERROR_MAIL_TO;
		$this->error_mail_title = DB_ERROR_MAIL_SUBJECT;
	}
	
	// クエリの実行
	function query($n ,$arr = "", $ignore_err = false){
		if ( $arr ) {
			$result = $this->conn->query($n, $arr);	
		} else {
			$result = $this->conn->query($n);
		}
	
		if ($this->conn->isError($result) && !$ignore_err){
			$this->send_err_mail ($result, $n);
		}
		
		$this->result = $result;
		return $this->result;
	}

	// 一件のみ取得
	function getOne($n, $arr = ""){
		
		if ( $arr ) {
			$result = $this->conn->getOne($n, $arr);
		} else {
			$result = $this->conn->getOne($n);
		}		
		if ($this->conn->isError($result)){
			$this->send_err_mail ($result ,$n);
		}
		$this->result = $result;
		return $this->result;
	}
	
	function getRow($n, $arr = ""){
		
		if ( $arr ) {
			$result = $this->conn->getRow($n, $arr);
		} else {
			$result = $this->conn->getRow($n);
		}		
		if ($this->conn->isError($result)){
			$this->send_err_mail ($result ,$n);
		}
		$this->result = $result;
		return $this->result;
	}

	// SELECT文の実行結果を全て取得
	function getAll($n, $arr = ""){
		if(PEAR::isError($this->conn)) {
			sfErrorHeader("DBへの接続に失敗しました。:" . $this->dsn);
			return 0;
		}		
		
		if ( $arr ){
			$result = $this->conn->getAll($n, $arr, DB_FETCHMODE_ASSOC);
		} else {
			$result = $this->conn->getAll($n, DB_FETCHMODE_ASSOC);
		}
		
		if ($this->conn->isError($result)){
			$this->send_err_mail ($result, $n);
		}
		$this->result = $result;
		
		return $this->result;
	}	
	
	function autoExecute($table_name, $fields_values, $sql_where = null){
	
		if ( $sql_where ) {
			$result = $this->conn->autoExecute( $table_name, $fields_values, DB_AUTOQUERY_UPDATE, $sql_where);
		} else {
			$result = $this->conn->autoExecute( $table_name, $fields_values, DB_AUTOQUERY_INSERT);
		}
		
		if ($this->conn->isError($result)){
			$this->send_err_mail ($result, $n);
		}
		$this->result = $result;
		return $this->result;
	}
	
	
	function prepare($n){
		global $sql;
		$sql = $n;		
		$result = $this->conn->prepare($n);
		$this->result = $result;
		return $this->result;
	}
	
	function execute($n, $obj){
		global $sql;
		$sql = $n;
		$result = $this->conn->execute($n, $obj);
		$this->result = $result;
		return $this->result;
	}	
	
	function reset(){
		$this->conn->disconnect();
	}

	function send_err_mail( $result, $sql ){
		
		if ($_SERVER['HTTPS'] == "on") {
			$url = "https://";
		} else {
			$url = "http://";
		}
		$url.= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		$errmsg = $url."\n\n";
		$errmsg.= $sql . "\n";
		$errmsg.= $result->message . "\n\n";
		$errmsg.= $result->userinfo . "\n\n";
		
		ob_start();
		print_R($result);	
		$errmsg .= ob_get_contents();
		ob_end_clean();	
		
		mb_send_mail ( $this->error_mail_to, $this->error_mail_title, "${errmsg}\n".date("Y/m/d H:i:s") );
		
		exit(sfprintr($errmsg));
	}
}

?>