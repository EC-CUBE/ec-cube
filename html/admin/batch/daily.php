<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
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
		
	$now_time = time();
		
	// ����ղ����κ��
	$path = GRAPH_DIR . "*.png";
	system ("rm -rf $path");
	
	// ������줿����ǡ����μ���ܺپ���κ��
	$objQuery = new SC_Query();
	$where = "order_id IN (SELECT order_id FROM dtb_order WHERE del_flg = 1)";
	$objQuery->delete("dtb_order_detail", $where);
	
	// �Ǹ�˹������줿���դ����
	$ret = $objQuery->max("dtb_bat_order_daily", "create_date");
	list($batch_last) = split("\.", $ret);
	$pass = $now_time - strtotime($batch_last);
		
	// �Ǹ�ΥХå��¹Ԥ���LOAD_BATCH_PASS�÷вᤷ�Ƥ��ʤ��ȼ¹Ԥ��ʤ���
	if(!$command && $pass < LOAD_BATCH_PASS) {
		gfPrintLog("LAST BATCH " . $arrRet[0]['create_date'] . " > " . $batch_pass . " -> EXIT BATCH $batch_date");
		return;
	}

	// ����
	for ($i = $start; $i < $term; $i++) {
		// ���ܻ��֤���$i��ʬ�����Τܤ�
		$tmp_time = $now_time - ($i * 24 * 3600);
				
		$batch_date = date("Y/m/d", $tmp_time);
		gfPrintLog("LOADING BATCH $batch_date");
					
		lfBatOrderDaily($tmp_time);
		lfBatOrderDailyHour($tmp_time);
		lfBatOrderAge($tmp_time);
		
        // �֥饦������μ¹Ԥξ��
        if(!$command) {
            // �����ॢ���Ȥ��ɤ�
            sfFlush();
        } else {
            print("LOADING BATCH $batch_date\n");
        }
	}
}

// �ꥢ�륿����ǽ��פ�»ܤ��롣���פ���λ���Ƥ���쥳���ɤϼ»ܤ��ʤ���
/*
	$sdate:YYYY-MM-DD hh:mm:ss����������
	$edate:YYYY-MM-DD hh:mm:ss����������
*/
function lfRealTimeDailyTotal($sdate, $edate) {
	$pass = strtotime($edate) - strtotime($sdate);
	$loop = intval($pass / 86400);
	
	for($i = 0; $i <= $loop; $i++) {
		$tmp_time = strtotime($sdate) + ($i * 86400);
		$batch_date = date("Y/m/d H:i:s", $tmp_time);
		$objQuery = new SC_Query();
		$arrRet = $objQuery->select("order_date, create_date", "dtb_bat_order_daily", "order_date = ?", array($batch_date));
		// ���Ǥ˥Хå���������λ���Ƥ��뤫�����å����롣
		if(count($arrRet) > 0) {
			list($create_date) = split("\.", $arrRet[0]['create_date']);
			list($order_date) = split("\.", $arrRet[0]['order_date']);
			$create_time = strtotime($create_date);
			$order_time = strtotime($order_date);
			// ���׳��������1���ʾ��˽��פ���Ƥ�����Ͻ��פ��ʤ����ʤ�
			if($order_time + 86400 < $create_time || $tmp_time > time()) {
				gfPrintLog("EXIT BATCH $batch_date $tmp_time" . " " . time());
				continue;
			}
		}
		gfPrintLog("LOADING BATCH $batch_date");
		lfBatOrderDaily($tmp_time);
		lfBatOrderDailyHour($tmp_time);
		lfBatOrderAge($tmp_time);
	}
}

// �Хå������Ѥ�SQLʸ��������롣
function lfGetOrderDailySQL($start, $end) {
	$from = " FROM dtb_order AS T1 LEFT JOIN dtb_customer AS T2 USING ( customer_id ) ";
	$where = " WHERE T1.del_flg = 0 AND T1.status <> " . ORDER_CANCEL . " AND T1.create_date BETWEEN '$start' AND '$end' ";

/*	mysql�Ǥ�����ʤ��褦�˽���
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
	$sql.= "int8(AVG(total)) AS total_average ";
	$sql.= $from;
	$sql.= $where;		// ����������Ǹ�������
*/
	$sql = "SELECT ";
	$sql.= "COUNT(*) AS total_order, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.customer_id = 0) AS nonmember ) AS nonmember, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.customer_id <> 0) AS member ) AS member, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.order_sex = 1) AS men ) AS men, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.order_sex = 2) AS women ) AS women, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.order_sex = 1 AND T2.customer_id <> 0) AS men_member ) AS men_member, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.order_sex = 1 AND T1.customer_id = 0) AS men_nonmember ) AS men_nonmember, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.order_sex = 2 AND T2.customer_id <> 0) AS women_member ) AS women_member, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.order_sex = 2 AND T1.customer_id = 0) AS women_nonmember ) AS women_nonmember, ";
	$sql.= "SUM(total) AS total, ";
	$sql.= "(AVG(total)) AS total_average ";
	$sql.= $from;
	$sql.= $where;		// ����������Ǹ�������

	return $sql;
}

// ��夲���ץХå�����(����)
function lfBatOrderDaily($time) {
	global $arrWDAY;
	
	// �����о������������
	$date = date("Y-m-d", $time);
	
	$start = $date . " 00:00:00";
	$end = $date . " 23:59:59";
	
	$sql = lfGetOrderDailySQL($start,$end);
	
	$objQuery = new SC_Query();
	$arrRet = $objQuery->getall($sql);	
	
	$sqlval = $arrRet[0];
	
	// ��ʸ����"0"���Ѵ�
	foreach($sqlval as $key => $val) {
		if ($val == "") {
			$sqlval[$key] = "0";
		}
	}
	
	$sqlval['create_date'] = 'now()';
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
	
	// �����о������������
	$date = date("Y-m-d", $time);
	$objQuery = new SC_Query();
	
	$start = $date . " 00:00:00";
	$objQuery->delete("dtb_bat_order_daily_hour", "order_date = ?", array($start));
	
	// 1������˽��פ��롣
	for($i = 0; $i < 24; $i++) {
		$sdate = sprintf("%s %02d:00:00", $date, $i);
		$edate = sprintf("%s %02d:59:59", $date, $i);
		$sql = lfGetOrderDailySQL($sdate, $edate);
		$arrRet = $objQuery->getall($sql);
		$sqlval = $arrRet[0];
		// ��ʸ����"0"���Ѵ�
		foreach($sqlval as $key => $val) {
			if ($val == "") {
				$sqlval[$key] = "0";
			}
		}
		$sqlval['create_date'] = "now()";
		$sqlval['order_date'] = $start;
		$sqlval['hour'] = "$i";
		$objQuery->insert("dtb_bat_order_daily_hour", $sqlval);
	}	
}

// ��夲���ץХå�����(ǯ����) 
function lfBatOrderAge($time) {
	
	$age_loop = intval(BAT_ORDER_AGE / 10);
	
	// ǯ����ϰϤ���ꤷ�ƥǡ������
	$sql.= "SELECT COUNT(*) AS order_count, SUM(total) AS total, (AVG(total)) AS total_average ";
	$sql.= "FROM dtb_order ";
	
	// �����о������������
	$date = date("Y-m-d", $time);
	
	$start = $date . " 00:00:00";
	$end = $date . " 23:59:59";
	
	$objQuery = new SC_Query();
	$objQuery->delete("dtb_bat_order_daily_age", "order_date = ?", array($start));

	/* ������� */

	$base_where = "WHERE (create_date BETWEEN ? AND ?) AND customer_id <> 0 AND del_flg = 0 AND status <> " . ORDER_CANCEL;

	$end_date = date("Y/m/d", time()); 
	$start_date = date("Y/m/d",strtotime("-10 year" ,strtotime($end_date)));
	$end_date = date("Y/m/d",strtotime("1 day" ,strtotime($end_date)));
	// ǯ����˽��פ��롣
	for($i = 0; $i <= $age_loop; $i++) {
		$where = $base_where . " AND order_birth >= cast('$start_date' as date)";
		$start_age = $i * 10;
		if($i < $age_loop) {
			$end_age = $start_age+9;
			$where = $where . " AND order_birth < cast('$end_date' as date)";
		}else{
			$where = $base_where . " AND order_birth < cast('$end_date' as date)";
			$end_age = 999;
		}
		lfBatOrderAgeSub($sql . $where, $start, $end, $start_age, $end_age, 1);
		$end_date = date("Y/m/d",strtotime("1 day" ,strtotime($start_date)));
		$start_date = date("Y/m/d",strtotime("-10 year",strtotime($start_date)));
	}
	
	// ���������Ϥʤ�
	$where = $base_where . " AND order_birth IS NULL ";
	lfBatOrderAgeSub($sql . $where, $start, $end, NULL, NULL, 1);

	/* �������� */
	
	$base_where = "WHERE (create_date BETWEEN ? AND ?) AND customer_id = 0 AND del_flg = 0 AND status <> " . ORDER_CANCEL;
	$where = $base_where . " AND (to_number(to_char(age(current_timestamp, order_birth), 'YYY'), 999) BETWEEN ? AND ?) ";

	$end_date = date("Y/m/d", time()); 
	$start_date = date("Y/m/d",strtotime("-10 year" ,strtotime($end_date)));
	$end_date = date("Y/m/d",strtotime("1 day" ,strtotime($end_date)));
	// ǯ����˽��פ��롣
	for($i = 0; $i <= $age_loop; $i++) {
		$where = $base_where . " AND order_birth >= cast('$start_date' as date)";
		$start_age = $i * 10;
		if($i < $age_loop) {
			$end_age = $start_age+9;
			$where = $where . " AND order_birth < cast('$end_date' as date)";
		}else{
			$where = $base_where . " AND order_birth < cast('$end_date' as date)";
			$end_age = 999;
		}
		lfBatOrderAgeSub($sql . $where, $start, $end, $start_age, $end_age, 0);
		$end_date = date("Y/m/d",strtotime("1 day" ,strtotime($start_date)));
		$start_date = date("Y/m/d",strtotime("-10 year",strtotime($start_date)));
	}
	
	// ���������Ϥʤ�
	$where = $base_where . " AND order_birth IS NULL AND del_flg = 0";
	lfBatOrderAgeSub($sql . $where, $start, $end, NULL, NULL, 0);	
}

// ��夲���ץХå�����(ǯ����) ��Ͽ��ʬ
function lfBatOrderAgeSub($sql, $start, $end, $start_age, $end_age, $member) {
	$objQuery = new SC_Query();
	
	$arrRet = $objQuery->getall($sql, array($start, $end));
	$sqlval = $arrRet[0];
	
	// ��ʸ����"0"���Ѵ�
	foreach($sqlval as $key => $val) {
		if ($val == "") {
			$sqlval[$key] = "0";
		}
	}

	$sqlval['create_date'] = "now()";
	$sqlval['order_date'] = $start;
	$sqlval['start_age'] = "$start_age";
	$sqlval['end_age'] = "$end_age";
	$sqlval['member'] = "$member";

	$objQuery->insert("dtb_bat_order_daily_age", $sqlval);
}

// ʸ�����SingleQuotation����Ϳ����
function lfSingleQuot($value){
	$ret = "";
	if (DB_TYPE == "mysql") {
		$ret = $value;
	}else{
		$ret = "'" . $value . "'";
	} 
	
	return $ret;
}

?>