<?php
require_once("../../require.php");

$objQuery = new SC_Query();

// ��å�����
$objQuery->query('LOCK TABLES dtb_order READ');

for($i=0; $i < 5; $i++) {
    sleep(3);
}

// ����Increment�����
$arrRet = $objQuery->getAll("SHOW TABLE STATUS LIKE ?", array('dtb_order'));
$auto_inc_no = $arrRet[0]["Auto_increment"];

echo $auto_inc_no;


// �ͤ򥫥���ȥ��åפ��Ƥ���
//$objQuery->conn->query("ALTER TABLE dtb_order AUTO_INCREMENT=?" , $auto_inc_no + 1);


for($i=0; $i < 5; $i++) {
    sleep(3);
}

// ��å����
$objQuery->query('UNLOCK TABLES');


?>
