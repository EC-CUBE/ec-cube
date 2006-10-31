<?php

$to = "naka@lockon.co.jp";
//$body = "テストです。アイウロエ??????彅??";
$body = "テストです。";

$body = mb_convert_encoding($body, 'UTF-8');

print("<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'></head><body>\n");
print("<b>" . $body . "</b>");

print("</body></html>");

/*
mb_language("uni");

if(mb_send_mail($to, "test", $body)){
	print("ok");
}
*/

?>