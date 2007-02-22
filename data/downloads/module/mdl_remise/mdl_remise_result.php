<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("./require.php");
require_once(DATA_PATH . "module/Request.php");

$objQuery = new SC_Query();

if($_GET['module_id'] != ""){
	$module_id = $_GET['module_id'];
}elseif($_POST['module_id'] != ""){
	$module_id = $_POST['module_id'];
}

$log_path = DATA_PATH . "logs/remise.log";
gfPrintLog("remise result start---------------------------------------------------------", $log_path);
foreach($_POST as $key => $val){
	gfPrintLog( "\t" . $key . " => " . $val, $log_path);
}
gfPrintLog("remise result end-----------------------------------------------------------", $log_path);

// ¿‹”Ô†
$order_id = $_POST["X-S_TORIHIKI_NO"];
$payment_total = $_POST["X-TOTAL"];

gfPrintLog("order_id : ".$order_id, $log_path);
gfPrintLog("payment_total : ".$payment_total, $log_path);

$arrTempOrder = $objQuery->getall("SELECT payment_total FROM dtb_order_temp WHERE order_id = ? ", array($order_id));

gfPrintLog("DATA COUNT : ".count($arrTempOrder), $log_path);

// ‹àŠz‚Ì‘Šˆá
if (count($arrTempOrder) > 0) {
	gfPrintLog("ORDER payment_total : ".$arrTempOrder[0]['payment_total'], $log_path);
	if ($arrTempOrder[0]['payment_total'] != $payment_total) {
		print("ERROR");
		exit;
	}
	print("<SDBKDATA>STATUS=800</SDBKDATA>");
	exit;
}
print("ERROR");
exit;

?>