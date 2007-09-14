<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once(DATA_PATH . "include/csv_output.inc");

// CSV���ϥǡ�����������롣
function lfGetCSV($from, $where, $option, $arrval, $arrCsvOutputCols = "") {
	global $arrCVSCOL;

	//$cols = sfGetCommaList($arrCVSCOL);
	$cols = sfGetCommaList($arrCsvOutputCols);
	
	$objQuery = new SC_Query();
	$objQuery->setoption($option);
	
	$list_data = $objQuery->select($cols, $from, $where, $arrval);	

	$max = count($list_data);
	for($i = 0; $i < $max; $i++) {
		// �ƹ��ܤ�CSV�����Ѥ��Ѵ����롣
		$data .= lfMakeCSV($list_data[$i]);
	}
	return $data;
}

// �ƹ��ܤ�CSV�����Ѥ��Ѵ����롣
function lfMakeCSV($list) {
	global $arrPref;
	
	$line = "";
	
	foreach($list as $key => $val) {
		$tmp = "";
		switch($key) {
		case 'order_pref':
			$tmp = $arrPref[$val];
			break;
		default:
			$tmp = $val;
			break;
		}

		$tmp = ereg_replace("[\",]", " ", $tmp);
		$line .= "\"".$tmp."\",";
	}
	// ʸ����","���Ѵ�
	$line = ereg_replace(",$", "\n", $line);
	return $line;
}

?>