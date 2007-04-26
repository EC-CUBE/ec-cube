<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/* セッション管理クラス */
class SC_SiteInfo {
	
	var $conn;
	var $data;
	
	function SC_SiteInfo($conn = ''){
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
		
		if ( is_object($this->conn)){
			$conn = $this->conn;
			$sql = "SELECT * FROM dtb_baseinfo";
			$data = $conn->getAll($sql);
			$this->data = $data[0];
		}
	}
	
}
?>