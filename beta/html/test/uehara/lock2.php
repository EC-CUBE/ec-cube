<?php
require_once("../../require.php");

$objQuery = new SC_Query();


// 次のIncrementを取得
$arrRet = $objQuery->getAll("SHOW TABLE STATUS LIKE ?", array('dtb_order'));
$auto_inc_no = $arrRet[0]["Auto_increment"];

echo $auto_inc_no;

?>
