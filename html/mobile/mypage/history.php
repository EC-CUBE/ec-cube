<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * MyPage
 */

require_once("../require.php");

class LC_Page{
	function LC_Page() {
		$this->tpl_mainpage = 'mypage/history.tpl';
		$this->tpl_title = 'MY�ڡ���/�����������';
		session_cache_limiter('private-no-expire');
	}
}

define ("HISTORY_NUM", 5);

$objPage = new LC_Page();
$objView = new SC_MobileView();
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();
$pageNo = isset($_GET['pageno']) ? $_GET['pageno'] : 0;

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

// ����������å�
if(!isset($_SESSION['customer'])) {
	sfDispSiteError(CUSTOMER_ERROR, "", false, "", true);
}

$col = "order_id, create_date, payment_id, payment_total";
$from = "dtb_order";
$where = "del_flg = 0 AND customer_id=?";
$arrval = array($objCustomer->getvalue('customer_id'));
$order = "order_id DESC";

$linemax = $objQuery->count($from, $where, $arrval);
$objPage->tpl_linemax = $linemax;

// �����ϰϤλ���(���Ϲ��ֹ桢�Կ��Υ��å�)
$objQuery->setlimitoffset(HISTORY_NUM, $pageNo);
// ɽ�����
$objQuery->setorder($order);

//��������μ���
$objPage->arrOrder = $objQuery->select($col, $from, $where, $arrval);

// next
if ($pageNo + HISTORY_NUM < $linemax) {
	$next = "<a href='history.php?pageno=" . ($pageNo + HISTORY_NUM) . "'>���آ�</a>";
} else {
	$next = "";
}

// previous
if ($pageNo - HISTORY_NUM > 0) {
	$previous = "<a href='history.php?pageno=" . ($pageNo - HISTORY_NUM) . "'>������</a>";
} elseif ($pageNo == 0) {
	$previous = "";
} else {
	$previous = "<a href='history.php?pageno=0'>������</a>";
}

// bar
if ($next != '' && $previous != '') {
	$bar = " | ";
} else {
	$bar = "";
}

$objPage->tpl_strnavi = $previous . $bar . $next;
$objView->assignobj($objPage);				//$objpage������ƤΥƥ�ץ졼���ѿ���smarty�˳�Ǽ
$objView->display(SITE_FRAME);				//�ѥ��ȥƥ�ץ졼���ѿ��θƤӽФ����¹�
?>
