<?php

$to = "naka@lockon.co.jp";
$body = "��������??????���??";

$body = mb_convert_encoding($body, 'UTF-8', mb_internal_encoding());

mb_language("uni");

if(mb_send_mail($to, "test", $body)){
	print("ok");
}

?>