<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/*�����ξ��ʤ���ä��ͤϤ���ʾ��ʤ���äƤ��ޤ������ץե�����  */
require_once(DATA_PATH . "lib/slib.php");
require_once(DATA_PATH . "lib/glib.php");
require_once(DATA_PATH . "class/SC_Query.php");
require_once(DATA_PATH . "class/SC_DbConn.php");
		
$objQuery = new SC_Query();

$objQuery->begin();
$objQuery->delete("dtb_bat_relate_products");
$arrCID = $objQuery->select("customer_id", "dtb_order", "del_flg = 0");
foreach($arrCID as $cdata) {
	$where = "order_id IN (SELECT order_id FROM dtb_order WHERE customer_id = ? )";
	//�ܵҤ������������ʣɣĤ��������
	$arrPID = $objQuery->select("product_id", "dtb_order_detail", $where, array($cdata['customer_id']));
	//�ܵҤ����ʤ�ʣ���������Ƥ����
	if(count($arrPID) > 1) {
		foreach($arrPID as $pdata1) {
			//���ξ���ID
			$sqlval['product_id'] = $pdata1['product_id'];
			foreach($arrPID as $pdata2) {
				if($pdata2['product_id'] != $pdata1['product_id']) {
					//����ʾ���ID
					$sqlval['relate_product_id'] = $pdata2['product_id'];
					$sqlval['create_date'] = "now()";
					//�ǡ�������
					$objQuery->insert("dtb_bat_relate_products", $sqlval);
				}
			}
		}
	}
}
$objQuery->commit();

?>