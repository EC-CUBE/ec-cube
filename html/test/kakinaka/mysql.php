<?php
require_once './DB.php'; // PEAR �� DB ���饹���ɤ߹���

print("start<br>");

$dsn = "mysql://eccube_db_user:password@210.188.212.163/eccube_db";
/*
print($dsn."<br>");

if(($db = DB::connect($dsn)) == 0){
  print "�����äȡ��ǡ����١�������³�Ǥ��ޤ���";
}
$result = $db->query("select * from dtb_baseinfo");
while($row = $result->fetchRow()){
    print_r($row);
}
*/

$sql = "SELECT * FROMdtb_baseinfoWHERE";
$sql = getMailAddress($sql);
print_r($sql);

print("end");

// ʸ��������¸�ߤ���᡼�륢�ɥ쥹�Τߤ������������Ȥ����֤�
function getMailAddress($str){
	$arrMail = array();
	preg_match_all("/FROM+([a-zA-Z0-9_\.\+\?-]+WHERE)/", $str, $arrMail);
	return $arrMail[0];
}

?> 