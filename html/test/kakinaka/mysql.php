<?php
include("DB.php"); // PEAR の DB クラスを読み込む

$db = DB::connect("mysql://データベースユーザ:パスワード@localhost/test");
$result = $db->query("select * from test");
while($row = $result->fetchRow()){
    print_r($row);
}

?> 