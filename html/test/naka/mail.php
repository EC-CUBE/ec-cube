<?php

$to = "naka@lockon.co.jp";
//$body = "�ƥ��ȤǤ�����������??????���??";
$body = "�ƥ��ȤǤ���";


$body = mb_convert_encoding($body, 'UTF-8', 'EUC-JP');

print("<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>\n");
print($body);

/*
mb_language("uni");

if(mb_send_mail($to, "test", $body)){
	print("ok");
}
*/

?>