<?php
include("DB.php"); // PEAR �� DB ���饹���ɤ߹���

print("start");

$db = DB::connect("mysql://eccube_db_user:password@naka.lockon.net/eccube_db");
$result = $db->query("select * from test");
while($row = $result->fetchRow()){
    print_r($row);
}


print("end");

?> 