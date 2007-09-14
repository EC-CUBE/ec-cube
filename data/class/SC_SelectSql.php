<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/* ---- SQLʸ���륯�饹 ---- */
class SC_SelectSql {
	
	var $sql;
	
	var $select;	
	var $where;
	var $order;
	var $group;
	var $limit;
	var $offset;
	var $arrSql;
	var $arrVal;

	//--�����󥹥ȥ饯����
	function SC_SelectSql($array = "") {
		if (is_array($array)) {
			$this->arrSql = $array;
		}
	}

	//-- SQLʸ����
	function getSql( $mode = "" ){
		$this->sql = $this->select ." ". $this->where ." ". $this->group ." ";
						
		// $mode == 1 �� limit & offset̵��						
		if ($mode == 2) {
			$this->sql .= $this->order;
		}elseif ( $mode != 1 ){
			$this->sql .= $this->order . " " .$this->limit ." ". $this->offset;	
		}

		return $this->sql;	
	}

		// ������
	function addSearchStr($val) {
		$return = sfManualEscape($val);
		$return = "%" .$return. "%";
		return $return;
	}
	
	//-- �ϰϸ����ʡ�������������ޤǡ�
	function selectRange($from, $to, $column) {

		// ����ñ�̤Τ߸���($from = $to)
		if(  $from == $to ) {
			$this->setWhere( $column ." = ?" );
			$return = array($from);
		//�����$to�ޤǸ���
		} elseif(  strlen($from) == 0 && strlen($to) > 0 ) {
			$this->setWhere( $column ." <= ? "); 
			$return = array($to);
		//�����$from�ʾ�򸡺�
		} elseif(  strlen($from) > 0 && strlen($to) == 0 ) {
			$this->setWhere( $column ." >= ? ");
			$return = array($from);
		//��$from���$to�θ���
		} else {
			$this->setWhere( $column ."	BETWEEN ? AND ?" ); 
			$return = array($from, $to);
		}
		return $return;
	}

	//--�����ָ����ʡ�ǯ��������������ǯ��������ޤǡ�
	function selectTermRange($from_year, $from_month, $from_day, $to_year, $to_month, $to_day, $column) {

		// FROM
		$date1 = $from_year . "/" . $from_month . "/" . $from_day;
		
		// TO
		$date2 = mktime (0, 0, 0, $to_month, $to_day,  $to_year);
		$date2 = $date2 + 86400;
        // SQLʸ��date�ؿ���Ϳ����ե����ޥåȤϡ�yyyy/mm/dd�ǻ��ꤹ�롣
		$date2 = date('Y/m/d', $date2);
		
		// ���ϴ��֤�������ξ��
		if( ( $from_year != "" ) && ( $from_month != "" ) && ( $from_day != "" ) &&	( $to_year == "" ) && ( $to_month == "" ) && ( $to_day == "" ) ) {
			$this->setWhere( $column ." >= '" . $date1 . "'");
		}

		//�����ϡ���λ
		if( ( $from_year != "" ) && ( $from_month != "" ) && ( $from_day != "" ) && 
			( $to_year != "" ) && ( $to_month != "" ) && ( $to_day != "" ) ) {
			$this->setWhere( $column ." >= '" . $date1 ."' AND ". $column . " < date('" . $date2 . "')" );
		}

		// ��λ���֤�������ξ��
		if( ( $from_year == "" ) && ( $from_month == "" ) && ( $from_day == "" ) && ( $to_year != "" ) && ( $to_month != "" ) && ( $to_day != "" ) ) {
			$this->setWhere( $column ." < date('" . $date2 . "')");
		}
		return $return;
	}	

	// checkbox�ʤɤ�Ʊ�쥫������ñ�졢�⤷����ʣ������褬ͭ���硡��: AND ( sex = xxx OR sex = xxx OR sex = xxx  ) AND ... 
	function setItemTerm( $arr, $ItemStr ) {

		foreach( $arr as $data ) {
	
			if( count( $arr ) > 1 ) {
				if( ! is_null( $data ) ) $item .= $ItemStr . " = ? OR ";	
			} else {
				if( ! is_null( $data ) ) $item = $ItemStr . " = ?";	
			}
			$return[] = $data;
		}

		if( count( $arr ) > 1 )  $item = "( " . rtrim( $item, " OR " ) . " )";
		$this->setWhere( $item );
		return $return;
	}

	//��NULL�ͤ�ɬ�פʾ��
	function setItemTermWithNull( $arr, $ItemStr ) {

		$item = " ${ItemStr} IS NULL ";
		
		if ( $arr ){
			foreach( $arr as $data ) {	
				if ($data != "����") {
					$item .= " OR ${ItemStr} = ?";
					$return[] = $data;
				}
			}
		}
		
	 	$item = "( ${item} ) ";
		$this->setWhere( $item );
		return $return;
	}
	// NULL�⤷����''�Ǹ���������
	function setItemTermWithNullAndSpace( $arr, $ItemStr ) {
		$count = count($arr);
		$item = " ${ItemStr} IS NULL OR ${ItemStr} = '' ";
		$i = 1;
		if ( $arr ){
			foreach( $arr as $data ) {	
				if ($i == $count) break;
				$item .= " OR ${ItemStr} = ?";	
				$return[] = $data;
				$i ++;
			}
		}
	 	$item = "( ${item} ) ";
		$this->setWhere( $item );
		return $return;
	}
	


	/* ʣ���Υ�����OR��ͥ�踡�������硡�㡧��AND ( item_flag1 = xxx OR item_flag2 = xxx OR item_flag3 = xxx  ) AND ... 

		����ι�¤�㡡
		if ( $_POST['show_site1'] ) $arrShowsite_1 = array( "column" => "show_site1",
															"value"  => $_POST['show_site1'] );

	*/
	function setWhereByOR( $arrWhere ){

		$count = count( $arrWhere );

		for( $i = 0; $i < $count; $i++ ) {

			if( isset( $arrWhere[$i]["value"] ) ) $statement .= $arrWhere[$i]["column"] ." = '" . addslashes( $arrWhere[$i]["value"] ) ."' OR "  ;
		}

		$statement = "( " . rtrim( $statement, " OR " ) . " )";

		if( $this->where ) {

			$this->where .= " AND " . $statement;

		} else {

			$this->where = "WHERE " . $statement;
		}
	}

	function setWhere($where){
		if ($where != "") {		
			if( $this->where ) {
	
				$this->where .= " AND " . $where;
	
			} else {
	
				$this->where = "WHERE " . $where;
			}
		}
	}
	
	function setOrder($order){
		
			$this->order =  "ORDER BY " . $order;
		
	}

	function setGroup( $group ) {
		
		$this->group =  "GROUP BY " . $group;
		
	}

	
	function setLimitOffset( $limit, $offset ){

		if ( is_numeric($limit) and is_numeric($offset) ){

			$this->limit = " LIMIT " .$limit;
			$this->offset = " OFFSET " .$offset;
		}	
	}
	
	function clearSql(){
		$this->select = "";
		$this->where = "";
		$this->group = "";
		$this->order = "";
		$this->limit = "";
		$this->offset = "";
	}
	
	function setSelect($sql) {
		$this->select = $sql;
	}
}
?>