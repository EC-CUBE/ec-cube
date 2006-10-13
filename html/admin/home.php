<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
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

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

// DB�С������μ���
$objPage->db_version = sfGetDBVersion();

// PHP�С������μ���
$objPage->php_version = "PHP " . phpversion();

// ���ߤβ����
$objPage->customer_cnt = lfGetCustomerCnt($conn);

// ����������
$objPage->order_yesterday_amount = lfGetOrderYesterday($conn, "SUM");

// �����������
$objPage->order_yesterday_cnt = lfGetOrderYesterday($conn, "COUNT");

// ���������
$objPage->order_month_amount = lfGetOrderMonth($conn, "SUM");

// ����������
$objPage->order_month_cnt = lfGetOrderMonth($conn, "COUNT");

// �ܵҤ��߷ץݥ����
$objPage->customer_point = lfGetTotalCustomerPoint();

//�����Υ�ӥ塼�񤭹��߿�
$objPage->review_yesterday_cnt = lfGetReviewYesterday($conn);

//��ӥ塼�񤭹�����ɽ����
$objPage->review_nondisp_cnt = lfGetReviewNonDisp($conn);

// ���ڤ쾦��
$objPage->arrSoldout = lfGetSoldOut();

// �������հ���
$arrNewOrder = lfGetNewOrder();

foreach ($arrNewOrder as $key => $val){
	$arrNewOrder[$key]['create_date'] = str_replace("-", "/", substr($val['create_date'], 0,19));
	
}
$objPage->arrNewOrder = $arrNewOrder;

// ���Τ餻�����μ���
$objPage->arrInfo = lfGetInfo();

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//---------------------------------------------------------

// �����
function lfGetCustomerCnt($conn){
	
	$sql = "SELECT COUNT(customer_id) FROM dtb_customer WHERE del_flg = 0 AND status = 2";
	$return = $conn->getOne($sql);
	return $return;
}

// ���������⡦�����
function lfGetOrderYesterday($conn, $method){
	if ( $method == 'SUM' or $method == 'COUNT'){
		// postgresql �� mysql �Ȥ�SQL��櫓��
		if (DB_TYPE == "pgsql") {
			$sql = "SELECT ".$method."(total) FROM dtb_order
					 WHERE del_flg = 0 AND to_char(create_date,'YYYY/MM/DD') = to_char(now() - interval '1 days','YYYY/MM/DD')";
		}else if (DB_TYPE == "mysql") {
			$sql = "SELECT ".$method."(total) FROM dtb_order
					 WHERE del_flg = 0 AND cast(substring(create_date,1, 10) as date) = DATE_ADD(current_date, interval -1 day)";
		}
		$return = $conn->getOne($sql);
	}
	return $return;
}

function lfGetOrderMonth($conn, $method){

	$month = date("Y/m", mktime());
	
	if ( $method == 'SUM' or $method == 'COUNT'){
	// postgresql �� mysql �Ȥ�SQL��櫓��
	if (DB_TYPE == "pgsql") {
		$sql = "SELECT ".$method."(total) FROM dtb_order
				 WHERE del_flg = 0 AND to_char(create_date,'YYYY/MM') = ? 
				 AND to_char(create_date,'YYYY/MM/DD') <> to_char(now(),'YYYY/MM/DD')";
	}else if (DB_TYPE == "mysql") {
		$sql = "SELECT ".$method."(total) FROM dtb_order
				 WHERE del_flg = 0 AND cast(substring(create_date,1,7) as date) = ? 
				 AND cast(substring(create_date,1, 10) as date) <> cast(substring(now(),1, 10) as date)";
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
	// postgresql �� mysql �Ȥ�SQL��櫓��
	if (DB_TYPE == "pgsql") {
		$sql = "SELECT COUNT(*) FROM dtb_review 
				 WHERE del_flg=0 AND to_char(create_date, 'YYYY/MM/DD') = to_char(now() - interval '1 days','YYYY/MM/DD')
				 AND to_char(create_date,'YYYY/MM/DD') != to_char(now(),'YYYY/MM/DD')";
	}else if (DB_TYPE == "mysql") {
		$sql = "SELECT COUNT(*) FROM dtb_review 
				 WHERE del_flg = 0 AND cast(substring(create_date,1, 10) as date) = DATE_ADD(current_date, interval -1 day)
				 AND cast(substring(create_date,1, 10) as date) != cast(substring(now(),1, 10) as date)";
	}
	$return = $conn->getOne($sql);
	return $return;
}

function lfGetReviewNonDisp($conn){
	$sql = "SELECT COUNT(*) FROM dtb_review WHERE del_flg=0 AND status=2";
	$return = $conn->getOne($sql);
	return $return;
}

// ���ڤ쾦���ֹ�μ���
function lfGetSoldOut() {
	$objQuery = new SC_Query();
	$where = "product_id IN (SELECT product_id FROM dtb_products_class WHERE stock_unlimited IS NULL AND stock <= 0)";
	$arrRet = $objQuery->select("product_id, name", "dtb_products", $where);
	return $arrRet;
}

// �������հ���
function lfGetNewOrder() {
	$objQuery = new SC_Query();
	$col = "ord.order_id, customer_id, ord.order_name01 AS name01, ord.order_name02 AS name02, ord.total, ";
	$col.= "(SELECT det.product_name FROM dtb_order_detail AS det WHERE ord.order_id = det.order_id LIMIT 1) AS product_name, ";
	$col.= "(SELECT pay.payment_method FROM dtb_payment AS pay WHERE ord.payment_id = pay.payment_id) AS payment_method, ";	
	$col.= "create_date AS create_date";
	$from = "dtb_order AS ord";
	$where = "del_flg = 0";
	$objQuery->setoption("ORDER BY create_date DESC LIMIT 10 OFFSET 0");
	$arrRet = $objQuery->select($col, $from, $where);
	return $arrRet;
}

// ���Τ餻����
function lfGetInfo() {
	// ���������ǿ��ˤ���
	$objQuery = new SC_Query();
	$path = UPDATE_HTTP . "info.txt";
	$fp = @fopen($path, "rb");
	
	$arrRet = array();
	if(!$fp) {
		sfErrorHeader(">> " . $path . "�μ����˼��Ԥ��ޤ�����");
	} else {
		while (!feof($fp)) {
			$arrRet[] = $arrCSV = fgetcsv($fp, UPDATE_CSV_LINE_MAX);
		}
		fclose($fp);
	}
	
	return $arrRet;
}



?>