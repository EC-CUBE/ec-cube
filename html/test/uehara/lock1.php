<?php
require_once("../../require.php");

$objQuery = new SC_Query();

// ロックする
$objQuery->query('LOCK TABLES dtb_order WRITE');

for($i=0; $i < 5; $i++) {
    sleep(3);
}

// 次のIncrementを取得
$arrRet = $objQuery->getAll("SHOW TABLE STATUS LIKE ?", array('dtb_order'));
$auto_inc_no = $arrRet[0]["Auto_increment"];

echo $auto_inc_no;


// 値をカウントアップしておく
//$objQuery->conn->query("ALTER TABLE dtb_order AUTO_INCREMENT=?" , $auto_inc_no + 1);


for($i=0; $i < 5; $i++) {
    sleep(3);
}

// ロック解除
$objQuery->query('UNLOCK TABLES');


?>
