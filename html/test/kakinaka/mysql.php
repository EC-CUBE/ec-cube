<?php
require_once 'DB.php'; // PEAR �� DB ���饹���ɤ߹���

print("start");

$dsn = "mysql://eccube_db_user:password@kakinaka.ec-cube.net/eccube_db";
if(($db = DB::connect($dsn)) == 0){
  print "�����äȡ��ǡ����١�������³�Ǥ��ޤ���";
}

$result = $db->query("select * from test");
while($row = $result->fetchRow()){
    print_r($row);
}

print("end");

?> 