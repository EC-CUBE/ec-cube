<?php
require_once("../../require.php");
require_once(MODULE_PATH . "mdl_epsilon/mdl_epsilon.inc");

$objQuery = new SC_Query();

// trans_code ����ꤵ��Ƥ��Ƴ�ġ�����Ѥߤξ��
if($_POST["trans_code"] != "" and $_POST["paid"] == 1 and $_POST["order_number"] != ""){
	// ���ơ�����������Ѥߤ��ѹ�����
	$sql = "UPDATE dtb_order SET status = 6, update_date = now() WHERE order_id = ? AND memo04 = ? ";
	$objQuery->query($sql, array($_POST["order_number"], $_POST["trans_code"]));
	
	// POST�����Ƥ����ƥ���¸
	$log_path = DATA_PATH . "logs/epsilon.log";
	gfPrintLog("epsilon conveni start---------------------------------------------------------", $log_path);
	foreach($_POST as $key => $val){
		gfPrintLog( "\t" . $key . " => " . $val, $log_path);
	}
	gfPrintLog("epsilon conveni end-----------------------------------------------------------", $log_path);
}

?>