<?php
require_once './DB.php'; // PEAR の DB クラスを読み込む

print("start<br>");

$dsn = "mysql://eccube_db_user:password@210.188.212.163/eccube_db";

print($dsn."<br>");

if(($db = DB::connect($dsn)) == 0){
  print "おおっと！データベースに接続できません。";
}

$result = $db->query("select * from test");
while($row = $result->fetchRow()){
    print_r($row);
}

print("end");

?> 