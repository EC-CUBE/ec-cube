<?php
require_once("../require.php");

$cgi_dir = "/home/web/ec-kit.lockon.co.jp/cgi-bin/";
$cgi_name = "mauthonly.cgi";

##  ������

$name01 = "fuku";
$name02 = "hiro";
$amount = "5000";
$order_id = date("YmdHis", mktime());
$card_no = "4111111111111111";
$card_exp = "12/05";							# MM/DD


$result = sfGetAuthonlyResult($cgi_dir, $cgi_name, $name01, $name02, $card_no, $card_exp, $amount, $order_id);


echo "��������ʸ������ޤ�<br><br>\n";

if ( $result['action-code'] === '000' ){
	echo "���:����<br><br>\n";
} else {
	echo "���:����<br><br>\n";
}

echo "<pre>";
print_R($result);
echo "</pre>";

##  ������

$name01 = "fuku";
$name02 = "hiro";
$amount = "500000";							
$order_id = date("YmdHis"."1", mktime());
$card_no = "4111111111111111";
$card_exp = "09/04";						## �դ뤤������					

echo "��ä���ʸ������ޤ�<br><br>\n";


$result = sfGetAuthonlyResult($cgi_dir, $cgi_name, $name01, $name02, $card_no, $card_exp, $amount, $order_id);


if ( $result['action-code'] === '000' ){
	echo "���:����<br><br>\n";
} else {
	echo "���:����<br><br>\n";
}

echo "<pre>";
print_R($result);
echo "</pre>";

?>