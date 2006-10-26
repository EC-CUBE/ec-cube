<?php

$to = "naka@lockon.co.jp";
$body = "アイウロエ??????彅??";

mb_convert_encoding($value, 'UTF-8', mb_internal_encoding());

mb_language("uni");
if(mb_send_mail($to, "test", $body)){
	print("ok");
}

?>