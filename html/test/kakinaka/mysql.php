<?php
require_once './DB.php'; // PEAR �� DB ���饹���ɤ߹���

print("start<br>");

$dsn = "mysql://eccube_db_user:password@210.188.212.163/eccube_db";

print($dsn."<br>");

if(($db = DB::connect($dsn)) == 0){
  print "�����äȡ��ǡ����١�������³�Ǥ��ޤ���";
}
print("end");
$result = $db->query("select * from test");
while($row = $result->fetchRow()){
    print_r($row);
}

print("end");

?> 