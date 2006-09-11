<?php
include("DB.php"); // PEAR の DB クラスを読み込む

$db = DB::connect("mysql://eccube_db_user:password@test.ec-cube.net");
$result = $db->query("select * from test");
while($row = $result->fetchRow()){
    print_r($row);
}

?> 