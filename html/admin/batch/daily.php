<?php

$daily_php_dir = realpath(dirname( __FILE__));
require_once($daily_php_dir . "/../require.php");

$term = 0;
$start = 1;	// ���״��֤ϡ�$start���$term�δ֤Ȥʤ롣�̾�����ʬ���顣
$command = false;

// �����оݴ��֤μ����ʻ�����ʬ�����Τܤ�)
if (sfIsInt($argv[1]) && $argv[1] <= 365) {
	$term = $argv[1];
	$command = true;
}

// ���׳�����
if (sfIsInt($argv[2]) && $argv[2] <= 365) {
	$start = $argv[2];
	$command = true;
}

if($term > 0) {
	// ���פγ���
	lfStartDailyTotal($term, $start, $command);
}

// ���פγ���
function lfStartDailyTotal($term, $start, $command = false) {
	
	print("term:" . $term);
	
	$now_time = time();
	
	// ����ղ����κ��
	$path = GRAPH_DIR . "*.png";
	system ("rm -rf $path");
	
	// ������줿����ǡ����μ���ܺپ���κ��
	$objQuery = new SC_Query();
	$where = "order_id IN (SELECT order_id FROM dtb_order WHERE delete = 1)";
	$objQuery->delete("dtb_order_detail", $where);

	// ����
	for ($i = $start; $i < $term; $i++) {
		// ���ܻ��֤���1��ʬ�����Τܤ�
		$tmp_time = $now_time - ($i * 24 * 3600);
				
		$batch_date = date("Y/m/d", $tmp_time);
		
		// �Ǹ�ΥХå��¹Ԥ���LOAD_BATCH_PASS�÷вᤷ�Ƥ��ʤ��ȼ¹Ԥ��ʤ���
		$batch_pass = date("Y/m/d H:m:s", $tmp_time - LOAD_BATCH_PASS);
		$objQuery = new SC_Query();
		$count = $objQuery->count("dtb_bat_order_daily", "create_date > ?", $batch_pass);
		
		$objQuery->getLastQuery();
				
		if($count > 0 && !$command) {
			gfPrintLog("LAST BATCH $batch_pass -> EXIT BATCH $batch_date");
			return;
		}
		
		gfPrintLog("LOADING BATCH $batch_date");
					
		lfBatOrderDaily($tmp_time);
		lfBatOrderDailyHour($tmp_time);
		lfBatOrderAge($tmp_time);
	}
}

// �Хå������Ѥ�SQLʸ��������롣
function lfGetOrderDailySQL() {
	$sql = "SELECT ";
	$sql.= "COUNT(*) AS total_order, ";
	$sql.= "SUM((SELECT COUNT(*) WHERE customer_id = 0)) AS nonmember, ";
	$sql.= "SUM((SELECT COUNT(*) WHERE customer_id <> 0 GROUP BY customer_id)) AS member, ";
	$sql.= "SUM((SELECT COUNT(*) WHERE order_sex = 1)) AS men, ";
	$sql.= "SUM((SELECT COUNT(*) WHERE order_sex = 2)) AS women, ";
	$sql.= "SUM((SELECT COUNT(*) WHERE order_sex = 1 AND customer_id <> 0)) AS men_member, ";
	$sql.= "SUM((SELECT COUNT(*) WHERE order_sex = 1 AND customer_id = 0)) AS men_nonmember, ";
	$sql.= "SUM((SELECT COUNT(*) WHERE order_sex = 2 AND customer_id <> 0)) AS women_member, ";
	$sql.= "SUM((SELECT COUNT(*) WHERE order_sex = 2 AND customer_id = 0)) AS women_nonmember, ";
	$sql.= "SUM(total) AS total, ";
	$sql.= "int4(AVG(total)) AS total_average ";
	$sql.= "FROM dtb_order AS T1 LEFT JOIN dtb_customer AS T2 USING ( customer_id ) ";
	$sql.= "WHERE T1.delete = 0 AND T1.create_date BETWEEN ? AND ?";		// ����������Ǹ�������
	return $sql;
}

// ��夲���ץХå�����(����)
function lfBatOrderDaily($time) {
	global $arrWDAY;
	
	$sql = lfGetOrderDailySQL();
	
	// �����о������������
	$date = date("Y-m-d", $time);
	
	$start = $date . " 00:00:00";
	$end = $date . " 23:59:59";
	
	$objQuery = new SC_Query();
	$arrRet = $objQuery->getall($sql, array($start, $end));	
		
	$sqlval = $arrRet[0];
	
	// ��ʸ����"0"���Ѵ�
	foreach($sqlval as $key => $val) {
		if ($val == "") {
			$sqlval[$key] = "0";
		}
	}
	
	$sqlval['order_date'] = $start;
	$sqlval['year'] = date("Y", $time);
	$sqlval['month'] = date("m", $time);
	$sqlval['day'] = date("d", $time);
	$sqlval['wday'] = date("w", $time);
	$sqlval['key_day'] = sprintf("%02d/%02d/%02d", substr($sqlval['year'],2), $sqlval['month'], $sqlval['day']);
	$sqlval['key_month'] = sprintf("%02d/%02d", substr($sqlval['year'],2), $sqlval['month']);
	$sqlval['key_year'] = sprintf("%d", $sqlval['year']);
	$sqlval['key_wday'] = sprintf("%s", $arrWDAY[$sqlval['wday']]);
	
	$objQuery->delete("dtb_bat_order_daily", "order_date = ?", array($start));
	$objQuery->insert("dtb_bat_order_daily", $sqlval);
}

// ��夲���ץХå�����(������) 
function lfBatOrderDailyHour($time) {
	
	$sql = lfGetOrderDailySQL();
	
	// �����о������������
	$date = date("Y-m-d", $time);
	$objQuery = new SC_Query();
	
	$start = $date . " 00:00:00";
	$objQuery->delete("dtb_bat_order_daily_hour", "order_date = ?", array($start));
	
	// 1������˽��פ��롣
	for($i = 0; $i < 24; $i++) {
		$sdate = sprintf("%s %02d:00:00", $date, $i);
		$edate = sprintf("%s %02d:59:59", $date, $i);
		$arrRet = $objQuery->getall($sql, array($sdate, $edate));
		$sqlval = $arrRet[0];
		// ��ʸ����"0"���Ѵ�
		foreach($sqlval as $key => $val) {
			if ($val == "") {
				$sqlval[$key] = "0";
			}
		}
		$sqlval['order_date'] = $start;
		$sqlval['hour'] = "$i";
		$objQuery->insert("dtb_bat_order_daily_hour", $sqlval);
	}	
}

// ��夲���ץХå�����(ǯ����) 
function lfBatOrderAge($time) {
	
	$age_loop = intval(BAT_ORDER_AGE / 10);
	
	// ǯ����ϰϤ���ꤷ�ƥǡ������
	$sql.= "SELECT COUNT(*) AS order_count, SUM(total) AS total, int4(AVG(total)) AS total_average ";
	$sql.= "FROM dtb_order ";
	
	// �����о������������
	$date = date("Y-m-d", $time);
	
	$start = $date . " 00:00:00";
	$end = $date . " 23:59:59";
	
	$objQuery = new SC_Query();
	$objQuery->delete("dtb_bat_order_daily_age", "order_date = ?", array($start));
	
	/* ������� */
	
	$base_where = "WHERE (create_date BETWEEN ? AND ?) AND customer_id <> 0 AND delete = 0 ";
	$where = $base_where . " AND (to_number(to_char(age(current_timestamp, order_birth), 'YYY'), 999) BETWEEN ? AND ?) ";
	
	// ǯ����˽��פ��롣
	for($i = 0; $i <= $age_loop; $i++) {
		$start_age = $i * 10;
		$end_age = $start_age + 9;
		if($i >= $age_loop) {
			$end_age = 999;
		}
		lfBatOrderAgeSub($sql . $where, $start, $end, $start_age, $end_age, 1);
	}
	
	// ���������Ϥʤ�
	$where = $base_where . " AND order_birth IS NULL ";
	lfBatOrderAgeSub($sql . $where, $start, $end, NULL, NULL, 1);
	
	/* �������� */
	
	$base_where = "WHERE (create_date BETWEEN ? AND ?) AND customer_id = 0 AND delete = 0";
	$where = $base_where . " AND (to_number(to_char(age(current_timestamp, order_birth), 'YYY'), 999) BETWEEN ? AND ?) ";
	
	// ǯ����˽��פ��롣
	for($i = 0; $i <= $age_loop; $i++) {
		$start_age = $i * 10;
		$end_age = $start_age + 9;
		if($i >= $age_loop) {
			$end_age = 999;
		}
		lfBatOrderAgeSub($sql . $where, $start, $end, $start_age, $end_age, 0);
	}
	
	// ���������Ϥʤ�
	$where = $base_where . " AND order_birth IS NULL AND delete = 0";
	lfBatOrderAgeSub($sql . $where, $start, $end, NULL, NULL, 0);	
}

// ��夲���ץХå�����(ǯ����) ��Ͽ��ʬ
function lfBatOrderAgeSub($sql, $start, $end, $start_age, $end_age, $member) {
	$objQuery = new SC_Query();
	
	if($start_age != NULL || $end_age != NULL) {
		$arrRet = $objQuery->getall($sql, array($start, $end, $start_age, $end_age));
	} else {
		$arrRet = $objQuery->getall($sql, array($start, $end));
	}
	$sqlval = $arrRet[0];
	
	// ��ʸ����"0"���Ѵ�
	foreach($sqlval as $key => $val) {
		if ($val == "") {
			$sqlval[$key] = "0";
		}
	}
		
	$sqlval['order_date'] = $start;
	$sqlval['start_age'] = "$start_age";
	$sqlval['end_age'] = "$end_age";
	$sqlval['member'] = "$member";

	$objQuery->insert("dtb_bat_order_daily_age", $sqlval);
}

?>