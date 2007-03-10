<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

$arrSubnavi = array(
	1 => 'product',
	2 => 'customer',
	3 => 'order',
	4 => 'campaign'
);

$arrSubnaviName = array(
	1 => '商品管理',
	2 => '顧客管理',
	3 => '受注管理',
	4 => 'キャンペーン'
);

/**************************************************************************************************************
 * 関数名	：sfgetCsvOutput
 * 処理内容	：CSV出力項目を取得する
 * 引数		：なし
 * 戻り値	：CSV出力項目
 **************************************************************************************************************/
function sfgetCsvOutput($csv_id = "", $where = "", $arrVal = array()){
	$objQuery = new SC_Query();
	$arrData = array();
	$ret = array();
	
	$sql = "";
	$sql .= " SELECT ";
	$sql .= "     no, ";
	$sql .= "     csv_id, ";
	$sql .= "     col, ";
	$sql .= "     disp_name, ";
	$sql .= "     rank, ";
	$sql .= "     status, ";
	$sql .= "     create_date, ";
	$sql .= "     update_date ";
	$sql .= " FROM ";
	$sql .= "     dtb_csv ";
	
	if ($where != "") {
		$sql .= $where;
		$arrData = $arrVal;
	}elseif($csv_id != ""){
		$sql .= " WHERE csv_id = ? ";
		$arrData = array($csv_id);
	}
	
	$sql .= " ORDER BY ";
	$sql .= "     rank , no";
	$sql .= " ";	
	
	$ret = $objQuery->getall($sql, $arrData);
	
	return $ret;
}

?>