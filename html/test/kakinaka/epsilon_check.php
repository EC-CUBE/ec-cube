<?php
require_once("../../require.php");
require_once(MODULE_PATH . "mdl_epsilon/mdl_epsilon.inc");

$objQuery = new SC_Query();

// trans_code ����ꤵ��Ƥ��Ƴ�ġ�����Ѥߤξ��
//if($_POST["trans_code"] != "" and $_POST["paid"] == 1){
	// ���ơ�����������Ѥߤ��ѹ�����
	//$sql = "UPDATE dtb_order SET status = 6 WHERE order_id = ?";
	//$objQuery->query($sql, array($_POST["order_number"]));
	$sql = "UPDATE dtb_order SET status = 6";
	$objQuery->query($sql);
//}

$objQuery->getlastquery();
?>