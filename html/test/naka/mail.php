<?php

$to = "naka@lockon.co.jp";
$body = "テストです。アイウロエ??????彅??";

$body = mb_convert_encoding($body, 'UTF-8', 'EUC-JP');

print($body);

/*
mb_language("uni");

if(mb_send_mail($to, "test", $body)){
	print("ok");
}
*/

?>