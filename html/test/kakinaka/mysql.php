<?php
require_once './DB.php'; // PEAR の DB クラスを読み込む

print("start<br>");

$dsn = "mysql://eccube_db_user:password@210.188.212.163/eccube_db";
/*
print($dsn."<br>");

if(($db = DB::connect($dsn)) == 0){
  print "おおっと！データベースに接続できません。";
}
$result = $db->query("select * from dtb_baseinfo");
while($row = $result->fetchRow()){
    print_r($row);
}
*/

$sql = "SELECT * FROM dtb_baseinfo WHERE";
$sql = getMailAddress($sql);
print_r($sql);

print("end");

// 文字列の中に存在するメールアドレスのみを取得し、配列として返す
function getMailAddress($str){
	$arrMail = array();
	preg_match_all("/FROM+([a-zA-Z0-9_\.\+\?-]+WHERE+)/", $str, $arrMail);
	return $arrMail[0];
}

?> 