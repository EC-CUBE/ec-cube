<?php

$to = "naka@tokado.jp";
$body = "テストですねん。";
$body = mb_convert_encoding($body, 'JIS', "EUC-JP");

if(mb_send_mail($to, "test", $body)){
	print("ok");
}
?>