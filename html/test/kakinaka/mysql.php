<?php
include("DB.php"); // PEAR �� DB ���饹���ɤ߹���

$db = DB::connect("mysql://�ǡ����١����桼��:�ѥ����@localhost/test");
$result = $db->query("select * from test");
while($row = $result->fetchRow()){
    print_r($row);
}

?> 