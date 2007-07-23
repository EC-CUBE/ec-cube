<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("./require.php");

class LC_Page {
	var $arrSession;
	function LC_Page() {
		$this->tpl_mainpage = 'home.tpl';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// 認証可否の判定
sfIsSuccess($objSess);

// DBバージョンの取得
$objPage->db_version = sfGetDBVersion();

// PHPバージョンの取得
$objPage->php_version = "PHP " . phpversion();

// 現在の会員数
$objPage->customer_cnt = lfGetCustomerCnt($conn);

// 昨日の売上高
$objPage->order_yesterday_amount = lfGetOrderYesterday($conn, "SUM");

// 昨日の売上件数
$objPage->order_yesterday_cnt = lfGetOrderYesterday($conn, "COUNT");

// 今月の売上高
$objPage->order_month_amount = lfGetOrderMonth($conn, "SUM");

// 今月の売上件数
$objPage->order_month_cnt = lfGetOrderMonth($conn, "COUNT");

// 顧客の累計ポイント
$objPage->customer_point = lfGetTotalCustomerPoint();

//昨日のレビュー書き込み数
$objPage->review_yesterday_cnt = lfGetReviewYesterday($conn);

//レビュー書き込み非表示数
$objPage->review_nondisp_cnt = lfGetReviewNonDisp($conn);

// 品切れ商品
$objPage->arrSoldout = lfGetSoldOut();

// 新規受付一覧
$arrNewOrder = lfGetNewOrder();

foreach ($arrNewOrder as $key => $val){
	$arrNewOrder[$key]['create_date'] = str_replace("-", "/", substr($val['create_date'], 0,19));
	
}
$objPage->arrNewOrder = $arrNewOrder;

// お知らせ一覧の取得
$objPage->arrInfo = lfGetInfo();

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//---------------------------------------------------------

// 会員数
function lfGetCustomerCnt($conn){
	
	$sql = "SELECT COUNT(customer_id) FROM dtb_customer WHERE del_flg = 0 AND status = 2";
	$return = $conn->getOne($sql);
	return $return;
}

// 昨日の売上高・売上件数
function lfGetOrderYesterday($conn, $method){
	if ( $method == 'SUM' or $method == 'COUNT'){
		// postgresql と mysql とでSQLをわける
		if (DB_TYPE == "pgsql") {
			$sql = "SELECT ".$method."(total) FROM dtb_order
					 WHERE del_flg = 0 AND to_char(create_date,'YYYY/MM/DD') = to_char(now() - interval '1 days','YYYY/MM/DD') AND status <> " . ORDER_CANCEL;
		}else if (DB_TYPE == "mysql") {
			$sql = "SELECT ".$method."(total) FROM dtb_order
					 WHERE del_flg = 0 AND cast(substring(create_date,1, 10) as date) = DATE_ADD(current_date, interval -1 day) AND status <> " . ORDER_CANCEL;
		}
		$return = $conn->getOne($sql);
	}
	return $return;
}

function lfGetOrderMonth($conn, $method){

	$month = date("Y/m", mktime());
	
	if ( $method == 'SUM' or $method == 'COUNT'){
	// postgresql と mysql とでSQLをわける
	if (DB_TYPE == "pgsql") {
		$sql = "SELECT ".$method."(total) FROM dtb_order
				 WHERE del_flg = 0 AND to_char(create_date,'YYYY/MM') = ? 
				 AND to_char(create_date,'YYYY/MM/DD') <> to_char(now(),'YYYY/MM/DD') AND status <> " . ORDER_CANCEL;
	}else if (DB_TYPE == "mysql") {
		$sql = "SELECT ".$method."(total) FROM dtb_order
				 WHERE del_flg = 0 AND date_format(create_date, '%Y/%m') = ? 
				 AND date_format(create_date, '%Y/%m/%d') <> date_format(now(), '%Y/%m/%d') AND status <> " . ORDER_CANCEL;
	}
		$return = $conn->getOne($sql, array($month));
	}
	return $return;
}

function lfGetTotalCustomerPoint() {
	$objQuery = new SC_Query();
	$col = "SUM(point)";
	$where = "del_flg = 0";
	$from = "dtb_customer";
	$ret = $objQuery->get($from, $col, $where);
	return $ret;	
}

function lfGetReviewYesterday($conn){
	// postgresql と mysql とでSQLをわける
	if (DB_TYPE == "pgsql") {
		$sql = "SELECT COUNT(*) FROM dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id  
				 WHERE A.del_flg=0 AND B.del_flg = 0 AND to_char(A.create_date, 'YYYY/MM/DD') = to_char(now() - interval '1 days','YYYY/MM/DD')
				 AND to_char(A.create_date,'YYYY/MM/DD') != to_char(now(),'YYYY/MM/DD')";
	}else if (DB_TYPE == "mysql") {
		$sql = "SELECT COUNT(*) FROM dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id 
				 WHERE A.del_flg = 0 AND B.del_flg = 0 AND cast(substring(A.create_date,1, 10) as date) = DATE_ADD(current_date, interval -1 day)
				 AND cast(substring(A.create_date,1, 10) as date) != cast(substring(now(),1, 10) as date)";
	}
	$return = $conn->getOne($sql);
	return $return;
}

function lfGetReviewNonDisp($conn){
	$sql = "SELECT COUNT(*) FROM dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id WHERE A.del_flg=0 AND A.status=2 AND B.del_flg=0";
	$return = $conn->getOne($sql);
	return $return;
}

// 品切れ商品番号の取得
function lfGetSoldOut() {
	$objQuery = new SC_Query();
	$where = "product_id IN (SELECT product_id FROM dtb_products_class WHERE stock_unlimited IS NULL AND stock <= 0)";
	$arrRet = $objQuery->select("product_id, name", "dtb_products", $where);
	return $arrRet;
}

// 新規受付一覧
function lfGetNewOrder() {
	$objQuery = new SC_Query();
	$sql = "SELECT 
			    ord.order_id,
			    ord.customer_id,
			    ord.order_name01 AS name01,
			    ord.order_name02 AS name02,
			    ord.total,
			    ord.create_date,
			    (SELECT
			        det.product_name
			    FROM
			        dtb_order_detail AS det
			    WHERE
			        ord.order_id = det.order_id LIMIT 1
			    ) AS product_name,
			    (SELECT
			        pay.payment_method
			    FROM
			        dtb_payment AS pay
			    WHERE
			        ord.payment_id = pay.payment_id
			    ) AS payment_method 
			FROM (
			    SELECT
			        order_id,
			        customer_id,
			        order_name01,
			        order_name02,
			        total,
			        create_date,
			        payment_id
			    FROM
			        dtb_order AS ord
			    WHERE
			        del_flg = 0 AND status <> " . ORDER_CANCEL . " 
			    ORDER BY
			        create_date DESC LIMIT 10 OFFSET 0
			) AS ord";
	$arrRet = $objQuery->getAll($sql);
	return $arrRet;
}

// お知らせ取得
function lfGetInfo() {
	// 更新情報を最新にする
	$objQuery = new SC_Query();
	$path = UPDATE_HTTP . "info.txt";
	$fp = @fopen($path, "rb");
	
	$arrRet = array();
	if(!$fp) {
		sfErrorHeader(">> " . $path . "の取得に失敗しました。");
	} else {
		while (!feof($fp)) {
			$arrRet[] = $arrCSV = fgetcsv($fp, UPDATE_CSV_LINE_MAX);
		}
		fclose($fp);
	}
	
	return $arrRet;
}



?>