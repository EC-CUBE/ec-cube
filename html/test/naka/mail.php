<?php

$to = "naka@lockon.co.jp";
$body = "�ƥ��ȤǤ�����������??????���??";

$body = mb_convert_encoding($body, 'UTF-8', 'EUC-JP');

print($body);

/*
mb_language("uni");

if(mb_send_mail($to, "test", $body)){
	print("ok");
}
*/

?>