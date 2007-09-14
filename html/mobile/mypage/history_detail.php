<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * ����
 */

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'mypage/history_detail.tpl';
		$this->tpl_title = "MY�ڡ���/��������ܺ�";
		session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$objView = new SC_MobileView();
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

//������������Ƚ��
$from = "dtb_order";
$where = "del_flg = 0 AND customer_id = ? AND order_id = ? ";
$arrval = array($objCustomer->getValue('customer_id'), $_POST['order_id']);
//DB�˾��󤬤��뤫Ƚ��
$cnt = $objQuery->count($from, $where, $arrval);

//�����󤷤Ƥ��ʤ����ޤ���DB�˾���̵�����
if (!$objCustomer->isLoginSuccess() or $cnt == 0){
	sfDispSiteError(CUSTOMER_ERROR, "", false, "", true);
} else {
	//����ܺ٥ǡ����μ���
	$objPage->arrDisp = lfGetOrderData($_POST['order_id']);
	// ��ʧ����ˡ�μ���
	$objPage->arrPayment = sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
	// �������֤μ���
	$arrRet = sfGetDelivTime($objPage->arrDisp['payment_id']);
	$objPage->arrDelivTime = sfArrKeyValue($arrRet, 'time_id', 'deliv_time');

	//�ޥ��ڡ����ȥå׸ܵҾ���ɽ����
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
	$objPage->CustomerPoint = $objCustomer->getvalue('point');
}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------

//����ܺ٥ǡ����μ���
function lfGetOrderData($order_id) {
	//�����ֹ椬�����Ǥ����
	if(sfIsInt($order_id)) {
		// DB������������ɤ߹���
		$objQuery = new SC_Query();
		$col = "order_id, create_date, payment_id, subtotal, tax, use_point, add_point, discount, ";
		$col .= "deliv_fee, charge, payment_total, deliv_name01, deliv_name02, deliv_kana01, deliv_kana02, ";
		$col .= "deliv_zip01, deliv_zip02, deliv_pref, deliv_addr01, deliv_addr02, deliv_tel01, deliv_tel02, deliv_tel03, deliv_time_id, deliv_date ";
		$from = "dtb_order";
		$where = "order_id = ?";
		$arrRet = $objQuery->select($col, $from, $where, array($order_id));
		$arrOrder = $arrRet[0];
		// ����ܺ٥ǡ����μ���
		$arrRet = lfGetOrderDetail($order_id);
		$arrOrderDetail = sfSwapArray($arrRet);
		$arrData = array_merge($arrOrder, $arrOrderDetail);
	}
	return $arrData;
}

// ����ܺ٥ǡ����μ���
function lfGetOrderDetail($order_id) {
	$objQuery = new SC_Query();
	$col = "product_id, product_code, product_name, classcategory_name1, classcategory_name2, price, quantity, point_rate";
	$where = "order_id = ?";
	$objQuery->setorder("classcategory_id1, classcategory_id2");
	$arrRet = $objQuery->select($col, "dtb_order_detail", $where, array($order_id));
	return $arrRet;
}

?>
