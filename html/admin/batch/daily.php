<?php

$daily_php_dir = realpath(dirname( __FILE__));
require_once($daily_php_dir . "/../require.php");

$term = 0;
$start = 1;	// 集計期間は、$start~$termの間となる。通常前日分から。
$command = false;

// 集計対象期間の取得（指定日分さかのぼる)
if (sfIsInt($argv[1]) && $argv[1] <= 365) {
	$term = $argv[1];
	$command = true;
}

// 集計開始日
if (sfIsInt($argv[2]) && $argv[2] <= 365) {
	$start = $argv[2];
	$command = true;
}

if($term > 0) {
	// 集計の開始
	lfStartDailyTotal($term, $start, $command);
}

// 集計の開始
function lfStartDailyTotal($term, $start, $command = false) {
		
	$now_time = time();
		
	// グラフ画像の削除
	$path = GRAPH_DIR . "*.png";
	system ("rm -rf $path");
	
	// 削除された受注データの受注詳細情報の削除
	$objQuery = new SC_Query();
	$where = "order_id IN (SELECT order_id FROM dtb_order WHERE del_flg = 1)";
	$objQuery->delete("dtb_order_detail", $where);
	
	// 最後に更新された日付を取得
	$ret = $objQuery->max("dtb_bat_order_daily", "create_date");
	list($batch_last) = split("\.", $ret);
	$pass = $now_time - strtotime($batch_last);
		
	// 最後のバッチ実行からLOAD_BATCH_PASS秒経過していないと実行しない。
	if($pass < LOAD_BATCH_PASS) {
		gfPrintLog("LAST BATCH " . $arrRet[0]['create_date'] . " > " . $batch_pass . " -> EXIT BATCH $batch_date");
		return;
	}
		
	// 集計
	for ($i = $start; $i < $term; $i++) {
		// 基本時間から$i日分さかのぼる
		$tmp_time = $now_time - ($i * 24 * 3600);
				
		$batch_date = date("Y/m/d", $tmp_time);
		gfPrintLog("LOADING BATCH $batch_date");
					
		lfBatOrderDaily($tmp_time);
		lfBatOrderDailyHour($tmp_time);
		lfBatOrderAge($tmp_time);
	}
}

// リアルタイムで集計を実施する。集計が終了しているレコードは実施しない。
/*
	$sdate:YYYY-MM-DD hh:mm:ss形式の日付
	$edate:YYYY-MM-DD hh:mm:ss形式の日付
*/
function lfRealTimeDailyTotal($sdate, $edate) {
	$pass = strtotime($edate) - strtotime($sdate);
	$loop = intval($pass / 86400);
	
	for($i = 0; $i <= $loop; $i++) {
		$tmp_time = strtotime($sdate) + ($i * 86400);
		$batch_date = date("Y/m/d H:i:s", $tmp_time);
		$objQuery = new SC_Query();
		$arrRet = $objQuery->select("order_date, create_date", "dtb_bat_order_daily", "order_date = ?", array($batch_date));
		// すでにバッチ処理が終了しているかチェックする。
		if(count($arrRet) > 0) {
			list($create_date) = split("\.", $arrRet[0]['create_date']);
			list($order_date) = split("\.", $arrRet[0]['order_date']);
			$create_time = strtotime($create_date);
			$order_time = strtotime($order_date);
			// オーダー開始日より一日以上後に集計されている場合は集計しなおさない
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

// バッチ集計用のSQL文を取得する。
function lfGetOrderDailySQL($start, $end) {
	$from = " FROM dtb_order AS T1 LEFT JOIN dtb_customer AS T2 USING ( customer_id ) ";
	$where = " WHERE T1.del_flg = 0 AND T1.create_date BETWEEN '$start' AND '$end' ";

/*	mysqlでも問題ないように修正
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
	$sql.= $where;		// 受注作成日で検索する
*/
	$sql = "SELECT ";
	$sql.= "COUNT(*) AS total_order, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.customer_id = 0) AS nonmember ) AS nonmember, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.customer_id <> 0) AS member ) AS member, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.order_sex = 1) AS men ) AS men, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.order_sex = 2) AS women ) AS women, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.order_sex = 1 AND T2.customer_id <> 0) AS men_member ) AS men_member, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.order_sex = 1 AND T2.customer_id = 0) AS men_nonmember ) AS men_nonmember, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.order_sex = 2 AND T2.customer_id <> 0) AS women_member ) AS women_member, ";
	$sql.= "(SELECT sum(cnt) FROM (SELECT COUNT(*) AS cnt $from $where AND T1.order_sex = 2 AND T2.customer_id = 0) AS women_nonmember ) AS women_nonmember, ";
	$sql.= "SUM(total) AS total, ";
	$sql.= "(AVG(total)) AS total_average ";
	$sql.= $from;
	$sql.= $where;		// 受注作成日で検索する

	return $sql;
}

// 売上げ集計バッチ処理(日別)
function lfBatOrderDaily($time) {
	global $arrWDAY;
	
	// 集計対象日を取得する
	$date = date("Y-m-d", $time);
	
	$start = $date . " 00:00:00";
	$end = $date . " 23:59:59";
	
	$sql = lfGetOrderDailySQL($start,$end);
	
	$objQuery = new SC_Query();
	$arrRet = $objQuery->getall($sql);	
		
	$sqlval = $arrRet[0];
	
	// 空文字を"0"に変換
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

// 売上げ集計バッチ処理(時間別) 
function lfBatOrderDailyHour($time) {
	
	// 集計対象日を取得する
	$date = date("Y-m-d", $time);
	$objQuery = new SC_Query();
	
	$start = $date . " 00:00:00";
	$objQuery->delete("dtb_bat_order_daily_hour", "order_date = ?", array($start));
	
	// 1時間毎に集計する。
	for($i = 0; $i < 24; $i++) {
		$sdate = sprintf("%s %02d:00:00", $date, $i);
		$edate = sprintf("%s %02d:59:59", $date, $i);
		$sql = lfGetOrderDailySQL($sdate, $edate);
		$arrRet = $objQuery->getall($sql);
		$sqlval = $arrRet[0];
		// 空文字を"0"に変換
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

// 売上げ集計バッチ処理(年齢別) 
function lfBatOrderAge($time) {
	
	$age_loop = intval(BAT_ORDER_AGE / 10);
	
	// 年齢の範囲を指定してデータ抽出
	$sql.= "SELECT COUNT(*) AS order_count, SUM(total) AS total, (AVG(total)) AS total_average ";
	$sql.= "FROM dtb_order ";
	
	// 集計対象日を取得する
	$date = date("Y-m-d", $time);
	
	$start = $date . " 00:00:00";
	$end = $date . " 23:59:59";
	
	$objQuery = new SC_Query();
	$objQuery->delete("dtb_bat_order_daily_age", "order_date = ?", array($start));

	/* 会員集計 */

	$base_where = "WHERE (create_date BETWEEN ? AND ?) AND customer_id <> 0 AND del_flg = 0 ";

	$end_date = date("Y/m/d",strtotime("-10 year"));
	$start_date = date("Y/m/d",strtotime("1 day" ,strtotime($end_date)));
	
	$end_date = date("Y/m/d", time()); 
	$start_date = date("Y/m/d",strtotime("-10 year"));
	$start_age = null;
	$end_age = null;
	// 年齢毎に集計する。
	for($i = 0; $i <= $age_loop; $i++) {
		$where = $base_where . " AND order_birth >= cast('$start_date' as date)";
		if($i <= $age_loop) {
			$where = $where . " AND order_birth <= cast('$end_date' as date)";
		}
		lfBatOrderAgeSub($sql . $where, $start, $end, $start_age, $end_age, 1);
		$end_date = date("Y/m/d",strtotime("1 day" ,strtotime($start_date)));
		$start_date = date("Y/m/d",strtotime("-10 year",strtotime($end_date)));
	}

	// 誕生日入力なし
	$where = $base_where . " AND order_birth IS NULL ";
	lfBatOrderAgeSub($sql . $where, $start, $end, NULL, NULL, 1);

	/* 非会員集計 */
	
	$base_where = "WHERE (create_date BETWEEN ? AND ?) AND customer_id = 0 AND del_flg = 0";
	$where = $base_where . " AND (to_number(to_char(age(current_timestamp, order_birth), 'YYY'), 999) BETWEEN ? AND ?) ";

	
	$end_date = date("Y/m/d", time()); 
	$start_date = date("Y/m/d",strtotime("-10 year"));
	$start_age = null;
	$end_age = null;
	// 年齢毎に集計する。
	for($i = 0; $i <= $age_loop; $i++) {
		$where = $base_where . " AND order_birth >= cast('$start_date' as date)";
		if($i <= $age_loop) {
			$where = $where . " AND order_birth <= cast('$end_date' as date)";
		}
		lfBatOrderAgeSub($sql . $where, $start, $end, $start_age, $end_age, 1);
		$end_date = date("Y/m/d",strtotime("1 day" ,strtotime($start_date)));
		$start_date = date("Y/m/d",strtotime("-10 year",strtotime($end_date)));
	}

	// 誕生日入力なし
	$where = $base_where . " AND order_birth IS NULL AND del_flg = 0";
	lfBatOrderAgeSub($sql . $where, $start, $end, NULL, NULL, 0);	
}

// 売上げ集計バッチ処理(年齢別) 登録部分
function lfBatOrderAgeSub($sql, $start, $end, $start_age, $end_age, $member) {
	$objQuery = new SC_Query();
	
	if($start_age != NULL || $end_age != NULL) {
		$arrRet = $objQuery->getall($sql, array($start, $end, $start_age, $end_age));
	} else {
		$arrRet = $objQuery->getall($sql, array($start, $end));
	}
	$sqlval = $arrRet[0];
	
	// 空文字を"0"に変換
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

/*
function lfBatOrderAgeSub($sql, $start, $end, $start_age, $end_age, $member) {
	$objQuery = new SC_Query();
	
	if($start_age != NULL || $end_age != NULL) {
		$arrRet = $objQuery->getall($sql, array($start, $end, $start_age, $end_age));
	} else {
		$arrRet = $objQuery->getall($sql, array($start, $end));
	}
	$sqlval = $arrRet[0];
	
	// 空文字を"0"に変換
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
*/
?>