<?php
require_once("../../require.php");
require_once(MODULE_PATH . "mdl_epsilon/mdl_epsilon.inc");

$objQuery = new SC_Query();

// trans_code ����ꤵ��Ƥ��Ƴ�ġ�����Ѥߤξ��
if($_POST["trans_code"] != "" and $_POST["paid"] == 1 and $_POST["order_number"] != ""){
	// ���ơ�����������Ѥߤ��ѹ�����
	$sql = "UPDATE dtb_order SET status = 6 WHERE order_id = ? AND memo04 = ? ";
	$objQuery->query($sql, array($_POST["order_number"], $_POST["trans_code"]));
	gfPrintLog("epsilon test");
}

//gfPrintLog("epsilon test", "logs/epsilon.log");
gfPrintLog("epsilon test");

?>