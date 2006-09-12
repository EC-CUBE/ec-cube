<?php
include("DB.php"); // PEAR の DB クラスを読み込む

print("start");

$db = DB::connect("mysql://eccube_db_user:password@210.188.212.163/eccube_db");
$result = $db->query("select * from test");
while($row = $result->fetchRow()){
    print_r($row);
}


print("end");

?> 