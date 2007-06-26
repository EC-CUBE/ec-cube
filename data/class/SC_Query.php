<?php
/**
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/**
 *  SC_Query���饹
 *
 *  @author     LOCKON CO.,LTD.
 *  @access     public
 */
class SC_Query {
   /**#@+
    * @access private
    */
    
   /**
    * SC_DBConn���֥�������
    * @var SC_DBConn
    */
    var $conn;
    
   /**
    * LIMIT,OFFSET ��
    * @var string
    */
    var $option;
    
   /**
    * WHERE ��
    * @var string
    */
    var $where;
    
   /**
    * GROUP BY ��
    * @var string
    */
    var $groupby;
    
   /**
    * ORDER BY ��
    * @var string
    */
    var $order;
    
    /**#@-*/
    
    /**
     *  SC_Query���饹�Υ��󥹥ȥ饯��
     *
     *  @access public
     *  @param  string  $dsn      DSN����
     *  @param  boolean $err_disp ���顼ɽ����Ԥ����ɤ���
     *  @param  boolean $new      ������DB��³��Ԥ����ɤ���
     */
    function SC_Query($dsn = "", $err_disp = true, $new = false) {
        $this->conn = new SC_DBconn($dsn, $err_disp, $new);
        $this->where   = "";
        $this->option  = "";
        $this->groupby = "";
        return $this->conn; //?
    }
    
    /**
     *  DB���顼��Ƚ��
     *
     *  @access public
     *  @return boolean ��������true ���Ի���false
     */
    function isError() {
        if(PEAR::isError($this->conn->conn)) {
            return true;
        }
        return false;
    }
    
    /**
     *  ���ץ����ν����
     *
     *  @access public
     *  @return void
     */
    function clear(){
        $arrProperty = array_keys((get_object_vars($this)));
        foreach ( $arrProperty as $property ) {
            if ($property != 'conn') {
                $this->$property = '';
            }
        }
    }
    
    /**
     *  COUNTʸ�μ¹�
     *
     *  @access public
     *  @param  string  $table  �ơ��֥�̾
     *  @param  string  $where  WHERE��
     *  @param  array   $arrval �ץ졼���ۥ��������
     *  @return string  �쥳���ɷ��
     */
    function count($table, $where = "", $arrval = array()) {
        if(strlen($where) <= 0) {
            $sqlse = "SELECT COUNT(*) FROM $table";
        } else {
            $sqlse = "SELECT COUNT(*) FROM $table WHERE $where";
        }
        // �������ʸ�μ¹�
        $ret = $this->conn->getOne($sqlse, $arrval);
        return $ret;
    }
    
    /**
     *  SELECTʸ�μ¹�
     *
     *  @access public
     *  @param  string  $col    �����̾
     *  @param  string  $table  �ơ��֥�̾
     *  @param  string  $where  WHERE��
     *  @param  array   $arrval �ץ졼���ۥ��������
     *  @return array   SELECTʸ�μ¹Է��
     */
    function select($col, $table, $where = "", $arrval = array()){
        $sqlse = $this->getsql($col, $table, $where);
        $ret = $this->conn->getAll($sqlse, $arrval);
        return $ret;
    }
    
    /**
     *  �Ǹ�˼¹Ԥ���SQLʸ���������
     *
     *  @access public
     *  @param  boolean $disp SQLʸ��print���뤫�ɤ���
     *  @return string  $disp==false�ξ�硧�Ǹ�˼¹Ԥ���SQLʸ��$disp==true�ξ�硧�ʤ�
     */
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

    /**
     *  SELECTʸ���ۤ���
     *
     *  @access public
     *  @param  string  $col    �����̾
     *  @param  string  $table  �ơ��֥�̾
     *  @param  string  $where  WHERE��
     *  @return string  SQLʸ
     */
    function getsql($col, $table, $where="") {
        if($where != "") {
            // ������$where��ͥ�褷�Ƽ¹Ԥ��롣
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
    
    /**
     *  WHERE,GROUPBY,ORDERBY�ʳ��Υ��ץ���ʥ�ʶ�򥻥åȤ���
     *
     *  @access public
     *  @param  string  $str ���ץ����˻��Ѥ���ʸ����
     */
    function setoption($str) {
        $this->option = $str;
    }
    
    /**
     *  LIMIT�硢OFFSET��򥻥åȤ���
     *
     *  @access public
     *  @param  mixed   $limit  LIMIT�η��
     *  @param  mixed   $offset OFFSET�η��
     *  @param  string  $return ��������LIMIT,OFFSET���return���뤫�ɤ���
     *  @return string  ��������LIMIT,OFFSET��
     */
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

    /**
     *  GROUP BY ��򥻥åȤ���
     *
     *  @access public
     *  @param  string  $str �����̾
     */
    function setgroupby($str) {
        $this->groupby = "GROUP BY " . $str;
    }
    
    /**
     *  WHERE ��򥻥åȤ���(AND)
     *
     *  @access  public
     *  @param   string  $str WHERE ��
     *  @example $objQuery->andWhere('product_id = ?');
     */
    function andwhere($str) {
        if($this->where != "") {
            $this->where .= " AND " . $str;
        } else {
            $this->where = $str;
        }
    }
    
    /**
     *  WHERE ��򥻥åȤ���(OR)
     *
     *  @access  public
     *  @param   string  $str WHERE ��
     *  @example $objQuery->orWhere('product_id = ?');
     */
    function orwhere($str) {
        if($this->where != "") {
            $this->where .= " OR " . $str;
        } else {
            $this->where = $str;
        }
    }
    
    /**
     *  WHERE ��򥻥åȤ���
     *
     *  @access  public
     *  @param   string  $str WHERE ��
     *  @example $objQuery->setWhere('product_id = ?');
     */
    function setwhere($str) {
        $this->where = $str;
    }
    
    /**
     *  ORDER BY ��򥻥åȤ���
     *
     *  @access  public
     *  @param   string  $str �����̾
     *  @example $objQuery->setorder("rank DESC");
     */
    function setorder($str) {
        $this->order = "ORDER BY " . $str;
    }
    
    /**
     *  LIMIT ��򥻥åȤ���
     *
     *  @access  public
     *  @param   mixed  $limit LIMIT�η��
     *  @example $objQuery->setlimit(50);
     */
    function setlimit($limit){
        if ( is_numeric($limit)){
            $this->option = " LIMIT " .$limit;
        }   
    }
    
    /**
     *  OFFSET ��򥻥åȤ���
     *
     *  @access  public
     *  @param   mixed  $offset OFFSET�η��
     *  @example $objQuery->setOffset(30);
     */
    function setoffset($offset) {
        if ( is_numeric($offset)){
            $this->offset = " OFFSET " .$offset;
        }   
    }
    
    /**
     *  INSERTʸ��¹Ԥ���
     *
     *  @access  public
     *  @param   string  $table  �ơ��֥�̾
     *  @param   array   $sqlval (�����̾ => ��)��Ϣ������
     *  
     *  @return  mixed   $result DB_Error���֥�������(���Ի�)�ޤ���DB_OK(������)�ޤ���false(����ब���Ĥ���ʤ�)
     */
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
		// ʸ����","����
		$strcol = ereg_replace(",$","",$strcol);
		// ʸ����","����
		$strval = ereg_replace(",$","",$strval);
		$sqlin = "INSERT INTO $table(" . $strcol. ") VALUES (" . $strval . ")";
		
		// INSERTʸ�μ¹�
		$ret = $this->conn->query($sqlin, $arrval);
		
		return $ret;		
	}
	
    /**
     *  INSERTʸ��¹Ԥ���
     *
     *  @access  public
     *  @param   string  $table  �ơ��֥�̾
     *  @param   array   $sqlval (�����̾ => ��)��Ϣ������
     *  
     *  @return  mixed   $result DB_Error���֥�������(���Ի�)�ޤ���DB_OK(������)�ޤ���false(����ब���Ĥ���ʤ�)
     */
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
		// ʸ����","����
		$strcol = ereg_replace(",$","",$strcol);
		// ʸ����","����
		$strval = ereg_replace(",$","",$strval);
		$sqlin = "INSERT INTO $table(" . $strcol. ") VALUES (" . $strval . ")";
		
		// INSERTʸ�μ¹�
		$ret = $this->conn->query($sqlin);
		
		return $ret;
	}
	
	
	// UPDATEʸ���������¹�
	// $table	:�ơ��֥�̾
	// $sqlval	:��̾ => �ͤγ�Ǽ���줿�ϥå�������
	// $where	:WHEREʸ����
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
				
		// ʸ����","����
		$strcol = ereg_replace(",$","",$strcol);
		// ʸ����","����
		$strval = ereg_replace(",$","",$strval);
		
		if($where != "") {
			$sqlup = "UPDATE $table SET $strcol WHERE $where";
		} else {
			$sqlup = "UPDATE $table SET $strcol";
		}
		
		if(is_array($arradd)) {
			// �ץ졼���ۥ�����Ѥ�������ɲ�
			foreach($arradd as $val) {
				$arrval[] = $val;
			}
		}
		
		// INSERTʸ�μ¹�
		$ret = $this->conn->query($sqlup, $arrval);
		return $ret;		
	}

	// MAXʸ�μ¹�
	function max($table, $col, $where = "", $arrval = array()) {
		if(strlen($where) <= 0) {
			$sqlse = "SELECT MAX($col) FROM $table";
		} else {
			$sqlse = "SELECT MAX($col) FROM $table WHERE $where";
		}
		// MAXʸ�μ¹�
		$ret = $this->conn->getOne($sqlse, $arrval);
		return $ret;
	}
	
	// MINʸ�μ¹�
	function min($table, $col, $where = "", $arrval = array()) {
		if(strlen($where) <= 0) {
			$sqlse = "SELECT MIN($col) FROM $table";
		} else {
			$sqlse = "SELECT MIN($col) FROM $table WHERE $where";
		}
		// MINʸ�μ¹�
		$ret = $this->conn->getOne($sqlse, $arrval);
		return $ret;
	}
	
	// ����Υ������ͤ����
	function get($table, $col, $where = "", $arrval = array()) {
		if(strlen($where) <= 0) {
			$sqlse = "SELECT $col FROM $table";
		} else {
			$sqlse = "SELECT $col FROM $table WHERE $where";
		}
		// SQLʸ�μ¹�
		$ret = $this->conn->getOne($sqlse, $arrval);
		return $ret;
	}
	
	function getone($sql, $arrval = array()) {
		// SQLʸ�μ¹�
		$ret = $this->conn->getOne($sql, $arrval);
		return $ret;
		
	}
		
	// ��Ԥ����
	function getrow($table, $col, $where = "", $arrval = array()) {
		if(strlen($where) <= 0) {
			$sqlse = "SELECT $col FROM $table";
		} else {
			$sqlse = "SELECT $col FROM $table WHERE $where";
		}
		// SQLʸ�μ¹�
		$ret = $this->conn->getRow($sqlse, $arrval);
		
		return $ret;
	}
		
	// �쥳���ɤκ��
	function delete($table, $where = "", $arrval = array()) {
		if(strlen($where) <= 0) {
			$sqlde = "DELETE FROM $table";
		} else {
			$sqlde = "DELETE FROM $table WHERE $where";
		}
		$ret = $this->conn->query($sqlde, $arrval);
		return $ret;
	}
	
    //���ꤷ�������ΰ��ֺǸ�˥쥳���ɤ�����
	function nextval($table, $colname) {
		$sql = "";
		// postgresql��mysql�Ȥǽ�����ʬ����
		if (DB_TYPE == "pgsql") {
			$seqtable = $table . "_" . $colname . "_seq";
			$sql = "SELECT NEXTVAL('$seqtable')";
            $ret = $this->conn->getOne($sql);
		}else if (DB_TYPE == "mysql") {
            $sql = "SELECT last_insert_id();";
		    $ret = $this->conn->getOne($sql);
        }
		
		
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
	
    // auto_increment���������
    function get_auto_increment($table_name){
        // ��å�����
        $this->query("LOCK TABLES $table_name WRITE");
        
        // ����Increment�����
        $arrRet = $this->getAll("SHOW TABLE STATUS LIKE ?", array($table_name));
        $auto_inc_no = $arrRet[0]["Auto_increment"];
        
        // �ͤ򥫥���ȥ��åפ��Ƥ���
        $this->conn->query("ALTER TABLE $table_name AUTO_INCREMENT=?" , $auto_inc_no + 1);
        
        // �������
        $this->query('UNLOCK TABLES');
        
        return $auto_inc_no;
    }
}
?>