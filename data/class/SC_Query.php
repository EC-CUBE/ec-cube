<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

class SC_Query {
	var $option;
	var $where;
	var $conn;
	var $groupby;
	var $order;
	
	// コンストラクタ
	/*
		$err_disp:エラー表示を行うか
		$new：新規に接続を行うか
	 */
	function SC_Query($dsn = "", $err_disp = true, $new = false) {
		$this->conn = new SC_DBconn($dsn, $err_disp, $new);
		$this->where = "";
		return $this->conn;
	}
	
	// エラー判定
	function isError() {
		if(PEAR::isError($this->conn->conn)) {
			return true;
		}
		return false;
	}
	
	// COUNT文の実行
	function count($table, $where = "", $arrval = array()) {
		if(strlen($where) <= 0) {
			$sqlse = "SELECT COUNT(*) FROM $table";
		} else {
			$sqlse = "SELECT COUNT(*) FROM $table WHERE $where";
		}
		// カウント文の実行
		$ret = $this->conn->getOne($sqlse, $arrval);
		return $ret;
	}
	
	function select($col, $table, $where = "", $arrval = array()){
		$sqlse = $this->getsql($col, $table, $where);
		$ret = $this->conn->getAll($sqlse, $arrval);
		return $ret;
	}

	function getLastQuery($disp = true) {
		$sql = $this->conn->conn->last_query;
		if($disp) { 
			print($sql.";<br />\n");
		}
		return $sql;
	}

	function commit() {
		$this->conn->query("COMMIT");
	}
	
	function begin() {
		$this->conn->query("BEGIN");
	}
	
	function rollback() {
		$this->conn->query("ROLLBACK");
	}
	
	function exec($str, $arrval = array()) {
		$this->conn->query($str, $arrval);
	}

	function autoselect($col, $table, $arrwhere = array(), $arrcon = array()) {
		$strw = "";			
		$find = false;
		foreach ($arrwhere as $key => $val) {
			if(strlen($val) > 0) {
				if(strlen($strw) <= 0) {
					$strw .= $key ." LIKE ?";
				} else if(strlen($arrcon[$key]) > 0) {
					$strw .= " ". $arrcon[$key]. " " . $key ." LIKE ?";
				} else {
					$strw .= " AND " . $key ." LIKE ?";
				}
				
				$arrval[] = $val;
			}
		}
		
		if(strlen($strw) > 0) {
			$sqlse = "SELECT $col FROM $table WHERE $strw ".$this->option;
		} else {
			$sqlse = "SELECT $col FROM $table ".$this->option;
		}
		$ret = $this->conn->getAll($sqlse, $arrval);
		return $ret;
	}
	
	function getall($sql, $arrval = array()) {
		$ret = $this->conn->getAll($sql, $arrval);
		return $ret;
	}

	function getsql($col, $table, $where) {
		if($where != "") {
			// 引数の$whereを優先して実行する。
			$sqlse = "SELECT $col FROM $table WHERE $where " . $this->groupby . " " . $this->order . " " . $this->option;
		} else {
			if($this->where != "") {
					$sqlse = "SELECT $col FROM $table WHERE $this->where " . $this->groupby . " " . $this->order . " " . $this->option;
				} else {
					$sqlse = "SELECT $col FROM $table " . $this->groupby . " " . $this->order . " " . $this->option;
			}
		}
		return $sqlse;
	}
			
	function setoption($str) {
		$this->option = $str;
	}
	
	function setlimitoffset($limit, $offset = 0, $return = false) {
		if (is_numeric($limit) && is_numeric($offset)){
			
			$option.= " LIMIT " . $limit;
			$option.= " OFFSET " . $offset;
			
			if($return){
				return $option;
			}else{
				$this->option.= $option;
			}
		}
	}
	
	function setgroupby($str) {
		$this->groupby = "GROUP BY " . $str;
	}
	
	function andwhere($str) {
		if($this->where != "") {
			$this->where .= " AND " . $str;
		} else {
			$this->where = $str;
		}
	}
	
	function orwhere($str) {
		if($this->where != "") {
			$this->where .= " OR " . $str;
		} else {
			$this->where = $str;
		}
	}
		
	function setwhere($str) {
		$this->where = $str;
	}
	
	function setorder($str) {
		$this->order = "ORDER BY " . $str;
	}
	
		
	function setlimit($limit){
		if ( is_numeric($limit)){
			$this->option = " LIMIT " .$limit;
		}	
	}
	
	function setoffset($offset) {
		if ( is_numeric($offset)){
			$this->offset = " OFFSET " .$offset;
		}	
	}
	
	
	// INSERT文の生成・実行
	// $table	:テーブル名
	// $sqlval	:列名 => 値の格納されたハッシュ配列
	function insert($table, $sqlval) {
		$strcol = '';
		$strval = '';
		$find = false;
		
		if(count($sqlval) <= 0 ) return false;
		
		foreach ($sqlval as $key => $val) {
			$strcol .= $key . ',';
			if(eregi("^Now\(\)$", $val)) {
				$strval .= 'Now(),';
			} else {
				$strval .= '?,';
				if($val != ""){
					$arrval[] = $val;
				} else {
					$arrval[] = NULL;
				}
			}
			$find = true;
		}
		if(!$find) {
			return false;
		}
		// 文末の","を削除
		$strcol = ereg_replace(",$","",$strcol);
		// 文末の","を削除
		$strval = ereg_replace(",$","",$strval);
		$sqlin = "INSERT INTO $table(" . $strcol. ") VALUES (" . $strval . ")";
		
		// INSERT文の実行
		$ret = $this->conn->query($sqlin, $arrval);
		
		return $ret;		
	}
	
		// INSERT文の生成・実行
	// $table	:テーブル名
	// $sqlval	:列名 => 値の格納されたハッシュ配列
	function fast_insert($table, $sqlval) {
		$strcol = '';
		$strval = '';
		$find = false;
		
		foreach ($sqlval as $key => $val) {
				$strcol .= $key . ',';
				if($val != ""){
					$eval = pg_escape_string($val);
					$strval .= "'$eval',";
				} else {
					$strval .= "NULL,";
				}
				$find = true;
		}
		if(!$find) {
			return false;
		}
		// 文末の","を削除
		$strcol = ereg_replace(",$","",$strcol);
		// 文末の","を削除
		$strval = ereg_replace(",$","",$strval);
		$sqlin = "INSERT INTO $table(" . $strcol. ") VALUES (" . $strval . ")";
		
		// INSERT文の実行
		$ret = $this->conn->query($sqlin);
		
		return $ret;		
	}
	
	
	// UPDATE文の生成・実行
	// $table	:テーブル名
	// $sqlval	:列名 => 値の格納されたハッシュ配列
	// $where	:WHERE文字列
	function update($table, $sqlval, $where = "", $arradd = "", $addcol = "") {
		$strcol = '';
		$strval = '';
		$find = false;
		foreach ($sqlval as $key => $val) {
			if(eregi("^Now\(\)$", $val)) {
				$strcol .= $key . '= Now(),';
			} else {
				$strcol .= $key . '= ?,';
				if($val != ""){
					$arrval[] = $val;
				} else {
					$arrval[] = NULL;
				}
			}
			$find = true;
		}
		if(!$find) {
			return false;
		}
		
		if($addcol != "") {
			foreach($addcol as $key => $val) {
				$strcol .= "$key = $val,";
			}
		}
				
		// 文末の","を削除
		$strcol = ereg_replace(",$","",$strcol);
		// 文末の","を削除
		$strval = ereg_replace(",$","",$strval);
		
		if($where != "") {
			$sqlup = "UPDATE $table SET $strcol WHERE $where";
		} else {
			$sqlup = "UPDATE $table SET $strcol";
		}
		
		if(is_array($arradd)) {
			// プレースホルダー用に配列を追加
			foreach($arradd as $val) {
				$arrval[] = $val;
			}
		}
		
		// INSERT文の実行
		$ret = $this->conn->query($sqlup, $arrval);
		return $ret;		
	}

	// MAX文の実行
	function max($table, $col, $where = "", $arrval = array()) {
		if(strlen($where) <= 0) {
			$sqlse = "SELECT MAX($col) FROM $table";
		} else {
			$sqlse = "SELECT MAX($col) FROM $table WHERE $where";
		}
		// MAX文の実行
		$ret = $this->conn->getOne($sqlse, $arrval);
		return $ret;
	}
	
	// MIN文の実行
	function min($table, $col, $where = "", $arrval = array()) {
		if(strlen($where) <= 0) {
			$sqlse = "SELECT MIN($col) FROM $table";
		} else {
			$sqlse = "SELECT MIN($col) FROM $table WHERE $where";
		}
		// MIN文の実行
		$ret = $this->conn->getOne($sqlse, $arrval);
		return $ret;
	}
	
	// 特定のカラムの値を取得
	function get($table, $col, $where = "", $arrval = array()) {
		if(strlen($where) <= 0) {
			$sqlse = "SELECT $col FROM $table";
		} else {
			$sqlse = "SELECT $col FROM $table WHERE $where";
		}
		// SQL文の実行
		$ret = $this->conn->getOne($sqlse, $arrval);
		return $ret;
	}
	
	function getone($sql, $arrval = array()) {
		// SQL文の実行
		$ret = $this->conn->getOne($sql, $arrval);
		return $ret;
		
	}
		
	// 一行を取得
	function getrow($table, $col, $where = "", $arrval = array()) {
		if(strlen($where) <= 0) {
			$sqlse = "SELECT $col FROM $table";
		} else {
			$sqlse = "SELECT $col FROM $table WHERE $where";
		}
		// SQL文の実行
		$ret = $this->conn->getRow($sqlse, $arrval);
		
		return $ret;
	}
		
	// レコードの削除
	function delete($table, $where = "", $arrval = array()) {
		if(strlen($where) <= 0) {
			$sqlde = "DELETE FROM $table";
		} else {
			$sqlde = "DELETE FROM $table WHERE $where";
		}
		$ret = $this->conn->query($sqlde, $arrval);
		return $ret;
	}
	
	function nextval($table, $colname) {
		$sql = "";
		// postgresqlとmysqlとで処理を分ける
		if (DB_TYPE == "pgsql") {
			$seqtable = $table . "_" . $colname . "_seq";
			$sql = "SELECT NEXTVAL('$seqtable')";
		}else if (DB_TYPE == "mysql") {
			$sql = "SELECT last_insert_id();";
		}
		$ret = $this->conn->getOne($sql);
		
		return $ret;
	}
	
	function currval($table, $colname) {
		$sql = "";
		if (DB_TYPE == "pgsql") {
			$seqtable = $table . "_" . $colname . "_seq";
			$sql = "SELECT CURRVAL('$seqtable')";
		}else if (DB_TYPE == "mysql") {
			$sql = "SELECT last_insert_id();";
		}
		$ret = $this->conn->getOne($sql);
		
		return $ret;
	}	
	
	function setval($table, $colname, $data) {
		$sql = "";
		if (DB_TYPE == "pgsql") {
			$seqtable = $table . "_" . $colname . "_seq";
			$sql = "SELECT SETVAL('$seqtable', $data)";
			$ret = $this->conn->getOne($sql);
		}else if (DB_TYPE == "mysql") {
			$sql = "ALTER TABLE $table AUTO_INCREMENT=$data";
			$ret = $this->conn->query($sql);
		}
		
		return $ret;
	}		
	
	function query($n ,$arr = "", $ignore_err = false){
		$result = $this->conn->query($n, $arr, $ignore_err);
		return $result;
	}
	
    // auto_incrementを取得する
    function get_auto_increment($table_name){
        // ロックする
        $this->query("LOCK TABLES $table_name WRITE");
        
        // 次のIncrementを取得
        $arrRet = $this->getAll("SHOW TABLE STATUS LIKE ?", array($table_name));
        $auto_inc_no = $arrRet[0]["Auto_increment"];
        
        // 値をカウントアップしておく
        $this->conn->query("ALTER TABLE $table_name AUTO_INCREMENT=?" , $auto_inc_no + 1);
        
        // 解除する
        $this->query('UNLOCK TABLES');
        
        return $auto_inc_no;
    }
}

?>