<?php
include("DB.php"); // PEAR の DB クラスを読み込む

print("start");

$db = DB::connect("mysql://eccube_db_user:password@test.ec-cube.net/eccube_db");
$result = $db->query("select * from test");
while($row = $result->fetchRow()){
    print_r($row);
}


print("end");

?> 